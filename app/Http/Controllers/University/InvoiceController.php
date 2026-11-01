<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\FeeTariff;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Student;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::with(['student', 'enrollment'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'LIKE', "%{$search}%")
                        ->orWhereHas('student', function ($student) use ($search) {
                            $student->where('name', 'LIKE', "%{$search}%")
                                ->orWhere('student_number', 'LIKE', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('status'), fn ($q) =>
                $q->where('status', $request->status)
            )
            ->when($request->filled('date_from'), fn ($q) =>
                $q->whereDate('issue_date', '>=', $request->date_from)
            )
            ->when($request->filled('date_to'), fn ($q) =>
                $q->whereDate('issue_date', '<=', $request->date_to)
            )
            ->latest('issue_date')
            ->paginate(15)
            ->withQueryString();

        return view('university.finance.invoices.index', compact('invoices'));
    }

    public function create()
    {
        $students = Student::where('is_active', true)
            ->orderBy('name')
            ->get();

        $enrollments = Enrollment::with(['student', 'academicYear', 'course'])
            ->whereIn('status', ['active', 'confirmed', 'pending'])
            ->latest('enrollment_date')
            ->get();

        $tariffs = FeeTariff::with(['feeConcept', 'academicYear', 'course'])
            ->where('is_active', true)
            ->whereHas('feeConcept', fn ($q) => $q->where('is_active', true))
            ->whereHas('academicYear', fn ($q) => $q->where('is_active', true))
            ->get();

        return view('university.finance.invoices.create', compact(
            'students',
            'enrollments',
            'tariffs'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'enrollment_id' => ['required', 'integer', 'exists:enrollments,id'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'penalty' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.fee_tariff_id' => ['required', 'integer', 'exists:fee_tariffs,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0', 'max:999999'],
        ]);

        $student = Student::findOrFail($data['student_id']);
        $enrollment = Enrollment::findOrFail($data['enrollment_id']);

        if ((int) $enrollment->student_id !== (int) $student->id) {
            throw ValidationException::withMessages([
                'enrollment_id' => 'A matrícula seleccionada não pertence ao estudante.',
            ]);
        }

        $tariffIds = collect($data['items'])
            ->pluck('fee_tariff_id')
            ->unique()
            ->values();

        $tariffs = FeeTariff::with('feeConcept')
            ->whereIn('id', $tariffIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        if ($tariffs->count() !== $tariffIds->count()) {
            throw ValidationException::withMessages([
                'items' => 'Uma ou mais tarifas seleccionadas não estão disponíveis.',
            ]);
        }

        foreach ($data['items'] as $item) {
            $tariff = $tariffs->get($item['fee_tariff_id']);

            if ((int) $tariff->academic_year_id !== (int) $enrollment->academic_year_id) {
                throw ValidationException::withMessages([
                    'items' => 'A tarifa seleccionada não pertence ao ano académico da matrícula.',
                ]);
            }

            if ($tariff->course_id && (int) $tariff->course_id !== (int) $enrollment->course_id) {
                throw ValidationException::withMessages([
                    'items' => 'A tarifa seleccionada não corresponde ao curso do estudante.',
                ]);
            }
        }

        $currencies = $tariffs->pluck('currency')->unique();

        if ($currencies->count() !== 1) {
            throw ValidationException::withMessages([
                'items' => 'Uma factura não pode conter tarifas de moedas diferentes.',
            ]);
        }

        
        $subtotal = collect($data['items'])->sum(function ($item) use ($tariffs) {
            $tariff = $tariffs->get($item['fee_tariff_id']);
            return (float) $tariff->amount * (float) $item['quantity'];
        });

        if ((float) ($data['discount'] ?? 0) > $subtotal) {
            throw ValidationException::withMessages([
                'discount' => 'O desconto não pode ser superior ao subtotal da factura.',
            ]);
        }

        return DB::transaction(function () use ($data, $enrollment, $tariffs, $currencies) {
            $tenantId = auth()->user()->tenant_id;

            // Bloqueia a universidade durante a geração do número da factura.
            University::whereKey($tenantId)->lockForUpdate()->firstOrFail();

            $year = now()->format('Y');
            $prefix = 'FAT-' . $year . '-';

            $number = Invoice::where('invoice_number', 'LIKE', $prefix . '%')->count() + 1;
            $invoiceNumber = $prefix . str_pad($number, 6, '0', STR_PAD_LEFT);

            $invoice = Invoice::create([
                'student_id' => $data['student_id'],
                'enrollment_id' => $enrollment->id,
                'invoice_number' => $invoiceNumber,
                'issue_date' => $data['issue_date'],
                'due_date' => $data['due_date'],
                'subtotal' => 0,
                'discount' => $data['discount'] ?? 0,
                'penalty' => $data['penalty'] ?? 0,
                'total' => 0,
                'amount_paid' => 0,
                'balance' => 0,
                'currency' => $currencies->first(),
                'status' => 'draft',
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($data['items'] as $item) {
                $tariff = $tariffs->get($item['fee_tariff_id']);
                $quantity = (float) $item['quantity'];
                $unitPrice = (float) $tariff->amount;

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'fee_concept_id' => $tariff->fee_concept_id,
                    'description' => $tariff->feeConcept->name,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'total' => round($quantity * $unitPrice, 2),
                ]);
            }

            $invoice->recalculateFinancials();

            return redirect()
                ->route('university.invoices.show', $invoice)
                ->with('success', 'Factura criada como rascunho. Confirme os dados antes de emitir.');
        });
    }

    public function show(string $invoice)
    {

        $invoice = Invoice::findOrFail($invoice); 

        $invoice->load([
            'student',
            'enrollment.course',
            'enrollment.academicYear',
            'items.feeConcept',
            'payments',
            'createdBy',
        ]);

        return view('university.finance.invoices.show', compact('invoice'));
    }

    public function issue(string $invoice)
    {
        $invoice = Invoice::findOrFail($invoice); 

        if ($invoice->status !== 'draft') {
            return back()->with('error', 'Apenas facturas em rascunho podem ser emitidas.');
        }

        if ($invoice->items()->count() === 0 || $invoice->total <= 0) {
            return back()->with('error', 'A factura precisa de pelo menos um artigo e um total superior a zero.');
        }

        $invoice->recalculateFinancials();
        $invoice->update(['status' => 'issued']);

        return back()->with('success', 'Factura emitida com sucesso.');
    }

    public function cancel(string $invoice)
    {
        $invoice = Invoice::findOrFail($invoice); 

        if (! in_array($invoice->status, ['draft', 'issued'], true)
            || (float) $invoice->amount_paid > 0) {
            return back()->with('error', 'Esta factura não pode ser cancelada.');
        }

        $invoice->update(['status' => 'cancelled']);

        return back()->with('success', 'Factura cancelada com sucesso.');
    }
}
