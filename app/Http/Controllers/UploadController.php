<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    /**
     * Handle file upload from FilePond
     */
    public function process(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:png,jpg,jpeg,pdf,zip|max:10240', // 10MB max
            'prefix' => 'required|string'
        ]);

        $file = $request->file('file');
        $prefix = $request->input('prefix');

        // Generate unique identifier
        $uniqueId = Str::random(16);

        // Create filename: prefix-uniqueId.extension
        $filename = sprintf(
            '%s-%s.%s',
            $prefix,
            $uniqueId,
            $file->getClientOriginalExtension()
        );

        // Store in non-public temp folder
        $path = $file->storeAs('temp/uploads', $filename, 'local');

        // Return the server ID (path) for FilePond
        return response()->json([
            'server_id' => $path
        ], 200)->header('Content-Type', 'text/plain');
    }

    /**
     * Handle file deletion (revert)
     */
    public function revert(Request $request)
    {
        $serverId = $request->getContent();

        if (Storage::disk('local')->exists($serverId)) {
            Storage::disk('local')->delete($serverId);
        }

        return response('', 200);
    }
}
