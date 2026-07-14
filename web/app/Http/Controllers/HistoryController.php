<?php

namespace App\Http\Controllers;

use App\Models\DetectionEvent;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = DetectionEvent::query();
        
        // Filter by Event Type
        if ($request->has('type') && $request->type != 'ALL' && $request->type != '') {
            $query->where('event_type', strtoupper($request->type));
        }

        // Filter by Start Date
        if ($request->filled('start_date')) {
            $query->whereDate('start_time', '>=', $request->start_date);
        }

        // Filter by End Date
        if ($request->filled('end_date')) {
            $query->whereDate('end_time', '<=', $request->end_date);
        }

        // Sorting
        $allowedSortColumns = ['start_time', 'duration_ms', 'max_level'];
        $sortBy = $request->input('sort_by', 'start_time');
        $sortOrder = $request->input('sort_order', 'desc');

        if (in_array($sortBy, $allowedSortColumns)) {
            $sortOrder = strtolower($sortOrder) === 'asc' ? 'asc' : 'desc';
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('start_time', 'desc');
        }

        $events = $query->paginate(20)->withQueryString();
        return view('history', compact('events'));
    }
}
