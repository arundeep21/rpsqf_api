<?php

namespace App\Controllers;


use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\PropertyModel;
use App\Libraries\mongodb\DatabaseConnector;

class Home extends BaseController
{

     use ResponseTrait;
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
