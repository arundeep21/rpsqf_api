<?php

namespace App\Controllers;


use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\LoginModel;

use App\Libraries\mongodb\DatabaseConnector;

class Mongo extends BaseController
{

    // get all properties
    public function index()
    {       
        $mongo=new DatabaseConnector();  
        $db=$mongo->getDatabase();   

        $collection = $db->property;
        $cursor = $collection->find();
   // iterate cursor to display title of documents
    
   foreach ($cursor as $document) {
      p($document);
   }

    }


    
}
