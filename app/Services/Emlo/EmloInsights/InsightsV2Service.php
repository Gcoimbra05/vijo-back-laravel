<?php

namespace App\Services\Emlo\EmloInsights;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserLogin;
use App\Models\VideoRequest;
use Illuminate\Support\Facades\Log;

class InsightsV2Service {
    public function getInsightsResponse(Request $request)
    {
        // aggregation = current Month, Past Month, Quarterly, Semi-Annualy, Annually
        // time_range = weekly, monthly, All Time

        $progressAggregation = $request->get('aggregation', 'current_month');
        $timeRange = $request->get('time_range', 'weekly');
        $emotion = $request->get('emotion', 'EDP-Stressful');

        // Validate parameters
        $validAggregations = ['current_month', 'past_month', 'quarterly', 'semi_annually', 'annually'];
        $validTimeRanges = ['weekly', 'monthly', 'all_time'];

        Log::info("EmloInsightsService - getInsightsResponse called with parameters: ", [
            'aggregation' => $progressAggregation,
            'time_range' => $timeRange,
            'emotion' => $emotion
        ]);

        if (!in_array($progressAggregation, $validAggregations)) {
            return response()->json(['error' => 'Invalid aggregation'], 400);
        }

        if (!in_array($timeRange, $validTimeRanges)) {
            return response()->json(['error' => 'Invalid time_range'], 400);
        }

        $emotionMap = [
            'EDP-Stressful' => 'stress',
            'EDP-Energetic' => 'energy',
            'EDP-Focused' => 'focus'
        ];

        $metricKey = $emotionMap[$emotion] ?? 'stress';

        // Base averages for each metric
        $baseAverages = [
            'stress' => 43,
            'energy' => 65,
            'focus' => 78
        ];

        $metricsData = [];
        $metricCategories = $this->getCategories($timeRange);
        foreach ($metricCategories as $category) {
            $value = rand(30, 100); // Random value between 30 and 100
            $avg = round($baseAverages[$metricKey] * (1 + (rand(-5, 5) / 100))); // ±5% variation
            $metricsData[] = [
                'category' => $category,
                'avg' => $avg,
                'value' => $value
            ];
        }

        // Static data for timeOfDayData
        $timeOfDayDataTemplate = [
            'stress' => ['morning' => rand(30, 60), 'afternoon' => rand(40, 70), 'evening' => rand(30, 60)],
            'energy' => ['morning' => rand(50, 80), 'afternoon' => rand(40, 70), 'evening' => rand(30, 60)],
            'focus' => ['morning' => rand(70, 90), 'afternoon' => rand(60, 80), 'evening' => rand(60, 80)]
        ];

        $timeOfDayData = $timeOfDayDataTemplate[$metricKey] ?? $timeOfDayDataTemplate['stress'];

        // Standardized secondaryMetrics as an array
        $secondaryMetrics = [
            [
                'name' => 'stressRecovery',
                'title' => 'Stress Recovery',
                'description' => 'Ability to return to calm after stress (Level 1 is best)',
                'currentValue' => rand(20, 30),
                'items' => [
                    ['range' => 1, 'label' => 'Excellent Recovery', 'percentage' => rand(10, 20)],
                    ['range' => 2, 'label' => 'Very Good', 'percentage' => rand(20, 30), 'isCurrent' => true],
                    ['range' => 3, 'label' => 'Good', 'percentage' => rand(15, 25)],
                    ['range' => 4, 'label' => 'Moderate', 'percentage' => rand(10, 20)],
                    ['range' => 5, 'label' => 'Needs Attention', 'percentage' => rand(5, 15)]
                ]
            ],
            [
                'name' => 'cognitiveBalance',
                'title' => 'Cognitive Balance',
                'description' => 'How well thoughts and emotions work together',
                'currentValue' => rand(20, 30),
                'items' => [
                    ['range' => '0.05-5', 'label' => 'Disconnected', 'percentage' => rand(10, 15)],
                    ['range' => '5-10', 'label' => 'Low Balance', 'percentage' => rand(20, 30), 'isCurrent' => true],
                    ['range' => '10-15', 'label' => 'Balanced', 'percentage' => rand(40, 50)],
                    ['range' => '15-17.5', 'label' => 'Overstimulated', 'percentage' => rand(10, 20)]
                ]
            ],
            [
                'name' => 'aggression',
                'title' => 'Aggression',
                'description' => 'How strongly anger comes through (0 is best)',
                'currentValue' => round((rand(0, 20) / 10), 1),
                'items' => [
                    ['range' => '0', 'label' => 'Optimal', 'percentage' => rand(60, 70)],
                    ['range' => '1-2', 'label' => 'Acceptable', 'percentage' => rand(25, 35), 'isCurrent' => true],
                    ['range' => '>2', 'label' => 'Needs Attention', 'percentage' => rand(5, 10)]
                ]
            ],
        ];

        $riskCurrentValue = rand(20, 30); // Random value for risk
        $risk = [
            'name' => 'risk',
            'title' => 'Risk (Self-Honesty)',
            'description' => 'How honest you\'re being with yourself (1-40 is best)',

            'min' => 1,
            'midpoint' => 40,
            'max' => 100,
            'currentValue' => $riskCurrentValue,

            'stats' => [
                ['label' => 'Current Level', 'value' => $riskCurrentValue, 'description' => 'Good Self-Honesty'],
                ['label' => 'Best Range', 'value' => '1-40', 'description' => 'Honest self-reflection'],
            ],
        ];

        // Progress over Time
        $progressData = [];
        $progressDataCategories = $this->getCategories($progressAggregation);

        foreach ($progressDataCategories as $category) {
            $value = rand(30, 100); // Random value between 30 and 100
            $avg = round($baseAverages[$metricKey] * (1 + (rand(-5, 5) / 100))); // ±5% variation
            $progressData[] = [
                'category' => $category,
                'avg' => $avg,
                'value' => $value
            ];
        }

        $daysOfWeek = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
        $weeklyActivity = [];
        foreach ($daysOfWeek as $index => $day) {
            $weeklyActivity[] = [
                'day' => $day,
                'active' => rand(50, 100),
            ];
        }

        $userId = Auth::id();
        $stats = $this->getUserActivityStats($userId);

        $activity = [
            'weekly' => $weeklyActivity,
            'stats' => $stats
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Insights data retrieved successfully',
            'data' => [
                'metricsData' => $metricsData,
                'timeOfDayData' => $timeOfDayData,
                'secondaryMetrics' => $secondaryMetrics,
                'risk' => $risk,
                'activity' => $activity,
                'progressData' => $progressData ?? [],
            ],
        ]);
    }

    private function getCategories($type)
    {
        switch ($type) {
            // Time ranges
            case 'weekly':
                return ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            case 'monthly':
                return ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
            case 'all_time':
                $now = Carbon::now();
                return [
                    $now->copy()->subMonths(5)->format('M'),
                    $now->copy()->subMonths(4)->format('M'),
                    $now->copy()->subMonths(3)->format('M'),
                    $now->copy()->subMonths(2)->format('M'),
                    $now->copy()->subMonths(1)->format('M'),
                    $now->format('M')
                ];

            // Aggregations
            case 'past_month':
            case 'current_month':
                return ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
            case 'quarterly':
                return ['Q1', 'Q2', 'Q3', 'Q4'];
            case 'semi_annually':
                return ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
            case 'annually':
                return ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            default:
                return ['Unknown'];
        }
    }

    public function getUserActivityStats($userId)
    {
        // Total number of check-ins (logins)
        $totalCheckIns = UserLogin::where('user_id', $userId)->count();

        // Check-ins for the current month
        $thisMonth = UserLogin::where('user_id', $userId)
            ->whereMonth('logged_in_at', Carbon::now()->month)
            ->whereYear('logged_in_at', Carbon::now()->year)
            ->count();

        // Distinct days with login (for streak calculation)
        $loginDays = UserLogin::where('user_id', $userId)
            ->orderBy('logged_in_at')
            ->pluck('logged_in_at')
            ->map(fn($dt) => Carbon::parse($dt)->toDateString())
            ->unique()
            ->values();

        // Streak calculation
        $currentStreak = 0;
        $longestStreak = 0;
        $streak = 0;
        $prev = null;

        foreach ($loginDays as $day) {
            if ($prev && Carbon::parse($prev)->diffInDays($day) === 1) {
                $streak++;
            } else {
                $streak = 1;
            }
            if ($streak > $longestStreak) {
                $longestStreak = $streak;
            }
            $prev = $day;
        }

        // If the last login was today, the current streak is valid
        $currentStreak = 0;
        if ($loginDays->count() && Carbon::parse($loginDays->last())->isToday()) {
            // Count how many consecutive days up to today
            $currentStreak = 1;
            for ($i = $loginDays->count() - 2; $i >= 0; $i--) {
                if (Carbon::parse($loginDays[$i])->diffInDays($loginDays[$i + 1]) === 1) {
                    $currentStreak++;
                } else {
                    break;
                }
            }
        }

        // Only video_requests that have related videos
        $totalRecordings = VideoRequest::where('user_id', $userId)
            ->whereHas('videos')
            ->count();

        $avgRecordings = $totalCheckIns > 0 ? round($totalRecordings / $totalCheckIns, 1) : 0;

        return [
            'avgRecordings' => $avgRecordings,
            'currentStreak' => $currentStreak,
            'longestStreak' => $longestStreak,
            'totalCheckIns' => $totalCheckIns,
            'thisMonth' => $thisMonth,
        ];
    }
}