<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\StudentScholarship;
use App\Models\ScholarshipType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScholarshipReportController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->filled('from') ? Carbon::parse($request->from)->startOfDay() : now()->startOfYear();
        $to = $request->filled('to') ? Carbon::parse($request->to)->endOfDay() : now()->endOfDay();

        /*
         * Total de bolsas por estado.
         */
        $statusCounts = StudentScholarship::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        /*
         * Bolsas aprovadas no período.
         */
        $approvedScholarships = StudentScholarship::query()
            ->where('status', 'approved')
            ->whereBetween('approved_at', [$from, $to])
            ->count();

        /*
         * Total de estudantes beneficiários.
         */
        $beneficiaryStudents = StudentScholarship::query()
            ->where('status', 'approved')
            ->where(function ($query) use ($from, $to) {
                $query->whereBetween('approved_at', [$from, $to])
                    ->orWhere(function ($q) use ($from, $to) {
                        $q->whereDate('start_date', '<=', $to)
                            ->where(function ($q2) use ($from) {
                                $q2->whereNull('end_date')
                                    ->orWhereDate('end_date', '>=', $from);
                            });
                    });
            })
            ->distinct('student_id')
            ->count('student_id');

        /*
         * Valor total dos descontos aplicados nas facturas.
         */
        $totalDiscount = Invoice::query()
            ->whereNotNull('student_scholarship_id')
            ->whereBetween('created_at', [$from, $to])
            ->sum('discount');

        /*
         * Total facturado antes dos descontos.
         */
        $grossInvoiced = Invoice::query()
            ->whereNotNull('student_scholarship_id')
            ->whereBetween('created_at', [$from, $to])
            ->sum('subtotal');

        /*
         * Total facturado depois dos descontos.
         */
        $netInvoiced = Invoice::query()
            ->whereNotNull('student_scholarship_id')
            ->whereBetween('created_at', [$from, $to])
            ->sum('total');

        /*
         * Bolsas por tipo.
         */
        $byType = ScholarshipType::query()
            ->withCount([
                'studentScholarships as approved_count' => function ($query) use ($from, $to) {
                    $query->where('status', 'approved')->whereBetween('approved_at', [$from, $to]);
                },
            ])
            ->orderByDesc('approved_count')
            ->get();

        /*
         * Descontos agrupados por mês.
         */
        // $monthlyDiscounts = Invoice::query()
        //     ->selectRaw("DATE_FORMAT(created_at, 'YYYY-MM') as month")
        //     ->selectRaw('SUM(discount) as total')
        //     ->whereNotNull('student_scholarship_id')
        //     ->whereBetween('created_at', [$from, $to])
        //     ->groupByRaw("DATE_FORMAT(created_at, 'YYYY-MM')")
        //     ->orderBy('month')
        //     ->get();

        $monthlyDiscounts = Invoice::query()
            ->selectRaw("TO_CHAR(created_at, 'YYYY-MM') as month")
            ->selectRaw('SUM(discount) as total')
            ->whereNotNull('student_scholarship_id')
            ->whereBetween('created_at', [$from, $to])
            ->groupByRaw("TO_CHAR(created_at, 'YYYY-MM')")
            ->orderBy('month')
            ->get();

        return view(
            'university.scholarships.reports.index',
            compact(
                'from',
                'to',
                'statusCounts',
                'approvedScholarships',
                'beneficiaryStudents',
                'totalDiscount',
                'grossInvoiced',
                'netInvoiced',
                'byType',
                'monthlyDiscounts'
            )
        );
    }
}
