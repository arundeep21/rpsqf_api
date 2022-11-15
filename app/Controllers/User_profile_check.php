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
if ($table->name != null) {
echo json_encode(array("message"=>"fulfiled","status"=>true));
}
else{
echo json_encode(array("message"=>"failed","status"=>false));
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


echo json_encode(array("status"=>"success","user_id" => $table->_id.$oid));
}
else{

$check_data = array('social' => [$data['provider'] => $data['email']]);
$table = $db->social_contacts->findOne($check_data);
if ($table != null) {
// update count

// if ($data['provider'] == 'facebook') {
// 	$check_provider = 'google';
// }else{
// 	$check_provider = 'facebook';
// }

/*$db->social_contacts->updateOne(['social' => [$data['provider'] => $data['email']]],['$set' => ['count' => [$data['provider'] => $table->count[$data['provider']]+1],[$check_provider => $table->count[$check_provider]]]]);*/

echo json_encode(array("status"=>"success","user_id" => $table->_id.$oid));

}else{

 $date = date("Y-m-d");
          $new_data_insert = $db->social_contacts->insertOne([
              'name'=> $data['name'],
              'social'=> array($data['provider']=>$data['email']),
              'count'=> array($data['provider'] => 1),
              'insert_date'=>$date
]);

echo json_encode(array("status"=>"success","user_id"=> $new_data_insert->getInsertedId().'$oid'));
}
}
}




}
?>



