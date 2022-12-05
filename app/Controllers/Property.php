<?php

namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
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
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }
    public function test()
    {
        echo "hii";
    }

    public function get_home_page_prop()
    {

        $db =  \Config\Database::connect();

        $builder = $db->table('property_entries');

        $export_select = "";
        $builder1 = $db->table('feature_title');
        $builder1->orderBy('id');

        $feature_titles = $builder1->get()->getResultArray();

        $export_select = ", ";

        $last_key = array_key_last($feature_titles);

        $i = 1;
        foreach ($feature_titles as $key => $title) {
            $title['title'] = addslashes(trim($title['title']));

            $export_select .= "max(if( title_id = '" . $title['id'] . "', option_val, 0 ))  AS '" . str_replace(" ", "_", $title['title']) . "|" . $title['id'] . "'";

            if ($last_key != $key) {
                $export_select .= ",";
            }

            $i++;
        }

        $model = new PropertyModel();

        $builder = $db->table('vw_property_details');
        $builder->select('property_id, category_name, total_price, total_size, area_unit_type,' . $export_select);

        $builder->join('property_option_entries', 'vw_property_details.property_id=property_option_entries.property_entry_id', 'left');

        $builder->join('feature_title', 'property_option_entries.title_id=feature_title.id', 'left');

        //$data = $model->findAll();
        $builder->where('published_in_web', 1);

        $data = $builder->get()->getResult();

        print_r($data);
        exit();
        if ($data) {
            return $this->respond($data);
        } else {
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


        $db =  \Config\Database::connect();
        $builder = $db->table('property_entries');
        $builder->insert($data);
        echo $db->insertID();

        if ($db->insertID()) {
            return $this->respond(array("message" => "Property successfully Inserted", "insert_id" => $db->insertID()), 200);
        } else {
            return $this->failNotFound('Data could not be inserted');
        }
    }


    public function property($property_id)
    {

        $db =  \Config\Database::connect();

        $builder = $db->table('vw_property_details');
        $builder->where('property_id', $property_id);

        $result_property = json_encode($builder->get()->getResultArray());

        return $result_property;
    }


    public function  property_by_category($category)
    {
        $mongo = new DatabaseConnector();

        $db = $mongo->getDatabase();

        $collection = $db->property;
        $filter = array('category' => new \MongoDB\BSON\Regex($category));

        $cursor = $collection->find($filter);

        $return = array();
        foreach ($cursor as $document) {
            $return[] = $document;
        }

        return $this->respond($return, 200);
    }

    public function  property_by_category1()
    {
        //echo "dsfsd";
        $mongo = new DatabaseConnector();

        $db = $mongo->getDatabase();

        $collection = $db->users;
        // $filter = array('category' => new \MongoDB\BSON\Regex($category));



        $cursor = $collection->find();
        //print_r($cursor); exit();
        $return = array();
        foreach ($cursor as $document) {
            $return[] = $document;
        }

        return $this->respond($return, 200);
    }



    public function our_brands()
    {

        $mongo = new DatabaseConnector();

        $db = $mongo->getDatabase();
        // $builder = $db->our_brands;
        // $builder->find();
        // print_r($builder);
        // exit(); 
        $collection = $db->our_brands;
        // $filter = array('id'=> '1' );

        $filter = [];
        // $query = new \MongoDB\Driver\Query($filter);

        $cursor = $collection->find($filter);

        $return = array();
        foreach ($cursor as $document) {
            $return[] = $document;
        }

        return $this->respond($return, 200);
        // print_r($cursor);
    }


    public function search_query()
    {

        $clean = explode(" ", "i need am looking for want to require in at a an");
        $synonym = ["flat" => "apartment", "buy" => "sale","home"=>"house","purchase"=>"sale","sq ft"=>"sq.ft","sqft"=>"sq.ft"];
      
        $clean = array_values(array_filter($clean)); #in case there is duplicate

        $query_string1 = strtolower($_GET['keywords']);
        $skip = (int)$_GET['skip'];
        // print_r($skip);
        // exit();
        $kk = array_keys($synonym);
        $vv = array_values($synonym); 
        $query_string = str_replace($kk,$vv,$query_string1);
        // print_r($query_string);
        // exit();
        $city = $_GET['city_name'];
        $filters = base64_decode($_GET['filters']);
        $mongo = new DatabaseConnector();

        $db = $mongo->getDatabase();
        $collection_query = $db->query_key_pair;
        $cursor = $collection_query->find(['user_inputs' => $query_string],['limit'=>1]);
        $key_data = [];
        foreach ($cursor as $document) {
            $key_data[] = $document;
        }
        $pagination = $skip;


        $options = ['sort' => ['property_id' => -1], 'skip' => $pagination, 'limit' => 3];

       
           
        
        if(count($key_data)>0 && $filters == ''){
            $keypair = $key_data[0]->key_pair;
            foreach($keypair as $key=>$val){
                $data_from_query[$key] = $val;
            }
            // 
            $collection = $db->property;

            $cursor = $collection->find($data_from_query,$options);
            $key_datas = [];
            foreach ($cursor as $document) {
                $key_datas[] = $document;


                unset($document->contact_phone);
                unset($document->contact_name);
                unset($document->map_url);
                unset($document->map_latitude);
                unset($document->map_longtitude);
                        }

                        // print_r($data_from_query);
                        // exit();
            return $this->respond($key_datas, 200);
        }

        else{
       


 
        // $pagination = 0;

        // $options = ['sort' => ['property_id' => -1], 'skip' => $pagination, 'limit' => 50];


        $mongo = new DatabaseConnector();

        $db = $mongo->getDatabase();
        $collection_data_m = $db->keyword_set;
        $array_string = array_values(array_filter(explode(' ', $query_string))); # remove space and reindexed

        #----------- clean up ---------------
        for ($n = 0; $n < count($array_string); $n++) {
            if (array_search($array_string[$n], $clean) > -1) {
                unset($array_string[$n]);
            }
        }
        $array_string = array_values($array_string);
        #----------- clean up ---------------

        #------------- locality correction ----------- patter [tarun nagar]
        for ($n = 0; $n < count($array_string); $n++) {
            if ($array_string[$n] == 'nagar') {
                $array_string[$n - 1] = ucfirst($array_string[$n - 1]) . " " . ucfirst($array_string[$n]);
                unset($array_string[$n]);
            }
        }
        $array_string = array_values($array_string);

        #------------- locality correction -----------

        for ($i = 0; $i < count($array_string); $i++) {
            // $keys_from_s[] = $collection_data_m->findOne([ucfirst(strtolower($array_string[$i])) => ['$exists' => true]]);      
            $keys_from_s[] = $collection_data_m->findOne([ucfirst($array_string[$i]) => ['$exists' => true]]);
            // echo  ucfirst($array_string[$i])."</br>";
        }



        $pp = json_decode(json_encode($keys_from_s, true), true);


        foreach ($pp as $k => $doc_val) {
            $keys_mongo[$k] = $doc_val;
        }
        $exact_value = array_filter($keys_mongo);
        $merge = array_merge(...$exact_value);
        $result_keys = array_keys($merge);
        $result_values = array_values($merge);


        $kys = [];
        foreach ($array_string as $v) {                 #user text
            //  echo $v;          
            foreach ($merge as $k => $m) {            #db keyset
                if (strtolower($k) == strtolower($v)) {
                    $kys[$m] = new \MongoDB\BSON\Regex('^' . ucfirst($v), 'i');
                }
            }
        }
        // print_r($result_keys);           


        #------ temporary key name changing
        foreach ($kys as $k => $v) {
            if ($k == 'city') {
                unset($kys['city']);
                $kys['city_name'] = $v;
            }
        }
        // $text = ['$text' => ['$search'=>$query_string]];

        if ($filters == '') {
            $filter_data = $kys;
        } 
        else {

            parse_str($filters, $get_array);
            $alldata = $get_array;

            $filter_data = $alldata;

            parse_str($filters, $pjg);
            // unset($pjg['category_name']);
            // unset($pjg['type']);
            // $kp=[];
            foreach($pjg as $k => $c){
               if( $k!='category_name' && $k!='type' ){
                  $pjg['details.'.$k]=$c;             
                  unset($pjg[$k]);             
               }           
    
            }
            $filter_data = $pjg;
        }
   
        // print_r($pjg);
        // $accpt=['category_name','type'];
       
        // exit();

        if (count($kys) > 0) {


            $collection_data_key_pair = $db->query_key_pair;


            $cursor = $collection_data_key_pair->findOneAndUpdate(
                array("user_inputs" => $query_string1),
                array('$set' => array('user_inputs' => $query_string1, 'key_pair' => $kys, "created_at" => date("Y/m/d"))),

                array(
                    "upsert" => true,
                )
            );


            //    exit();

            $collection_data_s = $db->property;
            $cursor = $collection_data_s->find($filter_data, $options);

            $return = array();
            foreach ($cursor as $document) {
                $return[] = $document;
                unset($document->contact_phone);
                unset($document->contact_name);
                unset($document->map_url);
                unset($document->map_latitude);
                unset($document->map_longtitude);
            }
            // if (count($return)>0){
            //     print_r($return[0]->contact_id);
            // }
            // exit();
            return $this->respond($return, 200);
            exit();
        } else {

            return $this->respond(['message' => 'Search not found but you can try below most searches', 'status' => 754], 200);
        }
    
    }
    exit();
    }


    public function new_search()
    {

        $query_string = $_GET['keywords'];
        $city = $_GET['city_name'];
    
        $array_string = explode(" ", $query_string);

        $orquery = array();
        for ($i = 0; $i < count($array_string); $i++) {

            $a = array(
                'category_name' => new \MongoDB\BSON\Regex('^' . $array_string[$i], 'i'),
            );

            $b = array(
                'type' => new \MongoDB\BSON\Regex('^' . $array_string[$i], 'i'),
            );
            $c = array(
                'map_address' => new \MongoDB\BSON\Regex($array_string[$i], 'i')
            );
            $d = array(
                'project_name' => new \MongoDB\BSON\Regex('^' . $array_string[$i], 'i')
            );

            array_push($orquery, $a);
            array_push($orquery, $b);
            array_push($orquery, $c);
            array_push($orquery, $d);
                // $orquery['purpose'] = new \MongoDB\BSON\Regex($array_string[$i], 'i');
                // $orquery['address'] = new \MongoDB\BSON\Regex($array_string[$i], 'i');
            ;
        }

        $mongo = new DatabaseConnector();

        $db = $mongo->getDatabase();

        $collection = $db->property;
        $pagination = 0;


        $alldata = $_SERVER['QUERY_STRING'];

        parse_str($alldata, $get_array);
        $alldata = $get_array;

        unset($alldata['keywords']);
        unset($alldata['key']);
 
        $combine = array_merge($finor,  $alldata);


        $options = ['sort' => ['property_id' => -1], 'skip' => $pagination, 'limit' => 5]; 
        $cursor = $collection->find($combine, $options);
        // $cursor = $collection->find();

        //  print_r($cursor);
        //  exit();
        $return = array();
        foreach ($cursor as $document) {
            $return[] = $document;
        }
        return $this->respond($return, 200);

    }





    public function testfetch()
    {

        $mongo = new DatabaseConnector();

        $db = $mongo->getDatabase();

        $collection = $db->property;

        $filter = [
            ['category' => new \MongoDB\BSON\Regex('Apartment', 'i')],
            //['purpose' => new \MongoDB\BSON\Regex('^sale','i')],
            ['address' => new \MongoDB\BSON\Regex('tarun', 'i')],

        ];
        $cursor = $collection->find(['category_name'=>'Apartment','type'=>'Sale','details.total_unit'=>'36','details.gym'=>'Yes'], ['limit' => 10]);

        $return = array();
        foreach ($cursor as $document) {
            $return[] = $document;
            unset($document->_id);
        }

        // print_r($filter);
        // exit(); 
        return $this->respond($return, 200);
        // print_r($cursor);




    }


    public function json_test()
    {




        $mongo = new DatabaseConnector();

        $db = $mongo->getDatabase();

        $collection = $db->property;


        $query_string = $_GET['keywords'];

        $array_string = explode(" ", $query_string);


        for ($i = 0; $i < count($array_string); $i++) {
        }

        $json1 = '{ $or : [ {"purpose": "Rent" },{"category": "Commercial"} ] }';
        $bson = \MongoDB\BSON\fromJSON($json1);
        $value = \MongoDB\BSON\toPHP($bson);

        $cursor = $collection->find($value);

        $return = array();
        foreach ($cursor as $document) {
            $return[] = $document;
        }
        return $this->respond($return, 200);

        print_r(($return));
    }



    public function city()
    {



        $mongo = new DatabaseConnector();

        $db = $mongo->getDatabase();

        $collection = $db->search_cities;
        $filter = [];
        $cursor = $collection->find($filter);
        $return = array();
        foreach ($cursor as $document) {
            $return[] = $document;
        }
        return $this->respond($return, 200);
    }


    public function indexing_search()
    {


        $query_string = $_GET['keywords'];
        $city = $_GET['city_name'];
        $filters = base64_decode($_GET['filters']);
        $text = ['$text' => ['$search' => $query_string]];

        if ($filters == '') {
            $filter_data = $text;
        } else {

            parse_str($filters, $get_array);
            $alldata = $get_array;

            $filter_data = $alldata;

        }
    

        $mongo = new DatabaseConnector();

        $db = $mongo->getDatabase();

        $collection = $db->test_property;
        $pagination = 0;
        $options = ['sort' => ['property_id' => -1], 'skip' => $pagination, 'limit' => 100];

        $cursor = $collection->find($filter_data, $options);
        $return = array();
        foreach ($cursor as $document) {
            $return[] = $document;
        }
        return $this->respond($return, 200);
    }




    public function autosuggest()
    {

        $strings = $_GET['string'];

 
        $mongo = new DatabaseConnector();

        $db = $mongo->getDatabase();

        $collection = $db->query_key_pair;

        if($strings !== ''){

        $cursor = $collection->find(['user_inputs' => new \MongoDB\BSON\Regex($strings, 'i')],['limit'=>5]);
        // print_r($strings);
        // exit();
        $return = array();
        foreach ($cursor as $document) {
            $return[] = $document;

        }
    
        return $this->respond($return, 200);
    }

    }

    public function showProp($id = null)
    {

        $mongo = new DatabaseConnector();
        $db = $mongo->getDatabase();
        $collection=$db->property;
        $filter = array('property_id' => $id);
        $cursor = $collection->find($filter);
        $return = array();
        foreach ($cursor as $document) {
            $return[] = $document;
            unset($document->contact_phone);
                unset($document->contact_name);
                unset($document->map_url);
                unset($document->map_latitude);
                unset($document->map_longtitude);

        }
        return $this->respond($return, 200);
    }


}
