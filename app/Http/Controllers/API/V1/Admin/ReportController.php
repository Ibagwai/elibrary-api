<?php

namespace App\Http\Controllers\API\V1\Admin;

use App\Http\Controllers\Controller;
use App\Domain\Content\Models\ContentItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/reports/downloads",
     *     tags={"Admin"},
     *     summary="Download statistics",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="period", in="query", @OA\Schema(type="string", enum={"daily","weekly","monthly"})),
     *     @OA\Response(response=200, description="Download statistics")
     * )
     */
    public function downloads(Request $request)
    {
        $period = $request->get('period', 'daily');
        
        $dateFormat = match($period) {
            'weekly' => '%Y-%u',
            'monthly' => '%Y-%m',
            default => '%Y-%m-%d',
        };

        $stats = ContentItem::selectRaw("
            DATE_FORMAT(created_at, '{$dateFormat}') as period,
            SUM(download_count) as total_downloads,
            COUNT(*) as content_count
        ")
        ->groupBy('period')
        ->orderBy('period', 'desc')
        ->limit(30)
        ->get();

        return response()->json(['data' => $stats]);
    }

    /**
     * @OA\Get(
     *     path="/admin/reports/popular",
     *     tags={"Admin"},
     *     summary="Most popular content",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="limit", in="query", @OA\Schema(type="integer", default=10)),
     *     @OA\Response(response=200, description="Popular content")
     * )
     */
    public function popular(Request $request)
    {
        $limit = $request->get('limit', 10);

        $popular = ContentItem::with(['categories', 'tags'])
            ->orderBy('download_count', 'desc')
            ->limit($limit)
            ->get();

        return response()->json(['data' => $popular]);
    }

    /**
     * @OA\Get(
     *     path="/admin/reports/activity",
     *     tags={"Admin"},
     *     summary="User activity logs",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Activity logs")
     * )
     */
    public function activity()
    {
        $activities = DB::table('activity_log')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        return response()->json(['data' => $activities]);
    }
}
