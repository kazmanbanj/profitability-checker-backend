<?php

if (! function_exists('extractJsonArray')) {
    function extractJsonArray(string $output): array
    {
        $cleaned = preg_replace('/^```json\s*|\s*```$/', '', trim($output));

        return json_decode($cleaned, true) ?? [];
    }
}
