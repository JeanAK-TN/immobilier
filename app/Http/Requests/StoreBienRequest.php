<?php

namespace App\Http\Requests;

use App\Enums\StatutBien;
use App\Enums\TypeBien;
use App\Models\Bien;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class StoreBienRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Bien::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(TypeBien::class)],
            'adresse' => ['required', 'string', 'max:255'],
            'ville' => ['required', 'string', 'max:255'],
            'pays' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'statut' => ['required', Rule::enum(StatutBien::class)],
            'photos' => ['nullable', 'array', 'max:10'],
            'photos.*' => ['bail', File::image()->max(5 * 1024)],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'description' => filled($this->description) ? trim((string) $this->description) : null,
            'nom' => trim((string) $this->nom),
            'adresse' => trim((string) $this->adresse),
            'ville' => trim((string) $this->ville),
            'pays' => trim((string) $this->pays),
        ]);
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nom' => 'nom du bien',
            'type' => 'type de bien',
            'adresse' => 'adresse',
            'ville' => 'ville',
            'pays' => 'pays',
            'description' => 'description',
            'statut' => 'statut',
            'photos' => 'photos',
            'photos.*' => 'photo',
        ];
    }
}
