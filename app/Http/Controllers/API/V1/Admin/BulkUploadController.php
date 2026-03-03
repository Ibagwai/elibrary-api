<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\BulkUploadService;
use Illuminate\Http\Request;

class BulkUploadController extends Controller
{
    public function __construct(
        private BulkUploadService $bulkUploadService
    ) {}

    /**
     * @OA\Post(
     *     path="/admin/bulk-upload",
     *     tags={"Admin"},
     *     summary="Bulk upload content via ZIP",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"file"},
     *                 @OA\Property(property="file", type="string", format="binary", description="ZIP file containing PDFs")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Bulk upload results")
     * )
     */
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:zip|max:512000', // 500MB max
        ]);

        $results = $this->bulkUploadService->processZipUpload($request->file('file'));

        return response()->json([
            'message' => 'Bulk upload processed',
            'results' => $results,
        ]);
    }
}
