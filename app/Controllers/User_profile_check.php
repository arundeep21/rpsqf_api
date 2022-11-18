<?php
namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\mongodb\DatabaseConnector;

class User_profile_check extends ResourceController{

public function check(){
// database  call
$mongo = new DatabaseConnector();  
$db = $mongo->getDatabase();  
$data = json_decode(file_get_contents('php://input'),true);
// data - insert query
$check_data = array('_id' => new \MongoDB\BSON\ObjectID($data['id']));

$table = $db->mongo_user->findOne($check_data);
if ($table->name == null) {
echo json_encode(array("message"=>null,"status"=>true));
}
else{
echo json_encode(array("message"=>"done","status"=>false));
}
}



public function fetch(){
// database  call
$mongo = new DatabaseConnector();  
$db = $mongo->getDatabase();  
$data = json_decode(file_get_contents('php://input'),true);
// data - insert query
$check_data = array('social' => [$data['provider'] => $data['email']]);
$table = $db->mongo_user->findOne($check_data);
if ($table != null) {


echo json_encode(array("status"=>"success","user_id" => $table->_id));
}
else{

$check_data = array('social' => [$data['provider'] => $data['email']]);
$table = $db->social_contacts->findOne($check_data);
if ($table != null) {

echo json_encode(array("status"=>"success","user_id" => $table->_id));

}else{

 $date = date("Y-m-d");
$new_data_insert = $db->social_contacts->insertOne([
    'name'=> $data['name'],
    'social'=> array($data['provider']=>$data['email']),
    'count'=> array($data['provider'] => 1),
    'insert_date'=>$date]);

echo json_encode(array("status"=>"success","user_id"=> $new_data_insert->getInsertedId()));
}
}
}




}
?>



