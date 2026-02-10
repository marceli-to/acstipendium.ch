<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCorrectionRequest;
use App\Notifications\Application\OwnerInformation;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Statamic\Facades\Entry;
use ZipArchive;

class CorrectionController extends Controller
{
    public function store(StoreCorrectionRequest $request)
    {
        $title = '[Korrektur] '.$request->input('firstname').' '.$request->input('name').', '.$request->input('email');

        // Normalize website URL - add https:// if no protocol present
        $website = $request->input('website');
        if ($website && ! preg_match('/^https?:\/\//', $website)) {
            $website = 'https://'.$website;
        }

        // Generate filename prefix from user data
        $filenamePrefix = $this->generateFilenamePrefix($request);

        // Upload all files for ZIP archive
        $age_verification_files = $this->uploadMultipleFiles($request, 'age_verification_files', $filenamePrefix.'alters_verifikation');
        $resume_files = $this->uploadMultipleFiles($request, 'resume_files', $filenamePrefix.'dossier');
        $geographic_relation_files = $this->uploadMultipleFiles($request, 'geographic_relation_proofs', $filenamePrefix.'bernbezug');

        // Map works array to individual fields (max 3 works)
        $works = $request->input('works', []);
        $work_1_data = [];
        $work_2_data = [];
        $work_3_data = [];

        if (isset($works[0])) {
            $work_1_data = [
                'work_1_title' => $works[0]['title'] ?? null,
                'work_1_year' => $works[0]['year'] ?? null,
                'work_1_dimensions' => $works[0]['dimensions'] ?? null,
                'work_1_duration' => $works[0]['duration'] ?? null,
                'work_1_technology' => $works[0]['technology'] ?? null,
                'work_1_remarks' => $works[0]['remarks'] ?? null,
            ];
        }

        if (isset($works[1])) {
            $work_2_data = [
                'work_2_title' => $works[1]['title'] ?? null,
                'work_2_year' => $works[1]['year'] ?? null,
                'work_2_dimensions' => $works[1]['dimensions'] ?? null,
                'work_2_duration' => $works[1]['duration'] ?? null,
                'work_2_technology' => $works[1]['technology'] ?? null,
                'work_2_remarks' => $works[1]['remarks'] ?? null,
            ];
        }

        if (isset($works[2])) {
            $work_3_data = [
                'work_3_title' => $works[2]['title'] ?? null,
                'work_3_year' => $works[2]['year'] ?? null,
                'work_3_dimensions' => $works[2]['dimensions'] ?? null,
                'work_3_duration' => $works[2]['duration'] ?? null,
                'work_3_technology' => $works[2]['technology'] ?? null,
                'work_3_remarks' => $works[2]['remarks'] ?? null,
            ];
        }

        // Create ZIP file with age verification and geographic relation files only (exclude resume)
        $zipPath = $this->createApplicationZip($request, $age_verification_files, $geographic_relation_files);

        // Build data
        $data = array_merge([
            'title' => $title,
            'name' => $request->input('name'),
            'firstname' => $request->input('firstname'),
            'name_artist_group' => $request->input('name_artist_group') ?? null,
            'dob' => $request->input('dob'),
            'street' => $request->input('street'),
            'zip' => $request->input('zip'),
            'location' => $request->input('location'),
            'phone' => $request->input('phone'),
            'website' => $website,
            'email' => $request->input('email'),
            'geographic_relation_text' => $request->input('geographic_relation_text') ?? null,
            'remarks' => $request->input('remarks') ?? null,
            'zip_file' => $zipPath,
            'resume_file' => ! empty($resume_files) ? $resume_files[0] : null,
        ], $work_1_data, $work_2_data, $work_3_data);

        $entry = Entry::make()
            ->collection('corrections')
            ->slug($title)
            ->data($data);

        $entry->save();

        // Clear Statamic Stache to ensure the new entry is immediately available
        \Statamic\Facades\Stache::clear();

        // Send owner information email
        try {
            Notification::route('mail', env('MAIL_TO'))
                ->notify(new OwnerInformation($data));
        } catch (\Exception $e) {
            \Log::error('Failed to send owner information email for correction', [
                'error' => $e->getMessage(),
                'entry_id' => $entry->id(),
            ]);
        }

        return response()->json(['message' => 'Store successful']);
    }

    /**
     * Upload multiple files for the correction (returns all files)
     */
    protected function uploadMultipleFiles(StoreCorrectionRequest $request, string $fileFieldName, string $filePrefix): array
    {
        if (! $request->hasFile($fileFieldName)) {
            return [];
        }

        $folderName = $this->generateUserFolderName($request);
        $uploadedFiles = [];

        foreach ($request->file($fileFieldName) as $file) {
            $filename = sprintf(
                '%s-%s.%s',
                $filePrefix,
                Str::random(8),
                $file->getClientOriginalExtension()
            );

            $path = $file->storeAs('corrections/'.$folderName, $filename);
            $uploadedFiles[] = $path;
        }

        return $uploadedFiles;
    }

    /**
     * Generate a unique folder name for the user based on their details
     */
    protected function generateUserFolderName(StoreCorrectionRequest $request): string
    {
        return sprintf(
            '%s-%s-%s',
            Str::slug($request->input('firstname')),
            Str::slug($request->input('name')),
            date('d-m-Y-H-i-s')
        );
    }

    /**
     * Generate filename prefix from user data (firstname-name-)
     */
    protected function generateFilenamePrefix(StoreCorrectionRequest $request): string
    {
        return sprintf(
            '%s-%s-',
            Str::slug($request->input('firstname')),
            Str::slug($request->input('name'))
        );
    }

    /**
     * Create a ZIP file containing age verification and geographic relation files (excludes resume)
     */
    protected function createApplicationZip(StoreCorrectionRequest $request, array $age_verification_files, array $geographic_relation_files): ?string
    {
        $filePaths = array_filter([
            ...$age_verification_files,
            ...$geographic_relation_files,
        ]);

        if (empty($filePaths)) {
            return null;
        }

        $folderName = $this->generateUserFolderName($request);
        $filenamePrefix = $this->generateFilenamePrefix($request);

        $zipFilename = sprintf(
            '%skorrektur-%s.zip',
            $filenamePrefix,
            date('Y-m-d_H-i-s')
        );

        $zipPath = 'corrections/'.$folderName.'/'.$zipFilename;
        $zipFullPath = Storage::path($zipPath);

        $zip = new ZipArchive;

        if ($zip->open($zipFullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($filePaths as $filePath) {
                $fullPath = Storage::path($filePath);
                if (file_exists($fullPath)) {
                    $zip->addFile($fullPath, basename($filePath));
                }
            }
            $zip->close();

            return $zipPath;
        }

        return null;
    }
}
