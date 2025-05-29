<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class AffiliateController extends Controller
{
    public function index()
    {
        $affiliates = Affiliate::all();
        return response()->json($affiliates);
    }

    public function show($id)
    {
        $affiliate = Affiliate::find($id);
        if (!$affiliate) {
            return response()->json(['message' => 'Affiliate not found'], 404);
        }
        return response()->json($affiliate);
    }

    public function store(Request $request)
    {
        $request->validate([
            'status' => 'string|max:50',
        ]);

        $data = $request->all();
        $data['user_id'] = Auth::id();
        $affiliate = Affiliate::create($data);
        return response()->json($affiliate, 201);
    }

    public function update(Request $request, $id)
    {
        $affiliate = Affiliate::find($id);
        if (!$affiliate) {
            return response()->json(['message' => 'Affiliate not found'], 404);
        }

        $request->validate([
            'status' => 'string|max:50',
        ]);

        $affiliate->update($request->all());
        return response()->json($affiliate);
    }

    public function destroy($id)
    {
        $affiliate = Affiliate::find($id);
        if (!$affiliate) {
            return response()->json(['message' => 'Affiliate not found'], 404);
        }

        $affiliate->delete();
        return response()->json(['message' => 'Affiliate deleted successfully']);
    }
}