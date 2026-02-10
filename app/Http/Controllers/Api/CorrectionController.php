<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Notifications\Application\CorrectionLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Statamic\Facades\Entry;
use ZipArchive;

class CorrectionController extends Controller
{
    /**
     * Request a correction link by email.
     */
    public function requestCorrection(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'E-Mail ist erforderlich',
            'email.email' => 'E-Mail muss gültig sein',
        ]);

        $entry = Entry::query()
            ->where('collection', 'applications')
            ->where('email', $request->input('email'))
            ->first();

        if ($entry) {
            $token = Str::random(64);
            $entry->set('correction_token', $token);
            $entry->set('correction_token_expires_at', now()->addHours(48)->toIso8601String());
            $entry->save();

            try {
                Notification::route('mail', $request->input('email'))
                    ->notify(new CorrectionLink($token));
            } catch (\Exception $e) {
                \Log::error('Failed to send correction link email', [
                    'email' => $request->input('email'),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json(['message' => 'ok']);
    }

    /**
     * Load application data for correction by token.
     */
    public function loadCorrection(string $token)
    {
        $entry = $this->findByToken($token);

        if (! $entry) {
            return response()->json(['message' => 'Token ungültig oder abgelaufen.'], 404);
        }

        $data = $entry->data()->toArray();

        // Reconstruct works array from individual fields
        $works = [];
        for ($i = 1; $i <= 3; $i++) {
            if (! empty($data["work_{$i}_title"])) {
                $works[] = [
                    'title' => $data["work_{$i}_title"] ?? '',
                    'year' => $data["work_{$i}_year"] ?? '',
                    'dimensions' => $data["work_{$i}_dimensions"] ?? '',
                    'duration' => $data["work_{$i}_duration"] ?? '',
                    'technology' => $data["work_{$i}_technology"] ?? '',
                    'remarks' => $data["work_{$i}_remarks"] ?? '',
                ];
            }
        }

        // Build file info by scanning the application folder
        $files = [];

        // Derive folder from zip_file or resume_file path
        $folder = null;
        if (! empty($data['zip_file'])) {
            $folder = dirname($data['zip_file']);
        } elseif (! empty($data['resume_file'])) {
            $folder = dirname($data['resume_file']);
        }

        if ($folder && Storage::exists($folder)) {
            $allFiles = Storage::files($folder);

            $files['age_verification'] = [];
            $files['geographic_relation'] = [];

            foreach ($allFiles as $filePath) {
                $basename = basename($filePath);
                $fileInfo = [
                    'name' => $basename,
                    'size' => Storage::size($filePath),
                    'download_url' => "/api/correction/{$token}/download/file?path=" . urlencode($basename),
                ];

                if (str_contains($basename, 'alters_verifikation')) {
                    $files['age_verification'][] = $fileInfo;
                } elseif (str_contains($basename, 'bernbezug')) {
                    $files['geographic_relation'][] = $fileInfo;
                }
            }

            // Clean up empty arrays
            if (empty($files['age_verification'])) unset($files['age_verification']);
            if (empty($files['geographic_relation'])) unset($files['geographic_relation']);
        }

        // Resume/dossier
        if (! empty($data['resume_file']) && Storage::exists($data['resume_file'])) {
            $files['resume'] = [
                'name' => basename($data['resume_file']),
                'size' => Storage::size($data['resume_file']),
                'download_url' => "/api/correction/{$token}/download/resume",
            ];
        }

        return response()->json([
            'name' => $data['name'] ?? '',
            'firstname' => $data['firstname'] ?? '',
            'name_artist_group' => $data['name_artist_group'] ?? '',
            'dob' => $data['dob'] ?? '',
            'street' => $data['street'] ?? '',
            'zip' => $data['zip'] ?? '',
            'location' => $data['location'] ?? '',
            'phone' => $data['phone'] ?? '',
            'website' => $data['website'] ?? '',
            'email' => $data['email'] ?? '',
            'geographic_relation_text' => $data['geographic_relation_text'] ?? '',
            'remarks' => $data['remarks'] ?? '',
            'works' => $works,
            'files' => $files,
        ]);
    }

    /**
     * Store corrected application data.
     */
    public function storeCorrection(Request $request, string $token)
    {
        $entry = $this->findByToken($token);

        if (! $entry) {
            return response()->json(['message' => 'Token ungültig oder abgelaufen.'], 404);
        }

        $request->validate([
            'name' => 'required',
            'firstname' => 'required',
            'name_artist_group' => 'nullable',
            'dob' => 'required|date',
            'street' => 'required',
            'zip' => 'required',
            'location' => 'required',
            'phone' => 'required',
            'email' => 'required|email|regex:/^[^\s@]+@[^\s@]+\.[^\s@]+$/',
            'geographic_relation_text' => 'required|max:500',
            'age_verification_files' => 'nullable|array',
            'age_verification_files.*' => 'file|mimes:png,jpg,jpeg,pdf|max:10240',
            'geographic_relation_proofs' => 'nullable|array',
            'geographic_relation_proofs.*' => 'file|mimes:png,jpg,jpeg,pdf|max:10240',
            'resume_files' => 'nullable|array',
            'resume_files.*' => 'file|mimes:pdf|max:20480',
            'works' => 'required|array|min:1|max:3',
            'works.*.title' => 'required|string|max:255',
            'works.*.year' => 'required|string|max:255',
            'works.*.dimensions' => 'nullable|string|max:255',
            'works.*.duration' => 'nullable|string|max:255',
            'works.*.technology' => 'required|string|max:500',
            'works.*.remarks' => 'nullable|string|max:500',
            'privacy_truthful' => 'accepted',
            'privacy_original_work' => 'accepted',
            'privacy_ai' => 'accepted',
            'privacy_data' => 'accepted',
        ], [
            'name.required' => 'Name ist erforderlich',
            'firstname.required' => 'Vorname ist erforderlich',
            'dob.required' => 'Geburtsdatum ist erforderlich',
            'dob.date' => 'Geburtsdatum muss ein gültiges Datum sein',
            'street.required' => 'Adresse ist erforderlich',
            'zip.required' => 'PLZ ist erforderlich',
            'location.required' => 'PLZ/Ort ist erforderlich',
            'phone.required' => 'Telefon ist erforderlich',
            'email.required' => 'E-Mail ist erforderlich',
            'email.email' => 'E-Mail muss gültig sein',
            'email.regex' => 'E-Mail muss gültig sein',
            'geographic_relation_text.required' => 'Bernbezug ist erforderlich',
            'geographic_relation_text.max' => 'Bernbezug darf maximal 500 Zeichen enthalten',
            'age_verification_files.*.mimes' => 'Altersnachweis muss eine PNG, JPG, JPEG oder PDF Datei sein',
            'age_verification_files.*.max' => 'Altersnachweis darf maximal 10 MB groß sein',
            'geographic_relation_proofs.*.mimes' => 'Beleg muss eine PNG, JPG, JPEG oder PDF Datei sein',
            'geographic_relation_proofs.*.max' => 'Beleg darf maximal 10 MB groß sein',
            'resume_files.*.mimes' => 'Dossier muss eine PDF Datei sein',
            'resume_files.*.max' => 'Dossier darf maximal 20 MB groß sein',
            'works.required' => 'Mindestens ein Werk ist erforderlich',
            'works.min' => 'Mindestens ein Werk muss angegeben werden',
            'works.max' => 'Maximal 3 Werke können eingereicht werden',
            'works.*.title.required' => 'Werktitel ist erforderlich',
            'works.*.year.required' => 'Jahr ist erforderlich',
            'works.*.technology.required' => 'Technik ist erforderlich',
            'privacy_truthful.accepted' => 'Sie müssen bestätigen, dass Ihre Angaben wahrheitsgemäss sind',
            'privacy_original_work.accepted' => 'Sie müssen bestätigen, dass die Arbeiten eigenständig entstanden sind',
            'privacy_ai.accepted' => 'Sie müssen bestätigen, dass der Einsatz von KI gekennzeichnet ist',
            'privacy_data.accepted' => 'Sie müssen die Teilnahmebedingungen und Datenschutzerklärung akzeptieren',
        ]);

        // Normalize website URL
        $website = $request->input('website');
        if ($website && ! preg_match('/^https?:\/\//', $website)) {
            $website = 'https://'.$website;
        }

        // Generate filename prefix
        $filenamePrefix = $this->generateFilenamePrefix($request);

        // Handle file uploads — only replace if new files are provided
        $newAgeFiles = $this->uploadMultipleFiles($request, 'age_verification_files', $filenamePrefix.'alters_verifikation');
        $newResumeFiles = $this->uploadMultipleFiles($request, 'resume_files', $filenamePrefix.'dossier');
        $newGeoFiles = $this->uploadMultipleFiles($request, 'geographic_relation_proofs', $filenamePrefix.'bernbezug');

        // If new files uploaded, recreate zip; otherwise keep existing
        if (! empty($newAgeFiles) || ! empty($newGeoFiles)) {
            $zipPath = $this->createApplicationZip($request, $newAgeFiles, $newGeoFiles);
            $entry->set('zip_file', $zipPath);
        }

        if (! empty($newResumeFiles)) {
            $entry->set('resume_file', $newResumeFiles[0]);
        }

        // Update title
        $title = $request->input('firstname').' '.$request->input('name').', '.$request->input('email');
        $entry->set('title', $title);

        // Update text fields
        $entry->set('name', $request->input('name'));
        $entry->set('firstname', $request->input('firstname'));
        $entry->set('name_artist_group', $request->input('name_artist_group'));
        $entry->set('dob', $request->input('dob'));
        $entry->set('street', $request->input('street'));
        $entry->set('zip', $request->input('zip'));
        $entry->set('location', $request->input('location'));
        $entry->set('phone', $request->input('phone'));
        $entry->set('website', $website);
        $entry->set('email', $request->input('email'));
        $entry->set('geographic_relation_text', $request->input('geographic_relation_text'));
        $entry->set('remarks', $request->input('remarks'));

        // Update works
        $works = $request->input('works', []);
        for ($i = 1; $i <= 3; $i++) {
            $workIndex = $i - 1;
            if (isset($works[$workIndex])) {
                $entry->set("work_{$i}_title", $works[$workIndex]['title'] ?? null);
                $entry->set("work_{$i}_year", $works[$workIndex]['year'] ?? null);
                $entry->set("work_{$i}_dimensions", $works[$workIndex]['dimensions'] ?? null);
                $entry->set("work_{$i}_duration", $works[$workIndex]['duration'] ?? null);
                $entry->set("work_{$i}_technology", $works[$workIndex]['technology'] ?? null);
                $entry->set("work_{$i}_remarks", $works[$workIndex]['remarks'] ?? null);
            } else {
                $entry->set("work_{$i}_title", null);
                $entry->set("work_{$i}_year", null);
                $entry->set("work_{$i}_dimensions", null);
                $entry->set("work_{$i}_duration", null);
                $entry->set("work_{$i}_technology", null);
                $entry->set("work_{$i}_remarks", null);
            }
        }

        // Clear token
        $entry->set('correction_token', null);
        $entry->set('correction_token_expires_at', null);
        $entry->save();

        // Regenerate download URLs
        $documentUrl = route('applications.download-zip-protected', ['id' => $entry->id()]);
        $resumeFile = $entry->get('resume_file');
        $resumeUrl = $resumeFile ? route('applications.download-resume-public', ['id' => $entry->id()]) : null;

        $entry->set('document_url', $documentUrl);
        $entry->set('resume_url', $resumeUrl);
        $entry->save();

        \Statamic\Facades\Stache::clear();

        return response()->json(['message' => 'Korrektur erfolgreich gespeichert.']);
    }

    /**
     * Download an existing file by correction token.
     */
    public function downloadFile(Request $request, string $token, string $type)
    {
        $entry = $this->findByToken($token);

        if (! $entry) {
            abort(404, 'Token ungültig oder abgelaufen.');
        }

        // Direct field downloads (zip, resume)
        $fieldMap = [
            'zip' => 'zip_file',
            'resume' => 'resume_file',
        ];

        if (isset($fieldMap[$type])) {
            $filePath = $entry->get($fieldMap[$type]);

            if (! $filePath || ! Storage::exists($filePath)) {
                abort(404, 'Datei nicht gefunden.');
            }

            return response()->download(Storage::path($filePath), basename($filePath));
        }

        // Individual file download by basename (scans the application folder)
        if ($type === 'file') {
            $requestedFile = $request->query('path');
            if (! $requestedFile) {
                abort(404, 'Kein Dateiname angegeben.');
            }

            // Derive folder from zip_file or resume_file
            $folder = null;
            $zipFile = $entry->get('zip_file');
            $resumeFile = $entry->get('resume_file');
            if ($zipFile) {
                $folder = dirname($zipFile);
            } elseif ($resumeFile) {
                $folder = dirname($resumeFile);
            }

            if ($folder) {
                $filePath = $folder . '/' . basename($requestedFile);
                if (Storage::exists($filePath)) {
                    return response()->download(Storage::path($filePath), basename($filePath));
                }
            }

            abort(404, 'Datei nicht gefunden.');
        }

        abort(404, 'Ungültiger Dateityp.');
    }

    /**
     * Find an application entry by valid (non-expired) correction token.
     */
    protected function findByToken(string $token)
    {
        $entries = Entry::query()
            ->where('collection', 'applications')
            ->where('correction_token', $token)
            ->get();

        foreach ($entries as $entry) {
            $expiresAt = $entry->get('correction_token_expires_at');
            if ($expiresAt && now()->lt($expiresAt)) {
                return $entry;
            }
        }

        return null;
    }

    /**
     * Upload multiple files (duplicated from ApplicationController).
     */
    protected function uploadMultipleFiles(Request $request, string $fileFieldName, string $filePrefix): array
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

            $path = $file->storeAs('applications/'.$folderName, $filename);
            $uploadedFiles[] = $path;
        }

        return $uploadedFiles;
    }

    /**
     * Generate a unique folder name for the user.
     */
    protected function generateUserFolderName(Request $request): string
    {
        return sprintf(
            '%s-%s-%s',
            Str::slug($request->input('firstname')),
            Str::slug($request->input('name')),
            date('d-m-Y-H-i-s')
        );
    }

    /**
     * Generate filename prefix from user data.
     */
    protected function generateFilenamePrefix(Request $request): string
    {
        return sprintf(
            '%s-%s-',
            Str::slug($request->input('firstname')),
            Str::slug($request->input('name'))
        );
    }

    /**
     * Create a ZIP file containing age verification and geographic relation files.
     */
    protected function createApplicationZip(Request $request, array $age_verification_files, array $geographic_relation_files): ?string
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

        $zipPath = 'applications/'.$folderName.'/'.$zipFilename;
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
