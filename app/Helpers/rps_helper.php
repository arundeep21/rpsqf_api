<?php


    // generate select options
    function gen_options ($data, $tx, $vl='id', $sl='Select'){
        //rowdata, select text, select value, select placeholder    

        $op="<option value=''>".$sl."</option>";
        foreach ($data->getResult() as $row)
                          {
                            $op.="<option value='".$row->$vl."'>".$row->$tx."</option>";                
                          }
        return $op;

    }

 // generate radio optios with checkbox and label
    function gen_radio_with_box ($data, $tx, $vl='id', $nm='xyz123'){
        //rowdata, select text, select value, select placeholder    
        $r='';
        foreach ($data->getResult() as $row)
                          {

                            $r = get_rand(5); //random no

                            $op.="<input id='".$r."' type='radio' name='".$nm."' value='".$row->$vl."'>".$row->$tx."/>";                
                            $op.="<label for '".$r."'>".$row->$tx."<label/>";                
                          }
        return $op;

    }


// generate random number -- n = random number length

function get_rand($n) { 
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'; 
    $randomString = ''; 
  
    for ($i = 0; $i < $n; $i++) { 
        $index = rand(0, strlen($characters) - 1); 
        $randomString .= $characters[$index]; 
    } 
  
    return $randomString; 
} 


function u($e=0){

  if($e==1){
    echo base_url()."/";
  }
  else {
    return base_url()."/";
  }

}







    function p($data , $r = 0){
        //p works only in development mode
        if(ENVIRONMENT=='production'): 
            return false; 
        endif;

        if($r==1){
            return print_r($data, true) ;
        }
        else{          
                echo "<pre>";
                    print_r($data);
                echo "</pre>";
           
        }

    }

function ph($arr)
{

    //$arr=convertSTDArray($arr);
    //p($arr);

    if(empty($arr))
        echo "<table border='1' cellpadding='20' ><thead><tr><th>Array</th></thead><tr><td>Empty</td></tr></table>";
    else
    {

            echo "<table border='1' cellpadding='20' ><thead><tr>";
            echo "<th>Array Index No.</th>";
            foreach ($arr[0] as $key => $value) {

                    echo "<th>".str_replace("_", " ",$key)."</th>";
            }
            echo "</tr></thead><tbody>";

            foreach ($arr as $key => $value) {
                echo "<tr>";
                    echo "<td>".($key+1)."</td>";
                    foreach ($value as $key1 => $value1) {
                    echo "<td>".$value1."</td>";
                }
                echo "</tr>";
            }

            echo "</tr></tbody></table>";   
        }


}

function simplemail($to="", $subject="", $message="")
{

        //$to = "arundeep.mds@gmail.com";
         //$subject = "This is subject";
         
        // $message = "<b>This is HTML message.</b>";
       //  $message .= "<h1>This is headline.</h1>";
         
         $header = "From:info@ratepersqft.com \r\n";
         //$header .= "Cc:afgh@somedomain.com \r\n";
         $header .= "MIME-Version: 1.0\r\n";
         $header .= "Content-type: text/html\r\n";
         
         try{
            $retval = mail ($to,$subject,$message,$header);

            $return_msg='{"status":"'.$retval.'", "status_message": "success"}';

            return $return_msg;
        }
        catch (\Exception $e)
         {
            
             $return_msg='{"status":"0", "status_message": "'.$e->getMessage().'"}';

             
         }
        /* 
         if( $retval == true ) {
            return 1;
         }else {
            return 0;
         }
    */
}



function simplemail2()
{
    $filename = 'myfile';
    $path = 'your path goes here';
    $file = $path . "/" . $filename;

    $mailto = 'mail@mail.com';
    $subject = 'Subject';
    $message = 'My message';

    $content = file_get_contents($file);
    $content = chunk_split(base64_encode($content));

    // a random hash will be necessary to send mixed content
    $separator = md5(time());

    // carriage return type (RFC)
    $eol = "\r\n";

    // main header (multipart mandatory)
    $headers = "From: name <test@test.com>" . $eol;
    $headers .= "MIME-Version: 1.0" . $eol;
    $headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"" . $eol;
    $headers .= "Content-Transfer-Encoding: 7bit" . $eol;
    $headers .= "This is a MIME encoded message." . $eol;

    // message
    $body = "--" . $separator . $eol;
    $body .= "Content-Type: text/plain; charset=\"iso-8859-1\"" . $eol;
    $body .= "Content-Transfer-Encoding: 8bit" . $eol;
    $body .= $message . $eol;

    // attachment
    $body .= "--" . $separator . $eol;
    $body .= "Content-Type: application/octet-stream; name=\"" . $filename . "\"" . $eol;
    $body .= "Content-Transfer-Encoding: base64" . $eol;
    $body .= "Content-Disposition: attachment" . $eol;
    $body .= $content . $eol;
    $body .= "--" . $separator . "--";

    //SEND Mail
    if (mail($mailto, $subject, $body, $headers)) {
        echo "mail send ... OK"; // or use booleans here
    } else {
        echo "mail send ... ERROR!";
        print_r( error_get_last() );
    }
}



