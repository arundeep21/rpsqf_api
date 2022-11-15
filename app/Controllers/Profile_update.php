<?php
namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\mongodb\DatabaseConnector;

class Profile_update extends ResourceController{

public function put(){

//database  call
$mongo = new DatabaseConnector();  
$db = $mongo->getDatabase();  
$data = json_decode(file_get_contents('php://input'),true);
//data - insert query


$table = $db->mongo_user->updateOne(['_id' => new \MongoDB\BSON\ObjectID($data['id'])],['$set' => ['name'=>$data['name']]]);


if ($table){
   echo json_encode(array('message'=>'fulfilled','status'=>true));
}else{
   echo json_encode(array('message'=>'fail','status'=>false));
}

}




public function fetch(){

//database  call
$mongo = new DatabaseConnector();  
$db = $mongo->getDatabase();  
$data = json_decode(file_get_contents('php://input'),true);
//data - insert query


$table = $db->mongo_user->findOne(['_id' => new \MongoDB\BSON\ObjectID($data['id'])]);


if ($table->name != null){
   echo json_encode(array('message'=>$table->name,'status'=>true));
}else{
   echo json_encode(array('message'=>'null','status'=>false));
}

}

}
?>



