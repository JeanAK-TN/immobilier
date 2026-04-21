<?php

namespace App\Http\Requests;

use App\Enums\ModePaiement;
use App\Enums\OperateurMobileMoney;
use App\Models\Paiement;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSimulatedPaiementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return ($this->user()?->can('create', Paiement::class) ?? false)
            && $this->user()?->locataire?->contratActif() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'periode' => ['required', 'date_format:Y-m'],
            'montant' => ['required', 'numeric', 'min:1'],
            'mode' => ['required', Rule::enum(ModePaiement::class)],
            'operateur_mobile_money' => [
                Rule::requiredIf(fn () => $this->input('mode') === ModePaiement::MobileMoney->value),
                'nullable',
                Rule::enum(OperateurMobileMoney::class),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'periode.date_format' => 'La période doit être au format AAAA-MM.',
            'operateur_mobile_money.required' => 'Choisissez un opérateur Mobile Money.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'periode' => 'période',
            'montant' => 'montant',
            'mode' => 'mode de paiement',
            'operateur_mobile_money' => 'opérateur Mobile Money',
        ];
    }
}
