<?php
namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Libraries\mongodb\DatabaseConnector;


class Dynamic_form extends ResourceController{

  public function subOption()
  {
  $data = json_decode(file_get_contents('php://input'),true);
    $array = array(
'apartment'=>array('sale','rent'),
'cafe'=>array('book','regular')
);


$array_key = array_keys($array);
$getval = [];
foreach ($array_key as  $value_key) {
  if ($value_key == $data['option']) {
    foreach ($array[$value_key] as  $value) {
   array_push($getval,$value);
}
  }

}
echo json_encode(array('message'=>$getval));


  }

  public function form(){

  $data = json_decode(file_get_contents('php://input'),true);
 
$child = array("apartment_sale"=> array(

 ["id" => '1',"name"=> "recent apartment",
  "data"=> [["id"=>"1","form_name"=>"apartment_type","form"=>

  [[
    ["id" => '1',"name"=> "all","type"=>"radio","value"=>'all'],
    ["id" => '2',"name"=> "rent","type"=>"radio","value"=>'rent'],
    ["id" => '3',"name"=> "sale","type"=>"radio","value"=>'sale']]]

],
["id"=>"2","form_name"=>"apartment_date","form"=> [[
      "id" => '1',"name"=> "date","type"=>"date","value"=>'']]
]]
],
["id" => '2',"name"=> "new apartment",
  "data"=> [["id"=>"3","form_name"=>"new_apartment_type","form"=>

  [[
    ["id" => '1',"name"=> "all","type"=>"radio","value"=>'all'],
    ["id" => '2',"name"=> "rent","type"=>"radio","value"=>'rent'],
    ["id" => '3',"name"=> "sale","type"=>"radio","value"=>'sale']]]

],
["id"=>"1","form_name"=>"new_apartment_date","form"=> [[
      "id" => '1',"name"=> "date","type"=>"date","value"=>'']]
]]
],
["id" => '3',"name"=> "old apartment",
  "data"=> [["id"=>"1","form_name"=>"old_apartment_type","form"=>

  [[
    ["id" => '1',"name"=> "all","type"=>"radio","value"=>'all'],
    ["id" => '2',"name"=> "rent","type"=>"radio","value"=>'rent'],
    ["id" => '3',"name"=> "sale","type"=>"radio","value"=>'sale']]]

],
["id"=>"2","form_name"=>"old_apartment_date","form"=> [[
      "id" => '1',"name"=> "date","type"=>"date","value"=>'']]
]]
]



),




);

 echo "<pre>";
 
  print_r($child);
$output = "";
$output .= "<h2>" . $child['apartment_sale'][0]['name'] . "</h2>";

for ($x=0; $x < count($child['apartment_sale']); $x++) { 
  


 for ($a=0; $a < count($child['apartment_sale'][$x]['data']); $a++) {

 for ($b=0; $b < count($child['apartment_sale'][$x]['data'][$a]['form']); $b++) {


    $output .= "<h2>" . $child['apartment_sale'][$x]['data'][$a]['form_name'] . "</h2>";
  if (isset($child['apartment_sale'][$x]['data'][$a]['form'][$b][0])) {
   

 for ($c=0; $c < count($child['apartment_sale'][$x]['data'][$a]['form'][$b]); $c++) {

  $output .= "<div><label>" . $child['apartment_sale'][$x]['data'][$a]['form'][$b][$c]['name'] . "<label/>";

 $output .= "

 <input type='" 
 . 
 $child['apartment_sale'][$x]['data'][$a]['form'][$b][$c]['type'] 
 . "' name='" . 
 $child['apartment_sale'][$x]['data'][$a]['form_name'] 


 . "[". $child['apartment_sale'][$x]['data'][$a]['id'] ."]'/></div>";
 }

}else{
   
$output .= "<div><label>" . $child['apartment_sale'][$x]['data'][$a]['form'][$b]['name'] . "<label/>";

 $output .= "

 <input type='" 
 . 
 $child['apartment_sale'][$x]['data'][$a]['form'][$b]['type'] 
 . "' name='" . 
 $child['apartment_sale'][$x]['data'][$a]['form_name'] 


 . "'/></div>";
  }

 }

 }
}
  echo $output;


// print_r($child['apartment_sale'][0]['data']['0']['form']);


}
}

?>



 <!-- "apartment_rent"=>array(
 ["id"=>'1',"name"=> "apartment_rent","type"=>"button","value"=>'all'],
 ["id"=>'2',"name"=> "apartment_rent","type"=>"button","value"=>'new'],
 ["id"=>'3',"name"=> "apartment_rent","type"=>"button","value"=>'recent'],
 ["id"=>'4',"name"=> "apartment_rent","type"=>"button","value"=>'old']),
 "cafe_book"=>array(
 ["id"=>'1',"name"=> "cafe_book","type"=>"checkbox","value"=>'yes'],
 ["id"=>'2',"name"=> "cafe_book","type"=>"checkbox","value"=>'no']),
 "cafe_regular"=>array(
 ["id"=>'1',"name"=> "cafe_regular","type"=>"checkbox","value"=>'yes'],
 ["id"=>'2',"name"=> "cafe_regular","type"=>"checkbox","value"=>'no']) -->