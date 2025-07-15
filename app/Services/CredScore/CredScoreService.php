<?php

namespace App\Services\CredScore;

use App\Exceptions\CatalogNotFoundException;
use App\Exceptions\CredScore\CredScoreNotFoundException;
use App\Models\CredScore;
use App\Models\CredScoreKpi;
use App\Models\KpiMetricSpecification;
use App\Models\KpiMetricValue;
use App\Models\VideoRequest;

use Illuminate\Support\Facades\Log;

class CredScoreService {

    public function processCredScore($requestId)
    {
        $catalogId = VideoRequest::select('catalog_id')
            ->where('id', $requestId)
            ->first();
        if (!$catalogId) {
            throw new CatalogNotFoundException ("request '{$requestId}' does not have an associated catalog");
        }

        $credScoreId = CredScore::select('id')
            ->where('catalog_id', $catalogId->catalog_id)
            ->first();
        if (!$credScoreId) {
            throw new CredScoreNotFoundException ("cred score not found for catalog '{$catalogId}' of request '{$requestId}'");
        }

        $credScore = $this->getCredScore($credScoreId, $requestId);
        return $credScore;
    }

    private function getCredScore($credScoreId, $requestId) 
    {
        $kpiScores = [];

        $kpis = CredScoreKpi::select('id')
            ->where('cred_score_id', $credScoreId->id)
            ->get();
        if (!$kpis) {
            throw new CredScoreNotFoundException ("cred score KPIs not found for cred score '{$credScoreId->id}'");
        }

        foreach($kpis as $kpi) {
            $kpiMetrics = KpiMetricSpecification::select('id', 'range', 'significance')
                ->where('kpi_id', $kpi->id)
                ->get();
            if (!$kpiMetrics) {
                throw new CredScoreNotFoundException ("KPI metric not found for cred score KPI '{$kpi->id}' of cred score '{$credScoreId->id}'");
            }

            $kpiScore = $this->calculateKpiScore($kpiMetrics, $requestId);
            $kpiScores [] = $kpiScore;
        }

        $credScore = array_sum($kpiScores);
        return $credScore;
    }

    private function calculateKpiScore($kpiMetrics, $requestId) 
    {   
            $metricScores = [];
  
            $sumOfSignificances = 0;
            foreach($kpiMetrics as $kpiMetric) {
                $sumOfSignificances += $kpiMetric->significance;
            }
            
            foreach($kpiMetrics as $kpiMetric) {
                $value = $this->getKpiMetricValue($kpiMetric, $requestId);
                $metricScore = $this->calculateMetricScore($kpiMetric, $value, $sumOfSignificances);
                $metricScores [] = $metricScore;
            }

            $kpiScore = array_sum($metricScores);
            return $kpiScore;
    }

    private function getKpiMetricValue($kpiMetric, $requestId)
    {
        $value = KpiMetricValue::select('value')
                    ->where('kpi_metric_spec_id', $kpiMetric->id)
                    ->where('request_id', $requestId)
                    ->first();
        if (!$value) {
            throw new CredScoreNotFoundException ("KPI metric value not found for cred score KPI metric specification '{$kpiMetric->id}' and request '{$requestId}'");
        }

        return $value->value;
    }

    private function calculateMetricScore($kpiMetric, $value, $sumOfSignificances)
    {
        Log::debug("value is $value");
        Log::debug("kpiMetric->range is $kpiMetric->range");
        Log::debug("kpiMetric->range is $kpiMetric->range");
        Log::debug("kpiMetric->significance is $kpiMetric->significance");
        Log::debug("sumOfSignificances is $sumOfSignificances");

        $metricScore = (($value - 1)/($kpiMetric->range) * (100-1) + 1) * ($kpiMetric->significance)/$sumOfSignificances;
        return $metricScore;
    }

}