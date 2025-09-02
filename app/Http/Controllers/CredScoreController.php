<?php

namespace App\Http\Controllers;

use App\Models\CredScore;
use App\Services\CredScore\CredScoreService;
use Illuminate\Support\Facades\Auth;

class CredScoreController {

    public function __construct(protected CredScoreService $credScoreService){}

    public function getCredScore($requestId) 
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['error' => 'user not found'], 404);
        }

        $credScore = $this->credScoreService->getCredScore($requestId);
        return response()->json([
            'status' => true,
            'message' => 'CRED score retrieved successfully.',
            'results' => [
                'cred_score' => $credScore
            ],
        ]);
    }
}