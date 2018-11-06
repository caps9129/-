<?php

require_once('./hi-life_key.php');


$arr_geocoding = array("curl_url"=>"http://geocoding.sgis.tw/position.php");

$arr_basic_7_11 = array("cookie_url"=>"https://emap.pcsc.com.tw/EMapSDK.aspx", "curl_url"=>"https://emap.pcsc.com.tw/EMapSDK.aspx");
$arr_cityNo_7_11 = array("台北市"=>"01", "基隆市"=>"02", "新北市"=>"03", "桃園市"=>"04", "新竹市"=>"05",
                         "新竹縣"=>"06", "苗栗縣"=>"07", "台中市"=>"08", "台中縣"=>"09", "彰化縣"=>"10",
                         "南投縣"=>"11", "雲林縣"=>"12", "嘉義市"=>"13", "嘉義縣"=>"14", "台南市"=>"15",
                         "台南縣"=>"16", "高雄市"=>"17", "高雄縣"=>"18", "屏東縣"=>"19", "宜蘭縣"=>"20",
                         "花蓮縣"=>"21", "台東縣"=>"22", "澎湖縣"=>"23", "連江縣"=>"24", "金門縣"=>"25");
$arr_7_11_info_tag = array("POIID", "POIName", "X", "Y", "Telno", "FaxNo", "Address", "StoreImageTitle");  

$arr_basic_hi_life = array("refer_url"=>"http://www.hilife.com.tw", "curl_url"=>"http://www.hilife.com.tw/storeInquiry_street.aspx");
$arr_city_hi_life = array("台北市", "基隆市", "新北市", "桃園市", "新竹市", 
                          "新竹縣", "苗栗縣", "台中市", "彰化縣", "南投縣",
                          "雲林縣", "嘉義市", "嘉義縣", "台南市", "高雄市",
                          "屏東縣", "宜蘭縣", "金門縣");

$arr_basic_family = array("refer_url"=>"http://www.family.com.tw", "curl_url"=>"http://api.map.com.tw/net/familyShop.aspx?");

$arr_basic_ok = array("cookie_url"=>"https://www.okmart.com.tw/convenient_shopSearch_ShopResult.aspx", "curl_url"=>"https://www.okmart.com.tw/convenient_shopSearch_ShopResult.aspx");
$arr_ok_info_tag = array("h1", "ul", "li");  

$arr_basic_google_map = array("refer_url"=>"http://www.google.com.tw", "get_ID_url"=>"https://maps.googleapis.com/maps/api/place/findplacefromtext/json?", "get_review_url"=>"https://maps.googleapis.com/maps/api/place/details/json?");
$api_key = "AIzaSyDSUbvnh8f6cHiOqIHlCaiJaQp9OJouydc";
$arr_google_review_attr = array("author_name", /*"author_url", "language", "profile_photo_url",*/ "rating", /*"relative_time_description",*/ "text", "time");
$arr_table_name = array("7-11", "family", "hi-life", "ok-mart");


date_default_timezone_set('Asia/Taipei');
$datetime = date('Y-m-d H:i:s ', time());



require_once('./connectDB.php');
require_once('./curl.php');
require_once('./parse_html.php');
require_once('./parse_xml.php');
require_once('./simple_html_dom.php');


do{
    printf("1.collect 7-11\n");
    printf("2.collect hi-life\n");
    printf("3.collect family\n");
    printf("4.collect ok-mart\n");
    printf("5.collect google review\n");
    printf("6.collect all data\n");
    printf("0.EXIT\n");
    $choice = read_chioce();
    if($choice == 1){
        exc_7_11();
    }
    else if($choice == 2){
     
        exc_hi_life();
    }
    else if($choice == 3){
        exc_family();
    }
    else if($choice == 4){
        exc_ok_mart();
    }
    else if($choice == 5){
        exc_review();
    }
    else if($choice == 6){
        exc_7_11();
        exc_hi_life();
        exc_family();
        exc_ok_mart();
    }
}while($choice != 0);

exit;


