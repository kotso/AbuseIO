<?php

use Illuminate\Database\Seeder;

class AccountsTableSeeder extends Seeder
{

    public function run()
    {
        //DB::table('accounts')->delete();

        $accounts = [
            [
                'id'            => '2',
                'name'          => 'Account 2',
                'description'   => 'Sample Account',
                'brand_id'      => 1,
            ],
            [
                'id'            => '3',
                'name'          => 'Account 3',
                'description'   => 'Sample Account',
                'brand_id'      => 1,
            ],
            [
                'id'            => '4',
                'name'          => 'Account 4',
                'description'   => 'Sample Account',
                'brand_id'      => 1,
            ],
            [
                'id'            => '5',
                'name'          => 'Account 5',
                'description'   => 'Sample Account',
                'brand_id'      => 1,
            ],
            [
                'id'            => '6',
                'name'          => 'Account 6',
                'description'   => 'Sample Account',
                'brand_id'      => 1,
            ],
            [
                'id'            => '7',
                'name'          => 'Account 7',
                'description'   => 'Sample Account',
                'brand_id'      => 1,
            ],
            [
                'id'            => '8',
                'name'          => 'Account 8',
                'description'   => 'Sample Account',
                'brand_id'      => 1,
            ],
            [
                'id'            => '9',
                'name'          => 'Account 9',
                'description'   => 'Sample Account',
                'brand_id'      => 1,
            ],
            [
                'id'            => '10',
                'name'          => 'Account 10',
                'description'   => 'Sample Account',
                'brand_id'      => 1,
            ],
            [
                'id'            => '11',
                'name'          => 'Account 11',
                'description'   => 'Sample Account',
                'brand_id'      => 1,
            ],
            [
                'id'            => '12',
                'name'          => 'Account 12',
                'description'   => 'Sample Account',
                'brand_id'      => 1,
            ],
            [
                'id'            => '13',
                'name'          => 'Account 13',
                'description'   => 'Sample Account',
                'brand_id'      => 1,
            ],
            [
                'id'            => '14',
                'name'          => 'Account 14',
                'description'   => 'Sample Account',
                'brand_id'      => 1,
            ],
            [
                'id'            => '15',
                'name'          => 'Account 15',
                'description'   => 'Sample Account',
                'brand_id'      => 1,
            ],
        ];

        DB::table('accounts')->insert($accounts);
    }
}
