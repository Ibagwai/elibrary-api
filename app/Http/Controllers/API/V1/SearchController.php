<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Domain\Content\Models\ContentItem;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * @OA\Get(
     *     path="/search",
     *     tags={"Search"},
     *     summary="Search content",
     *     @OA\Parameter(name="q", in="query", required=true, @OA\Schema(type="string"), description="Search query"),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=20)),
     *     @OA\Response(response=200, description="Search results")
     * )
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query)) {
            return response()->json(['data' => []]);
        }

        $results = ContentItem::search($query)
            ->where('status', 'published')
            ->paginate($request->get('per_page', 20));

        return response()->json(['data' => $results]);
    }
}
