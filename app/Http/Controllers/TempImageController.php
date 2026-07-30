<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TempImageController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|image|max:2048',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            $tempDir = public_path('images/temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            $file->move($tempDir, $filename);
            
            return response()->json([
                'filename' => $filename,
                'path' => 'images/temp/' . $filename,
            ]);
        }

        return response()->json(['error' => 'Upload failed'], 400);
    }

    public function destroy($filename)
    {
        $path = public_path('images/temp/' . $filename);
        if (file_exists($path)) {
            unlink($path);
        }

        return response()->json(['success' => true]);
    }
}