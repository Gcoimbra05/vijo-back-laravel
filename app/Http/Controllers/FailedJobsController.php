<?php

namespace App\Http\Controllers;

use App\Models\FailedJob;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class FailedJobsController extends Controller
{
    //Listen
    public function index()
    {
        $failed_jobs = \DB::table('failed_jobs')->orderBy('failed_at', 'desc')->get();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Failed Jobs retrieved successfully.',
                'data' => $failed_jobs,
            ]);
        }

        $breadcrumbs = [
            ['label' => 'Failed Jobs', 'url' => null],
        ];

        $nav_bar = 'failed_jobs';
        $pageTitle = 'Failed Jobs';

        return view('admin.failedjobs.list', compact('failed_jobs', 'pageTitle', 'nav_bar', 'breadcrumbs'));
}


    //Create
    public function create()
    {
        $pageTitle = "ADD Failed Job";
        $nav_bar = "failed_jobs";
        $breadcrumbs = [
            ['label' => 'Jobs', 'url' => route('failed_jobs.index')],
            ['label' => 'Add Failed Job', 'url' => null],
        ];

        return view('admin.failedjobs.form', [
            'action' => 'create',
            'pageTitle' => $pageTitle,
            'nav_bar' => $nav_bar,
            'breadcrumbs' => $breadcrumbs,
            'FailedJob' => null,
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

        return redirect()->route('failed_jobs.index')
            ->with('success', 'Failed Job created successfully.');
    }

    //Edit
    public function show($id)
    {
        $Job_Batches= Job::findOrFail($id);

        $pageTitle = "Edit Failed Job";
        $nav_bar = "failed_jobs";
        $breadcrumbs = [
            ['label' => 'Jobs', 'url' => route('failed_jobs.index')],
            ['label' => 'Edit Failed Job', 'url' => null],
        ];

        return view('admin.failedjobs.form', [
            'action' => 'edit',
            'pageTitle' => $pageTitle,
            'nav_bar' => $nav_bar,
            'breadcrumbs' => $breadcrumbs,
            'FailedJob' => $Job,
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

        return redirect()->route('failed_jobs.index')
            ->with('success', 'Failed Job updated successfully.');
    }

    //Delete
    public function destroy($id)
    {
        $Job_Batches= Job::find($id);

        if (!$Job) {
            return redirect()->route('failed_jobs.index')
                ->with('error', 'Failed Job not found.');
        }

        $Job->delete();

        return redirect()->route('failed_jobs.index')
            ->with('success', 'Failed Job deleted successfully.');
    }

}
