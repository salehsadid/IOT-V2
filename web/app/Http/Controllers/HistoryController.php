<?php

namespace App\Http\Controllers;

use App\Models\DetectionEvent;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index()
    {
        $events = DetectionEvent::orderBy('end_time', 'desc')->paginate(20);
        return view('history', compact('events'));
    }
}
