@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <div>
        <label class="block text-sm font-medium text-gray-700">
            Conta bancária
        </label>

        <select name="bank_account_id" required class="mt-1 block w-full rounded-md border-gray-300">
            <option value="">Seleccione a conta</option>

            @foreach($accounts as $account)
            <option value="{{ $account->id }}" @selected(old('bank_account_id', $integration->bank_account_id ?? '') == $account->id)>
                {{ $account->name }} - {{ $account->bank_name }}
            </option>
            @endforeach
        </select>
        @error('bank_account_id')
        <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Banco</label>

        <select name="provider" required class="mt-1 block w-full rounded-md border-gray-300">
            <option value="">Seleccione o banco</option>

            @foreach($providers as $key => $label)
            <option value="{{ $key }}" @selected(old('provider', $integration->provider ?? '') == $key)>
                {{ $label }}
            </option>
            @endforeach
        </select>
        @error('provider')
        <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">
            Nome da integração
        </label>

        <input type="text" name="name" required value="{{ old('name', $integration->name ?? '') }}" placeholder="Ex.: Integração principal BFA" class="mt-1 block w-full rounded-md border-gray-300">

        @error('name')
        <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">
            Ambiente
        </label>

        <select name="environment" required class="mt-1 block w-full rounded-md border-gray-300">
            <option value="sandbox" @selected(old('environment', $integration->environment ?? 'sandbox') == 'sandbox')>
                Testes (Sandbox)
            </option>

            <option value="production" @selected(old('environment', $integration->environment ?? '') == 'production')>
                Produção
            </option>
        </select>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">
            URL base da API
        </label>

        <input type="url" name="base_url" value="{{ old('base_url', $integration->base_url ?? '') }}" placeholder="https://api.banco.co.ao" class="mt-1 block w-full rounded-md border-gray-300">

        @error('base_url')
        <p class="text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

</div>

<div class="mt-8 border-t pt-6">
    <h3 class="text-lg font-semibold text-gray-800">
        Credenciais da API
    </h3>

    <p class="text-sm text-gray-500 mt-1">
        Estes campos são opcionais enquanto o banco não disponibilizar
        as credenciais. Os valores são armazenados de forma encriptada.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-5">

        <div>
            <label class="block text-sm font-medium text-gray-700">
                Client ID
            </label>
            <input type="text" name="client_id" autocomplete="off" class="mt-1 block w-full rounded-md border-gray-300">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">
                Client Secret
            </label>
            <input type="password" name="client_secret" autocomplete="new-password" class="mt-1 block w-full rounded-md border-gray-300">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">
                API Key
            </label>
            <input type="password" name="api_key" autocomplete="new-password" class="mt-1 block w-full rounded-md border-gray-300">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">
                API Secret
            </label>
            <input type="password" name="api_secret" autocomplete="new-password" class="mt-1 block w-full rounded-md border-gray-300">
        </div>

        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700">
                Webhook Secret
            </label>
            <input type="password" name="webhook_secret" autocomplete="new-password" class="mt-1 block w-full rounded-md border-gray-300">
        </div>

    </div>
</div>

<div class="mt-8 flex justify-end gap-3">
    <a href="{{ route('university.bank-integrations.index') }}" class="px-4 py-2 border rounded-md text-gray-700">
        Cancelar
    </a>

    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
        Guardar integração
    </button>
</div>
