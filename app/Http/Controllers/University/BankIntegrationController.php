<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\BankIntegration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\Finance\FinancialAuditService;
use Illuminate\Validation\Rule;

class BankIntegrationController extends Controller
{
    private array $providers = [
        'bfa' => 'Banco de Fomento Angola (BFA)',
        'bai' => 'Banco Angolano de Investimentos (BAI)',
        'bic' => 'Banco BIC',
        'bpc' => 'Banco de Poupança e Crédito (BPC)',
        'atlantico' => 'Banco Millennium Atlântico',
        'standard_bank' => 'Standard Bank Angola',
        'other' => 'Outro banco',
    ];

    public function index()
    {
        $integrations = BankIntegration::query()
            ->with('bankAccount')
            ->latest()
            ->paginate(15);

        return view(
            'university.finance.bank-integrations.index',
            compact('integrations')
        );
    }

    public function create()
    {
        $accounts = BankAccount::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'university.finance.bank-integrations.create',
            [
                'accounts' => $accounts,
                'providers' => $this->providers,
            ]
        );
    }

    public function store(Request $request)
    {
        $data = $this->validateIntegration($request);

        $account = BankAccount::query()
            ->where('is_active', true)
            ->findOrFail($data['bank_account_id']);

        $exists = BankIntegration::query()
            ->where('bank_account_id', $account->id)
            ->where('provider', $data['provider'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors([
                    'provider' => 'Já existe uma integração deste banco associada a esta conta.',
                ]);
        }

        DB::transaction(function () use ($data, $request) {
            $integration = BankIntegration::create([
                'public_id' => (string) Str::uuid(),
                'bank_account_id' => $data['bank_account_id'],
                'provider' => $data['provider'],
                'name' => $data['name'],
                'environment' => $data['environment'],
                'status' => 'configured',
                'base_url' => $data['base_url'] ?? null,
                'webhook_url' => null,
                'settings' => $data['settings'] ?? [],
                'created_by' => $request->user()->id,
            ]);

            $credentials = $this->extractCredentials($data);

            if (!empty($credentials)) {
                $integration->credentials()->create([
                    'credentials' => $credentials,
                ]);
            }
        });

        return redirect()
            ->route('university.bank-integrations.index')
            ->with('success', 'Integração bancária registada com sucesso.');
    }

    public function show(BankIntegration $bankIntegration)
    {
        $bankIntegration->load([
            'bankAccount',
            'credentials',
        ]);

        $hasCredentials = $bankIntegration->credentials !== null;

        return view(
            'university.finance.bank-integrations.show',
            compact('bankIntegration', 'hasCredentials')
        );
    }

    public function edit(BankIntegration $bankIntegration)
    {
        $accounts = BankAccount::query()
            ->where('is_active', true)
            ->orWhere('id', $bankIntegration->bank_account_id)
            ->orderBy('name')
            ->get();

        return view(
            'university.finance.bank-integrations.edit',
            [
                'integration' => $bankIntegration,
                'accounts' => $accounts,
                'providers' => $this->providers,
            ]
        );
    }

    public function update(
        Request $request,
        BankIntegration $bankIntegration
    ) {
        $data = $this->validateIntegration($request, $bankIntegration);

        $account = BankAccount::query()
            ->findOrFail($data['bank_account_id']);

        $exists = BankIntegration::query()
            ->where('bank_account_id', $account->id)
            ->where('provider', $data['provider'])
            ->where('id', '!=', $bankIntegration->id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors([
                    'provider' => 'Já existe uma integração deste banco associada a esta conta.',
                ]);
        }

        DB::transaction(function () use ($data, $bankIntegration) {
            $bankIntegration->update([
                'bank_account_id' => $data['bank_account_id'],
                'provider' => $data['provider'],
                'name' => $data['name'],
                'environment' => $data['environment'],
                'base_url' => $data['base_url'] ?? null,
                'settings' => $data['settings'] ?? [],
            ]);

            $newCredentials = $this->extractCredentials($data);

            if (!empty($newCredentials)) {
                $currentCredentials = $bankIntegration->credentials
                    ? $bankIntegration->credentials->credentials
                    : [];

                $bankIntegration->credentials()->updateOrCreate(
                    [],
                    [
                        'credentials' => array_merge(
                            $currentCredentials ?? [],
                            $newCredentials
                        ),
                    ]
                );
            }
        });

        return redirect()
            ->route('university.bank-integrations.show', $bankIntegration)
            ->with('success', 'Configuração actualizada com sucesso.');
    }

    public function disable(BankIntegration $bankIntegration, FinancialAuditService $audit)
    {
        $oldStatus = $bankIntegration->status;

        $bankIntegration->update([
            'status' => 'disabled',
        ]);


        $audit->record(
            module: 'banking',
            event: 'bank_integration.disabled',
            description: 'Uma integração bancária foi desactivada.',
            record: $bankIntegration,
            oldValues: [
                'status' => $oldStatus,
            ],
            newValues: [
                'status' => 'disabled',
            ]
        );

        return redirect()
            ->route('university.bank-integrations.show', $bankIntegration)
            ->with('success', 'Integração bancária desactivada com sucesso.');
    }

    private function validateIntegration(
        Request $request,
        ?BankIntegration $integration = null
    ): array {
        return $request->validate([
            'bank_account_id' => [
                'required',
                'integer',
                'exists:bank_accounts,id',
            ],

            'provider' => [
                'required',
                Rule::in(array_keys($this->providers)),
            ],

            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'environment' => [
                'required',
                Rule::in(['sandbox', 'production']),
            ],

            'base_url' => [
                'nullable',
                'url',
                'max:255',
            ],

            'client_id' => [
                'nullable',
                'string',
                'max:500',
            ],

            'client_secret' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'api_key' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'api_secret' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'webhook_secret' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'settings' => [
                'nullable',
                'array',
            ],
        ]);
    }

    private function extractCredentials(array $data): array
    {
        $fields = [
            'client_id',
            'client_secret',
            'api_key',
            'api_secret',
            'webhook_secret',
        ];

        $credentials = [];

        foreach ($fields as $field) {
            if (!empty($data[$field])) {
                $credentials[$field] = $data[$field];
            }
        }

        return $credentials;
    }
}
