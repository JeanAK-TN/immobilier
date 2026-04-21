<?php

namespace App\Http\Requests;

use App\Enums\CategorieTicket;
use App\Enums\PrioriteTicket;
use App\Models\TicketMaintenance;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketMaintenanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return ($this->user()?->can('create', TicketMaintenance::class) ?? false)
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
            'titre' => ['required', 'string', 'max:255'],
            'categorie' => ['required', Rule::enum(CategorieTicket::class)],
            'priorite' => ['required', Rule::enum(PrioriteTicket::class)],
            'description' => ['required', 'string', 'min:10'],
            'photos' => ['nullable', 'array', 'max:5'],
            'photos.*' => ['image', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'photos.max' => 'Vous pouvez ajouter au maximum 5 photos.',
            'photos.*.image' => 'Chaque pièce jointe doit être une image valide.',
            'photos.*.max' => 'Chaque image doit faire au maximum 5 Mo.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'titre' => 'titre',
            'categorie' => 'catégorie',
            'priorite' => 'priorité',
            'description' => 'description',
            'photos' => 'photos',
            'photos.*' => 'photo',
        ];
    }
}
