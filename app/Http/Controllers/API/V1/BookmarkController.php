<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookmarkController extends Controller
{
    /**
     * @OA\Get(
     *     path="/bookmarks",
     *     tags={"Bookmarks"},
     *     summary="Get user bookmarks",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="User bookmarks")
     * )
     */
    public function index(Request $request)
    {
        $bookmarks = DB::table('bookmarks')
            ->join('content_items', 'bookmarks.content_item_id', '=', 'content_items.id')
            ->where('bookmarks.user_id', $request->user()->id)
            ->select('content_items.*', 'bookmarks.created_at as bookmarked_at')
            ->orderBy('bookmarks.created_at', 'desc')
            ->get();

        return response()->json(['data' => $bookmarks]);
    }

    /**
     * @OA\Post(
     *     path="/bookmarks",
     *     tags={"Bookmarks"},
     *     summary="Add bookmark",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"content_item_id"},
     *             @OA\Property(property="content_item_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Bookmarked successfully")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content_item_id' => 'required|exists:content_items,id',
        ]);

        $bookmark = DB::table('bookmarks')->insertOrIgnore([
            'user_id' => $request->user()->id,
            'content_item_id' => $validated['content_item_id'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Bookmarked successfully']);
    }

    /**
     * @OA\Delete(
     *     path="/bookmarks/{contentId}",
     *     tags={"Bookmarks"},
     *     summary="Remove bookmark",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="contentId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Bookmark removed")
     * )
     */
    public function destroy(Request $request, $contentId)
    {
        DB::table('bookmarks')
            ->where('user_id', $request->user()->id)
            ->where('content_item_id', $contentId)
            ->delete();

        return response()->json(['message' => 'Bookmark removed']);
    }
}
