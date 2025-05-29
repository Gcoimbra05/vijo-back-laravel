<?php

namespace App\Http\Controllers;


use App\Models\EmloResponse;
use App\Services\Emlo\EmloResponseService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class EmloController extends Controller
{
    protected $emloModel;
    protected $emloService;
    
    public function __construct()
    {
        // Load the model instead of the library
        $this->emloModel = new EmloResponse();
        $this->emloService = new EmloResponseService();
    }

    /**
     * POST /emlo/response/
     * Add a new EMLO response
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *     - 201: Successfully added EMLO response
     *     - 400: Bad request (invalid format, missing required fields)
     *     - 500: Server error
     * 
     * Request body (JSON):
     * {
     *   "raw_response": string
     * }
     */
    public function store(Request $request) 
    {
        $raw_response = $request->input("raw_response");
        $rawResponseString = is_string($raw_response) ? $raw_response : json_encode($raw_response);

        $request_id = $request->input("request_id");
        
        // Insert the raw response
        $result = $this->emloModel->store($rawResponseString, $request_id);

        if (!$result['success']) {
            if (isset($result['errors'])) {
                return response()->json(['success' => false, 'errors' => $result['errors']], 422);
            }
            return response()->json(['success' => false, 'message' => 'Failed to create EMLO response'], 500);
        }
        
        // Extract and store fields using the paths defined in api_response_paths
        $result = $this->emloService->extractAndStorePathValues($rawResponseString, $result['id']);
        if (!$result['success']) {
            if (isset($result['errors'])) {
                return response()->json(['success' => false, 'errors' => $result['errors']], 422);
            }
            return response()->json(['success' => false, 'message' => 'Failed to create EMLO response'], 500);
        }
        
        // Include extraction stats in the response
        $response = [
            'success' => true,
            'extraction' => [
                'processed' => $result['processed'],
                'stored' => $result['stored']
            ]
        ];
        
        // Include errors if any
        if (!$result['success'] && !empty($result['errors'])) {
            $response['extraction']['errors'] = $result['errors'];
        }
        
        return response()->json($response, 201);
    }

    /**
     * GET /emlo/response/{id}
     * Retrieve a specific EMLO response
     *
     * @param int $id The EMLO response ID to retrieve
     * @return \Illuminate\Http\JsonResponse
     *     - 200: Successful retrieval with EMLO response
     *     - 404: EMLO response not found
     */
    public function show($id)
    {
        $result = $this->emloModel->show($id);
        
        if (!$result['success']){
            if (strpos($result['message'], 'not found') !== false) {
                return response()->json(['success' => false, 'message' => $result['message']], 404);
            } 
        }
       
        return response()->json(['success' => true, 'data' => $result['data']]);
    }

    /**
     * GET /emlo/response/all
     * Retrieve a list of all EMLO responses with optional filtering and pagination
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     *     - 200: Successful retrieval with list of EMLO responses
     *     - 500: Server error
     * 
     * Query parameters:
     * - orderby: (string) Field to sort by (default: created_at)
     * - direction: (string) Sort direction, 'ASC' or 'DESC' (default: DESC)
     * - limit: (int) Maximum number of records to return (default: 20)
     * - offset: (int) Number of records to skip for pagination (default: 0)
     */
    public function getAllEmloResponses(Request $request) 
    {
        // Get ordering parameters
        $orderBy = $request->query('orderby', 'created_at');
        $direction = $request->query('direction', 'DESC');
         
        // Get pagination parameters
        $limit = (int)$request->query('limit', 20);
        $offset = (int)$request->query('offset', 0);
        
        // Get results from model
        $result = $this->emloModel->getAllEmloResponses($limit, $offset, $orderBy, $direction);
        
        if (!$result['success']) {
            return response()->json(['success' => false, 'message' => $result['message']], 500);
        }
        
        return response()->json([
            'success' => true,
            'data' => $result['data']
        ]);
    }

    /**
     * DELETE /emlo/response/{id}
     * Delete an existing EMLO response
     *
     * @param int $id The EMLO response ID to delete
     * @return \Illuminate\Http\JsonResponse
     *     - 200: Successful delete of EMLO response
     *     - 404: EMLO response not found
     *     - 500: Server error
     */
    public function deleteEmloResponse($id)
    {
        $result = $this->emloModel->deleteEmloResponse($id);
        
        if (!$result['success']) {
            if (strpos($result['message'], 'not found') !== false) {
                return response()->json(['success' => false, 'message' => $result['message']], 404);
            } 
            return response()->json(['success' => false, 'message' => $result['message']], 500);
        }
        
        return response()->json(['success' => true, 'message' => 'EMLO response deleted successfully'], 200);
    }

    /**
     * GET /emlo/response/param/{param}
     * Get single parameter of EMLO response with filtering options
     *
     * @param Request $request
     * @param string $param_name The parameter to get
     * @return \Illuminate\Http\JsonResponse
     *     - 200: Successful retrieval of parameter
     *     - 404: Parameter not found
     */
    public function getEmloResponseParamValue(Request $request, $param_name)
    {
        // Get query parameters
        $response_id = $request->query('response_id');
        $start_time = $request->query('start_time');
        $end_time = $request->query('end_time');
    
        // Get ordering parameters
        $orderBy = $request->query('orderby', 'created_at');
        $direction = $request->query('direction', 'DESC');
            
        // Build filters array (only include non-empty values)
        $filters = [];
        if (!empty($response_id)) $filters['response_id'] = $response_id;

        // Get pagination parameters
        $limit = (int)$request->query('limit', 20);
        $offset = (int)$request->query('offset', 0);
        
        // Call service method with time parameters
        $result = $this->emloService->getEmloResponseParamValue(
            $param_name,
            $filters,
            $limit,
            $offset,
            $orderBy,
            $direction,
            $start_time,
            $end_time
        );
        
        // Return JSON response
        return response()->json($result);
    }
}