<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // DB::table('order_status')->insert([
        //     'title_ar'=>'جارى المراجعه',
        //     'title_en'=>'Reviewing in progress',
        //     'persage'=>20,
        //     'backgroud_color'=>'#007DFC'
        // ]);
        // DB::table('order_status')->insert([
        //     'title_ar'=>'تمت الموافقة علي الطلب',
        //     'title_en'=>'The request has been approved',
        //     'persage'=>100,
        //     'backgroud_color'=>'#2ECC71'
        // ]);
        // DB::table('order_status')->insert([
        //     'title_ar'=>'تم رفض  الطلب',
        //     'title_en'=>'reques has been rejected',
        //     'persage'=>0,
        //     'backgroud_color'=>'#EF0808'
        // ]);
        // DB::table('currancies')->insert([
        //     'name'=>'dollar',
        //    'symble'=>'$'
        // ]);

        DB::table('stocks')->insert([
                'name'=>'Gold 1 KM',
                'symble'=>'A7A7',
                'measure'=>0,
                'feas_buy'=>10,
                'feas_seller'=>20,
                'min_deliverd'=>2,
                'feas_deliverd_for_one'=>1,

            ]);

            DB::table('transactions_messagas')->insert([
                'title_ar'=>'تم ايداع',
                'title_en'=>''

            ]);

            DB::table('transactions_messagas')->insert([
                'title_ar'=>'',
                'title_en'=>''

            ]);


    }
}
