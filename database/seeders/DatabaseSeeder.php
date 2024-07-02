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
        DB::table('peroid_globels')->insert([
            'title_ar' => "شهر",
            'title_en' => "month",
            'num_months' => 1
        ]);

        DB::table('peroid_globels')->insert([
            'title_ar' => "شهرين",
            'title_en' => "two months",
            'num_months' => 2
        ]);

        DB::table('peroid_globels')->insert([
            'title_ar' => "ثلاثة أشهر",
            'title_en' => "three months",
            'num_months' => 3
        ]);

        DB::table('peroid_globels')->insert([
            'title_ar' => "أربعة أشهر",
            'title_en' => "four months",
            'num_months' => 4
        ]);

        DB::table('peroid_globels')->insert([
            'title_ar' => "خمسة أشهر",
            'title_en' => "five months",
            'num_months' => 5
        ]);

        DB::table('peroid_globels')->insert([
            'title_ar' => "ستة أشهر",
            'title_en' => "six months",
            'num_months' => 6
        ]);

        DB::table('peroid_globels')->insert([
            'title_ar' => "سبعة أشهر",
            'title_en' => "seven months",
            'num_months' => 7
        ]);

        DB::table('peroid_globels')->insert([
            'title_ar' => "ثمانية أشهر",
            'title_en' => "eight months",
            'num_months' => 8
        ]);

        DB::table('peroid_globels')->insert([
            'title_ar' => "تسعة أشهر",
            'title_en' => "nine months",
            'num_months' => 9
        ]);

        DB::table('peroid_globels')->insert([
            'title_ar' => "عشرة أشهر",
            'title_en' => "ten months",
            'num_months' => 10
        ]);

        DB::table('peroid_globels')->insert([
            'title_ar' => "أحد عشر شهرًا",
            'title_en' => "eleven months",
            'num_months' => 11
        ]);

        DB::table('peroid_globels')->insert([
            'title_ar' => "اثنا عشر شهرًا",
            'title_en' => "twelve months",
            'num_months' => 12
        ]);

        $periods = [
            1 => 20,
            2 => 15,
            3 => 18,
            4 => 22,
            5 => 25,
            6 => 17,
            7 => 21,
            8 => 19,
            9 => 23,
            10 => 16,
            11 => 24,
            12 => 26,
        ];

        foreach ($periods as $period_globle_id => $percent) {
            DB::table('interest_calcs')->insert([
                'period_globle_id' => $period_globle_id,
                'percent' => $percent,
            ]);
        }

        DB::table('program_types')->insert([
            'title_ar' => "تمويل شخصي",
            'title_en' => "personal financing",
        ]);

        DB::table('programs')->insert([
            'value' => 5000,
            'description_ar' => "هذا النص وصف للبرنامج التمويلي هذا النص وصف النص هذا للبرنامج التمويلي هذا النص وصف للبرنامج التمويلي هذا ; النص وصف للبرنامج التمويلي هذا النص وصف للبرنامج التمويلي هذا النص وصف للبرنامج التمويلي هذا النص هذا وصف للبرنامج ; التمويلي هذا النص وصف للبرنامج التمويلي ",
            'description_en' => "This text is a description of the financing program. This text is a description of this financing program. This text is a description of this financing program; The text is a description of the financing program. This text is a description of the financing program. This text is a description of the financing program. This text is a description of the program. Financing This text is a description of the financing program",
            'program_type_id'=>1,
            'calender_ar'=>"من شهر الي 6 اشهر",
            'calender_en'=>"From one month to 6 months",
            'interest_ar'=>'%0 بدون فوائد',
            'interest_en'=>'0% interest free',

        ]);

        foreach ($periods as $period_globle_id => $percent) {
            DB::table('program_periods')->insert([
                'period_globel_id' =>$period_globle_id,
                'program_id' => 1,
                'percent'=>$percent
            ]);
        }


        DB::table('fields')->insert([
            'title_ar' => "صورة الهوية الشخصية",
            'title_en' => "Photo of personal ID",
        ]);

        DB::table('fields')->insert([
            'title_ar' => "كشف حساب بنكى",
            'title_en' => "Bank statement",
        ]);

        DB::table('program_fields')->insert([
            'program_id' => 1,
            'field_id' => 1,
        ]);
        DB::table('program_fields')->insert([
            'program_id' => 1,
            'field_id' => 2,
        ]);

        DB::table('contract_programs')->insert([
            'program_id' => 1,
            'contract' => "هذا النص وصف للبرنامج التمويلي هذا النص وصف النص هذا للبرنامج التمويلي هذا النص وصف للبرنامج التمويلي هذا ; النص وصف للبرنامج التمويلي هذا النص وصف للبرنامج التمويلي هذا النص وصف للبرنامج التمويلي هذا النص هذا وصف للبرنامج ; التمويلي هذا النص وصف للبرنامج التمويلي  هذا النص وصف للبرنامج التمويلي  هذا النص وصف للبرنامج التمويلي  هذا النص وصف للبرنامج التمويلي  هذا النص وصف للبرنامج التمويلي  هذا النص وصف للبرنامج التمويلي  هذا النص وصف للبرنامج التمويلي  هذا النص وصف للبرنامج التمويلي  هذا النص وصف للبرنامج التمويلي هذا النص وصف النص هذا للبرنامج التمويلي هذا النص وصف للبرنامج التمويلي هذا ; النص وصف للبرنامج التمويلي هذا النص وصف للبرنامج التمويلي هذا النص وصف للبرنامج التمويلي هذا النص هذا وصف للبرنامج ; التمويلي هذا النص وصف للبرنامج التمويلي  هذا النص وصف للبرنامج التمويلي  هذا النص وصف للبرنامج التمويلي  هذا النص وصف للبرنامج التمويلي  هذا النص وصف للبرنامج التمويلي  هذا النص وصف للبرنامج التمويلي  هذا النص وصف للبرنامج التمويلي  هذا النص وصف للبرنامج التمويلي هذا النص وصف للبرنامج التمويلي هذا النص وصف النص هذا للبرنامج التمويلي هذا النص وصف للبرنامج التمويلي هذا ; النص وصف للبرنامج التمويلي هذا النص وصف للبرنامج التمويلي هذا النص وصف للبرنامج التمويلي هذا النص هذا وصف للبرنامج ; التمويلي هذا النص وصف للبرنامج التمويلي  هذا النص وصف للبرنامج التمويلي  هذا النص وصف للبرنامج التمويلي  هذا النص وصف للبرنامج التمويلي  هذا النص وصف للبرنامج التمويلي  هذا النص وصف للبرنامج التمويلي  هذا النص وصف للبرنامج التمويلي  هذا النص وصف للبرنامج التمويلي ",
            'title_ar'=>'العقد 1',
            'title_en'=>'Contract 1'

        ]);


        DB::table('banks')->insert([
            'title_ar'=>'بنك عوده',
            'title_en'=>'Bank Audi'
        ]);
        DB::table('banks')->insert([
            'title_ar'=>'بنك مصر',
            'title_en'=>'Egypt Bank'
        ]);

        DB::table('order_status')->insert([
            'title_ar'=>'جارى المراجعه',
            'title_en'=>'Reviewing in progress',
            'persage'=>20,
            'backgroud_color'=>'#007DFC'
        ]);
        DB::table('order_status')->insert([
            'title_ar'=>'تمت الموافقة علي الطلب',
            'title_en'=>'The request has been approved',
            'persage'=>100,
            'backgroud_color'=>'#2ECC71'
        ]);
        DB::table('order_status')->insert([
            'title_ar'=>'تم رفض  الطلب',
            'title_en'=>'reques has been rejected',
            'persage'=>0,
            'backgroud_color'=>'#EF0808'
        ]);

    }
}
