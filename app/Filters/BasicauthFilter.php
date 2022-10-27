<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
//use App\Models\PropertyModel;

class BasicauthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {

        //echo "filter";
       //print_r($request); exit();
       // print_r($_SERVER);


         //header("Content-Type:application/json");
       //$rawData=file_get_contents('php://input');
       // $data_json = json_decode(file_get_contents('php://input'), true);
       //  print_r($data_json);
        // exit();


        if(isset($_SERVER['HTTP_X_API_KEY'])) $request_api_key=$_SERVER['HTTP_X_API_KEY'];
        else{
            echo json_encode(array(
                "status" => false,
                "message" => "No API KEY Sent"
            ));
            die;
        }

        $host_name=$_SERVER['HTTP_HOST'];

        $db      = \Config\Database::connect('api_db');
        $builder=$db->table("api_keys");
        $builder->where('api_key', $request_api_key);
       // $builder->where('host_name', $host_name);
        $key_found=$builder->get()->getResult();
       //print_r($key_found);

        //$username = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : "";
        //$password = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : "";


        if(count($key_found)==0){
          
            header("Content-type: application/json");
          
            echo json_encode(array(
                "status" => false,
                "message" => "Invalid credentials"
            ));
            die;
        }



    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
       
    }
}
