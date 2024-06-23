<?php

namespace App\Services\Interfaces;

interface ICurrencyExchangeService 
{
    public function convert(string $source, string $target,  $amount): string;
}