function read_chioce(){
    
    $fp1=fopen("php://stdin", "r");
    $input=fgets($fp1, 255);
    fclose($fp1);

    return $input;
}

function exc_review(){

    global $arr_basic_google_map, $api_key, $arr_table_name, $arr_google_review_attr;
    $DataBase = new DBClass();
    $curl = new CurlClass($arr_basic_google_map['refer_url']);

    print("collect google review........\n");

    foreach($arr_table_name as $table_name){
        $DataBase->select($table_name);
        foreach($DataBase->select_result as $select_result){
            $post_count = 0;
            $arr_storedata = array();
            do {
                if($table_name == "7-11"){
                    if($post_count == 0)
                        $input = "7-ELEVEN".$select_result['name'];
                    else if($post_count == 1)
                        $input = "7-11".$select_result['name'];
                }
                else if($table_name == "family"){
                    if($post_count == 0)
                        $input = "全家".$select_result['name'];
                    else if($post_count == 1)
                        $input = "family".$select_result['name'];
                }
                else if($table_name == "hi-life"){
                    if($post_count == 0)
                        $input = "萊爾富".$select_result['name'];
                    else if($post_count == 1)
                        $input = "hi-life".$select_result['name'];
                }
                else if($table_name == "ok-mart"){
                    if($post_count == 0)
                        $input = "OK便利商店".$select_result['name'];
                    else if($post_count == 1)
                        $input = "OK".$select_result['name'];
                }
                $post = http_build_query(array("input" => $input, "inputtype" => "textquery", "fields" => "place_id", "key" => $api_key));
  
                $curl_url_post = $arr_basic_google_map['get_ID_url'].$post;

                //print($select_result['name']."\n");
                $curl->GetHTML($curl_url_post, null, $arr_basic_google_map['refer_url']);
                
                $json_file = json_decode($curl->html, true);

                $post_count++;

                //print($json_file["status"]."\n");

            } while($post_count < 2 && $json_file["status"] != "OK");
            
            $length = count($json_file["candidates"]);

            $post_count = 1;
                
            foreach($json_file["candidates"] as $place_id){
                
                
            
                $post = http_build_query(array("placeid" => $place_id["place_id"], "key" => $api_key));
                $curl_url_post = $arr_basic_google_map['get_review_url'].$post;

                $curl->GetHTML($curl_url_post, null, $arr_basic_google_map['refer_url']);

                $json_file = json_decode($curl->html, true);

                

                print("out".$post_count."\n");

                if($post_count > $length || strlen($json_file["result"]["rating"]) != 0){
                    
                    print("in".$post_count."\n");

                    break;
                }

                $post_count++;

                
            }

            

            if(@$json_file["result"]["rating"]){
                array_push($arr_storedata, $json_file["result"]["rating"]);
            }
            else{
                array_push($arr_storedata, "NA");
            }
            
            $arr_2d_reviews = array();
            //print(count($json_file["result"]["reviews"])."\n");
            if(@$json_file["result"]["reviews"]){

                array_push($arr_storedata, count($json_file["result"]["reviews"]));
                foreach($json_file["result"]["reviews"] as $json_file_reviews){
                    $arr_reviews = array();
                    
                    foreach($arr_google_review_attr as $attr){

                        if(strlen($json_file_reviews[$attr]) != 0){

                            if($attr == "time"){
                                $json_file_reviews[$attr] = date('Y-m-d h:i:s',$json_file_reviews[$attr]);
                            }

                            array_push($arr_reviews, urlencode(DeleteHtml($json_file_reviews[$attr])));
                        }
                        else{
                            array_push($arr_reviews, "NA");
                        }
                    }
                    array_push($arr_2d_reviews, $arr_reviews);
                }

                array_push($arr_storedata, urldecode(json_encode($arr_2d_reviews)));
            }
            else{
                array_push($arr_storedata, 'NA');
                array_push($arr_storedata, 'NA');
            }    

            array_push($arr_storedata, $select_result['id']);

            print_r($arr_storedata);
            $DataBase->insert($arr_storedata, $table_name, true);
            
            //exit;s   
          
        }
       
    }





}


