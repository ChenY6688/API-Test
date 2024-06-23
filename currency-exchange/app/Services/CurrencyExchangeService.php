<?php

namespace App\Services;

use InvalidArgumentException;
use App\Services\Interfaces\ICurrencyExchangeService;

class CurrencyExchangeService implements ICurrencyExchangeService
{
    private $rates;

    public function __construct()
    {
        $this->rates = config('constants.currency_rates');;
    }

    public function convert(string $source, string $target, $amount): string
    {
        $errors = [];

        if (!isset($this->rates[$source])) {
            $errors[] = "Source currency not supported.";
        }

        if (!isset($this->rates[$target])) {
            $errors[] = "Target currency not supported.";
        }

        $amount = str_replace(',', '', $amount);
        if (!is_numeric($amount) || $amount <= 0) {
            $errors[] = "Amount must be a positive number.";
        }

        //假定最高限額是1億
        if (is_numeric($amount) && $amount > 100000000) {
            $errors[] = "Amount exceeds maximum limit.";
        }

        try {
            if (!empty($errors)) {
                throw new InvalidArgumentException(implode(' ', $errors));
            }

            $rate = $this->rates[$source][$target];
            $convertedAmount = round(round((float) $amount, 2) * $rate, 2);

            return number_format($convertedAmount, 2, '.', ',');

        } catch (Exception $e) {
            throw new Exception("An error occurred while converting currency.");
        }
    }
}