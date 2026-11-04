<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\FinancialAuditLog;
use Illuminate\Http\Request;

class FinancialAuditController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $filters = $request->validate([
            'module' => ['nullable', 'string', 'max:80'],
            'event' => ['nullable', 'string', 'max:80'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $query = FinancialAuditLog::query()
            ->where('tenant_id', $tenantId)
            ->with('user');

        if (!empty($filters['module'])) {
            $query->where('module', $filters['module']);
        }

        if (!empty($filters['event'])) {
            $query->where('event', 'ILIKE', '%' . $filters['event'] . '%');
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $logs = $query
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        return view('university.finance.audit.index', compact('logs'));
    }

    public function show(int $audit)
    {
        $log = FinancialAuditLog::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->with('user')
            ->findOrFail($audit);

        return view('university.finance.audit.show', compact('log'));
    }
}
