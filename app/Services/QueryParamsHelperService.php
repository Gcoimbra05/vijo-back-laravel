<?php

namespace App\Services;

use Illuminate\Http\Request;

class QueryParamsHelperService
{
    public static function getQueryOptions(Request $request, array $defaults = [], array $allowed = [])
    {
        $standardDefaults = [
            'limit' => null,
            'offset' => null,
            'orderColumn' => 'created_at',
            'orderDirection' => 'DESC',
            'start_time' => null,
            'end_time' => null,
            'page' => null,
            'per_page' => null,
        ];

        // Merge with custom defaults
        $defaults = array_merge($standardDefaults, $defaults);

        // Handle both JSON and query params
        $data = $request->isJson() ? $request->json()->all() : $request->all();
        
        $options = [
            'limit' => $data['limit'] ?? null,
            'offset' => $data['offset'] ?? null,
            'orderColumn' => $data['order_column'] ?? $data['orderColumn'] ?? $defaults['orderColumn'],
            'orderDirection' => $data['order_direction'] ?? $data['orderDirection'] ?? $defaults['orderDirection'],
            'start_time' => $data['start_time'] ?? $data['startTime'] ?? null,
            'end_time' => $data['end_time'] ?? $data['endTime'] ?? null,
            'page' => $data['page'] ?? null,
            'per_page' => $data['per_page'] ?? $data['perPage'] ?? null,
        ];

        // If allowed params specified, only return those
        if (!empty($allowed)) {
            $options = array_intersect_key($options, array_flip($allowed));
        }

        // Remove null/empty values and apply defaults
        $result = [];
        foreach ($options as $key => $value) {
            if ($value !== null && $value !== '') {
                $result[$key] = $value;
            } elseif (isset($defaults[$key]) && $defaults[$key] !== null) {
                $result[$key] = $defaults[$key];
            }
        }

        return $result;
    }


    public static function sanitizeOrderParams($orderColumn, $orderDirection, array $allowedColumns = ['created_at', 'updated_at'])
    {
        $orderColumn = in_array($orderColumn, $allowedColumns) ? $orderColumn : 'created_at';
        $orderDirection = strtoupper($orderDirection);
        $orderDirection = in_array($orderDirection, ['ASC', 'DESC']) ? $orderDirection : 'DESC';
        
        return [$orderColumn, $orderDirection];
    }

    public static function applyQueryOptions($query, array $options, array $allowedOrderColumns = ['created_at', 'updated_at'])
    {
        // Apply date range filters
        if (!empty($options['start_time'])) {
            $query->where('created_at', '>=', $options['start_time']);
        }
        if (!empty($options['end_time'])) {
            $query->where('created_at', '<=', $options['end_time']);
        }

        // Apply ordering
        if (!empty($options['orderColumn']) || !empty($options['orderDirection'])) {
            [$orderColumn, $orderDirection] = self::sanitizeOrderParams(
                $options['orderColumn'] ?? 'created_at',
                $options['orderDirection'] ?? 'DESC',
                $allowedOrderColumns
            );
            $query->orderBy($orderColumn, $orderDirection);
        }

        // Apply pagination
        if (!empty($options['limit'])) {
            $query->limit($options['limit']);
            
            if (!empty($options['offset'])) {
                $query->offset($options['offset']);
            }
        }

        return $query;
    }
}