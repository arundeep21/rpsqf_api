<?php
namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\mongodb\DatabaseConnector;


class Contact_us extends ResourceController{

  public function insert_data(){

  $data = json_decode(file_get_contents("php://input"),true);

  

  print_r($data);
    

}


}

?>



