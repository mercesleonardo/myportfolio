<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProfessionalProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'professional_title' => ['required', 'string', 'max:255'],
            'bio'                => ['required', 'string', 'max:2000'],
            'avatar'             => ['nullable', 'image', 'max:2048'],
            'resume'             => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ];
    }
}
