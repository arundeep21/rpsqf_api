<?php

namespace App\Controllers;


use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\PropertyModel;
use App\Libraries\mongodb\DatabaseConnector;


class Website extends ResourceController
{

    use ResponseTrait;

    // get all properties
    public function index()
    {
        $model = new PropertyModel();
        $data = $model->findAll(10, 0);
        return $this->respond($data, 200);
    }


//get single Property
    
public function our_brands()
{
    
    $mongo=new DatabaseConnector();

    $db=$mongo->getDatabase();
    // $builder = $db->our_brands;
    // $builder->find();
    // print_r($builder);
    // exit(); 
    $collection=$db->our_brands;
    // $filter = array('id'=> '1' );

    $filter = [];
    // $query = new \MongoDB\Driver\Query($filter);

    $cursor = $collection->find($filter);   

    $return=array();
      foreach ($cursor as $document) {
        $return[]=$document;
     }

     return $this->respond($return, 200);
    // print_r($cursor);
    
}

public function service_list()
{
        $mongo=new DatabaseConnector();
        $db=$mongo->getDatabase();

        $collection=$db->our_services;
       // $filter = array('category' => new \MongoDB\BSON\Regex($category));

        $cursor = $collection->find();   

        $return=array();
          foreach ($cursor as $document) {
            $return[]=$document;
         }
         return $this->respond($return, 200);
    }    


public function third_party_service_list()
{
        $mongo=new DatabaseConnector();
        $db=$mongo->getDatabase();

        $collection=$db->thirdparty_services;
        //$filter = array('category' => new \MongoDB\BSON\Regex($category));

        $cursor = $collection->find();   

        $return=array();
          foreach ($cursor as $document) {
            $return[]=$document;
         }
         return $this->respond($return, 200);
    }   

    public function choose_us()
{
        $mongo=new DatabaseConnector();
        $db=$mongo->getDatabase();

        $collection=$db->whychoose_us;
        //$filter = array('category' => new \MongoDB\BSON\Regex($category));

        $cursor = $collection->find();   

        $return=array();
          foreach ($cursor as $document) {
            $return[]=$document;
         }
         return $this->respond($return, 200);
    }  
}