<?php

namespace App\Http\Requests;

use App\Models\Contrat;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SignerContratRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $contrat = $this->contratCourant();

        return $contrat !== null && ($this->user()?->can('sign', $contrat) ?? false);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'confirmation_signature' => ['accepted'],
            'signe_nom' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'confirmation_signature.accepted' => 'Vous devez confirmer la signature du contrat.',
            'signe_nom.required' => 'Le nom du signataire est obligatoire.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'confirmation_signature' => 'confirmation de signature',
            'signe_nom' => 'nom du signataire',
        ];
    }

    private function contratCourant(): ?Contrat
    {
        return $this->user()?->locataire?->contratCourant();
    }
}
