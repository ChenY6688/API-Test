<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CurrencyExchangeService;
use InvalidArgumentException;

class CurrencyExchangeController extends Controller
{
    private $currencyExchangeService;

    public function __construct(CurrencyExchangeService $currencyExchangeService)
    {
        $this->currencyExchangeService = $currencyExchangeService;
    }

    public function convert(Request $request)
    {
        $request->validate([
            'source' => ['required', 'string'],
            'target' => ['required', 'string'],
            'amount' => ['nullable', 'string'],
        ]); 

        $source = $request->input('source');
        $target = $request->input('target');
        $amount = $request->input('amount');

        try {
            $convertedAmount = $this->currencyExchangeService->convert($source, $target, $amount);
            return response()->json([
                'msg' => 'success',
                'amount' => $convertedAmount
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'msg' => $e->getMessage()
            ], 400);
        }
    }
}