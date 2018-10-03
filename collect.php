<?php

$arr_basic_7_11 = array("cookie_url"=>"https://emap.pcsc.com.tw/EMapSDK.aspx", "curl_url"=>"https://emap.pcsc.com.tw/EMapSDK.aspx");
$arr_cityNo_7_11 = array("台北市"=>"01", "基隆市"=>"02", "新北市"=>"03", "桃園市"=>"04", "新竹市"=>"05",
                         "新竹縣"=>"06", "苗栗縣"=>"07", "台中市"=>"08", "台中縣"=>"09", "彰化縣"=>"10",
                         "南投縣"=>"11", "雲林縣"=>"12", "嘉義市"=>"13", "嘉義縣"=>"14", "台南市"=>"15",
                         "台南縣"=>"16", "高雄市"=>"17", "高雄縣"=>"18", "屏東縣"=>"19", "宜蘭縣"=>"20",
                         "花蓮縣"=>"21", "台東縣"=>"22", "澎湖縣"=>"23", "連江縣"=>"24", "金門縣"=>"25");
$arr_7_11_info_tag = array("POIID", "POIName", "X", "Y", "Telno", "FaxNo", "Address", "StoreImageTitle");                         
require_once('./connectDB.php');
require_once('./curl.php');
require_once('./parse_xml.php');

date_default_timezone_set('Asia/Taipei');
$datetime = date('Y-m-d H:i:s ', time());

$DataBase = new DBClass();



do{
    printf("1.collect 7-11\n");
    printf("2.collect hi-life\n");
    printf("3.collect family\n");
    printf("4.collect ok-mart\n");
    printf("5.collect all xml\n");
    printf("0.EXIT\n");
    $choice = read_chioce();
    if($choice == 1){
        print("collect data........\n");
        $curl = new CurlClass($arr_basic_7_11['cookie_url']);
        foreach($arr_cityNo_7_11 as $key => $value){
            $post = http_build_query(array("commandid" => "GetTown", "cityid" => $value));
            $curl->GetXML($arr_basic_7_11['curl_url'], $post);
            $xml = new XMLParseClass($curl->html);
            $xml->ParseXML("GeoPosition", "TownName", "GetTown");
            
            foreach($xml->arr_townNO as $townNO){
                $post = http_build_query(array("commandid" => "SearchStore", "city" => $key, "town" => $townNO));
                $curl->GetXML($arr_basic_7_11['curl_url'], $post);
                $xml = new XMLParseClass($curl->html);
                $xml->ParseXML("GeoPosition", $arr_7_11_info_tag, "SearchStore");
                $arr_2d_storedata = array_chunk($xml->arr_storeinfo, count($arr_7_11_info_tag));
                foreach($arr_2d_storedata as $arr_storedata){
                    array_push($arr_storedata, $datetime);
                    $DataBase->insert($arr_storedata);
                }
   
            }

        }
        $DataBase->disconnect();
        print("disconnect\n");
    }
    else if($choice == 2){

    }
    else if($choice == 3){

    }
    else if($choice == 4){
      

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