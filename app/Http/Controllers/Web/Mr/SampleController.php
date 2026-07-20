<?php

namespace App\Http\Controllers\Web\Mr;

use App\Http\Controllers\Controller;
use App\Services\SampleAssignmentService;
use Illuminate\Http\Request;

class SampleController extends Controller
{
    protected SampleAssignmentService $sampleService;

    public function __construct(SampleAssignmentService $sampleService)
    {
        $this->sampleService = $sampleService;
    }

    /**
     * Display the MR's sample assignments.
     */
    public function index(Request $request)
    {
        $assignments = $this->sampleService->getMrSamples(auth()->id());

        return view('mr.samples.index', compact('assignments'));
    }
}
