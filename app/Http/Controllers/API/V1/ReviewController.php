<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * @OA\Get(
     *     path="/content/{contentId}/reviews",
     *     tags={"Reviews"},
     *     summary="Get content reviews",
     *     @OA\Parameter(name="contentId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Reviews list")
     * )
     */
    public function index($contentId)
    {
        $reviews = DB::table('reviews')
            ->join('users', 'reviews.user_id', '=', 'users.id')
            ->where('reviews.content_item_id', $contentId)
            ->where('reviews.is_approved', true)
            ->select('reviews.*', 'users.name as user_name')
            ->orderBy('reviews.created_at', 'desc')
            ->get();

        return response()->json(['data' => $reviews]);
    }

    /**
     * @OA\Post(
     *     path="/content/{contentId}/reviews",
     *     tags={"Reviews"},
     *     summary="Submit review",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="contentId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"rating"},
     *             @OA\Property(property="rating", type="integer", minimum=1, maximum=5),
     *             @OA\Property(property="review_text", type="string")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Review submitted")
     * )
     */
    public function store(Request $request, $contentId)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:1000',
        ]);

        DB::table('reviews')->insert([
            'user_id' => $request->user()->id,
            'content_item_id' => $contentId,
            'rating' => $validated['rating'],
            'review_text' => $validated['review_text'] ?? null,
            'is_approved' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Review submitted for approval'], 201);
    }
}
