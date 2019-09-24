<?php
namespace App\Http\Controllers\Api\v1;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TestController extends controller{
    public function test() {
        dd('test');
    }
}
