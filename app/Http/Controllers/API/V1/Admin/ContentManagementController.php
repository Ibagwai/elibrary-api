<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ContentManagementController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/content",
     *     tags={"Admin"},
     *     summary="List all content (admin)",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="status", in="query", @OA\Schema(type="string", enum={"draft","under_review","published","rejected"})),
     *     @OA\Response(response=200, description="Content list")
     * )
     */
    public function index(Request $request)
    {
        $content = ContentItem::with(['categories', 'tags', 'uploader'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(20);

        return response()->json(['data' => $content]);
    }

    /**
     * @OA\Post(
     *     path="/admin/content",
     *     tags={"Admin"},
     *     summary="Create content",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"type","title","access_level"},
     *             @OA\Property(property="type", type="string", enum={"ebook","journal","student_project","lecture"}),
     *             @OA\Property(property="title", type="string", example="Introduction to Laravel"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="abstract", type="string"),
     *             @OA\Property(property="language", type="string", example="en"),
     *             @OA\Property(property="published_year", type="integer", example=2024),
     *             @OA\Property(property="access_level", type="string", enum={"public","authenticated","faculty_only","admin_only"})
     *         )
     *     ),
     *     @OA\Response(response=201, description="Content created")
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:ebook,journal,student_project,lecture',
            'title' => 'required|max:500',
            'description' => 'nullable',
            'abstract' => 'nullable',
            'language' => 'nullable|max:10',
            'published_year' => 'nullable|integer',
            'access_level' => 'required|in:public,authenticated,faculty_only,admin_only',
            'file' => 'nullable|file|mimes:pdf|max:51200', // 50MB max
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB max
        ]);

        // Handle file upload
        $filePath = null;
        $fileSize = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('content', $fileName, 'public');
            $fileSize = $file->getSize();
        }

        // Handle thumbnail upload
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $thumbnailName = time() . '_thumb_' . $thumbnail->getClientOriginalName();
            $thumbnailPath = $thumbnail->storeAs('thumbnails', $thumbnailName, 'public');
        }

        $content = ContentItem::create([
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'abstract' => $validated['abstract'] ?? null,
            'language' => $validated['language'] ?? 'en',
            'published_year' => $validated['published_year'] ?? null,
            'access_level' => $validated['access_level'],
            'slug' => Str::slug($validated['title']),
            'status' => 'draft',
            'uploaded_by' => $request->user()->id,
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'thumbnail_path' => $thumbnailPath,
        ]);

        // Create type-specific record
        if ($validated['type'] === 'ebook' && $request->author) {
            \DB::table('ebooks')->insert([
                'content_item_id' => $content->id,
                'author' => $request->author,
                'isbn' => $request->isbn,
                'publisher' => $request->publisher,
                'pages' => $request->pages ? (int)$request->pages : null,
                'edition' => $request->edition,
            ]);
        } elseif ($validated['type'] === 'journal' && $request->journal_name) {
            \DB::table('journals')->insert([
                'content_item_id' => $content->id,
                'journal_name' => $request->journal_name,
                'volume' => $request->volume,
                'issue' => $request->issue,
                'doi' => $request->doi,
            ]);
        } elseif ($validated['type'] === 'student_project' && $request->student_name) {
            \DB::table('student_projects')->insert([
                'content_item_id' => $content->id,
                'student_name' => $request->student_name,
                'supervisor_name' => $request->supervisor_name,
                'degree_level' => $request->degree_level,
                'department' => $request->project_department,
            ]);
        } elseif ($validated['type'] === 'lecture' && $request->instructor_name) {
            \DB::table('lectures')->insert([
                'content_item_id' => $content->id,
                'instructor_name' => $request->instructor_name,
                'course_code' => $request->course_code,
                'course_name' => $request->course_name,
                'duration_seconds' => $request->duration_minutes ? (int)$request->duration_minutes * 60 : null,
            ]);
        }

        return response()->json(['data' => $content], 201);
    }

    /**
     * @OA\Put(
     *     path="/admin/content/{id}",
     *     tags={"Admin"},
     *     summary="Update content",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="status", type="string", enum={"draft","under_review","published","rejected"})
     *         )
     *     ),
     *     @OA\Response(response=200, description="Content updated")
     * )
     */
    public function update(Request $request, $id)
    {
        $content = ContentItem::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'nullable|max:500',
            'description' => 'nullable',
            'abstract' => 'nullable',
            'status' => 'nullable|in:draft,under_review,published,rejected',
            'allow_download' => 'nullable|boolean',
            'file' => 'nullable|file|mimes:pdf|max:51200',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        // Handle file upload if provided
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($content->file_path && \Storage::disk('public')->exists($content->file_path)) {
                \Storage::disk('public')->delete($content->file_path);
            }
            
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('content', $fileName, 'public');
            
            $content->file_path = $filePath;
            $content->file_size = $file->getSize();
        }

        // Handle thumbnail upload if provided
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail if exists
            if ($content->thumbnail_path && \Storage::disk('public')->exists($content->thumbnail_path)) {
                \Storage::disk('public')->delete($content->thumbnail_path);
            }
            
            $thumbnail = $request->file('thumbnail');
            $thumbnailName = time() . '_thumb_' . $thumbnail->getClientOriginalName();
            $thumbnailPath = $thumbnail->storeAs('thumbnails', $thumbnailName, 'public');
            
            $content->thumbnail_path = $thumbnailPath;
        }

        // Update other fields
        if (isset($validated['title'])) {
            $content->title = $validated['title'];
            $content->slug = Str::slug($validated['title']);
        }
        if (isset($validated['description'])) $content->description = $validated['description'];
        if (isset($validated['abstract'])) $content->abstract = $validated['abstract'];
        if (isset($validated['status'])) $content->status = $validated['status'];
        if ($request->has('allow_download')) $content->allow_download = $request->boolean('allow_download');
        
        $content->save();

        // Update type-specific records
        if ($content->type === 'ebook') {
            \DB::table('ebooks')->updateOrInsert(
                ['content_item_id' => $content->id],
                [
                    'author' => $request->input('author'),
                    'isbn' => $request->input('isbn'),
                    'publisher' => $request->input('publisher'),
                    'pages' => $request->input('pages') ? (int)$request->input('pages') : null,
                    'edition' => $request->input('edition'),
                ]
            );
        } elseif ($content->type === 'journal') {
            \DB::table('journals')->updateOrInsert(
                ['content_item_id' => $content->id],
                [
                    'journal_name' => $request->input('journal_name'),
                    'volume' => $request->input('volume'),
                    'issue' => $request->input('issue'),
                    'doi' => $request->input('doi'),
                ]
            );
        } elseif ($content->type === 'student_project') {
            \DB::table('student_projects')->updateOrInsert(
                ['content_item_id' => $content->id],
                [
                    'student_name' => $request->input('student_name'),
                    'supervisor_name' => $request->input('supervisor_name'),
                    'degree_level' => $request->input('degree_level'),
                    'department' => $request->input('project_department'),
                ]
            );
        } elseif ($content->type === 'lecture') {
            \DB::table('lectures')->updateOrInsert(
                ['content_item_id' => $content->id],
                [
                    'instructor_name' => $request->input('instructor_name'),
                    'course_code' => $request->input('course_code'),
                    'course_name' => $request->input('course_name'),
                    'duration_seconds' => $request->input('duration_minutes') ? (int)$request->input('duration_minutes') * 60 : null,
                ]
            );
        }

        return response()->json(['data' => $content]);
    }

    /**
     * @OA\Post(
     *     path="/admin/content/{id}/publish",
     *     tags={"Admin"},
     *     summary="Publish content",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Content published")
     * )
     */
    public function publish($id)
    {
        $content = ContentItem::findOrFail($id);
        $content->update([
            'status' => 'published',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return response()->json(['data' => $content]);
    }

    /**
     * @OA\Delete(
     *     path="/admin/content/{id}",
     *     tags={"Admin"},
     *     summary="Delete content",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Content deleted")
     * )
     */
    public function destroy($id)
    {
        ContentItem::findOrFail($id)->delete();
        return response()->json(['message' => 'Content deleted']);
    }
}
