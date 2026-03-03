<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Content\Models\ContentItem;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/dashboard/stats",
     *     tags={"Admin"},
     *     summary="Get dashboard statistics",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Dashboard statistics")
     * )
     */
    public function stats()
    {
        return response()->json([
            'data' => [
                'total_content' => ContentItem::count(),
                'total_users' => User::count(),
                'total_downloads' => ContentItem::sum('download_count'),
                'pending_approval' => ContentItem::where('status', 'under_review')->count(),
                'content_by_type' => ContentItem::selectRaw('type, count(*) as count')
                    ->groupBy('type')
                    ->get(),
            ]
        ]);
    }
}
