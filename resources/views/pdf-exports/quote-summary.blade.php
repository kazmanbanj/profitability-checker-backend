@extends('layouts.app')

@section('content')
<style>
    .container {
        width: 90%;
        max-width: 900px;
        margin: 40px auto 60px auto;
        padding: 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    h2, h4 {
        color: #343a40;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        background-color: white;
        margin-bottom: 1rem;
    }
    th, td {
        border: 1px solid #d3d3d3;
        padding: 10px 12px;
        text-align: left;
    }
    thead th {
        background: #222;
        color: #fff;
        font-weight: bold;
    }
    tfoot td {
        background: #f0f0f0;
        font-weight: bold;
    }
    table th, table td {
        border: 1px solid #dee2e6;
        padding: 0.75rem;
        text-align: left;
    }
    table th {
        background-color: #f1f3f5;
        color: #343a40;
    }
    .mb-4 { margin-bottom: 24px; }
    .mt-5 { margin-top: 40px; }
    .text-danger { color: #d32f2f; }
    .text-success { color: #388e3c; }
    .fw-bold { font-weight: bold; }
    .table-sm th, .table-sm td { padding: 7px 10px; }
</style>
<div class="container">
    <h2 class="mb-4">Quote Summary</h2>

    <h4>Quote Overview</h4>
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Cost Price</th>
                <th>Sell Price</th>
                <th>Quantity</th>
                <th>Total Cost</th>
                <th>Total Revenue</th>
                <th>Margin %</th>
                <th>Low Margin</th>
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
                    <td>{{ $item['margin_percent'] }}%</td>
                    <td>
                        @if($item['is_low_margin'])
                            <span class="text-danger fw-bold">Yes</span>
                        @else
                            No
                        @endif
                    </td>
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
                <th>Meets Target</th>
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
                <th>Health Status</th>
                <td></td>
                <td>
                    @if ($analysis['health_status'] === 'green')
                        <span class="text-success">Good</span>
                    @elseif ($analysis['health_status'] === 'amber')
                        <span style="color: #FFBF00;">Needs Review</span>
                    @elseif ($analysis['health_status'] === 'red')
                        <span class="text-danger">Poor</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
