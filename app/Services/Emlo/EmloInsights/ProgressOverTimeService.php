<?php

namespace App\Services\Emlo\EmloInsights;
use Illuminate\Support\Facades\Log;

use Carbon\Carbon;

class ProgressOverTimeService {

    public function __construct(protected AveragesService $averagesService){}

    public function getProgressOverTimeData($allParamValues, $filterBy, $pluckBy = 'value')
    {
        $result = $this->processData($allParamValues, $filterBy, $pluckBy);
        return $result;
    }

    private function processData($allParamValues, $filter = 'last_7_days', $pluckBy = 'value')
    {
        $collection = is_array($allParamValues) ? collect($allParamValues) : $allParamValues;

        // Apply date filter at the beginning
        $filteredCollection = $this->applyDateFilter($collection, $filter);

        // Group existing data by day
        $allDatesWValues = $filteredCollection
            ->filter(function ($item) use ($pluckBy) {
                $value = $this->getItemValue($item, $pluckBy);
                return $value !== null && $value != 0;
            })
            ->groupBy(function ($item) {
                $createdAt = $this->getItemValue($item, 'created_at');
                $date = Carbon::parse($createdAt);
                return $date->format('Y-m-d');
            });

        $formattedData = [];

        foreach($allDatesWValues as $date => $dateWValues) {
            if ($dateWValues->count() > 1) {
                // Average multiple values
                $values = $dateWValues->map(function($item) use ($pluckBy) {
                    return $this->getItemValue($item, $pluckBy);
                })->filter(function($value) {
                    return $value !== null && is_numeric($value);
                });
                
                $averageValue = $values->avg();
                $value = round($averageValue);
            } else {
                // Single value
                $singleValue = $this->getItemValue($dateWValues->first(), $pluckBy);
                $value = round($singleValue);
            }
            
            $formattedData[] = [
                "date" => $date,
                "value" => $value
            ];
        }

        // Sort by date to ensure chronological order
        usort($formattedData, function($a, $b) {
            return strcmp($a['date'], $b['date']);
        });

        return $formattedData;
    }

    // Helper method to safely get values from both arrays and objects
    private function getItemValue($item, $field)
    {
        if (is_array($item)) {
            return $item[$field] ?? null;
        } elseif (is_object($item)) {
            return $item->{$field} ?? null;
        }
        return null;
    }

    private function applyDateFilter($collection, $filter)
    {
        switch ($filter) {
            case 'last_7_days':
                $startDate = Carbon::now()->subDays(7)->startOfDay();
                return $collection->filter(function ($item) use ($startDate) {
                    return Carbon::parse($item->created_at)->gte($startDate);
                });

            case 'last_30_days':
                $startDate = Carbon::now()->subDays(30)->startOfDay();
                return $collection->filter(function ($item) use ($startDate) {
                    return Carbon::parse($item->created_at)->gte($startDate);
                });

            case 'since_start':
            default:
                // Return all data (no filtering)
                return $collection;
        }
    }
}