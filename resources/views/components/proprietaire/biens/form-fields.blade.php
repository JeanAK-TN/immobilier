@props([
    'bien',
    'typeOptions' => [],
    'statutOptions' => [],
])

<div class="grid gap-6">
    <div class="grid gap-6 md:grid-cols-2">
        <div class="grid gap-2">
            <x-input-label for="nom" :value="__('Nom du bien')" />
            <x-text-input
                id="nom"
                name="nom"
                type="text"
                class="w-full"
                :value="old('nom', $bien->nom)"
                required
            />
            <x-input-error :messages="$errors->get('nom')" />
        </div>

        <div class="grid gap-2">
            <x-input-label for="type" :value="__('Type de bien')" />
            <select
                id="type"
                name="type"
                class="border-gray-300 focus:border-gray-500 focus:ring-gray-500 rounded-md shadow-sm"
                required
            >
                <option value="">{{ __('Choisir un type') }}</option>
                @foreach ($typeOptions as $typeOption)
                    <option
                        value="{{ $typeOption->value }}"
                        @selected(old('type', $bien->type?->value ?? $bien->type) === $typeOption->value)
                    >
                        {{ $typeOption->label() }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('type')" />
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div class="grid gap-2 md:col-span-2">
            <x-input-label for="adresse" :value="__('Adresse')" />
            <x-text-input
                id="adresse"
                name="adresse"
                type="text"
                class="w-full"
                :value="old('adresse', $bien->adresse)"
                required
            />
            <x-input-error :messages="$errors->get('adresse')" />
        </div>

        <div class="grid gap-2">
            <x-input-label for="ville" :value="__('Ville')" />
            <x-text-input
                id="ville"
                name="ville"
                type="text"
                class="w-full"
                :value="old('ville', $bien->ville)"
                required
            />
            <x-input-error :messages="$errors->get('ville')" />
        </div>

        <div class="grid gap-2">
            <x-input-label for="pays" :value="__('Pays')" />
            <x-text-input
                id="pays"
                name="pays"
                type="text"
                class="w-full"
                :value="old('pays', $bien->pays ?: 'France')"
                required
            />
            <x-input-error :messages="$errors->get('pays')" />
        </div>
    </div>

    <div class="grid gap-2">
        <x-input-label for="description" :value="__('Description')" />
        <textarea
            id="description"
            name="description"
            rows="5"
            class="border-gray-300 focus:border-gray-500 focus:ring-gray-500 rounded-md shadow-sm"
        >{{ old('description', $bien->description) }}</textarea>
        <x-input-error :messages="$errors->get('description')" />
    </div>

    <div class="grid gap-2">
        <div class="flex items-center justify-between gap-3">
            <x-input-label for="statut" :value="__('Statut déclaré du bien')" />
            @if (($bien->contrat_actif_count ?? 0) > 0)
                <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-200">
                    {{ __('Un contrat actif impose une occupation actuelle') }}
                </span>
            @endif
        </div>
        <select
            id="statut"
            name="statut"
            class="border-gray-300 focus:border-gray-500 focus:ring-gray-500 rounded-md shadow-sm"
            required
        >
            @foreach ($statutOptions as $statutOption)
                <option
                    value="{{ $statutOption->value }}"
                    @selected(old('statut', $bien->statut?->value ?? $bien->statut) === $statutOption->value)
                >
                    {{ $statutOption->label() }}
                </option>
            @endforeach
        </select>
        <p class="text-sm text-gray-500">
            {{ __('L\'indicateur d\'occupation affiché dans l\'application est également lié à l\'existence d\'un contrat actif.') }}
        </p>
        <x-input-error :messages="$errors->get('statut')" />
    </div>

    <div class="grid gap-3 rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-5">
        <div class="grid gap-1">
            <x-input-label for="photos" :value="__('Galerie photos')" />
            <p class="text-sm text-gray-600">
                {{ __('Ajoutez jusqu\'à 10 photos au format image. Taille maximale : 5 Mo par image.') }}
            </p>
        </div>

        <input
            id="photos"
            name="photos[]"
            type="file"
            accept="image/*"
            multiple
            class="block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-gray-800 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-gray-700"
        >
        <x-input-error :messages="$errors->get('photos')" />
        <x-input-error :messages="$errors->get('photos.*')" />
    </div>

    @if ($bien->exists && $bien->relationLoaded('photos') && $bien->photos->isNotEmpty())
        <div class="grid gap-4">
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-base font-semibold text-gray-900">{{ __('Photos existantes') }}</h3>
                <p class="text-sm text-gray-500">
                    {{ __('Cochez les photos à retirer lors de l\'enregistrement.') }}
                </p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($bien->photos as $photo)
                    <label class="grid gap-3 rounded-2xl border border-gray-200 bg-white p-3 shadow-sm">
                        <img
                            src="{{ $photo->url() }}"
                            alt="{{ $photo->nom_original }}"
                            class="h-48 w-full rounded-xl object-cover"
                        >

                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-gray-900">{{ $photo->nom_original }}</p>
                                <p class="text-xs text-gray-500">{{ $photo->tailleFormatee() }}</p>
                            </div>

                            <span class="inline-flex items-center gap-2 text-sm text-red-600">
                                <input
                                    type="checkbox"
                                    name="photos_a_supprimer[]"
                                    value="{{ $photo->id }}"
                                    class="rounded border-gray-300 text-red-600 focus:ring-red-500"
                                    @checked(collect(old('photos_a_supprimer', []))->contains((string) $photo->id) || collect(old('photos_a_supprimer', []))->contains($photo->id))
                                >
                                {{ __('Retirer') }}
                            </span>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>
    @endif
</div>