function exc_ok_mart(){
    
    global $arr_basic_ok, $arr_ok_info_tag, $datetime, $arr_geocoding;

    $DataBase = new DBClass();
    print("collect ok-mart data........\n");
    $curl = new CurlClass($arr_basic_ok['cookie_url']);
    $ok_ID = 2;
    
    do{

        $ok_ID = str_pad($ok_ID, 4, '0', STR_PAD_LEFT);
        $ok_ID = (string)$ok_ID;

        $post = http_build_query(array("id" => $ok_ID));
        $curl->GetHTML($arr_basic_ok['curl_url'], $post);
        $html = new HTMLParseClass($curl->html);
        $html->ParseHTML($arr_ok_info_tag);
        // var_dump(empty($html->arr_storeinfo));
        if($html->arr_storeinfo){
           
            $post = http_build_query(array("addr" => $html->arr_storeinfo[1]));
            $curl->GetHTML($arr_geocoding['curl_url'], $post);
            $json_file = json_decode($curl->html, true);
            if($json_file["accuracy"] != 3){
                array_push($html->arr_storeinfo, $json_file["lat"]);
                array_push($html->arr_storeinfo, $json_file["lng"]);
            }
            else{
                array_push($html->arr_storeinfo, 'NA');
                array_push($html->arr_storeinfo, 'NA');
            }
            array_push($html->arr_storeinfo, $datetime);
            $DataBase->insert($html->arr_storeinfo, "ok-mart");
            //print_r($html->arr_storeinfo);
            // exit;
           
        }

        $ok_ID = intval($ok_ID);
        $ok_ID ++;  
        
    }while($ok_ID <= 10000);

}

function exc_family(){

    global $arr_cityNo_7_11,  $arr_basic_family, $datetime;

    $DataBase = new DBClass();
    print("collect family data........\n");
    $curl = new CurlClass($arr_basic_family['refer_url']);
    foreach($arr_cityNo_7_11 as $key => $value){
   
        $post = http_build_query(array("searchType" => "ShowTownList", 
                                       "type" => "", 
                                       "city" => $key, 
                                       "fun" => "storeTownList", 
                                       "key" => "6F30E8BF706D653965BDE302661D1241F8BE9EBC"));
        $curl_url_post = $arr_basic_family['curl_url'].$post;
        
        $curl->GetHTML($curl_url_post, null, $arr_basic_family['refer_url']);
        //print($curl->html);
        if(preg_match('/([a-zA-Z]+)([\(])([^X]+)([\)])/u' ,$curl->html ,$matches)){
          
            $json_file = json_decode($matches[3], true);

            foreach($json_file as $arr_value){
                $post = http_build_query(array("searchType" => "ShopList", 
                                                "type" => "", 
                                                "city" => $key, 
                                                "area" => $arr_value['town'],
                                                "road"=> "",
                                                "fun" => "showStoreList", 
                                                "key" => "6F30E8BF706D653965BDE302661D1241F8BE9EBC"));

                $curl_url_post = $arr_basic_family['curl_url'].$post;

                $curl->GetHTML($curl_url_post, null, $arr_basic_family['refer_url']);
                if(preg_match('/([a-zA-Z]+)([\(])([^X]+)([\)])/u' ,$curl->html ,$matches)){
              
                    $json_file = json_decode($matches[3], true);

                    foreach($json_file as $arr_storedata){
                        $arr_storedata['datetime'] = $datetime;
                        $DataBase->insert($arr_storedata, 'family');
                    }
                   
                }

            }
           
        } 
  
    }
}

