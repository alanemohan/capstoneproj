<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ChunkUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'upload_id' => 'required|string',
            'chunk_index' => 'required|integer',
            'total_chunks' => 'required|integer',
            'file' => 'required|file',
            'filename' => 'required|string',
            'type' => 'required|string|in:lessons,lesson-contents',
        ]);

        $uploadId = $request->input('upload_id');
        $chunkIndex = (int) $request->input('chunk_index');
        $totalChunks = (int) $request->input('total_chunks');
        $file = $request->file('file');
        $filename = $request->input('filename');
        $type = $request->input('type'); // 'lessons' or 'lesson-contents'

        // Clean filename and get extension safely
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Validate MIME type/extension
        $allowedExtensions = ['pdf', 'mp4', 'mov', 'avi', 'mkv', 'webm', 'jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($extension, $allowedExtensions)) {
            return response()->json(['error' => 'Invalid file extension.'], 400);
        }

        // Temporary directory for chunks
        $tempDir = 'chunks/' . $uploadId;

        // Store the chunk file
        $chunkName = 'chunk_' . $chunkIndex;
        Storage::disk('local')->putFileAs($tempDir, $file, $chunkName);

        // Check if all chunks are uploaded
        $uploadedChunks = count(Storage::disk('local')->files($tempDir));

        if ($uploadedChunks === $totalChunks) {
            // Merge all chunks
            $finalFilename = Str::random(40) . '.' . $extension;
            $finalPath = $type . '/' . $finalFilename;

            // Make sure the destination directory exists
            Storage::disk('public')->makeDirectory($type);

            $finalDestinationPath = Storage::disk('public')->path($finalPath);
            $out = fopen($finalDestinationPath, 'wb');

            if ($out === false) {
                return response()->json(['error' => 'Failed to open output stream.'], 500);
            }

            // Write chunk by chunk
            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkPath = Storage::disk('local')->path($tempDir . '/chunk_' . $i);
                $in = fopen($chunkPath, 'rb');
                if ($in) {
                    while ($buff = fread($in, 4096)) {
                        fwrite($out, $buff);
                    }
                    fclose($in);
                }
            }
            fclose($out);

            // Clean up chunks
            Storage::disk('local')->deleteDirectory($tempDir);

            return response()->json([
                'completed' => true,
                'file_path' => $finalPath,
                'filename' => basename($finalPath)
            ]);
        }

        return response()->json([
            'completed' => false,
            'progress' => round(($uploadedChunks / $totalChunks) * 100, 2)
        ]);
    }
}
