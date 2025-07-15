<?php

namespace App\Http\Controllers;

use App\Models\CredScore;
use App\Services\CredScore\CredScoreService;

class CredScoreController {

    public function __construct(protected CredScoreService $credScoreService){}

    public function getCredScore($requestId) 
    {
        $credScore = $this->credScoreService->processCredScore($requestId);
        return response()->json([
            'status' => true,
            'message' => 'CRED score retrieved successfully.',
            'results' => [
                'cred_score' => $credScore
            ],
        ]);
    }
}