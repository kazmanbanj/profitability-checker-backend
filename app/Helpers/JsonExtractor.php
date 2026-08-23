<?php

namespace App\Helpers;

class JsonExtractor
{
    public static function extract(string $output): array
    {
        $cleaned = preg_replace('/^```json\s*|\s*```$/', '', trim($output));

        return json_decode($cleaned, true) ?? [];
    }
}
