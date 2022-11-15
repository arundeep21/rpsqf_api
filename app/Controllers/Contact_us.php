<?php
namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\mongodb\DatabaseConnector;

class Contact_us extends ResourceController{

  public function insert_data(){
    // database  call
    $mongo = new DatabaseConnector();  
    $db = $mongo->getDatabase();  
  $data = json_decode(file_get_contents('php://input'),true);
// data - insert query
$date = date("Y-m-d");
$new_data_insert = $db->contact_us_enquiries->insertOne([
'name'=> $data['name'],
'email' => $data['email'],
'subject' => $data['subject'],
'message'=> $data['message'],
'entry_date'=> $date]);

if($new_data_insert){
 $subject = $data['subject'];
 $from = 'team@ratepersqft.com';
 $headers  = 'MIME-Version: 1.0' . "\r\n";
 $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
 $headers .= 'From: '.$from."\r\n".
     'Reply-To: '.$from."\r\n" .
     'X-Mailer: PHP/' . phpversion();
$message = $data['message'];
mail($data['email'], $subject, $message, $headers);
echo json_encode(array("message"=>"successful","status"=>true));
}
else{
   echo json_encode(array("message"=>"failed","status"=>false));
}
}



}


?>



