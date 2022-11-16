<?php
namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\mongodb\DatabaseConnector;


class Otp_auth extends ResourceController{

  public function auth($number,$otp){

    $mongo = new DatabaseConnector();  
    $db = $mongo->getDatabase();  
    $table = $db->temp_otp->findOne(['number' => $number]);

// match otp
    if ($table->otp == $otp) {
        // check user exist number
        $test_number = array('phone' => $number);
        $test_table = $db->mongo_user->findOne($test_number);
        // check user table
        if ($test_table === null) {
          $date = date("Y-m-d");
          $new_data_insert = $db->mongo_user->insertOne([
              'name'=> null,
              'phone' => $number,
              'tem_phone' => ['ph1'=> null,'ph2'=>null],

              'social'=>['facebook'=>null,'google'=>null],

              'insert_date'=>$date

          ]);
         echo json_encode(array("status"=>"success","user_id"=> $new_data_insert->getInsertedId().'$oid'));

     
        }else{
          
        echo json_encode(array("status"=>"success","user_id"=> $test_table->_id.$oid));
        }

// end     
    }else{
      echo  json_encode(array("status"=>"fail"));
    }

}


}

?>



