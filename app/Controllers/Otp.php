<?php
namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\mongodb\DatabaseConnector;


class Otp extends ResourceController{

  public function generate_otp($number){
    $otp = random_int(100000,999999);


    $curl= curl_init();
    $campaign_name="testing"; //My First Campaign
    $authKey="7113955c8c36af44200175c9f0fce150";  //Valid Authentication Key
    $mobileNumber= $number; //Receivers 
    $sender="KDMPRP"; //Sender Approved from Dlt 
    $message="Your OTP for login is ". $otp .". KDM Proptech Pvt. Ltd.";  //Content Approved from Dlt
    $route="TR";  //TR for tranactional,PR for promotional 
    $template_id= "1507164499063060118"; //Template Id Approved from Dlt 
    $scheduleTime=""; //if required fill parameter in given formate 07-05-2022 12:00:00 dd-mm-yyyy hh:mm:ss 
    $coding="1"; //If english $coding = "1" otherwise if required other language $coding = "2" 
    $postData = array(
    "campaign_name" => $campaign_name, 
    "auth_key" => $authKey, 
    "receivers"  => $mobileNumber, 
    "sender"  => $sender, 
    "route"  => $route, 
    "message" => ['msgdata' => $message,'Template_ID' => $template_id,'coding' => $coding], 
    "scheduleTime" => $scheduleTime, 
    );
    curl_setopt_array($curl, array(
    CURLOPT_URL  => 'http://neat.freebeesms.com/api/send/sms',
    CURLOPT_RETURNTRANSFER  => true,
    CURLOPT_ENCODING  => '',
    CURLOPT_MAXREDIRS  => 10,
    CURLOPT_TIMEOUT  => 0,
    CURLOPT_FOLLOWLOCATION  => true,
    CURLOPT_HTTP_VERSION  => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST  => 'POST',
    CURLOPT_POSTFIELDS  => json_encode($postData),
    CURLOPT_HTTPHEADER  => array(
      'Content-Type: application/json'
    ),
    ));







    $response = curl_exec($curl);
    curl_close($curl); 


  //  insret data 
    $mongo = new DatabaseConnector();  
    $db = $mongo->getDatabase();  

    $var = array('number' => $number);
    $table = $db->temp_otp->findOne($var);

    if ($table != null) {
    
      $table = $db->temp_otp->updateOne(['number'=>$number],['$set' => ['otp' => $otp]]);
    }else{
      $table = $db->temp_otp->insertOne(['number' => $number, 'otp' =>$otp]);
    }

    echo json_encode($response);
}


}



   ?>



