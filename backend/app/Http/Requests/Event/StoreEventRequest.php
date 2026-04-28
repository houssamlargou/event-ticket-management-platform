<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
           'title' => 'required|string|max:255',
           'description' => 'required|string',
           'event_date' => 'required|date',
           'location' => 'required|string|max:255',
           'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
}
