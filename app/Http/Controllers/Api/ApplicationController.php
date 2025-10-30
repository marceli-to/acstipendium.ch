<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreApplicationRequest;
use Illuminate\Support\Facades\Notification;
use Statamic\Facades\Entry;
use App\Notifications\Application\UserConfirmation;
use App\Notifications\Application\OwnerInformation;

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
    ];

    $entry = Entry::make()
      ->collection('applications')
      ->slug($title)
      ->data($data)
      ->save();

    // Clear Statamic caches
    \Statamic\Facades\Stache::clear();
    \Illuminate\Support\Facades\Artisan::call('cache:clear');

    // Notification::route('mail', $request->input('email'))
    //   ->notify(new UserConfirmation($data));

    // Notification::route('mail', env('MAIL_TO'))
    //   ->notify(new OwnerInformation($data));

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
        \Illuminate\Support\Str::random(8),
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
    // Create URL-safe email for folder name
    $email = $request->input('email');
    $emailSafe = str_replace('@', '-at-', $email);
    $emailSafe = preg_replace('/\.([a-z]{2,})$/i', '_$1', $emailSafe);

    // Create folder name: name-firstname-email
    return sprintf(
      '%s-%s-%s',
      \Illuminate\Support\Str::slug($request->input('name')),
      \Illuminate\Support\Str::slug($request->input('firstname')),
      $emailSafe
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
      \Illuminate\Support\Str::slug($request->input('firstname')),
      \Illuminate\Support\Str::slug($request->input('name'))
    );
  }
}