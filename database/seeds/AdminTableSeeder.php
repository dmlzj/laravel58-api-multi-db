<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(DB::connection('pgsql')->table('admin')->get()->count() == 0) {
            DB::connection('pgsql')->table('admin')->insert(
                [
                    'id' => 1,
                    'username' => 'sy_admin',
                    'status' => 1,
                    'password' => Hash::make('dmlzj789'),
                ]
            );
        }
    }
}
