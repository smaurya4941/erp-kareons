<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\DoctorVisitResource;
use App\Models\DoctorVisit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorVisitController extends BaseApiController
{
    /**
     * Display a listing of doctor visits (Admin view).
     */
    public function index(Request $request): JsonResponse
    {
        $query = DoctorVisit::with([
            'user', 
            'discussedProducts.product', 
            'distributedSamples.product', 
            'order.items.product'
        ]);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        } elseif ($request->has('date')) {
            $query->where('date', $request->date);
        }

        if ($request->has('specialization')) {
            $query->where('specialization', $request->specialization);
        }

        if ($request->has('doctor_response')) {
            $query->where('doctor_response', $request->doctor_response);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('doctor_name', 'like', "%{$search}%")
                  ->orWhere('clinic_name', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%");
            });
        }

        $query->orderBy('date', 'desc')->orderBy('time', 'desc');

        $visits = $query->paginate($request->get('per_page', 15));

        return $this->successResponse(
            DoctorVisitResource::collection($visits)->response()->getData(true),
            'Doctor visits retrieved successfully'
        );
    }

    /**
     * Display the specified doctor visit.
     */
    public function show(DoctorVisit $doctorVisit): JsonResponse
    {
        $doctorVisit->load([
            'user', 
            'discussedProducts.product', 
            'distributedSamples.product', 
            'order.items.product'
        ]);
        
        return $this->successResponse(
            new DoctorVisitResource($doctorVisit), 
            'Doctor visit retrieved successfully'
        );
    }
}
