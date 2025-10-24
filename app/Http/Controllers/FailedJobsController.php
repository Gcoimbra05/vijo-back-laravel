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


}
