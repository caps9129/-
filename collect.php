<?php


$arr_geocoding = array("curl_url"=>"http://geocoding.sgis.tw/position.php");

$arr_basic_7_11 = array("cookie_url"=>"https://emap.pcsc.com.tw/EMapSDK.aspx", "curl_url"=>"https://emap.pcsc.com.tw/EMapSDK.aspx");
$arr_cityNo_7_11 = array("台北市"=>"01", "基隆市"=>"02", "新北市"=>"03", "桃園市"=>"04", "新竹市"=>"05",
                         "新竹縣"=>"06", "苗栗縣"=>"07", "台中市"=>"08", "台中縣"=>"09", "彰化縣"=>"10",
                         "南投縣"=>"11", "雲林縣"=>"12", "嘉義市"=>"13", "嘉義縣"=>"14", "台南市"=>"15",
                         "台南縣"=>"16", "高雄市"=>"17", "高雄縣"=>"18", "屏東縣"=>"19", "宜蘭縣"=>"20",
                         "花蓮縣"=>"21", "台東縣"=>"22", "澎湖縣"=>"23", "連江縣"=>"24", "金門縣"=>"25");
$arr_7_11_info_tag = array("POIID", "POIName", "X", "Y", "Telno", "FaxNo", "Address", "StoreImageTitle");  


$arr_basic_family = array("cookie_url"=>"http://www.family.com.tw", "curl_url"=>"http://api.map.com.tw/net/familyShop.aspx?");




$arr_basic_ok = array("cookie_url"=>"https://www.okmart.com.tw/convenient_shopSearch_ShopResult.aspx", "curl_url"=>"https://www.okmart.com.tw/convenient_shopSearch_ShopResult.aspx");


$arr_ok_info_tag = array("h1");  

require_once('./connectDB.php');
require_once('./curl.php');
require_once('./parse_html.php');
require_once('./parse_xml.php');
require_once('./simple_html_dom.php');

date_default_timezone_set('Asia/Taipei');
$datetime = date('Y-m-d H:i:s ', time());





do{
    printf("1.collect 7-11\n");
    printf("2.collect hi-life\n");
    printf("3.collect family\n");
    printf("4.collect ok-mart\n");
    printf("5.collect all xml\n");
    printf("0.EXIT\n");
    $choice = read_chioce();
    if($choice == 1){
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
    else if($choice == 2){


    }
    else if($choice == 3){
        $DataBase = new DBClass();
        print("collect family data........\n");
        $curl = new CurlClass($arr_basic_family['cookie_url']);
        foreach($arr_cityNo_7_11 as $key => $value){
       
            $post = http_build_query(array("searchType" => "ShowTownList", 
                                           "type" => "", 
                                           "city" => $key, 
                                           "fun" => "storeTownList", 
                                           "key" => "6F30E8BF706D653965BDE302661D1241F8BE9EBC"));
            $curl_url_post = $arr_basic_family['curl_url'].$post;
            
            $curl->GetHTML($curl_url_post, null, $arr_basic_family['cookie_url']);
            //print($curl->html);
            if(preg_match('/([a-zA-Z]+)([\(])([^X]+)([\)])/u' ,$curl->html ,$matches)){
              
                $json_file = json_decode($matches[3], true);
               
            }
        
            foreach($json_file as $arr_value){
                $post = http_build_query(array("searchType" => "ShopList", 
                                                "type" => "", 
                                                "city" => $key, 
                                                "area" => $arr_value['town'],
                                                "road"=> "",
                                                "fun" => "showStoreList", 
                                                "key" => "6F30E8BF706D653965BDE302661D1241F8BE9EBC"));

                $curl_url_post = $arr_basic_family['curl_url'].$post;

                $curl->GetHTML($curl_url_post, null, $arr_basic_family['cookie_url']);
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
    else if($choice == 4){
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
                    
                    // 
                }
                array_push($html->arr_storeinfo, $datetime);
                $DataBase->insert($html->arr_storeinfo, "ok-mart");
               
            }
            

            $ok_ID = intval($ok_ID);
            $ok_ID ++;
            
            
        
        }while($ok_ID <= 10000);


    }
    else if($choice == 5){

    }
}while($choice != 0);

exit;


function read_chioce(){
    
    $fp1=fopen("php://stdin", "r");
    $input=fgets($fp1, 255);
    fclose($fp1);

    return $input;
}


?>