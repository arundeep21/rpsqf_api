<?php

namespace App\Controllers;


use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\PropertyModel;
use App\Libraries\mongodb\DatabaseConnector;
class Property extends ResourceController
{


    public function index(){

        $query = $_GET['keywords'];
        echo $query;

    }


}