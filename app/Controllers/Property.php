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
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
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


        $collection=$db->property;

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
        }

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
            }
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
            ['category' => new \MongoDB\BSON\Regex('^commercialspace', 'i')],
            //['purpose' => new \MongoDB\BSON\Regex('^sale','i')],
            ['address' => new \MongoDB\BSON\Regex('tarun', 'i')],

        ];
        $cursor = $collection->find(['$or' => $filter], ['limit' => 5000]);

        $return = array();
        foreach ($cursor as $document) {
            $return[] = $document;
        }

        print_r($filter);
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
}

  
    public function search_query(){

        $query_string = strtolower($_GET['keywords']);
        
        $mongo=new DatabaseConnector();

        $db=$mongo->getDatabase();

        $collection_data=$db->synonyms;
               
        $filter = [];
        // $query = new \MongoDB\Driver\Query($filter);

        $cursor = $collection_data->find($filter);

         $synonym=array();
          foreach ($cursor as $document) {
            $synonym[]=$document;
         }
        
         $syno_key = array_keys(json_decode(json_encode($synonym[0], true), true));
         $syno_val = array_values(json_decode(json_encode($synonym[0], true), true));
        
        
        
        for($tiktok = 0; $tiktok < count($syno_key); $tiktok++){
    
            if(strpos($query_string, strtolower($syno_key[$tiktok]))){
            
                $keys_syno[] = $syno_key[$tiktok];
                $valu_syno[] = $syno_val[$tiktok];
                
            }
            }
            $proper_string = str_replace($keys_syno, $valu_syno, $query_string);
            $array_string = explode(' ', $proper_string);
            $collection_data_m=$db->keyword_set;
               
            $filter = [];
            // $query = new \MongoDB\Driver\Query($filter);
    // print_r(count($array_string));
    // exit();
            // $cursor = $collection_data->find($filter);
            // echo ucfirst($array_string[9]);
            // exit();

            for($i=0; $i < count($array_string); $i++){

                $keys_from_s[] = $collection_data_m->findOne([ucfirst($array_string[$i]) => ['$exists' => true]]);

            }

            $pp = json_decode(json_encode($keys_from_s, true), true);

           for($aa=0; $aa< count($pp); $aa++){

            // if(strpos($proper_string, strtolower($pp[$aa]))){
                    
                $keys_pp[] = array_keys(array_filter($pp));
                // $valu[] = $ccv[$tiktik];
                
         
            }
           print_r($keys_pp);
// foreach($pp as $ppk => $ppv){
//     $newdd[$ppk] = $ppv;
// }
           
//             print_r(array_filter($newdd));

            exit();
            
            foreach ($pp as $document =>$doc_val) {
                $keys_mongo[$document]=$doc_val;
             }
                    // print_r($keys_mongo);
                    return $this->respond($keys_mongo, 200);

            exit();
        //     print_r($proper_string);
        // exit();
        // $query_string = "I need a BHK full Commercial Type Rooms";
        $mongo=new DatabaseConnector();

        $db=$mongo->getDatabase();

        $collection_data=$db->keyword_set;
               
        $filter = [];
        // $query = new \MongoDB\Driver\Query($filter);

        $cursor = $collection_data->find($filter);

         $return=array();
          foreach ($cursor as $document) {
            $return[]=$document;
         }
         return $this->respond($return, 200);
         exit();
                $cc = array_keys(json_decode(json_encode($return, true), true));
                $ccv = array_values(json_decode(json_encode($return[0], true), true));

                print_r($cc[1]);
                exit();

                $keys=array();
                for($tiktik = 0; $tiktik < count($cc); $tiktik++){
    
                    if(strpos($proper_string, strtolower($cc[$tiktik]))){
                    
                        $keys[] = $cc[$tiktik];
                        $valu[] = $ccv[$tiktik];
                        
                 
                    }
                  
                   
                }
                // print_r($keys);
                // print_r($valu);
                // exit();
                // $bee = array();
                // foreach($keys as $finalkey => $finalvalue){
                //     $bee[$finalkey] = $finalvalue;
                // }
                // print_r($bee);
                // exit();
                for($tt =0; $tt < count($keys); $tt++){
                    // echo "pjg";
                    // $ready_params = [ => new \MongoDB\BSON\Regex("pjg")];
                    
                    // $bee[] = [$valu[$tt] => new \MongoDB\BSON\Regex($keys[$tt])];
                    $bee1[strtolower($valu[$tt])] = new \MongoDB\BSON\Regex($keys[$tt]);

                     
                    
                }
                // for($dd=0; $dd < count($bee); $dd++ ){
                //     $newbee= $bee[$dd];
                // }
            //  $testing_data = implode(" ",$bee[0]);
                // print_r($bee);
                // print_r($bee);
                // print_r($bee1);
                //     exit();
                // $mybee=array();
                // foreach($bee as $mykey => $myvalue){
                //         $mybee[$mykey] = $myvalue;
                // }
                // print_r($mybee);

                    // exit();

                $collection=$db->property;
                $rrr = $collection->find($bee1, ['limit' => 2]);

                $return_data=array();
      foreach ($rrr as $documents) {
        $return_data[]=$documents;
     }
    //  print_r($return_data);
     return $this->respond($return_data, 200);
                // $data = json_decode(json_encode($return[0], true), true);
                // $chck = explode(' ',$query_string, strlen($query_string));
                // foreach ($chck as $cval) {
                //     foreach ($data as $key=>$val) {
                //         $tmp_str = explode(" ",$key);   // print_r($tmp_str);
                //         if (in_array($cval,$tmp_str))  {    
                //  echo  " key -[ $key ] -> val [$cval] : , --$val <br>";
                
                // }
                //     }
                // }
                  
    }

    
    public function new_search(){

$query_string = "full bhk type ambika heritage rooms ";
//  $query_string = "commercial tarun";

 $array_string = explode(" ",$query_string);

$orquery=array();
 for($i=0; $i<count($array_string); $i++){

$a = array(
'category'=> new \MongoDB\BSON\Regex('^'.$array_string[$i],'i'),
);

$b = array(   
    'purpose'=> new \MongoDB\BSON\Regex('^'.$array_string[$i],'i'),    
);
$c = array(               
        'address'=> new \MongoDB\BSON\Regex($array_string[$i],'i') 
        )   ; 
$d = array(               
            'project'=> new \MongoDB\BSON\Regex('^'.$array_string[$i],'i') 
            )   ;         

array_push( $orquery, $a  );
array_push( $orquery, $b  );
array_push( $orquery, $c  );
array_push( $orquery, $d  );
// $orquery['purpose'] = new \MongoDB\BSON\Regex($array_string[$i], 'i');
// $orquery['address'] = new \MongoDB\BSON\Regex($array_string[$i], 'i');
;




 }

 // print_r($orquery);
 //exit();
 $mongo=new DatabaseConnector();

 $db=$mongo->getDatabase();

 $collection=$db->property;


 $cursor = $collection->find(['$or' => $orquery],['limit' => 50]);   
 

    $return=array();
      foreach ($cursor as $document) {
        $return[]=$document;
     }
     return $this->respond($return, 200);
     exit();
     print_r($return);
    }
