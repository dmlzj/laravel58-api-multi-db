<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeederr extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(DB::connection('pgsql')->table('users')->get()->count() == 0){

            // $insertArray =['phone' => '123', 'first_name' => 'andi','last_name' =>'rosadi','address'=>'kopo','born_place'=>'Bandung','born_date'=>'1992-07-17','npwp'=>'123'];
            // $insertid = DB::connection('mongodb')->collection('muser')->insertGetId($insertArray);

            DB::connection('pgsql')->table('users')->insert(
                [
                    'id'=>1,
                    'username'=>'dmlzj',
                    'email' =>'284832506@qq.com',
                    'password' => Hash::make('dmlzj789'),
                    'status' => 1,
                    'mobile' => '13361567903',
                    'nickname' => '蓝蓝天空',
                    // 'role_id' => '1',
                    // '_iduser' => $insertid
                ]
            );
        }
    }
}
