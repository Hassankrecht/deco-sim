<?php

namespace App\Support;

final class Currency
{
    public static function format($amount): string
    {
        $amount = $amount ?? 0;
        $amount = (float) $amount;

        $decimals = config('currency.decimals', 2);
        $decimalSeparator = config('currency.decimal_separator', ',');
        $thousandsSeparator = config('currency.thousands_separator', '.');
        $symbol = config('currency.symbol', 'Kz');
        $symbolPosition = config('currency.symbol_position', 'after');
        $spaceBetween = config('currency.space_between', true);

        $formatted = number_format($amount, $decimals, $decimalSeparator, $thousandsSeparator);

        if ($symbolPosition === 'after') {
            $space = $spaceBetween ? ' ' : '';
            return $formatted . $space . $symbol;
        }

        $space = $spaceBetween ? ' ' : '';
        return $symbol . $space . $formatted;
    }

    public static function code(): string
    {
        return config('currency.code', 'AOA');
    }
}
