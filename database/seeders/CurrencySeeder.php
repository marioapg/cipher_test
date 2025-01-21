<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            ['name' => 'US Dollar', 'symbol' => '$', 'exchange_rate' => 1.0],
            ['name' => 'Euro', 'symbol' => '€', 'exchange_rate' => 0.85],
            ['name' => 'British Pound', 'symbol' => '£', 'exchange_rate' => 0.75],
            ['name' => 'Canadian Dollar', 'symbol' => 'C$', 'exchange_rate' => 1.25],
            ['name' => 'Australian Dollar', 'symbol' => 'A$', 'exchange_rate' => 1.35],
            ['name' => 'Japanese Yen', 'symbol' => '¥', 'exchange_rate' => 110.50],
            ['name' => 'Swiss Franc', 'symbol' => 'CHF', 'exchange_rate' => 0.92],
            ['name' => 'Chinese Yuan', 'symbol' => '¥', 'exchange_rate' => 6.45],
            ['name' => 'Indian Rupee', 'symbol' => '₹', 'exchange_rate' => 75.10],
            ['name' => 'Brazilian Real', 'symbol' => 'R$', 'exchange_rate' => 5.35],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}
