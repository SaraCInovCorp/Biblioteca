<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::query();

        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id);
        }
        if ($request->filled('log_name')) {
            $query->where('log_name', 'like', '%' . $request->log_name . '%');
        }
        if ($request->filled('description')) {
            $query->where('description', 'like', '%' . $request->description . '%');
        }
        if ($request->filled('subject_type')) {
            $query->where('subject_type', 'like', '%' . $request->subject_type . '%');
        }
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->with(['causer', 'subject'])
                      ->orderBy('created_at', 'desc')
                      ->paginate(20);

        return view('admin.activity-logs.index', compact('logs'));
    }
}
