<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            ['name' => 'Kas Tunai', 'type' => 'kas', 'saldo_awal' => 2000000],
            ['name' => 'Kas Bank', 'type' => 'kas', 'saldo_awal' => 5000000],
            ['name' => 'Peralatan Dapur & Kasir', 'type' => 'aset_tetap', 'saldo_awal' => 15000000],
            ['name' => 'Furnitur & Interior', 'type' => 'aset_tetap', 'saldo_awal' => 10000000],
            ['name' => 'Hutang Supplier', 'type' => 'kewajiban', 'saldo_awal' => 3000000],
            ['name' => 'Modal Pemilik', 'type' => 'modal', 'saldo_awal' => 29000000],
        ];

        foreach ($accounts as $account) {
            Account::firstOrCreate(['name' => $account['name']], $account);
        }
    }
}
