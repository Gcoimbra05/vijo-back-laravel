<?php

namespace App\Services\Emlo;

use Illuminate\Support\Facades\DB;

class EmloDatabaseLoader
{
    private static $segments = [];
    private static $zeroValuedSegments = [];
    private static $segmentsInUse = [];
    private static $paramsInUse = [];
    private static $edpParamsInUse = [];
    private static $secondaryMetricParams = [];
    private static $catalogsInUse = [];
    private static $metricCatalogsInUse = [];
    private static $initialized = false;

    /**
     * Load data from database on application startup
     *
     * @param \Illuminate\Database\Connection|null $db
     * @return void
     */
    public static function initialize($db = null)
    {
        // Skip if already initialized
        if (self::$initialized) {
            return;
        }

        // Get database connection if not provided
        if ($db === null) {
            $db = DB::connection();
        }

        // Fetch 127 rows sorted by id ascending
        // Check if table exists before querying
        if ($db->getSchemaBuilder()->hasTable('emlo_response_segments')) {
            self::$segments = $db->table('emlo_response_segments')
            ->select('name')
            ->orderBy('number', 'ASC')
            ->limit(127)
            ->get()
            ->toArray();

            // Fetch 13 zero valued segment rows
            self::$zeroValuedSegments = $db->table('emlo_response_segments')
            ->select('name')
            ->orderBy('number', 'ASC')
            ->offset(127)
            ->limit(13)
            ->get()
            ->toArray();

            $segmentParamsInUse = config('emlo.segmentParamsInUse');

            self::$segmentsInUse = $db->table('emlo_response_segments')
                ->select('name', 'number')
                ->whereIn('name', $segmentParamsInUse)
                ->get()
                ->toArray();

               
            self::$segmentsInUse = $db->table('emlo_response_param_specs')
                ->select('param_name')
                ->where('type', 'segment')
                ->get()
                ->toArray();
            

            self::$paramsInUse = $db->table('emlo_response_param_specs')
                ->get()
                ->toArray();

            self::$edpParamsInUse = $db->table('emlo_response_param_specs')
                ->whereLike('param_name', '%EDP%')
                ->get()
                ->toArray();

            self::$catalogsInUse = $db->table('catalogs')
                ->get()
                ->toArray();

            self::$metricCatalogsInUse = $db->table('catalogs')
                ->whereNot('video_type_id', 1)
                ->get()
                ->toArray();

            self::$secondaryMetricParams= $db->table('emlo_response_param_specs')
                ->where('param_name', 'Aggression')
                ->orWhere('param_name', 'clStress')
                ->orWhere('param_name', 'overallCognitiveActivity')
                ->orWhere('param_name', 'self_honesty')
                ->get()
                ->toArray();


        } else {
            // Initialize with empty arrays if table doesn't exist
            self::$segments = [];
            self::$zeroValuedSegments = [];
            self::$segmentsInUse = [];
            self::$paramsInUse = [];
            self::$catalogsInUse = [];
            self::$secondaryMetricParams = [];
            self::$metricCatalogsInUse = [];
        }

        self::$initialized = true;
    }

    public static function getSegments()
    {
        return self::$segments;
    }

    public static function getZeroValuedSegments()
    {
        return self::$zeroValuedSegments;
    }

    public static function getSegmentsInUse()
    {
        return self::$segmentsInUse;
    }

    public static function getParamsInUse()
    {
        return self::$paramsInUse;
    }

    public static function getEdpParamsInUse()
    {
        return self::$edpParamsInUse;
    }

    public static function getCatalogsInUse()
    {
        return self::$catalogsInUse;
    }

    public static function getMetricCatalogsInUse()
    {
        return self::$metricCatalogsInUse;
    }

    public static function getSecondaryMetricParams()
    {
        return self::$secondaryMetricParams;
    }
}
