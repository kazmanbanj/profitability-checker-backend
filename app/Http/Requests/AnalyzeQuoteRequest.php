<?php

namespace App\Http\Requests;

class AnalyzeQuoteRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'labor_hours' => 'required|numeric|min:0',
            'labor_cost_per_hour' => 'required|numeric|min:0.01',
            'fixed_overheads' => 'required|numeric|min:0',
            'target_profit_margin' => 'required|numeric|min:0|max:100',
            'line_items' => 'required|array|min:1',
            'line_items.*.name' => 'required|string',
            'line_items.*.cost_price' => 'required|numeric|min:0.01',
            'line_items.*.sell_price' => 'required|numeric|min:0.01',
            'line_items.*.quantity' => 'required|integer|min:0',
            'line_items.*.additional_info' => 'nullable|array',
        ];
    }
}
