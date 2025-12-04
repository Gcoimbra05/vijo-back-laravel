<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Models\CatalogAnswer;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Video;
use App\Models\VideoRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        $nav_bar = 'dashboard';

        // user metrics
        $totalMembers = User::count();
        $activeMembers = User::where('status', true)->count();
        $newMembersThisMonth = User::whereMonth('created_at', now()->month)
                                   ->whereYear('created_at', now()->year)
                                   ->count();

        // revenue metrics
        $totalRevenue = Payment::where('status', 'succeeded')->sum('amount') / 100; // Convertendo de centavos
        $revenueThisMonth = Payment::where('status', 'succeeded')
                                               ->whereMonth('created_at', now()->month)
                                               ->whereYear('created_at', now()->year)
                                               ->sum('amount') / 100;

        // video metrics
        $activeVideos = Video::whereNotNull('video_url')->count();
        $totalVideoRequests = VideoRequest::count();
        $completedVideoRequests = VideoRequest::where('status', 'completed')->count();

        // engagement metrics
        $totalCatalogs = Catalog::where('status', 1)->where('is_deleted', 0)->count();
        $totalAnswers = CatalogAnswer::count();
        $avgEngagement = $totalMembers > 0 ? round(($totalAnswers / $totalMembers), 2) : 0;

        // active subscriptions
        $activeSubscriptions = Subscription::where('status', 'active')->count();
        $cancelledSubscriptions = Subscription::where('status', 'canceled')->count();

        // recent activity (last 30 days)
        $recentActivity = [
            'new_users' => User::where('created_at', '>=', now()->subDays(30))->count(),
            'new_videos' => Video::where('created_at', '>=', now()->subDays(30))->count(),
            'new_journals' => VideoRequest::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        // Old variables kept for compatibility
        $totalReferences = 0;
        $currentYear = now()->year;
        $websiteDevelopedYear = 2025;
        $current_timestamp = now();
        $totalApiThisMonth = 0;
        $totalApis = 0;
        $monthlyAvgResponseTime = 0;
        $allTimeAvgResponseTime = 0;
        $dailyAvgResponseTime = 0;
        $weeklyAvgResponseTime = 0;

        return view('admin.dashboard.index', compact(
            'nav_bar',
            'totalMembers',
            'activeMembers',
            'newMembersThisMonth',
            'totalRevenue',
            'revenueThisMonth',
            'activeVideos',
            'totalVideoRequests',
            'completedVideoRequests',
            'avgEngagement',
            'totalCatalogs',
            'activeSubscriptions',
            'cancelledSubscriptions',
            'recentActivity',
            'totalReferences',
            'currentYear',
            'websiteDevelopedYear',
            'current_timestamp',
            'totalApiThisMonth',
            'totalApis',
            'monthlyAvgResponseTime',
            'allTimeAvgResponseTime',
            'dailyAvgResponseTime',
            'weeklyAvgResponseTime'
        ));
    }

    /**
     * Load total API calls by month via AJAX.
     */
    public function loadTotalApiCallsByMonth(Request $request)
    {
        // Lógica para carregar dados de API calls por mês
        // Retorna JSON para requisições AJAX

        return response()->json([
            'success' => true,
            'data' => []
        ]);
    }

    /**
     * Download CSV file.
     */
    public function downloadCSV(string $type)
    {
        // Lógica para gerar e fazer download do CSV
        // baseado no tipo especificado

        return response()->download($pathToFile);
    }

    /**
     * Logout admin user.
     */
    public function logout(): RedirectResponse
    {
        auth('admin')->logout();

        return redirect()->route('admin.login');
    }
}
