@props([
    'contrat',
    'biens' => collect(),
    'locataires' => collect(),
    'statutOptions' => [],
])

<div class="grid gap-6">
    <div class="grid gap-6 md:grid-cols-2">
        <div class="grid gap-2">
            <x-input-label for="bien_id" :value="__('Bien')" />
            <select
                id="bien_id"
                name="bien_id"
                class="border-gray-300 focus:border-gray-500 focus:ring-gray-500 rounded-md shadow-sm"
                required
            >
                <option value="">{{ __('Sélectionner un bien') }}</option>
                @foreach ($biens as $bien)
                    <option value="{{ $bien->id }}" @selected((string) old('bien_id', $contrat->bien_id) === (string) $bien->id)>
                        {{ $bien->nom }} — {{ $bien->ville }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('bien_id')" />
        </div>

        <div class="grid gap-2">
            <x-input-label for="locataire_id" :value="__('Locataire')" />
            <select
                id="locataire_id"
                name="locataire_id"
                class="border-gray-300 focus:border-gray-500 focus:ring-gray-500 rounded-md shadow-sm"
                required
            >
                <option value="">{{ __('Sélectionner un locataire') }}</option>
                @foreach ($locataires as $locataireOption)
                    <option value="{{ $locataireOption->id }}" @selected((string) old('locataire_id', $contrat->locataire_id) === (string) $locataireOption->id)>
                        {{ $locataireOption->nomComplet() }} — {{ $locataireOption->email }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('locataire_id')" />
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
        <div class="grid gap-2">
            <x-input-label for="date_debut" :value="__('Date de début')" />
            <x-text-input
                id="date_debut"
                name="date_debut"
                type="date"
                class="w-full"
                :value="old('date_debut', optional($contrat->date_debut)->format('Y-m-d'))"
                required
            />
            <x-input-error :messages="$errors->get('date_debut')" />
        </div>

        <div class="grid gap-2">
            <x-input-label for="date_fin" :value="__('Date de fin')" />
            <x-text-input
                id="date_fin"
                name="date_fin"
                type="date"
                class="w-full"
                :value="old('date_fin', optional($contrat->date_fin)->format('Y-m-d'))"
            />
            <x-input-error :messages="$errors->get('date_fin')" />
        </div>

        <div class="grid gap-2">
            <x-input-label for="jour_paiement" :value="__('Jour de paiement')" />
            <x-text-input
                id="jour_paiement"
                name="jour_paiement"
                type="number"
                min="1"
                max="28"
                class="w-full"
                :value="old('jour_paiement', $contrat->jour_paiement)"
                required
            />
            <x-input-error :messages="$errors->get('jour_paiement')" />
        </div>

        <div class="grid gap-2">
            <x-input-label for="statut" :value="__('Statut')" />
            <select
                id="statut"
                name="statut"
                class="border-gray-300 focus:border-gray-500 focus:ring-gray-500 rounded-md shadow-sm"
                required
            >
                @foreach ($statutOptions as $statutOption)
                    <option value="{{ $statutOption->value }}" @selected(old('statut', $contrat->statut?->value ?? $contrat->statut) === $statutOption->value)>
                        {{ $statutOption->label() }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('statut')" />
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
        <div class="grid gap-2">
            <x-input-label for="loyer_mensuel" :value="__('Loyer mensuel')" />
            <x-text-input
                id="loyer_mensuel"
                name="loyer_mensuel"
                type="number"
                step="0.01"
                min="0.01"
                class="w-full"
                :value="old('loyer_mensuel', $contrat->loyer_mensuel)"
                required
            />
            <x-input-error :messages="$errors->get('loyer_mensuel')" />
        </div>

        <div class="grid gap-2">
            <x-input-label for="depot_garantie" :value="__('Dépôt de garantie')" />
            <x-text-input
                id="depot_garantie"
                name="depot_garantie"
                type="number"
                step="0.01"
                min="0"
                class="w-full"
                :value="old('depot_garantie', $contrat->depot_garantie)"
            />
            <x-input-error :messages="$errors->get('depot_garantie')" />
        </div>

        <div class="grid gap-2">
            <x-input-label for="charges" :value="__('Charges')" />
            <x-text-input
                id="charges"
                name="charges"
                type="number"
                step="0.01"
                min="0"
                class="w-full"
                :value="old('charges', $contrat->charges)"
            />
            <x-input-error :messages="$errors->get('charges')" />
        </div>

        <div class="flex items-end">
            <label class="inline-flex items-center gap-3 rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-medium text-gray-700">
                <input type="hidden" name="reconduction_auto" value="0">
                <input
                    type="checkbox"
                    name="reconduction_auto"
                    value="1"
                    class="rounded border-gray-300 text-gray-900 shadow-sm focus:ring-gray-500"
                    @checked(old('reconduction_auto', $contrat->reconduction_auto))
                >
                {{ __('Reconduction automatique') }}
            </label>
        </div>
    </div>

    <div class="grid gap-3 rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-5">
        <div class="grid gap-1">
            <x-input-label for="document_pdf" :value="__('Document du contrat (PDF)')" />
            <p class="text-sm text-gray-600">
                {{ __('Téléversez un PDF du bail. Il est requis pour un contrat en attente de signature ou actif.') }}
            </p>
        </div>

        <input
            id="document_pdf"
            name="document_pdf"
            type="file"
            accept="application/pdf"
            class="block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-gray-800 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-gray-700"
        >
        <x-input-error :messages="$errors->get('document_pdf')" />

        @if ($contrat->documentDisponible())
            <p class="text-sm text-gray-600">
                {{ __('Document actuel : :nom', ['nom' => $contrat->nomDocument()]) }}
            </p>
        @endif
    </div>
</div>
