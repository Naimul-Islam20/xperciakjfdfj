<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAboutPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'about_page_title' => ['required', 'string', 'max:255'],
            'about_page_subtitle' => ['nullable', 'string', 'max:500'],
            'about_page_body' => ['required', 'string', 'max:20000'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('about_page_subtitle') && trim((string) $this->input('about_page_subtitle')) === '') {
            $this->merge(['about_page_subtitle' => null]);
        }
    }
}
