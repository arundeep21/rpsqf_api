<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {

        echo "Home";
        return view('welcome_message');
    }
    public function test()
    {
        return view('test');
    }
}
