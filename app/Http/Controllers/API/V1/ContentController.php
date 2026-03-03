<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\ContentItem;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class ContentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/content",
     *     tags={"Content"},
     *     summary="List all published content",
     *     @OA\Parameter(name="filter[type]", in="query", @OA\Schema(type="string", enum={"ebook","journal","student_project","lecture"})),
     *     @OA\Parameter(name="filter[language]", in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="sort", in="query", @OA\Schema(type="string", enum={"created_at","-created_at","view_count","-view_count"})),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=20)),
     *     @OA\Response(response=200, description="Content list")
     * )
     */
    public function index(Request $request)
    {
        $query = QueryBuilder::for(ContentItem::class)
            ->allowedFilters([
                AllowedFilter::exact('type'),
                AllowedFilter::exact('status'),
                'language',
            ])
            ->allowedSorts(['created_at', 'view_count', 'download_count'])
            ->where('status', 'published')
            ->with(['categories', 'tags']);

        // Add search functionality
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        // Filter by year
        if ($request->has('year') && $request->year) {
            $query->where('published_year', $request->year);
        }

        $content = $query->paginate($request->get('per_page', 20));

        return response()->json(['data' => $content]);
    }

    /**
     * @OA\Get(
     *     path="/content/{id}",
     *     tags={"Content"},
     *     summary="Get content by ID",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Content details"),
     *     @OA\Response(response=404, description="Content not found")
     * )
     */
    public function show(string $id)
    {
        $content = ContentItem::with(['categories', 'tags', 'ebook', 'journal', 'student_project', 'lecture'])
            ->findOrFail($id);

        // Check access level
        $user = request()->user();
        if (!$user && $content->access_level !== 'public') {
            abort(403, 'This content requires authentication');
        }
        if ($user && $user->role === 'student' && !in_array($content->access_level, ['public', 'authenticated'])) {
            abort(403, 'You do not have permission to access this content');
        }
        if ($user && in_array($user->role, ['faculty', 'staff']) && $content->access_level === 'admin_only') {
            abort(403, 'This content is restricted to administrators');
        }

        $content->increment('view_count');

        return response()->json(['data' => $content]);
    }

    /**
     * @OA\Get(
     *     path="/content/featured",
     *     tags={"Content"},
     *     summary="Get featured content",
     *     @OA\Response(response=200, description="Featured content list")
     * )
     */
    public function featured()
    {
        $content = ContentItem::where('featured', true)
            ->where('status', 'published')
            ->with(['categories', 'tags'])
            ->limit(10)
            ->get();

        return response()->json(['data' => $content]);
    }
}
