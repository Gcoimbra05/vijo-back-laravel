<?php

namespace App\Http\Controllers;

use App\Models\JobBatch;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class JobBatchesController extends Controller
{
    //Listen
    public function index()
    {
        $job_batches = \DB::table('job_batches')->orderBy('created_at', 'desc')->get();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Job batches retrieved successfully.',
                'data' => $job_batches,
            ]);
        }

        $breadcrumbs = [
            ['label' => 'Job Batches', 'url' => null],
        ];

        $nav_bar = 'job_batches';
        $pageTitle = 'Job Batches';

        return view('admin.jobbatches.list', compact('job_batches', 'pageTitle', 'nav_bar', 'breadcrumbs'));
}


    //Create
    public function create()
    {
        $pageTitle = "Add Job Batch";
        $nav_bar = "job_batches";
        $breadcrumbs = [
            ['label' => 'Jobs', 'url' => route('job_batches.index')],
            ['label' => 'Add Job Batch', 'url' => null],
        ];

        return view('admin.jobbatches.form', [
            'action' => 'create',
            'pageTitle' => $pageTitle,
            'nav_bar' => $nav_bar,
            'breadcrumbs' => $breadcrumbs,
            'Job_Batch' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    //Store
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'status' => 'nullable|boolean',
            'description' => 'nullable|string',
            'created_at' => 'nullable|date',
        ]);

        Job::create($validated);

        return redirect()->route('job_batches.index')
            ->with('success', 'Job Batch created successfully.');
    }

    //Edit
    public function show($id)
    {
        $Job_Batches= Job::findOrFail($id);

        $pageTitle = "Edit Job Batch";
        $nav_bar = "job_batches";
        $breadcrumbs = [
            ['label' => 'Jobs', 'url' => route('job_batches.index')],
            ['label' => 'Edit Job Batch', 'url' => null],
        ];

        return view('admin.jobbatches.form', [
            'action' => 'edit',
            'pageTitle' => $pageTitle,
            'nav_bar' => $nav_bar,
            'breadcrumbs' => $breadcrumbs,
            'Job_Batch' => $Job,
        ]);
    }

    //Update
    public function update(Request $request, $id)
    {
        $Job_Batches= Job::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'status' => 'nullable|boolean',
            'description' => 'nullable|string',
            'created_at' => 'nullable|date',
        ]);

        $Job->update([
            'name' => $request->name,
            'subject' => $request->subject,
            'body' => $request->body,
            'status' => $request->status,
            'description' => $request->description,
            'created_at' => $request->created_at,
        ]);

        return redirect()->route('job_batches.index')
            ->with('success', 'Job Batch updated successfully.');
    }

    //Delete
    public function destroy($id)
    {
        $Job_Batches= Job::find($id);

        if (!$Job) {
            return redirect()->route('job_batches.index')
                ->with('error', 'Job Batch not found.');
        }

        $Job->delete();

        return redirect()->route('job_batches.index')
            ->with('success', 'Job Batch deleted successfully.');
    }

}
