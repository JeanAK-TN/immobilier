@props([
    'locataire',
    'edition' => false,
])

<div class="grid gap-6">
    <div class="grid gap-6 md:grid-cols-2">
        <div class="grid gap-2">
            <x-input-label for="prenom" :value="__('Prénom')" />
            <x-text-input
                id="prenom"
                name="prenom"
                type="text"
                class="w-full"
                :value="old('prenom', $locataire->prenom)"
                required
            />
            <x-input-error :messages="$errors->get('prenom')" />
        </div>

        <div class="grid gap-2">
            <x-input-label for="nom" :value="__('Nom')" />
            <x-text-input
                id="nom"
                name="nom"
                type="text"
                class="w-full"
                :value="old('nom', $locataire->nom)"
                required
            />
            <x-input-error :messages="$errors->get('nom')" />
        </div>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div class="grid gap-2">
            <x-input-label for="email" :value="__('Adresse e-mail')" />
            <x-text-input
                id="email"
                name="email"
                type="email"
                class="w-full"
                :value="old('email', $locataire->email)"
                required
            />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="grid gap-2">
            <x-input-label for="telephone" :value="__('Téléphone')" />
            <x-text-input
                id="telephone"
                name="telephone"
                type="text"
                class="w-full"
                :value="old('telephone', $locataire->telephone)"
            />
            <x-input-error :messages="$errors->get('telephone')" />
        </div>
    </div>

    @if ($edition)
        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-900">{{ __('Statut du compte') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ __('Un compte désactivé ne peut plus se connecter tant qu\'il n\'est pas réactivé.') }}
                    </p>
                </div>

                <label class="inline-flex items-center gap-3 text-sm font-medium text-gray-700">
                    <input
                        type="hidden"
                        name="is_active"
                        value="0"
                    >
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        class="rounded border-gray-300 text-gray-900 shadow-sm focus:ring-gray-500"
                        @checked(old('is_active', $locataire->user?->is_active))
                    >
                    {{ __('Compte actif') }}
                </label>
            </div>
            <x-input-error :messages="$errors->get('is_active')" class="mt-3" />
        </div>
    @else
        <div class="rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-5">
            <h3 class="text-base font-semibold text-gray-900">{{ __('Compte créé automatiquement') }}</h3>
            <p class="mt-2 text-sm text-gray-600">
                {{ __('Un mot de passe temporaire sera généré et affiché après la création. Le locataire devra le changer à sa première connexion.') }}
            </p>
        </div>
    @endif
</div>
