<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class JobController extends Controller
{
    //Listen
    public function index()
    {
        $jobs = Job::orderBy('id', 'desc')->get();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Jobs retrieved successfully.',
                'data' => $Jobs,
            ]);
        }

        $breadcrumbs = [
            ['label' => 'Jobs', 'url' => null],
        ];

        $nav_bar = 'job';
        $pageTitle = 'Jobs';

        return view('admin.job.list', compact('jobs', 'pageTitle', 'nav_bar', 'breadcrumbs'));
    }

    //Create
    public function create()
    {
        $pageTitle = "Add Job";
        $nav_bar = "job";
        $breadcrumbs = [
            ['label' => 'Jobs', 'url' => route('job.index')],
            ['label' => 'Add Job', 'url' => null],
        ];

        return view('admin.job.form', [
            'action' => 'create',
            'pageTitle' => $pageTitle,
            'nav_bar' => $nav_bar,
            'breadcrumbs' => $breadcrumbs,
            'Job' => null,
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

        return redirect()->route('job.index')
            ->with('success', 'Job created successfully.');
    }

    //Edit
    public function show($id)
    {
        $Job = Job::findOrFail($id);

        $pageTitle = "Edit Job";
        $nav_bar = "job";
        $breadcrumbs = [
            ['label' => 'Jobs', 'url' => route('job.index')],
            ['label' => 'Edit Job', 'url' => null],
        ];

        return view('admin.job.form', [
            'action' => 'edit',
            'pageTitle' => $pageTitle,
            'nav_bar' => $nav_bar,
            'breadcrumbs' => $breadcrumbs,
            'Job' => $Job,
        ]);
    }

    //Update
    public function update(Request $request, $id)
    {
        $Job = Job::findOrFail($id);

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

        return redirect()->route('job.index')
            ->with('success', 'Job updated successfully.');
    }

    //Delete
    public function destroy($id)
    {
        $Job = Job::find($id);

        if (!$Job) {
            return redirect()->route('job.index')
                ->with('error', 'Job not found.');
        }

        $Job->delete();

        return redirect()->route('job.index')
            ->with('success', 'Job deleted successfully.');
    }

}
