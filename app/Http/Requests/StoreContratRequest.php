<?php

namespace App\Http\Requests;

use App\Enums\StatutContrat;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class StoreContratRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Contrat::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'bien_id' => ['required', 'integer', Rule::exists('biens', 'id')],
            'locataire_id' => ['required', 'integer', Rule::exists('locataires', 'id')],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['nullable', 'date', 'after_or_equal:date_debut'],
            'reconduction_auto' => ['nullable', 'boolean'],
            'loyer_mensuel' => ['required', 'numeric', 'min:0.01'],
            'depot_garantie' => ['nullable', 'numeric', 'min:0'],
            'charges' => ['nullable', 'numeric', 'min:0'],
            'jour_paiement' => ['required', 'integer', 'between:1,28'],
            'statut' => ['required', Rule::enum(StatutContrat::class)],
            'document_pdf' => ['nullable', File::types(['pdf'])->max(10 * 1024)],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'reconduction_auto' => $this->boolean('reconduction_auto'),
            'depot_garantie' => $this->filled('depot_garantie') ? $this->input('depot_garantie') : 0,
            'charges' => $this->filled('charges') ? $this->input('charges') : 0,
        ]);
    }

    /**
     * Configure the validator instance.
     *
     * @return array<int, \Closure(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $user = $this->user();
                $bien = Bien::query()->find($this->integer('bien_id'));
                $locataire = Locataire::query()->find($this->integer('locataire_id'));
                $statut = $this->input('statut');

                if ($bien && $bien->user_id !== $user?->id) {
                    $validator->errors()->add('bien_id', 'Le bien sélectionné ne vous appartient pas.');
                }

                if ($locataire && $locataire->cree_par_user_id !== $user?->id) {
                    $validator->errors()->add('locataire_id', 'Le locataire sélectionné ne vous appartient pas.');
                }

                if (
                    $bien
                    && $statut === StatutContrat::Actif->value
                    && $bien->contratActif()->exists()
                ) {
                    $validator->errors()->add(
                        'bien_id',
                        'Ce bien possède déjà un contrat actif.'
                    );
                }

                if (
                    in_array($statut, [StatutContrat::EnAttente->value, StatutContrat::Actif->value], true)
                    && ! $this->hasFile('document_pdf')
                ) {
                    $validator->errors()->add(
                        'document_pdf',
                        'Un document PDF est requis pour un contrat en attente de signature ou actif.'
                    );
                }

                if (
                    in_array($statut, [StatutContrat::Termine->value, StatutContrat::Resilie->value], true)
                    && ! filled($this->input('date_fin'))
                ) {
                    $validator->errors()->add(
                        'date_fin',
                        'La date de fin est requise pour un contrat terminé ou résilié.'
                    );
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'bien_id' => 'bien',
            'locataire_id' => 'locataire',
            'date_debut' => 'date de début',
            'date_fin' => 'date de fin',
            'reconduction_auto' => 'reconduction automatique',
            'loyer_mensuel' => 'loyer mensuel',
            'depot_garantie' => 'dépôt de garantie',
            'charges' => 'charges',
            'jour_paiement' => 'jour de paiement',
            'statut' => 'statut',
            'document_pdf' => 'document PDF',
        ];
    }
}
