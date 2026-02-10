<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Statamic\Facades\Entry;

class DownloadController extends Controller
{
    /**
     * Download application ZIP file (authenticated users only)
     *
     * @param  string  $id  The application entry ID
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function downloadProtectedZip(string $id)
    {
        // Find the application entry
        $entry = Entry::find($id);

        if (! $entry || $entry->collection()->handle() !== 'applications') {
            abort(404, 'Application not found');
        }

        $zipPath = $entry->get('zip_file');

        if (! $zipPath) {
            abort(404, 'ZIP file not found for this application');
        }

        $fullPath = Storage::path($zipPath);

        if (! file_exists($fullPath)) {
            abort(404, 'ZIP file does not exist on server');
        }

        // Get a friendly download filename (sanitize to prevent filesystem issues with slashes etc.)
        $downloadName = sprintf(
            '%s-%s-bewerbung.zip',
            Str::slug($entry->get('firstname') ?? 'applicant'),
            Str::slug($entry->get('name') ?? 'unknown')
        );

        return response()->download($fullPath, $downloadName);
    }

    /**
     * Download resume PDF file (publicly accessible)
     *
     * @param  string  $id  The application entry ID
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\JsonResponse
     */
    public function downloadPublicResume(string $id)
    {
        // Find the application entry
        $entry = Entry::find($id);

        if (! $entry || $entry->collection()->handle() !== 'applications') {
            abort(404, 'Application not found');
        }

        $resumePath = $entry->get('resume_file');

        if (! $resumePath) {
            abort(404, 'Resume file not found for this application');
        }

        $fullPath = Storage::path($resumePath);

        if (! file_exists($fullPath)) {
            abort(404, 'Resume file does not exist on server');
        }

        // Get a friendly download filename (sanitize to prevent filesystem issues with slashes etc.)
        $downloadName = sprintf(
            '%s-%s-dossier.pdf',
            Str::slug($entry->get('firstname') ?? 'applicant'),
            Str::slug($entry->get('name') ?? 'unknown')
        );

        return response()->download($fullPath, $downloadName);
    }
}
