<?php

namespace App\Controllers;


use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\LoginModel;

class Login extends ResourceController
{

    use ResponseTrait;

    // get all properties
    public function index()
    {
        //echo "Login";

        $data_json = json_decode(file_get_contents('php://input'), true);
       //    print_r($data_json); exit();    
        $model = new LoginModel();        
        $model->select('encrypted');    


        $data = $model->getWhere(['email ' => $data_json['user_email']])->getResult();//print_r($data);
        if(count($data)>0)
        {
            if(password_verify($data_json['user_pass'], $data[0]->encrypted))
                return $this->respond("User found", 200); 
            else
                {
                     return $this->failNotFound('Email or Password not found', 200);
                }   
        }

       else {
            return $this->failNotFound('EMAIL or Password not found'.$data_json['user_email']);
        } 
    }


    
}
