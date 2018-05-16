<?php

use Database\traits\TruncateTable;
use Database\traits\DisableForeignKeys;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LangsSeeder extends Seeder
{
    use DisableForeignKeys, TruncateTable;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys();
        $this->truncate('langs');

        $langs = [
            ['name' => 'Чеченский', 'image' => 'language', 'basic_price' => 100, 'pro_price' => 500, 'indiv_price_60' => 200, 'indiv_price_45' => 150],
            ['name' => 'Английский', 'image' => 'language', 'basic_price' => 100, 'pro_price' => 500, 'indiv_price_60' => 200, 'indiv_price_45' => 150],
            ['name' => 'Немецкий', 'image' => 'language', 'basic_price' => 100, 'pro_price' => 500, 'indiv_price_60' => 200, 'indiv_price_45' => 150],
            ['name' => 'Русский', 'image' => 'language', 'basic_price' => 100, 'pro_price' => 500, 'indiv_price_60' => 200, 'indiv_price_45' => 150]
        ];

        DB::table('langs')->insert($langs);

        $this->enableForeignKeys();
    }
}
