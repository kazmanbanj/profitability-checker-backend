<?php

namespace App\Http\Requests;

class ReAnalyzeQuoteRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'quote_id' => 'required|integer',
            'line_items' => 'nullable|array|min:1',
            'line_items.*.id' => 'required_with:line_items|integer',
            'line_items.*.suggestion' => 'nullable|string',
            'labor_suggestions' => 'nullable|array',
            'labor_suggestions.comment' => 'nullable|string',
            'ai_suggestions' => 'nullable|array',
            'ai_suggestions.target_margin_adjustments' => 'nullable|string',
            'ai_suggestions.labor_allocation_improvements' => 'nullable|string',
            'ai_suggestions.product_swaps' => 'nullable|string',
            'ai_suggestions.profitability_summary' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'line_items.*.id.required_with' => 'Line item ID is required when providing line items.',
            'line_items.*.id.integer' => 'Line item ID must be an integer.',
            'line_items.*.suggestion.string' => 'Line item suggestion must be a string.',
            'labor_suggestions.comment.string' => 'Labor suggestion comment must be a string.',
            'ai_suggestions.target_margin_adjustments.string' => 'Target margin adjustments must be a string.',
            'ai_suggestions.labor_allocation_improvements.string' => 'Labor allocation improvements must be a string.',
            'ai_suggestions.product_swaps.string' => 'Product swaps must be a string.',
            'ai_suggestions.profitability_summary.string' => 'Profitability summary must be a string.',
        ];
    }
}
