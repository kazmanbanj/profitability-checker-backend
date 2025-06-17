@extends('layouts.app')

@section('content')
<style>
    body {
        font-family: Arial, sans-serif;
        color: #333;
    }

    .container {
        width: 90%;
        max-width: 900px;
        margin: 40px auto 60px auto;
        padding: 30px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    h2, h4 {
        color: #222;
        margin-bottom: 12px;
    }

    h2 {
        font-size: 22px;
        border-bottom: 2px solid #e0e0e0;
        padding-bottom: 6px;
    }

    h4 {
        font-size: 18px;
        margin-top: 32px;
        margin-bottom: 8px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 24px;
        font-size: 14px;
    }

    th, td {
        border: 1px solid #dcdcdc;
        padding: 10px;
        text-align: left;
        vertical-align: top;
    }

    thead th {
        background-color: #f5f5f5;
        color: #222;
        font-weight: bold;
    }

    tfoot td {
        background-color: #f9f9f9;
        font-weight: bold;
    }

    td.text-right, th.text-right {
        text-align: right;
    }

    .text-danger {
        color: #d32f2f;
        font-weight: bold;
    }

    .text-success {
        color: #388e3c;
        font-weight: bold;
    }

    .text-warning {
        color: #ff9800;
        font-weight: bold;
    }

    .status-cell {
        min-width: 100px;
    }

    .action-cell {
        max-width: 240px;
        word-wrap: break-word;
        white-space: normal;
    }

    .section-title {
        margin-top: 40px;
        margin-bottom: 10px;
    }

    .quote-items-table {
        font-size: 12px;
    }
    .quote-items-table th,
    .quote-items-table td {
        padding: 6px 8px;
    }
</style>

<div class="container">
    <h2 class="mb-4">{{ $companyName }} Quote Summary</h2>

    <h4>Quote Items Overview</h4>
    <table style="width: 100%;" class="table table-sm table-bordered quote-items-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Cost</th>
                <th>Sell</th>
                <th>Quantity</th>
                <th>Total Cost</th>
                <th>Total Revenue</th>
                <th>Margin %</th>
                <th>Status</th>
                <th>Recommendation</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($analysis['line_items'] as $item)
                @php
                    $totalCost = $item['cost_price'] * $item['quantity'];
                    $totalRevenue = $item['sell_price'] * $item['quantity'];
                @endphp
                <tr>
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $analysis['currency_symbol'] }}{{ number_format($item['cost_price'], 2) }}</td>
                    <td>{{ $analysis['currency_symbol'] }}{{ number_format($item['sell_price'], 2) }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>{{ $analysis['currency_symbol'] }}{{ number_format($totalCost, 2) }}</td>
                    <td>{{ $analysis['currency_symbol'] }}{{ number_format($totalRevenue, 2) }}</td>
                    <td class="{{ $item['margin_percent'] < 0 ? 'text-danger' : 'text-success' }}">
                        {{ $item['margin_percent'] }}%
                    </td>
                    <td class="status-cell {{ strtolower($item['status'] ?? '') === 'low margin' ? 'text-danger' : 'text-success' }}">
                        {{ $item['status'] ?? 'N/A' }}
                    </td>
                    <td class="action-cell">{{ $item['suggestion'] ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><strong>{{ $analysis['currency_symbol'] }}{{ number_format($analysis['total_cost'], 2) }}</strong></td>
                <td><strong>{{ $analysis['currency_symbol'] }}{{ number_format($analysis['total_revenue'], 2) }}</strong></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <h4 class="mt-5">Financial Summary</h4>
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>Metric</th>
                <th>Value</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>Total Revenue</th>
                <td></td>
                <td><strong>{{ $analysis['currency_symbol'] }}{{ number_format($analysis['total_revenue'], 2) }}</strong></td>
            </tr>
            <tr>
                <th>Labor Hours</th>
                <td>{{ $analysis['labor_hours'] }} hrs</td>
                <td></td>
            </tr>
            <tr>
                <th>Labor Cost Per Hour</th>
                <td>{{ $analysis['currency_symbol'] }}{{ number_format($analysis['labor_cost_per_hour'], 2) }}</td>
                <td></td>
            </tr>
            <tr>
                <th>Total Labor Cost</th>
                <td><strong>{{ $analysis['currency_symbol'] }}{{ number_format($analysis['labor_cost'], 2) }}</strong></td>
                <td></td>
            </tr>
            <tr>
                <th>Total Items Cost</th>
                <td><strong>{{ $analysis['currency_symbol'] }}{{ number_format($analysis['total_cost'], 2) }}</strong></td>
                <td></td>
            </tr>
            <tr>
                <th>Fixed Overheads</th>
                <td><strong>{{ $analysis['currency_symbol'] }}{{ number_format($analysis['fixed_overheads'], 2) }}</strong></td>
                <td></td>
            </tr>
            <tr>
                <th>Cost of Goods Sold (COGS)</th>
                <td></td>
                <td><strong>{{ $analysis['currency_symbol'] }}{{ number_format($analysis['cost_of_goods_sold'], 2) }}</strong></td>
            </tr>
            <tr>
                <th>Gross Profit</th>
                <td></td>
                <td class="{{ $analysis['gross_profit'] < 0 ? 'text-danger' : 'text-success' }}">
                    <strong>{{ $analysis['currency_symbol'] }}{{ number_format($analysis['gross_profit'], 2) }}</strong>
                </td>
            </tr>
            <tr>
                <th>Profit Margin</th>
                <td></td>
                <td class="{{ $analysis['profit_margin'] < 0 ? 'text-danger' : 'text-success' }}">
                    <strong>{{ number_format($analysis['profit_margin'], 2) }}%</strong>
                </td>
            </tr>
            <tr>
                <th>Target Margin</th>
                <td></td>
                <td>{{ number_format($analysis['target_profit_margin'], 2) }}%</td>
            </tr>
            <tr>
                <th>Target Met</th>
                <td></td>
                <td>
                    @if ($analysis['meets_target'])
                        Yes
                    @else
                        No
                    @endif
                </td>
            </tr>
            <tr>
                <th>Profitability Health Status</th>
                <td></td>
                <td>
                    @php
                        $indicator = $analysis['ai_suggestions']['profitability_health_indicator'] ?? null;
                        $statusMap = [
                            'green' => ['label' => 'Good', 'class' => 'text-success'],
                            'amber' => ['label' => 'Needs Review', 'class' => '', 'style' => 'color: #FFBF00;'],
                            'red' => ['label' => 'Poor', 'class' => 'text-danger'],
                        ];
                        $status = $statusMap[$indicator] ?? null;
                    @endphp
                    <span
                        @if (!empty($status['class'])) class="{{ $status['class'] }}" @endif
                        @if (!empty($status['style'])) style="{{ $status['style'] }}" @endif
                    >{{ $status['label'] ?? 'N/A' }}</span>
                </td>
            </tr>
        </tbody>
    </table>

    @if (!empty($analysis['labor_suggestions']))
        <h4 class="mt-5">Labor Efficiency Analysis</h4>
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Metric</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>Estimated Sustainable Hours</th>
                    <td>{{ $analysis['labor_suggestions']['estimated_sustainable_hours'] }}</td>
                </tr>
                <tr>
                    <th>Labour Hours Exceeded</th>
                    <td>
                        @if ($analysis['labor_suggestions']['labor_hours_exceeded'])
                            Yes
                        @else
                            No
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Comment</th>
                    <td>{{ $analysis['labor_suggestions']['comment'] }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    @if (!empty($analysis['ai_suggestions']))
        <h4 class="mt-5">Suggested Improvements</h4>
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>Metric</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th>Target Margin Adjustments</th>
                    <td>{{ $analysis['ai_suggestions']['target_margin_adjustments'] }}</td>
                </tr>
                <tr>
                    <th>Labor Allocation Improvements</th>
                    <td>{{ $analysis['ai_suggestions']['labor_allocation_improvements'] }}</td>
                </tr>
                <tr>
                    <th>Product Swaps</th>
                    <td>{{ $analysis['ai_suggestions']['product_swaps'] }}</td>
                </tr>
                <tr>
                    <th>Profitability Summary</th>
                    <td>{{ $analysis['ai_suggestions']['profitability_summary'] }}</td>
                </tr>
            </tbody>
        </table>
    @endif
</div>
@endsection
