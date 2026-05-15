<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreSpaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => 'required|string|max:255',
            'slug'          => 'nullable|string|max:255|unique:spaces,slug',
            'type'          => 'required|in:university,corporate',
            'capacity'      => 'required|integer|min:1|max:10000',
            'description'   => 'required|string|max:2000',
            'rules'         => 'nullable|string|max:2000',
            'price_per_hour'=> 'required|numeric|min:0',
            'is_active'     => 'boolean',
            'location'      => 'nullable|string|max:255',
            'image'         => 'nullable|string|max:500',
        ];
    }

    protected function prepareForValidation(): void
    {
        if (empty($this->slug) && $this->name) {
            $this->merge(['slug' => Str::slug($this->name)]);
        }
    }
}