<?php 

namespace App\Services\Emlo\EmloInsights;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class AveragesService {
    public function createPerDayAverages($allValuesOfParam)
    {
        try {
            $daysWValues = [
                'Sunday' => [],
                'Monday' => [],
                'Tuesday' => [],
                'Wednesday' => [],
                'Thursday' => [],
                'Friday' => [],
                'Saturday' => [],
            ];

            foreach ($allValuesOfParam as $valueOfParam) {
                $createdAt = Carbon::parse($valueOfParam->created_at);
                $dayName = $createdAt->format('l');
                
                // Add to the array (not overwrite)
                $daysWValues[$dayName][] = $valueOfParam->value;
            }

            // Calculate averages
            foreach($daysWValues as $day => $values) {
                Log::debug("$day values: " . json_encode($values));
                if (count($values) > 0) {
                    $daysWValues[$day] = array_sum($values) / count($values);
                } else {
                    $daysWValues[$day] = 0;
                }
            }

            Log::debug('Final averages: ' . json_encode($daysWValues));
            
            return $daysWValues;
            
        } catch (Exception $e) {
            Log::debug($e->getMessage());
        }
    }

    public function createTimeOfDayAverages($allValuesOfParam, $pluckBy = 'value')
    {
        try {
            $timeOfDayParamValues = [
                'Morning' => [],
                'Afternoon' => [],
                'Evening' => []
            ];

            foreach ($allValuesOfParam as $valueOfParam) {
                $createdAt = Carbon::parse($valueOfParam->created_at);
                $hour = $createdAt->hour;

                // Use if/elseif instead of switch for range comparisons
                if ($hour >= 2 && $hour < 10) {
                    $timeOfDayParamValues['Morning'][] = data_get($valueOfParam, $pluckBy);;
                } elseif ($hour >= 10 && $hour < 17) {
                    $timeOfDayParamValues['Afternoon'][] = data_get($valueOfParam, $pluckBy);;
                } else { // 18-23
                    $timeOfDayParamValues['Evening'][] = data_get($valueOfParam, $pluckBy);;
                }
            }

           foreach($timeOfDayParamValues as $timeOfDay => $values) {
                if (count($values) > 0) {
                    $timeOfDayParamValues[$timeOfDay] = (int) round(array_sum($values) / count($values));
                } else {
                    $timeOfDayParamValues[$timeOfDay] = 0; // or null, or whatever default you want
                }
            }

            return $timeOfDayParamValues;
        } catch (Exception $e) {
            Log::debug($e->getMessage());
            return [];
        }
    }

    public function aggregateData($result, $filterBy, $pluckBy = 'value')
    {
        $collection = is_array($result) ? collect($result) : $result;
        $metrics = $this->aggregateWeeklyData($collection, $filterBy, $pluckBy);

        return $metrics;
    }

    private function aggregateWeeklyData($collection, $filter = 'since_start', $pluckBy = 'value')
    {
        // Apply date filtering at the beginning
        $filteredCollection = $this->applyDateFilter($collection, $filter);

        $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $result = [];

        // Group existing data by day
        $grouped = $filteredCollection->groupBy(function ($item) {
            $date = Carbon::parse($item->created_at);
            return $date->format('l');
        });

        // Create entry for each day of the week
        foreach ($daysOfWeek as $day) {
            if (isset($grouped[$day])) {
                // Day has data
                $values = $grouped[$day]->pluck($pluckBy)->filter(function ($value) {
                    return $value !== null && is_numeric($value);
                });

                $result[] = [
                    'category' => $day,
                    'value' => $values->isEmpty() ? 0 : ceil($values->avg()),
                ];
            } else {
                // Day has no data
                $result[] = [
                    'category' => $day,
                    'value' => 0,
                ];
            }
        }

        return $result;
    }

