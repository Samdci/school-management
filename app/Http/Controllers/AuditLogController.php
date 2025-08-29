<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        
        // Only allow users with 'admin' role to access these methods
        $this->middleware('role:admin')->only(['index', 'show', 'destroy']);
    }

    /**
     * Display a listing of the audit logs.
     */
    public function index(Request $request)
    {
        $query = AuditLog::with(['user', 'auditable'])
            ->latest();

        // Apply filters
        if ($request->has('event') && $request->event) {
            $query->where('event', $request->event);
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('auditable_type') && $request->auditable_type) {
            $query->where('auditable_type', $request->auditable_type);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(25)->withQueryString();

        // Get filter options
        $events = AuditLog::select('event')->distinct()->pluck('event');
        $auditableTypes = AuditLog::select('auditable_type')
            ->distinct()
            ->whereNotNull('auditable_type')
            ->pluck('auditable_type')
            ->mapWithKeys(function ($type) {
                $parts = explode('\\', $type);
                return [$type => end($parts)];
            });

        $users = User::whereIn('id', AuditLog::select('user_id')
            ->distinct()
            ->whereNotNull('user_id')
            ->pluck('user_id'))
            ->pluck('name', 'id');

        return view('audit-logs.index', compact('logs', 'events', 'auditableTypes', 'users'));
    }

    /**
     * Display the specified audit log.
     */
    public function show($id)
    {
        $log = AuditLog::with(['user', 'auditable'])->findOrFail($id);
        
        return view('audit-logs.show', compact('log'));
    }

    /**
     * Remove the specified audit log from storage.
     */
    public function destroy($id)
    {
        $log = AuditLog::findOrFail($id);
        $log->delete();

        return redirect()->route('audit-logs.index')
            ->with('success', 'Audit log deleted successfully');
    }

    /**
     * Clear old audit logs.
     */
    public function clearOldLogs()
    {
        $days = 30; // Default to 30 days
        $cutoffDate = now()->subDays($days);
        
        $deleted = AuditLog::where('created_at', '<', $cutoffDate)->delete();
        
        return redirect()->route('audit-logs.index')
            ->with('success', "Successfully deleted {$deleted} audit logs older than {$days} days.");
    }
}

