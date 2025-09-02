<?php

namespace App\Services\VideoDetailServices;

use App\Models\VideoRequest;
use App\Models\CredScore;
use App\Models\KpiMetricSpecification;
use App\Models\EmloResponseParamSpecs;
use App\Models\EmloInsightsParamAggregate;

use App\Exceptions\CatalogNotFoundException;
use App\Exceptions\CredScore\CredScoreNotFoundException;
use App\Services\CredScore\CredScoreService;

class EmotionService {

    public function __construct(protected CredScoreService $credScoreService){}
    
    public function getFormattedEmotions($requestId, $userId)
    {
        $catalogId = $this->getCatalogIdForRequest($requestId);
        $credScoreId = $this->getCredScoreIdForCatalog($catalogId, $requestId);
        $metricSpecs = $this->getMetricSpecifications($credScoreId, $requestId);
        $emotionMetrics = $this->filterEmotionMetrics($metricSpecs);
        
        return $this->buildEmotionMetricsArray($emotionMetrics, $requestId, $userId);
    }

    private function getCatalogIdForRequest($requestId)
    {
        $catalogId = VideoRequest::select('catalog_id')
            ->where('id', $requestId)
            ->first();
            
        if (!$catalogId) {
            throw new CatalogNotFoundException("catalog for request {$requestId} not found");
        }
        
        return $catalogId->catalog_id;
    }

    private function getCredScoreIdForCatalog($catalogId, $requestId)
    {
        $credScoreId = CredScore::select('id')
            ->where('catalog_id', $catalogId)
            ->first();
            
        if (!$credScoreId) {
            throw new CredScoreNotFoundException("cred score for catalog {$catalogId} of request {$requestId} not found");
        }
        
        return $credScoreId->id;
    }

    private function getMetricSpecifications($credScoreId, $requestId)
    {
        $metricSpecs = KpiMetricSpecification::select('id', 'emlo_param_spec_id', 'significance')
            ->where('kpi_id', $credScoreId)
            ->get();
            
        if ($metricSpecs->isEmpty()) {
            throw new CredScoreNotFoundException("kpi metric specification for cred score {$credScoreId} of request {$requestId} not found");
        }
        
        return $metricSpecs;
    }

    private function filterEmotionMetrics($metricSpecs)
    {
        return $metricSpecs
            ->whereNotNull('significance')
            ->whereNotNull('emlo_param_spec_id')
            ->where('emlo_param_spec_id', '!=', 15);
    }

    private function buildEmotionMetricsArray($emotionMetrics, $requestId, $userId)
    {
        $metricEmotions = [];
        
        foreach ($emotionMetrics as $emotionMetric) {
            $metricSpec = EmloResponseParamSpecs::select('*')
                ->where('id', $emotionMetric->emlo_param_spec_id)
                ->first();

            $metricValue = $this->credScoreService->getKpiMetricValue($emotionMetric, $requestId, $userId);

            $metricAverage = EmloInsightsParamAggregate::select('total_average')
                ->where('request_id', $requestId)
                ->where('emlo_param_spec_id', $emotionMetric->emlo_param_spec_id)
                ->first();

            $metricEmotions[] = [
                'value' => $metricValue ?? 0,
                'average' => $metricAverage->total_average ?? 0,
                'emotion' => $metricSpec->simplified_param_name ?? '',
                'description' => $metricSpec->description ?? '',
                'emoji' => $metricSpec->emoji ?? ''
            ];
        }

        return $metricEmotions;
    }

}