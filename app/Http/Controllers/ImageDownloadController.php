<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageDownloadController extends Controller
{
    public function download(string $filename): BinaryFileResponse
    {
        // Only allow the filename, preventing path traversal.
        $filename = basename($filename);

        $path = public_path('images/' . $filename);

        if (!File::exists($path)) {
            abort(404, 'Image not found.');
        }

        return response()->download(
            $path,
            $filename,
            [
                'Content-Type' => File::mimeType($path),
            ]
        );
    }
}