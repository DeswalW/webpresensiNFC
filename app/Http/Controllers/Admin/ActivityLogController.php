<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('student');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('nfc_id')) {
            $query->where('nfc_id', $request->nfc_id);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        $logs = $query->orderByDesc('id')->paginate(25);

        return view('admin.activity-logs.index', compact('logs'));
    }
}