function exc_hi_life(){
    global $arr_city_hi_life, $arr_key, $arr_event, $arr_basic_hi_life, $arr_geocoding, $datetime;
    $DataBase = new DBClass();
    print("collect hi-life data........\n");
    $curl = new CurlClass($arr_basic_hi_life['refer_url']);
    foreach($arr_city_hi_life as $key){


        $post = http_build_query(array( "__EVENTTARGET" => "AREA",
                                        "__EVENTARGUMENT" => "",
                                        "__LASTFOCUS" => "",
                                        "__VIEWSTATE" => $arr_key[$key],
                                        "__VIEWSTATEGENERATOR"=> "B77476FC",
                                        "__EVENTVALIDATION" => $arr_event[$key],
                                        "CITY" => $key,
                                        "AREA" => ""
                                ));
        $curl->GetHTML($arr_basic_hi_life['curl_url'], $post, $arr_basic_hi_life['refer_url']);

        $html = new HTMLParseClass($curl->html);
        //print($curl->html);
        $html->ParseCounty();

        foreach($html->arr_county as $county_value){

            print($key.":".$county_value."\n");

            $post = http_build_query(array( "__EVENTTARGET" => "AREA",
                                            "__EVENTARGUMENT" => "",
                                            "__LASTFOCUS" => "",
                                            "__VIEWSTATE" => $arr_key[$key],
                                            "__VIEWSTATEGENERATOR"=> "B77476FC",
                                            "__EVENTVALIDATION" => $arr_event[$key],
                                            "CITY" => $key,
                                            "AREA" => $county_value
            ));
            $curl->GetHTML($arr_basic_hi_life['curl_url'], $post, $arr_basic_hi_life['refer_url']);
            $html = new HTMLParseClass($curl->html);
            $html->ParseStoreINFO();
            //print_r($html->arr_storeinfo);
            $arr_2d_storedata = array_chunk($html->arr_storeinfo, 5);
            foreach($arr_2d_storedata as $arr_storedata){
                $post = http_build_query(array("addr" => $arr_storedata[2]));
                $curl->GetHTML($arr_geocoding['curl_url'], $post);
                $json_file = json_decode($curl->html, true);
                if($json_file["accuracy"] != 3){
                    array_push($arr_storedata, $json_file["lat"]);
                    array_push($arr_storedata, $json_file["lng"]);
                }
                else{
                    array_push($arr_storedata, 'NA');
                    array_push($arr_storedata, 'NA');
                    
                    // 
                }
                array_push($arr_storedata, $datetime);
                $DataBase->insert($arr_storedata, 'hi-life');
            }
        }                       

    }

}

function exc_7_11(){
    global $arr_cityNo_7_11, $arr_basic_7_11, $arr_7_11_info_tag, $datetime;
    
    $DataBase = new DBClass();
    print("collect 7-11 data........\n");
    $curl = new CurlClass($arr_basic_7_11['cookie_url']);
    foreach($arr_cityNo_7_11 as $key => $value){
        $post = http_build_query(array("commandid" => "GetTown", "cityid" => $value));
        $curl->GetHTML($arr_basic_7_11['curl_url'], $post);
        $xml = new XMLParseClass($curl->html);
        $xml->ParseXML("GeoPosition", "TownName", "GetTown");
        
        foreach($xml->arr_townNO as $townNO){
            $post = http_build_query(array("commandid" => "SearchStore", "city" => $key, "town" => $townNO));
            $curl->GetHTML($arr_basic_7_11['curl_url'], $post);
            $xml = new XMLParseClass($curl->html);
            $xml->ParseXML("GeoPosition", $arr_7_11_info_tag, "SearchStore");
            $arr_2d_storedata = array_chunk($xml->arr_storeinfo, count($arr_7_11_info_tag));
            foreach($arr_2d_storedata as $arr_storedata){
                array_push($arr_storedata, $datetime);
                $DataBase->insert($arr_storedata, '7-11');
            }

        }

    }
    $DataBase->disconnect();
    print("disconnect\n");
}

function DeleteHtml($str){
    $str = trim($str);
    $str = strip_tags($str,"");
    $str = str_replace("\t","",$str);
    $str = str_replace("\r\n","",$str); 
    $str = str_replace("\r","",$str); 
    $str = str_replace("\n","",$str); 
    $str = str_replace(" "," ",$str); 
    $str = str_replace("&nbsp;","",$str);
    return $str;
}




?>