function filterWhere($arrWhere)
{
    $newArr=array();

    
    foreach ($arrWhere as $key => $value)
    {
            if($value!="")
                $newArr[$key]= $value;
    }

    return $newArr;

}

function filterWhereString($arrWhere)
{
    $newArr=array();
    $returnString="";

    
    foreach ($arrWhere as $key => $value)
    {
            if($value!=""){
                $returnString.=" and ".$key;                           
                
            }
    }

     return $returnString;

}


function convertSTDArray($stdObject)
{
    $array = json_decode(json_encode($stdObject), true);
    return ($array);

}


function email_logs($email, $occasion, $table_name, $table_id, $email_content, $sent_status, $sent_status_remarks, $entry_by)
{

     $db      = \Config\Database::connect();
     $data['email']=$email;
     $data['occasion']=$occasion;
     $data['table_name']=$table_name;
     $data['table_id']=$table_id;
     $data['email_content']=$email_content;
     $data['sent_status']=$sent_status;
     $data['sent_status_remarks']=$sent_status_remarks;
     $data['entry_by']=$entry_by;

     $builder = $db->table('email_logs');
     $builder->insert($data);
     $insert_id=$db->insertID();



}



function calender_logs($invite_email, $occasion, $table_name, $table_id, $event_date, $event_name, $event_description, $event_location, $event_color, $time_start, $time_end, $event_all_day, $htmlLink, $sent_status, $sent_status_remarks, $entry_by)
{


     $db      = \Config\Database::connect();

     $data['invite_email']=$invite_email;
     $data['occasion']=$occasion;
     $data['table_name']=$table_name;
     $data['table_id']=$table_id;
     $data['event_date']=$event_date;
     $data['event_name']=$event_name;
     $data['event_description']=$event_description;
     $data['event_location']=$event_location;
     $data['event_color']=$event_color;
     $data['time_start']=$time_start;
     $data['time_end']=$time_end;
     $data['event_all_day']=$event_all_day;
     $data['htmlLink']=$htmlLink;
     $data['sent_status']=$sent_status;
     $data['sent_status_remarks']=$sent_status_remarks;
     $data['entry_by']=$entry_by;




     $builder = $db->table('calender_logs');
     $builder->insert($data);
     $insert_id=$db->insertID();



}


function n($d=0)
{
    if ($d){echo date("Y-m-d H:i:s"); }
    else{ return  date("Y-m-d H:i:s");   }
   
        
}




function minify_html($html)
{
   $search = array(
    '/(\n|^)(\x20+|\t)/',
    '/(\n|^)\/\/(.*?)(\n|$)/',
    '/\n/',
    '/\<\!--.*?-->/',
    '/(\x20+|\t)/', # Delete multispace (Without \n)
    '/\>\s+\</', # strip whitespaces between tags
    '/(\"|\')\s+\>/', # strip whitespaces between quotation ("') and end tags
    '/=\s+(\"|\')/'); # strip whitespaces between = "'

   $replace = array(
    "\n",
    "\n",
    " ",
    "",
    " ",
    "><",
    "$1>",
    "=$1");

    $html = preg_replace($search,$replace,$html);
    return $html;
}

function time_to_secs($time)
{
    $str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $time);
    sscanf($time, "%d:%d:%d", $hours, $minutes, $seconds);
    $secs = $hours * 3600 + $minutes * 60 + $seconds;
     return $secs;
}



function downloadFile($file){
       $file_name = $file;
       $mime = 'application/force-download';
       header('Pragma: public');    
       header('Expires: 0');        
       header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
       header('Cache-Control: private',false);
       header('Content-Type: '.$mime);
       header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
       header('Content-Transfer-Encoding: binary');
       header('Connection: close');
       readfile($file_name);    
       exit();
    }


    function sms_credentials()
    {

        $sms_data['authKey'] = "7113955c8c36af44200175c9f0fce150";
        $sms_data['senderId'] = "KDMPRP";
        $sms_data['route'] = "B";

        return $sms_data;

    }


    function ordinal_suffix($num){

       if($num==""){ return "";}
       if(!is_numeric($num)){return $num;}
       $num = $num % 100; // protect against large numbers
       
    if($num < 11 || $num > 13){
         switch($num % 10){
            case 1: return $num.'st';
            case 2: return $num.'nd';
            case 3: return $num.'rd';
        }
    }
    return $num.'th';
}


function deleteKeysfromArray($array, $key_to_keep)
{


                    foreach ($array as $key => $value) {
                            if($key==$key_to_keep) continue;
                            unset($array[$key]);
                    }

                    return $array;
                
}


?>