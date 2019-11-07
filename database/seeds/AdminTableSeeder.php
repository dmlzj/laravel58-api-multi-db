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
        if(DB::connection('pgsql')->table('admins')->get()->count() == 0) {
            DB::connection('pgsql')->table('admins')->insert(
                [
                    'id' => 1,
                    'username' => 'sy_admin',
                    'avatar' => 'https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif',
                    'status' => 1,
                    'password' => Hash::make('dmlzj789'),
                ]
            );
        }
    }
}