public function testfetch(){

    $mongo=new DatabaseConnector();

    $db=$mongo->getDatabase();
    // $builder = $db->our_brands;
    // $builder->find();
    // print_r($builder);
    // exit(); 
    $collection=$db->property;
    // $filter = array('id'=> '1' );
    // $filter = array('category' => new \MongoDB\BSON\Regex('Commercial'));
    // $filter = ['category'=>'Commercial'];
    // $query = new \MongoDB\Driver\Query($filter);
$filter = [
['category'=> new \MongoDB\BSON\Regex('^commercialspace','i')],
//['purpose' => new \MongoDB\BSON\Regex('^sale','i')],
['address' => new \MongoDB\BSON\Regex('tarun','i')],
// ['category'=> new \MongoDB\BSON\Regex("Commercial")],
// ['purpose' => new \MongoDB\BSON\Regex("Commercial")],
// ['address' => new \MongoDB\BSON\Regex("Commercial")],
// ['category'=> new \MongoDB\BSON\Regex("Sale")],
// ['purpose' => new \MongoDB\BSON\Regex("Sale")],
// ['address' => new \MongoDB\BSON\Regex("Sale")]
];
    $cursor = $collection->find( ['$or' =>$filter],['limit' => 5000]);   

    $return=array();
      foreach ($cursor as $document) {
        $return[]=$document;
     }

     print_r($filter);
   // exit(); 
      return $this->respond($return, 200);
    // print_r($cursor);




}


        public function json_test(){

            $mongo=new DatabaseConnector();

            $db=$mongo->getDatabase();
           
            $collection=$db->property;


$query_string = "I need a BHK full commercialspace Rooms";

$array_string = explode(" ",$query_string);


for($i=0; $i<count($array_string); $i++){




}

            // $json = '{ $or : [ {"purpose": /Rent/},{"category": "Commercial" } ] }';
            $json1 = '{ $or : [ {"purpose": "Rent" },{"category": "Commercial"} ] }';
$bson = \MongoDB\BSON\fromJSON($json1);
$value = \MongoDB\BSON\toPHP($bson);

 $cursor = $collection->find($value);

 $return=array();
      foreach ($cursor as $document) {
        $return[]=$document;
     }
     return $this->respond($return, 200);

 print_r(($return));

        }
        




