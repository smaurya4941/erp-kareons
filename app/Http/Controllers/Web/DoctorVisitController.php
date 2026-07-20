<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DoctorVisit;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DoctorVisitController extends Controller
{
    /**
     * Display a listing of doctor visits (Admin view).
     */
    public function index(Request $request)
    {
        $query = DoctorVisit::with(['user', 'discussedProducts']);

        if ($request->has('date') && $request->date != '') {
            $query->where('date', $request->date);
        }

        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('doctor_response') && $request->doctor_response != '') {
            $query->where('doctor_response', $request->doctor_response);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('doctor_name', 'like', "%{$search}%")
                  ->orWhere('clinic_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qUser) use ($search) {
                      $qUser->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $query->orderBy('date', 'desc')->orderBy('time', 'desc');
        $visits = $query->paginate($request->get('per_page', 15))->withQueryString();

        $mrs = User::role('MR')->get();
        
        // Calculate daily stats for dashboard cards
        $todayStr = $request->get('date', Carbon::today()->toDateString());
        $totalVisitsToday = DoctorVisit::where('date', $todayStr)->count();
        $uniqueDoctorsToday = DoctorVisit::where('date', $todayStr)->distinct('doctor_name')->count();
        $ordersToday = \App\Models\Order::whereDate('created_at', $todayStr)->count();

        return view('admin.visits.index', compact('visits', 'mrs', 'totalVisitsToday', 'uniqueDoctorsToday', 'ordersToday'));
    }

    /**
     * Display the specified visit details.
     */
    public function show(DoctorVisit $visit)
    {
        $visit->load([
            'user', 
            'discussedProducts.product', 
            'distributedSamples.product', 
            'order.items.product'
        ]);
        return view('admin.visits.show', compact('visit'));
    }
}
