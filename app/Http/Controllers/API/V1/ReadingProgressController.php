<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReadingProgressController extends Controller
{
    /**
     * @OA\Get(
     *     path="/reading-progress/{contentId}",
     *     tags={"Reading Progress"},
     *     summary="Get reading progress",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="contentId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Reading progress")
     * )
     */
    public function show(Request $request, $contentId)
    {
        $progress = DB::table('reading_progress')
            ->where('user_id', $request->user()->id)
            ->where('content_item_id', $contentId)
            ->first();

        return response()->json(['data' => $progress]);
    }

    /**
     * @OA\Put(
     *     path="/reading-progress/{contentId}",
     *     tags={"Reading Progress"},
     *     summary="Update reading progress",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="contentId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="total_pages", type="integer"),
     *             @OA\Property(property="progress_percent", type="number")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Progress updated")
     * )
     */
    public function update(Request $request, $contentId)
    {
        $validated = $request->validate([
            'current_page' => 'required|integer',
            'total_pages' => 'nullable|integer',
            'progress_percent' => 'nullable|numeric',
        ]);

        $now = now();
        DB::table('reading_progress')->updateOrInsert(
            [
                'user_id' => $request->user()->id,
                'content_item_id' => $contentId,
            ],
            [
                'current_page' => $validated['current_page'],
                'total_pages' => $validated['total_pages'] ?? null,
                'progress_percent' => $validated['progress_percent'] ?? 0,
                'last_read_at' => $now,
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        return response()->json(['message' => 'Progress updated']);
    }

    /**
     * @OA\Get(
     *     path="/history",
     *     tags={"Reading Progress"},
     *     summary="Get reading history",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Reading history")
     * )
     */
    public function history(Request $request)
    {
        $history = DB::table('reading_progress')
            ->join('content_items', 'reading_progress.content_item_id', '=', 'content_items.id')
            ->where('reading_progress.user_id', $request->user()->id)
            ->select('content_items.*', 'reading_progress.current_page', 'reading_progress.progress_percent', 'reading_progress.last_read_at')
            ->orderBy('reading_progress.last_read_at', 'desc')
            ->paginate(20);

        return response()->json(['data' => $history]);
    }
}