    public function aggregateMonthlyData($aggregate, $filter = 'since_start')
    {
        if (!$aggregate) {
            Log::debug('here');
            return [];
            
        }

        // Assuming the field is something like 'progress_over_time' or similar
        $jsonData = json_decode($aggregate->since_start_progress_over_time); // adjust field name
        
        if (!$jsonData) {
            Log::debug('here here');
            return [];
        }

        // Filter data based on the time period
        $filteredData = $this->filterDataByPeriod($jsonData, $filter);
        
        $result = [];
        
        // Group filtered data by month
        $grouped = collect($filteredData)->groupBy(function ($item) {
            $date = Carbon::parse($item->date);
            return $date->format('Y-m');
        });

        // Get all months in the range (including empty ones)
        $allMonths = $this->getMonthRange($filter, $filteredData);

        foreach ($allMonths as $yearMonth) {
            $monthLabel = Carbon::createFromFormat('Y-m', $yearMonth)->format('M');
            
            if (isset($grouped[$yearMonth])) {
                $values = $grouped[$yearMonth]->pluck('value')->filter(function ($value) {
                    return $value !== null && is_numeric($value);
                });

                $result[] = [
                    'label' => $monthLabel,
                    'value' => $values->isEmpty() ? 0 : ceil($values->avg()),
                ];
            } else {
                // Empty month
                $result[] = [
                    'label' => $monthLabel,
                    'value' => 0,
                ];
            }
        }

        return $result;
    }

    private function getMonthRange($filter, $data)
    {
        $now = Carbon::now();
        
        switch ($filter) {
            case '3months':
                $startDate = $now->copy()->subMonths(2)->startOfMonth();
                break;
            case '6months':
                $startDate = $now->copy()->subMonths(5)->startOfMonth();
                break;
            default:
                if (empty($data)) {
                    return [];
                }
                $startDate = $now->copy()->subMonths(11)->startOfMonth();
                break;
        }
        
        $endDate = $now->copy()->endOfMonth();
        $months = [];
        
        while ($startDate <= $endDate) {
            $months[] = $startDate->format('Y-m');
            $startDate->addMonth();
        }
        
        return $months;
    }

    private function filterDataByPeriod($jsonData, $filter)
    {
        $now = Carbon::now();
        
        return collect($jsonData)->filter(function ($item) use ($filter, $now) {
            $itemDate = Carbon::parse($item->date);
            
            switch ($filter) {
                case '3months':
                    return $itemDate >= $now->copy()->subMonths(3);
                case '6months':
                    return $itemDate >= $now->copy()->subMonths(6);
                default:
                    return $itemDate >= $now->copy()->subMonths(12);
            }
        })->values()->toArray();
    }

    private function applyDateFilter($collection, $filter)
    {
        $now = Carbon::now();
        
        switch ($filter) {
            case 'last_7_days':
                $startDate = $now->copy()->subDays(7);
                return $collection->filter(function ($item) use ($startDate) {
                    return Carbon::parse($item->created_at)->gte($startDate);
                });
                
            case 'last_30_days':
                $startDate = $now->copy()->subDays(30);
                return $collection->filter(function ($item) use ($startDate) {
                    return Carbon::parse($item->created_at)->gte($startDate);
                });
                
            case 'since_start':
            default:
                // Return the entire collection without filtering
                return $collection;
        }
    }

    public function getOverallAverage($collection, $filter = 'since_start', $pluckBy = 'value')
    {
        // Apply date filtering at the beginning
        $filteredCollection = $this->applyDateFilter($collection, $filter);
        
        // Extract all numeric values
        $values = $filteredCollection->pluck($pluckBy)->filter(function ($value) {
            return $value !== null && is_numeric($value);
        });
        
        // Return average or 0 if no valid values
        return $values->isEmpty() ? 0 : round($values->avg());
    }

    public function create30DayData($aggregate)
    {
        $allDays = [];

        // Decode aggregate data into array
        $daysData = json_decode($aggregate->last_30_days_progress_over_time, true);

        // Convert to associative array keyed by date (Y-m-d)
        $daysLookup = [];
        if (!empty($daysData)) {
            foreach ($daysData as $dayData) {
                $daysLookup[$dayData['date']] = $dayData['value'];
            }
        }

        // Generate last 30 days
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $label = date('M j', strtotime($date));

            $value = (int) ($daysLookup[$date] ?? 0); // default to 0 if missing

            $allDays[] = ['label' => $label, 'value' => $value];
        }

        return $allDays;
    }

    public function createWeeklyData($aggregate = null) 
    {
        $allDays = [];

        $weeklyData = json_decode($aggregate->since_start);
        foreach ($weeklyData as $dailyData) {
            $day = strtolower(substr($dailyData->category, 0, 3));
            $allDays [$day] = (int) $dailyData->value;
        }

        return $allDays;
    }
}