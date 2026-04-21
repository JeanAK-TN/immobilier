<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLocataireRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('locataire')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'prenom' => ['required', 'string', 'max:255'],
            'nom' => ['required', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique((new User)->getTable(), 'email')->ignore($this->locataire->user_id),
                Rule::unique('locataires', 'email')->ignore($this->locataire->id),
            ],
            'is_active' => ['required', 'boolean'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'prenom' => trim((string) $this->prenom),
            'nom' => trim((string) $this->nom),
            'telephone' => filled($this->telephone) ? trim((string) $this->telephone) : null,
            'email' => trim((string) $this->email),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'prenom' => 'prénom',
            'nom' => 'nom',
            'telephone' => 'téléphone',
            'email' => 'adresse e-mail',
            'is_active' => 'statut du compte',
        ];
    }
}
