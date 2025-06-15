<?php

namespace App\Http\Requests;

class StoreQuoteRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'labor_hours' => 'required|numeric',
            'labor_cost_per_hour' => 'required|numeric',
            'fixed_overheads' => 'required|numeric',
            'target_profit_margin' => 'required|numeric|max:100|min:0',
            'line_items' => 'required|array',
            'line_items.*.name' => 'required|string',
            'line_items.*.cost_price' => 'required|numeric',
            'line_items.*.sell_price' => 'required|numeric',
            'line_items.*.quantity' => 'required|integer',
            'line_items.*.additional_info' => 'nullable|array',
        ];
    }
}
