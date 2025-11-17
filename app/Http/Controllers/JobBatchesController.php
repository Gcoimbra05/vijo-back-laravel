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

}
