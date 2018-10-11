<?php


$a = 'http://api.map.com.tw/net/familyShop.aspx?'.http_build_query([
  "searchType"=>"ShopList",
  "type"=>"", 
  "city"=>"台北市",
  "area"=>"大同區",
  "road"=>"",
  "fun"=>"showStoreList",
  "key"=>"6F30E8BF706D653965BDE302661D1241F8BE9EBC",
], '', '&');

// print($a);
// exit;

$m = getURL(
  'http://api.map.com.tw/net/familyShop.aspx?'.http_build_query([
    "searchType"=>"ShopList",
    "type"=>"", 
    "city"=>"台北市",
    "area"=>"大同區",
    "road"=>"",
    "fun"=>"showStoreList",
    "key"=>"6F30E8BF706D653965BDE302661D1241F8BE9EBC",
  ], '', '&'),null,'http://www.family.com.tw');

echo $m."\n";




function getURL($url,$post=null,$referer=false){
   
 
  if (is_array($post)){
    ksort($post);
    $post=http_build_query($post, '', '&');
  }
  
  $ch = curl_init($url);   //更底層的擷取資料函式
  if($ch!==false){
    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36');
    //允許網頁跳轉
    curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
    curl_setopt($ch,CURLOPT_AUTOREFERER,1);
    //關閉SSL錯誤警告
    curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);

    if($referer)
    curl_setopt($ch, CURLOPT_REFERER, $referer);

    //curl_setopt($ch,CURLOPT_SSLVERSION,3);
    //設定cookies file
    if(isset($_SESSION['ckfile'])){
      curl_setopt($ch,CURLOPT_COOKIEFILE, $_SESSION['ckfile']); 
      curl_setopt($ch,CURLOPT_COOKIEJAR, $_SESSION['ckfile']);
    }

    //curl_setopt($ch, CURLOPT_HEADER, 1);
    //處理post資料
    if($post!=null){        
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST,'POST');
      curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
    }
    curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch,CURLOPT_NOSIGNAL,true);
    //設定time out
    $time_out=60*1000;
    curl_setopt($ch,CURLOPT_TIMEOUT_MS,$time_out);
    try {
      //echo "curl_exec....\n";
      if(($m = curl_exec($ch))===false){
        echo "curl_exec error: return false\n\turl:{$url}\n\tpost:{$post}\n";
        curl_close($ch);
        return false;
      }
      curl_close($ch);
      return $m;
    }
    catch(Exception $e){
      echo "curl_exec error\n";
      usleep(100000);
      return $this->getURL($url,$post);
    }
  }
  else{
    echo "curl_init error\n";
    usleep(100000);
    return $this->getURL($url,$post);
  }
}
