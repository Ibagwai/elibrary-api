<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    /**
     * @OA\Post(
     *     path="/media/upload",
     *     tags={"Media"},
     *     summary="Upload file",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"file","content_item_id"},
     *                 @OA\Property(property="file", type="string", format="binary"),
     *                 @OA\Property(property="content_item_id", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="File uploaded successfully")
     * )
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:102400', // 100MB max
            'content_item_id' => 'required|exists:content_items,id',
        ]);

        $file = $request->file('file');
        $path = $file->store('content', 'public');

        // Store in media_files table
        $media = \DB::table('media')->insert([
            'model_type' => 'App\Domain\Content\Models\ContentItem',
            'model_id' => $request->content_item_id,
            'collection_name' => 'default',
            'name' => $file->getClientOriginalName(),
            'file_name' => basename($path),
            'mime_type' => $file->getMimeType(),
            'disk' => 'public',
            'size' => $file->getSize(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'File uploaded successfully',
            'path' => Storage::url($path)
        ]);
    }

    /**
     * @OA\Get(
     *     path="/media/{id}/download",
     *     tags={"Media"},
     *     summary="Download file",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="File download"),
     *     @OA\Response(response=404, description="File not found")
     * )
     */
    public function download($id)
    {
        $media = \DB::table('media')->where('id', $id)->first();
        
        if (!$media) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $path = storage_path('app/public/content/' . $media->file_name);
        
        if (!file_exists($path)) {
            return response()->json(['error' => 'File not found'], 404);
        }

        return response()->download($path, $media->name);
    }
}
