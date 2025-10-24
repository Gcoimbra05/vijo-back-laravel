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

}
