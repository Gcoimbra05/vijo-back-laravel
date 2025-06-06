<?php

namespace App\Http\Controllers;

use App\Models\EmloResponseParamSpecs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class EmloResponseParamSpecsController extends Controller
{
    public function index()
    {
        $specifications = EmloResponseParamSpecs::with('emlo_response_param_specs')->get();
        return response()->json([
            'status' => true,
            'message' => 'EMLO parameter specification retrieved successfully.',
            'results' => [
                'specifications' => $specifications
            ],
        ]);
    }

    public function show($id)
    {
        $spec = EmloResponseParamSpecs::with('emlo_response_param_specs')->find($id);
        if (!$spec) {
            return response()->json([
                'status' => false,
                'message' => 'EMLO parameter specification not found.',
                'results' => null,
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'EMLO parameter specification retrieved successfully.',
            'results' => [
                'specification' => $spec
            ],
        ]);
    }

    public function showByParamName($paramName)
    {
        $spec = EmloResponseParamSpecs::findByParamName($paramName);
        if (!$spec) {
            return response()->json([
                'status' => false,
                'message' => 'EMLO parameter specification not found.',
                'results' => null,
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'EMLO parameter specification retrieved successfully.',
            'results' => [
                'specification' => $spec
            ],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'param_name' => 'required|string|max:255',
            'description' => 'required|string',
            'min' => 'required|integer',
            'max' => 'required|integer',
        ]);

        $spec = EmloResponseParamSpecs::create($request->all());
        return response()->json([
            'status' => true,
            'message' => 'EMLO parameter specification created successfully.',
            'results' => $spec->load('emlo_response_param_specs'),
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $spec = EmloResponseParamSpecs::find($id);
        if (!$spec) {
            return response()->json([
                'status' => false,
                'message' => 'EMLO parameter specification not found.',
                'results' => null,
            ], 404);
        }

        $request->validate([
            'param_name' => 'required|string|max:255',
            'description' => 'required|string',
            'min' => 'required|integer',
            'max' => 'required|integer',
        ]);

        $spec->update($request->all());
        return response()->json([
            'status' => true,
            'message' => 'EMLO parameter specification updated successfully.',
            'results' => $spec->load('catalogs'),
        ]);
    }

    public function destroy($id)
    {
        $spec = EmloResponseParamSpecs::find($id);
        if (!$spec) {
            return response()->json([
                'status' => false,
                'message' => 'EMLO parameter specification not found.',
                'results' => null,
            ], 404);
        }
        $spec->delete();
        return response()->json([
            'status' => true,
            'message' => 'EMLO parameter specification deleted successfully.',
            'results' => null,
        ]);
    }
}