<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApplicationRequest;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Statamic\Facades\Entry;
use App\Notifications\Application\UserConfirmation;
use App\Notifications\Application\OwnerInformation;
use ZipArchive;

class ApplicationController extends Controller
{
  public function store(StoreApplicationRequest $request)
  {
    $title = $request->input('firstname') . ' ' . $request->input('name') . ', ' . $request->input('email');

    // Generate filename prefix from user data
    $filenamePrefix = $this->generateFilenamePrefix($request);

    // Upload files and get file path
    $proof_dob = $this->uploadFiles($request, 'age_verification_files', $filenamePrefix . 'alters_verifikation');

    // Upload resume/dossier
    $resume = $this->uploadFiles($request, 'resume_files', $filenamePrefix . 'dossier');

    // Upload geographic relation proofs and build grid structure
    $geographic_relation_proof = $this->uploadMultipleFiles($request, 'geographic_relation_proofs', $filenamePrefix . 'bernbezug');

    // Build geographic_relation grid field
    $geographic_relation_data = [];
    if ($request->input('geographic_relation_text') || !empty($geographic_relation_proof)) {
      $geographic_relation_data[] = [
        'geographic_relation_text' => $request->input('geographic_relation_text') ?? null,
        'geographic_relation_proof' => $geographic_relation_proof,
      ];
    }

    // Build works grid field
    $works_data = [];
    $works = $request->input('works', []);
    foreach ($works as $work) {
      $works_data[] = [
        'work_title' => $work['title'] ?? null,
        'work_year' => $work['year'] ?? null,
        'work_dimensions' => $work['dimensions'] ?? null,
        'work_duration' => $work['duration'] ?? null,
        'technology' => $work['technology'] ?? null,
        'remarks' => $work['remarks'] ?? null,
      ];
    }

    // Create ZIP file with all uploaded files
    $zipPath = $this->createApplicationZip($request, $proof_dob, $resume, $geographic_relation_proof);

    // Build data
    $data = [
      'title' => $title,
      'name' => $request->input('name'),
      'firstname' => $request->input('firstname'),
      'name_artist_group' => $request->input('name_artist_group') ?? null,
      'dob' => $request->input('dob'),
      'street' => $request->input('street'),
      'zip' => $request->input('zip'),
      'location' => $request->input('location'),
      'phone' => $request->input('phone'),
      'website' => $request->input('website') ?? null,
      'email' => $request->input('email'),
      'geographic_relation' => $geographic_relation_data,
      'proof_dob' => $proof_dob,
      'resume' => $resume,
      'works' => $works_data,
      'remarks' => $request->input('remarks') ?? null,
      'zip_file' => $zipPath,
    ];

    $entry = Entry::make()
      ->collection('applications')
      ->slug($title)
      ->data($data);

    $entry->save();

    // Add entry ID to data for notifications
    $data['entry_id'] = $entry->id();

    // Clear Statamic caches
    \Statamic\Facades\Stache::clear();
    \Illuminate\Support\Facades\Artisan::call('cache:clear');

    Notification::route('mail', $request->input('email'))
      ->notify(new UserConfirmation($data));

    Notification::route('mail', env('MAIL_TO'))
      ->notify(new OwnerInformation($data));

    return response()->json(['message' => 'Store successful']);
  }

  /**
   * Upload files for the application (returns first file only)
   *
   * @param StoreApplicationRequest $request
   * @param string $fileFieldName The form field name containing the files
   * @param string $filePrefix Prefix for the uploaded files
   * @return string|null The path to the first uploaded file, or null if no files
   */
  protected function uploadFiles(StoreApplicationRequest $request, string $fileFieldName, string $filePrefix): ?string
  {
    $files = $this->uploadMultipleFiles($request, $fileFieldName, $filePrefix);
    return !empty($files) ? $files[0] : null;
  }

  /**
   * Upload multiple files for the application (returns all files)
   *
   * @param StoreApplicationRequest $request
   * @param string $fileFieldName The form field name containing the files
   * @param string $filePrefix Prefix for the uploaded files
   * @return array Array of uploaded file paths
   */
  protected function uploadMultipleFiles(StoreApplicationRequest $request, string $fileFieldName, string $filePrefix): array
  {
    if (!$request->hasFile($fileFieldName)) {
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

      // Store in bewerbungen/{folderName} relative to assets disk
      $path = $file->storeAs('bewerbungen/' . $folderName, $filename, 'assets');
      $uploadedFiles[] = $path;
    }

    return $uploadedFiles;
  }

  /**
   * Generate a unique folder name for the user based on their details
   *
   * @param StoreApplicationRequest $request
   * @return string
   */
  protected function generateUserFolderName(StoreApplicationRequest $request): string
  {
    // Create folder name: firstname-name-datetime
    return sprintf(
      '%s-%s-%s',
      Str::slug($request->input('firstname')),
      Str::slug($request->input('name')),
      date('d-m-Y-H-i-s')
    );
  }

  /**
   * Generate filename prefix from user data (firstname-name-)
   *
   * @param StoreApplicationRequest $request
   * @return string
   */
  protected function generateFilenamePrefix(StoreApplicationRequest $request): string
  {
    return sprintf(
      '%s-%s-',
      Str::slug($request->input('firstname')),
      Str::slug($request->input('name'))
    );
  }

  /**
   * Create a ZIP file containing all uploaded application files
   *
   * @param StoreApplicationRequest $request
   * @param string|null $proof_dob
   * @param string|null $resume
   * @param array $geographic_relation_proof
   * @return string|null The path to the created ZIP file
   */
  protected function createApplicationZip(StoreApplicationRequest $request, ?string $proof_dob, ?string $resume, array $geographic_relation_proof): ?string
  {
    // Collect all file paths
    $filePaths = array_filter([
      $proof_dob,
      $resume,
      ...$geographic_relation_proof
    ]);

    // If no files, return null
    if (empty($filePaths)) {
      return null;
    }

    $folderName = $this->generateUserFolderName($request);
    $filenamePrefix = $this->generateFilenamePrefix($request);

    // Create ZIP filename
    $zipFilename = sprintf(
      '%sbewerbung-%s.zip',
      $filenamePrefix,
      date('Y-m-d_H-i-s')
    );

    $zipPath = 'bewerbungen/' . $folderName . '/' . $zipFilename;
    $zipFullPath = Storage::disk('assets')->path($zipPath);

    // Create ZIP archive
    $zip = new ZipArchive();

    if ($zip->open($zipFullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
      foreach ($filePaths as $filePath) {
        $fullPath = Storage::disk('assets')->path($filePath);
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