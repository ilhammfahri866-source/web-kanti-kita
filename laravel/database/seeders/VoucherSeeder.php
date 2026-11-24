<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        $vouchers = [
            [
                'code'             => 'KANTINHEMAT',
                'name'             => 'Promo Diskon Kantin 20%',
                'discount_percent' => 20,
                'expires_at'       => now()->addMonths(3)->endOfDay(),
                'is_active'        => true,
            ],
            [
                'code'             => 'SENINHEMAT15',
                'name'             => 'Diskon Sarapan Pagi Senin 15%',
                'discount_percent' => 15,
                'expires_at'       => now()->addMonths(1)->endOfDay(),
                'is_active'        => true,
            ],
            [
                'code'             => 'JUMATBERKAH25',
                'name'             => 'Promo Berkah Makan Siang 25%',
                'discount_percent' => 25,
                'expires_at'       => now()->addMonths(2)->endOfDay(),
                'is_active'        => true,
            ],
            [
                'code'             => 'KANTINKITA10',
                'name'             => 'Diskon Pelajar KantinKita 10%',
                'discount_percent' => 10,
                'expires_at'       => now()->addMonths(4)->endOfDay(),
                'is_active'        => true,
            ],
        ];

        foreach ($vouchers as $voucher) {
            Voucher::firstOrCreate(
                ['code' => $voucher['code']],
                $voucher
            );
        }
    }
}
