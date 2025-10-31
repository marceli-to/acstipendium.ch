<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Statamic\Facades\Entry;

class DownloadController extends Controller
{
  /**
   * Download application ZIP file (authenticated users only)
   *
   * @param string $id The application entry ID
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
   */
  public function downloadZip(string $id)
  {
    // Find the application entry
    $entry = Entry::find($id);

    if (!$entry || $entry->collection()->handle() !== 'applications') {
      abort(404, 'Application not found');
    }

    $zipPath = $entry->get('zip_file');

    if (!$zipPath) {
      abort(404, 'ZIP file not found for this application');
    }

    $fullPath = Storage::disk('assets')->path($zipPath);

    if (!file_exists($fullPath)) {
      abort(404, 'ZIP file does not exist on server');
    }

    // Get a friendly download filename
    $downloadName = sprintf(
      '%s-%s-bewerbung.zip',
      $entry->get('firstname') ?? 'applicant',
      $entry->get('name') ?? 'unknown'
    );

    return response()->download($fullPath, $downloadName);
  }
}
