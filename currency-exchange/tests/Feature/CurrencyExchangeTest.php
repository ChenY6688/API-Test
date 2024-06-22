<?php

namespace Tests\Feature;

use Tests\TestCase;

class CurrencyExchangeTest extends TestCase
{
    //無千分位
    public function test_valid_conversion()
    {
        $response = $this->get('/convert?source=USD&target=JPY&amount=1525');

        $response->assertStatus(200);
        $response->assertJson([
            'msg' => 'success',
            'amount' => '170,496.53'
        ]);
    }

    //有千分位
    public function test_valid_conversion_with_thousands_separator()
    {
        $response = $this->get('/convert?source=USD&target=JPY&amount=1,525');

        $response->assertStatus(200);
        $response->assertJson([
            'msg' => 'success',
            'amount' => '170,496.53'
        ]);
    }

    //驗證來源貨幣
    public function test_invalid_source_currency()
    {
        $response = $this->get('/convert?source=XYZ&target=JPY&amount=1525');

        $response->assertStatus(400);
        $response->assertJson([
            'msg' => 'Source currency not supported.'
        ]);
    }

    //驗證目標貨幣
    public function test_invalid_target_currency()
    {
        $response = $this->get('/convert?source=USD&target=XYZ&amount=1525');

        $response->assertStatus(400);
        $response->assertJson([
            'msg' => 'Target currency not supported.'
        ]);
    }

    //同時驗證來源貨幣和目標貨幣
    public function test_invalid_both_currency()
    {
        $response = $this->get('/convert?source=XYZ&target=XYZ&amount=1525');

        $response->assertStatus(400);
        $response->assertJson([
            'msg' => 'Source currency not supported. Target currency not supported.'
        ]);
    }

    //金額未輸入
    public function test_invalid_empty_amount()
    {
        $response = $this->get('/convert?source=USD&target=JPY&amount=');

        $response->assertStatus(400);
        $response->assertJson([
            'msg' => 'Amount must be a positive number.'
        ]);
    }

    //驗證金額
    public function test_invalid_amount1()
    {
        $response = $this->get('/convert?source=USD&target=JPY&amount=invalid');

        $response->assertStatus(400);
        $response->assertJson([
            'msg' => 'Amount must be a positive number.'
        ]);
    }

    //驗證負數
    public function test_invalid_amount2()
    {
        $response = $this->get('/convert?source=USD&target=JPY&amount=-20000');

        $response->assertStatus(400);
        $response->assertJson([
            'msg' => 'Amount must be a positive number.'
        ]);
    }

    //驗證超過特定金額(假定最高限額是1億)
    public function test_invalid_amount3()
    {
        $response = $this->get('/convert?source=USD&target=JPY&amount=123456789');

        $response->assertStatus(400);
        $response->assertJson([
            'msg' => 'Amount exceeds maximum limit.'
        ]);
    }

    //同時驗證來源貨幣和目標貨幣和金額
    public function test_invalid_both_currency_and_amount1()
    {
        $response = $this->get('/convert?source=XYZ&target=XYZ&amount=%&(#');

        $response->assertStatus(400);
        $response->assertJson([
            'msg' => 'Source currency not supported. Target currency not supported. Amount must be a positive number.'
        ]);
    }

    //同時驗證來源貨幣和目標貨幣和超過特定金額
    public function test_invalid_both_currency_and_amount2()
    {
        $response = $this->get('/convert?source=XYZ&target=XYZ&amount=123456789');

        $response->assertStatus(400);
        $response->assertJson([
            'msg' => 'Source currency not supported. Target currency not supported. Amount exceeds maximum limit.'
        ]);
    }

    //輸入後測試(四捨):138025.04256
    public function test_rounding_and_formatting1()
    {
        $response = $this->get('/convert?source=USD&target=JPY&amount=1234.564');

        $response->assertStatus(200);
        $response->assertJson([
            'msg' => 'success',
            'amount' => '138,025.04'
        ]);
    }

    //輸入後測試(五入):138026.16057
    public function test_rounding_and_formatting2()
    {
        $response = $this->get('/convert?source=USD&target=JPY&amount=1234.567');

        $response->assertStatus(200);
        $response->assertJson([
            'msg' => 'success',
            'amount' => '138,026.16'
        ]);
    }

    //轉換後測試(四捨):138018.3345
    public function test_rounding_and_formatting3()
    {
        $response = $this->get('/convert?source=USD&target=JPY&amount=1234.5');

        $response->assertStatus(200);
        $response->assertJson([
            'msg' => 'success',
            'amount' => '138,018.33'
        ]);
    }

    //轉換後測試(五入):138186.036
    public function test_rounding_and_formatting4()
    {
        $response = $this->get('/convert?source=USD&target=JPY&amount=1236');

        $response->assertStatus(200);
        $response->assertJson([
            'msg' => 'success',
            'amount' => '138,186.04'
        ]);
    }

    //無小數測試:1380259146.078
    public function test_rounding_and_formatting_no_decimals()
    {
        $response = $this->get('/convert?source=USD&target=JPY&amount=12345678');

        $response->assertStatus(200);
        $response->assertJson([
            'msg' => 'success',
            'amount' => '1,380,259,146.08'
        ]);
    }
}