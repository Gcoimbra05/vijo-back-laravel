<?php

namespace App\Http\Controllers;

use App\Models\InsightsFilter;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InsightsFilterController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $user = User::find($userId);
        $timezone = $user && $user->timezone ? $user->timezone : config('app.timezone', 'America/New_York');
        $filters = InsightsFilter::where('user_id', $userId)->get();
        if ($filters->isEmpty()) {
            $startDate = $user ? $user->created_at->setTimezone($timezone)->toDateString() : now()->setTimezone($timezone)->toDateString();
            $filter = InsightsFilter::create([
                'user_id' => $userId,
                'title' => 'Current',
                'start_date' => $startDate,
                'end_date' => null,
                'default' => true,
            ]);
            $filters = collect([$filter]);
        }

        $filters = $filters->map(function($filter) use ($timezone) {
            if ($filter->start_date) {
                $filter->start_date = 
                    \Carbon\Carbon::parse($filter->start_date)->setTimezone($timezone)->toDateString();
            }
            if ($filter->end_date) {
                $filter->end_date = 
                    \Carbon\Carbon::parse($filter->end_date)->setTimezone($timezone)->toDateString();
            }
            return $filter;
        });

        return response()->json($filters);
    }

    public function show($id)
    {
        $userId = Auth::id();
        $filter = InsightsFilter::where('user_id', $userId)->findOrFail($id);
        return response()->json($filter);
    }

    public function store(Request $request)
    {
        $userId = Auth::id();
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'default' => 'boolean',
        ]);
        $data['user_id'] = $userId;

        // Se for default, remove default dos outros filtros do usuário
        if (!empty($data['default'])) {
            InsightsFilter::where('user_id', $userId)->update(['default' => false]);
        }

        $filter = InsightsFilter::create($data);
        return response()->json($filter, 201);
    }

    public function update(Request $request, $id)
    {
        $userId = Auth::id();
        $filter = InsightsFilter::where('user_id', $userId)->findOrFail($id);
        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date',
            'default' => 'boolean',
        ]);

        // Se for default, remove default dos outros filtros do usuário
        if (!empty($data['default'])) {
            InsightsFilter::where('user_id', $userId)->update(['default' => false]);
        }

        $filter->update($data);
        return response()->json($filter);
    }

    public function destroy($id)
    {
        $userId = Auth::id();
        $filter = InsightsFilter::where('user_id', $userId)->findOrFail($id);
        $filter->delete();
        return response()->json(['message' => 'Filter deleted successfully.']);
    }
}
