<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($this->product->id)],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'thumbnail' => [
                'nullable',
                function($attribute, $value, $fail) {
                    if (request()->hasFile('thumbnail')) {
                        $validator = Validator::make(['thumbnail' => $value], 
                        ['thumbnail' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:2048']]);
                        if ($validator->fails()) {
                            $fail($validator->errors()->first('thumbnail'));
                        }
                    }
                }
            ],
            'categories' => ['required', 'array', 'min:1'],
            'new_categories' => ['nullable', 'array', 'min:1'],
            'new_categories.*' => ['string', 'max:255'],
            'brand_id' => ['required', 'integer'],
            'new_brand' => ['nullable', 'string', 'max:255'],
        ];
    }
}
