<?php

namespace App\Services\Emlo;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\Services\Emlo\EmloResponseService;

class EmloInsightsService
{
    public function getInsightsData(Request $request, $paramName)
    {        
        $aggregation = $request->get('aggregation', 'daily');
        $timeRange = $request->get('time_range', 'current_week');
        
        $timeWindow = $this->getTimeWindow($timeRange);
    
        $emotion = EmloResponseService::getEmloResponseParamValue(
            path_key: $paramName,
            start_time: $timeWindow['start'],
            end_time: $timeWindow['end'],
        );

        $rawData = $this->extractParamValues($emotion, $paramName);
        $aggregatedData = $this->aggregateData($rawData, $aggregation, $paramName);

        $response = [
            'data' => $aggregatedData,
            'aggregation' => $aggregation,
            'time_range' => $timeRange,
            'period' => [
                'start' => $timeWindow['start'],
                'end' => $timeWindow['end']
            ],
        ];
                
        return response()->json($response);
    }

    private function extractParamValues($data, $paramName)
    {
        if (!isset($data['status']) || !$data['status'] || !isset($data['results']['param_value'])) {
            return collect([]);
        }

        $processedRecords = [];
        
        foreach ($data['results']['param_value'] as $record) {
            $processedRecord = $this->processRecord($record, $paramName);
            
            if ($this->isValidRecord($processedRecord, $paramName)) {
                $processedRecords[] = $processedRecord;
            }
        }

        return collect($processedRecords);
    }

    private function processRecord($record, $paramName)
    {
        // Get the value (numeric_value or string_value)
        $value = null;
        if ($record['numeric_value'] !== null) {
            $value = (float)$record['numeric_value'];
        } elseif ($record['string_value'] !== null) {
            $value = (float)$record['string_value'];
        }

        // Return in the format aggregateData expects
        return (object) [
            $paramName => $value,
            'created_at' => $record['created_at']
        ];
    }

    private function isValidRecord($record, $paramName)
    {
        // Since we just created this object, we know the property exists
        // Just check if the value is not null
        return $record->$paramName !== null;
    }
    
    private function getTimeWindow($timeRange)
    {
        $now = Carbon::now();
        
        switch ($timeRange) {
            case 'current_week':
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end' => $now->copy()->endOfWeek()
                ];
                
            case 'last_5_weeks':
                return [
                    'start' => $now->copy()->subWeeks(5)->startOfWeek(),
                    'end' => $now->copy()->endOfWeek()
                ];
                
            case 'current_month':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
                
            case 'last_3_months':
                return [
                    'start' => $now->copy()->subMonths(3)->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
                
            case 'last_6_months':
                return [
                    'start' => $now->copy()->subMonths(6)->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
                
            case 'last_12_months':
                return [
                    'start' => $now->copy()->subMonths(12)->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
                
            case 'since_start':
                $earliest = DB::table('requests')->min('created_at');
                return [
                    'start' => $earliest ? Carbon::parse($earliest) : $now->copy()->subYear(),
                    'end' => $now->copy()
                ];
                
            default:
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end' => $now->copy()->endOfWeek()
                ];
        }
    }
    
    private function aggregateData(Collection $rawData, $aggregation, $paramName)
    {
        if ($rawData->isEmpty()) {
            return [];
        }
        
        // Group the data based on aggregation type
        $grouped = $rawData->groupBy(function ($item) use ($aggregation) {
            $date = Carbon::parse($item->created_at);
            
            switch ($aggregation) {
                case 'daily':
                    return $date->format('Y-m-d');
                    
                case 'day_of_week':
                    return $date->format('l'); // Monday, Tuesday, etc.
                    
                case 'weekly':
                    return $date->format('Y') . '-W' . $date->format('W');
                    
                case 'monthly':
                    return $date->format('Y-m');
                    
                case 'quarterly':
                    return $date->format('Y') . '-Q' . ceil($date->format('n') / 3);
                    
                case 'yearly':
                    return $date->format('Y');
                    
                default:
                    return $date->format('Y-m-d');
            }
        });
        
        // Calculate aggregated metrics for each group
        $result = $grouped->map(function ($group, $period) use ($aggregation, $paramName) {
            $values = $group->pluck($paramName);

            return [
                'name' => $paramName,
                'period' => $period,
                'period_display' => $this->formatPeriodForDisplay($period, $aggregation),
                'avg' => round($values->avg(), 2),
                'min' => $values->min(),
                'max' => $values->max(),
                'request_count' => $group->count(),
                'sort_order' => $this->getSortOrder($period, $aggregation)
            ];
        });
        
        // Sort the results properly
        return $result->sortBy('sort_order')->values()->all();
    }
    
    private function formatPeriodForDisplay($period, $aggregation)
    {
        switch ($aggregation) {
            case 'daily':
                return Carbon::parse($period)->format('M j, Y'); // Jan 15, 2025
                
            case 'day_of_week':
                return $period; // Monday, Tuesday, etc.
                
            case 'weekly':
                // Convert "2025-W03" to "Week 3, 2025"
                preg_match('/(\d{4})-W(\d{2})/', $period, $matches);
                return "Week {$matches[2]}, {$matches[1]}";
                
            case 'monthly':
                return Carbon::parse($period . '-01')->format('M Y'); // Jan 2025
                
            case 'quarterly':
                // Convert "2025-Q1" to "Q1 2025"
                return str_replace('-', ' ', $period);
                
            case 'yearly':
                return $period;
                
            default:
                return $period;
        }
    }
    
    private function getSortOrder($period, $aggregation)
    {
        switch ($aggregation) {
            case 'daily':
            case 'monthly':
            case 'yearly':
                return $period;
                
            case 'day_of_week':
                $dayOrder = [
                    'Monday' => 1, 'Tuesday' => 2, 'Wednesday' => 3, 
                    'Thursday' => 4, 'Friday' => 5, 'Saturday' => 6, 'Sunday' => 7
                ];
                return $dayOrder[$period] ?? 8;
                
            case 'weekly':
                // Convert "2025-W03" to sortable format
                preg_match('/(\d{4})-W(\d{2})/', $period, $matches);
                return $matches[1] . $matches[2];
                
            case 'quarterly':
                // Convert "2025-Q1" to sortable format
                preg_match('/(\d{4})-Q(\d)/', $period, $matches);
                return $matches[1] . '0' . $matches[2];
                
            default:
                return $period;
        }
    }
    
    public function getAggregationOptions()
    {
        return response()->json([
            'aggregation_options' => [
                ['value' => 'daily', 'label' => 'Daily'],
                ['value' => 'day_of_week', 'label' => 'Day of Week'],
                ['value' => 'weekly', 'label' => 'Weekly'],
                ['value' => 'monthly', 'label' => 'Monthly'],
                ['value' => 'quarterly', 'label' => 'Quarterly'],
                ['value' => 'yearly', 'label' => 'Yearly']
            ],
            'time_range_options' => [
                ['value' => 'current_week', 'label' => 'Current Week'],
                ['value' => 'last_5_weeks', 'label' => 'Last 5 Weeks'],
                ['value' => 'current_month', 'label' => 'Current Month'],
                ['value' => 'last_3_months', 'label' => 'Last 3 Months'],
                ['value' => 'last_6_months', 'label' => 'Last 6 Months'],
                ['value' => 'last_12_months', 'label' => 'Last 12 Months'],
                ['value' => 'since_start', 'label' => 'Since Start']
            ]
        ]);
    }
}
