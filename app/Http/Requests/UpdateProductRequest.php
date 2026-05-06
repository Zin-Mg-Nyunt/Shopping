<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        Gate::authorize('admin');

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($this->product->id)],
            'description' => ['required', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'thumbnail' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($this->hasFile('thumbnail')) {
                        $validator = Validator::make(['thumbnail' => $value],
                            ['thumbnail' => ['image', 'mimes:jpeg,png,jpg,svg,webp', 'max:2048']]);
                        if ($validator->fails()) {
                            $fail($validator->errors()->first('thumbnail'));
                        }
                    }
                },
            ],
            'categories' => ['required', 'array', 'min:1'],
            'brand_id' => ['required', 'integer'],
        ];
        if (!empty($this->new_categories) && count($this->new_categories) > 0) {
            $rules['new_categories'] = ['array', 'min:1'];
            $rules['new_categories.*'] = ['string', 'max:255'];
        }
        if ($this->new_brand !== null) {
            $rules['new_brand'] = ['string', 'max:255'];
        }

        return $rules;
    }
}
