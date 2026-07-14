<?php

namespace App\Http\Controllers;

use App\Models\DetectionEvent;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = DetectionEvent::orderBy('end_time', 'desc');
        
        if ($request->has('type')) {
            $query->where('event_type', strtoupper($request->type));
        }

        $events = $query->paginate(20)->withQueryString();
        return view('history', compact('events'));
    }
}
