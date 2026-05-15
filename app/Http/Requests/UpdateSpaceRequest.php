<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSpaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $spaceId = $this->route('space')?->id;

        return [
            'name'          => 'required|string|max:255',
            'slug'          => "required|string|max:255|unique:spaces,slug,{$spaceId}",
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
}