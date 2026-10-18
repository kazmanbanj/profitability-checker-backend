<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Company Name
    |--------------------------------------------------------------------------
    |
    | The trading name used on exported documents such as PDF quote summaries.
    | Override via the COMPANY_NAME environment variable.
    |
    */

    'company_name' => env('COMPANY_NAME', 'My Company'),

    /*
    |--------------------------------------------------------------------------
    | Currency Symbol
    |--------------------------------------------------------------------------
    |
    | The currency symbol displayed in profitability calculations and reports.
    | Override via the CURRENCY_SYMBOL environment variable.
    |
    */

    'currency_symbol' => env('CURRENCY_SYMBOL', '$'),

    /*
    |--------------------------------------------------------------------------
    | AI Endpoint Rate Limits
    |--------------------------------------------------------------------------
    |
    | Maximum requests per minute allowed per IP for AI-backed endpoints.
    | Override via the environment variables below.
    |
    */

    'rate_limit_analyze'    => (int) env('RATE_LIMIT_ANALYZE', 10),
    'rate_limit_re_analyze' => (int) env('RATE_LIMIT_RE_ANALYZE', 10),
];
