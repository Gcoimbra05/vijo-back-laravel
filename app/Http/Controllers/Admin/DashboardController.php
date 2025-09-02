<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
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

        $totalMembers = User::count();
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
