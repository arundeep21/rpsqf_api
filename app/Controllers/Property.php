<?php

namespace App\Controllers;


use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\PropertyModel;
use App\Libraries\mongodb\DatabaseConnector;


class Property extends ResourceController
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
    public function show($id = null)
    {

        //echo "dsfsfsd";exit();
        $model = new PropertyModel();
        //$model->select("property_id, category_name, type, total_size, area_unit_type, locality");
        $data = $model->getWhere(['property_id' => $id])->getResult();
        if($data){
            return $this->respond($data);
        }else{
            return $this->failNotFound('No Data Found with id '.$id);
        }

    }


    public function get_home_page_prop()
    {

        $db=  \Config\Database::connect();

        $builder = $db->table('property_entries');

        $export_select="";
        $builder1 = $db->table('feature_title');
        $builder1->orderBy('id');

        $feature_titles = $builder1->get()->getResultArray();

        $export_select=", ";

        $last_key=array_key_last($feature_titles);

        $i=1;
        foreach ($feature_titles as $key => $title) {           
                    $title['title']=addslashes(trim($title['title']));
                    
            $export_select.="max(if( title_id = '".$title['id']."', option_val, 0 ))  AS '".str_replace(" ", "_",$title['title'])."|".$title['id']."'";

                    if($last_key!=$key){$export_select.=",";}

                    $i++;
                
            }

         $model = new PropertyModel();

         $builder = $db->table('vw_property_details');
         $builder->select('property_id, category_name, total_price, total_size, area_unit_type,'.$export_select);

        $builder->join('property_option_entries', 'vw_property_details.property_id=property_option_entries.property_entry_id', 'left');

      $builder->join('feature_title', 'property_option_entries.title_id=feature_title.id', 'left');

       //$data = $model->findAll();
      $builder->where('published_in_web', 1);

          $data = $builder->get()->getResult();

          print_r($data); exit();
        if($data){
            return $this->respond($data);
        }else{
            return $this->failNotFound('No Data Found');
        }

    }

    public function add_prop()
    {


         $data_json = json_decode(file_get_contents('php://input'), true);

        
     $data = [
              'property_category_id' => $data_json['category'],
              'property_description' => $data_json['description'],
              'property_purpose_id' => $data_json['purpose'],
              'state_id' => $data_json['state'],
              'district_id' => $data_json['district'],
              'city_id' => $data_json['city'],
              'locality_id' => $data_json['locality']
              // 'pin' => $data_json['pin'],
              // 'map_latitude' => $data_json['map_lat'],
              // 'map_longtitude' => $data_json['map_long'],
              // 'map_address' => $data_json['map_add'],
              // 'unit_price' => $data_json['unit_cost'],
              // 'total_price' => $data_json['total_cost'],
              // 'total_size' => $data_json['total_size'],
              // 'area_unit' => $data_json['area_unit']
            ];


        $db=  \Config\Database::connect();
        $builder=$db->table('property_entries');
        $builder->insert($data);
        echo $db->insertID();

        if($db->insertID()){
            return $this->respond(array("message"=>"Property successfully Inserted", "insert_id"=>$db->insertID()), 200);
        }else{
            return $this->failNotFound('Data could not be inserted');
        }

       

    }


    public function property($property_id)
    {

        $db=  \Config\Database::connect();

        $builder=$db->table('vw_property_details');
        $builder->where('property_id', $property_id);

        $result_property = json_encode($builder->get()->getResultArray());

        return $result_property;

       
    }


    public function  property_by_category($category)
    {
        $mongo=new DatabaseConnector();

        $db=$mongo->getDatabase();

        $collection=$db->csv_data;
        $filter = array('category' => new \MongoDB\BSON\Regex($category));

        $cursor = $collection->find($filter);   

        $return=array();
          foreach ($cursor as $document) {
            $return[]=$document;
         }

         return $this->respond($return, 200);


    }

    public function  property_by_category1()
    {
        //echo "dsfsd";
        $mongo=new DatabaseConnector();

        $db=$mongo->getDatabase();

        $collection=$db->users;
       // $filter = array('category' => new \MongoDB\BSON\Regex($category));



        $cursor = $collection->find();   
        //print_r($cursor); exit();
        $return=array();
          foreach ($cursor as $document) {
            $return[]=$document;
         }

         return $this->respond($return, 200);


    }



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


    
}
