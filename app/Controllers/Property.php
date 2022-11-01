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
public function test(){
    echo "hii";
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

        $collection=$db->property;
        $filter = array('category' => new \MongoDB\BSON\Regex($category));

        $cursor = $collection->find($filter);   

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
}
