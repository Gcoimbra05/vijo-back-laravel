<?php

namespace App\Http\Controllers;

use App\Models\VideoType;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class VideoTypeController extends Controller
{
    public function index()
    {
        $types = VideoType::all();
        return response()->json([
            'success' => true,
            'message' => 'Video types retrieved successfully.',
            'data' => $types,
        ]);
    }

    public function show($id)
    {
        $type = VideoType::find($id);
        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Video type not found.',
                'data' => null,
            ], 404);
        }
        return response()->json([
            'success' => true,
            'message' => 'Video type retrieved successfully.',
            'data' => $type,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:100',
            'kpi_no' => 'required|integer',
            'metric_no' => 'required|integer',
            'video_no' => 'required|integer',
            'status' => 'nullable|integer|in:0,1,2,3',
        ]);

        $type = VideoType::create($request->all());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Video type created successfully.',
                'data' => $type,
            ], 201);
        }

        session()->flash('display_msg', array(
            'msg'   => 'Video type created successfully.',
            'type'  => 'success',
            'icon'  => 'bx bx-check'
        ));

        return redirect()->route('videoTypes.list');
    }

    public function update(Request $request, $id)
    {
        $type = VideoType::find($id);
        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Video type not found.',
                'data' => null,
            ], 404);
        }

        $request->validate([
            'name' => 'nullable|string|max:100',
            'kpi_no' => 'sometimes|required|integer',
            'metric_no' => 'sometimes|required|integer',
            'video_no' => 'sometimes|required|integer',
            'status' => 'nullable|integer|in:0,1,2,3',
        ]);

        $type->update($request->all());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Video type updated successfully.',
                'data' => $type,
            ]);
        }

        session()->flash('display_msg', array(
            'msg'   => 'Video type updated successfully.',
            'type'  => 'success',
            'icon'  => 'bx bx-check'
        ));

        return redirect()->route('videoTypes.list');
    }

    public function destroy($id)
    {
        $type = VideoType::find($id);
        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Video type not found.',
                'data' => null,
            ], 404);
        }

        $type->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Video type deleted successfully.',
                'data' => null,
            ]);
        }

        session()->flash('display_msg', array(
            'msg'   => 'Video type deleted successfully.',
            'type'  => 'success',
            'icon'  => 'bx bx-check'
        ));

        return redirect()->route('videoTypes.list');
    }

    public function journalTypesIndex()
    {
        $nav_bar = 'journal_types';
        $pageTitle = 'Journal Types List';

        $breadcrumbs = [
            ['label' => 'Journal Types', 'url' => null],
        ];

        $types = VideoType::all();
        return view('admin.videoTypes.list', compact('types', 'pageTitle', 'nav_bar', 'breadcrumbs'));
    }

    public function add()
    {
        $action = 'Add';
        $nav_bar = 'journal_types';
        $pageTitle = 'Add Journal Type';

        $breadcrumbs = [
            ['label' => 'Journal Types', 'url' => route('videoTypes.list')],
            ['label' => $action . ' Journal Type', 'url' => null],
        ];

        return view('admin.videoTypes.form', compact('pageTitle', 'nav_bar', 'action', 'breadcrumbs'));
    }

    public function edit($id)
    {
        $type = VideoType::find($id);
        if (!$type) {
            return redirect()->route('videoTypes.list')->with('error', 'Video type not found.');
        }

        $action = 'Edit';
        $nav_bar = 'journal_types';
        $pageTitle = 'Edit Journal Type';

        $breadcrumbs = [
            ['label' => 'Journal Types', 'url' => route('videoTypes.list')],
            ['label' => $action . ' Journal Type', 'url' => null],
        ];

        $info = $type ? [$type->toArray()] : [];

        return view('admin.videoTypes.form', compact('type', 'pageTitle', 'nav_bar', 'action', 'info', 'breadcrumbs'));
    }

    public function deactivate($id)
    {
        $type = VideoType::find($id);
        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Video type not found.',
                'data' => null,
            ], 404);
        }

        $type->status = 0;
        $type->save();

        $display_msg = array(
            'msg'   => 'Video type deactivated successfully.',
            'type'  => 'success',
            'icon'  => 'bx bx-check'
        );

        session()->flash('display_msg', $display_msg);
        return redirect()->route('videoTypes.list');
    }

    public function activate($id)
    {
        $type = VideoType::find($id);
        if (!$type) {
            return response()->json([
                'success' => false,
                'message' => 'Video type not found.',
                'data' => null,
            ], 404);
        }

        $type->status = 1;
        $type->save();

        $display_msg = array(
            'msg'   => 'Video type activated successfully.',
            'type'  => 'success',
            'icon'  => 'bx bx-check'
        );

        session()->flash('display_msg', $display_msg);
        return redirect()->route('videoTypes.list');
    }
}