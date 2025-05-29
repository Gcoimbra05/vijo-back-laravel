<?php

namespace App\Http\Controllers;

use App\Models\Csv;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CsvController extends Controller
{
    /**
     * Get all CSV records with pagination and search.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $perPage = $request->query('limit', 15);
        $page = (int) $request->query('page', 1);
        $startAt = $perPage * ($page - 1);

        if ($search) {
            $csvs = Csv::searchCsvs($search, $perPage);
        } else {
            $csvs = Csv::getAllCsvs($startAt, $perPage);
        }

        return response()->json([
            'success' => true,
            'message' => 'Csvs retrieved successfully.',
            'data' => $csvs,
            'meta' => [
                'total' => Csv::countCsvs(),
                'per_page' => $perPage,
                'current_page' => $page,
            ]
        ]);
    }

    /**
     * Get a specific CSV record.
     */
    public function show($id)
    {
        $csv = Csv::findCsv($id);
        
        if (!$csv) {
            return response()->json([
                'success' => false,
                'message' => 'Csv not found.',
                'data' => null,
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Csv retrieved successfully.',
            'data' => $csv,
        ]);
    }

    /**
     * Create a new CSV record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'response_id' => 'required|string|max:100',
            's3_url' => 'nullable|string',
        ]);

        $csv = Csv::createCsv(
            $validated['response_id'],
            $validated['s3_url'] ?? null
        );

        return response()->json([
            'success' => true,
            'message' => 'Csv created successfully.',
            'data' => $csv,
        ], 201);
    }

    /**
     * Update a CSV record.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'response_id' => 'sometimes|required|string|max:100',
            's3_url' => 'nullable|string',
        ]);

        $updated = Csv::updateCsv($id, $validated);
        
        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'Csv not found.',
                'data' => null,
            ], 404);
        }

        // Get the updated record to return
        $csv = Csv::findCsv($id);
        
        return response()->json([
            'success' => true,
            'message' => 'Csv updated successfully.',
            'data' => $csv,
        ]);
    }

    /**
     * Delete a CSV record.
     */
    public function destroy($id)
    {
        $deleted = Csv::deleteCsv($id);
        
        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Csv not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Csv deleted successfully.',
            'data' => null,
        ]);
    }

    /**
     * Upload CSV file and store record.
     */
    public function uploadAndStore(Request $request)
    {
        $request->validate([
            'response_id' => 'required|exists:emlo_responses,id',
            'file' => 'required_without:csv_path|file|mimes:csv|max:512000', // up to 500MB
            'csv_path' => 'required_without:file|string',
        ]);

        // Handle file upload using MediaStorageController
        $mediaController = app(\App\Http\Controllers\MediaStorageController::class);
        $uploadResponse = $mediaController->uploadCsv($request);

        $uploadData = $uploadResponse->getData(true);

        if (empty($uploadData['success']) || !$uploadData['success']) {
            return response()->json([
                'success' => false,
                'message' => $uploadData['message'] ?? 'Upload failed.',
                'errors'  => $uploadData['errors'] ?? null,
            ], 422);
        }

        // Build S3 object URL
        $parsed = parse_url($uploadData['s3_url']);
        $path = ltrim($parsed['path'], '/');
        $s3ObjectUrl = 'https://s3.' . env('AWS_DEFAULT_REGION') . '.amazonaws.com/' . env('AWS_BUCKET') . '/' . $path;

        // Create CSV record using model method
        $csv = Csv::createWithS3Upload(
            $request->input('response_id'),
            $s3ObjectUrl
        );

        return response()->json([
            'success' => true,
            'message' => 'Csv uploaded and saved successfully.',
            'data'    => $csv,
        ], 201);
    }

    /**
     * Get CSVs by response ID.
     */
    public function getByResponse($responseId)
    {
        $csvs = Csv::getCsvsByResponse($responseId);

        return response()->json([
            'success' => true,
            'message' => 'Csvs for response retrieved successfully.',
            'data' => $csvs,
        ]);
    }

    /**
     * Get only CSVs that have S3 URLs.
     */
    public function getWithUrls()
    {
        $csvs = Csv::getCsvsWithUrls();

        return response()->json([
            'success' => true,
            'message' => 'Csvs with URLs retrieved successfully.',
            'data' => $csvs,
        ]);
    }
}