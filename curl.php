<?php


class CurlClass {

    var $cookie_file = "valid.tmp",
        $timeout = 10,
        $html;

    public function __construct($cookie_url) {
        print("cookie write in......\n");
        $this->GetCookie($cookie_url);
    }

    public function GetCookie($cookie_url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $cookie_url); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //以輸出文件的方式取代直接輸出
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->timeout);  //等待瀏覽器的回應時間
        curl_setopt($curl,CURLOPT_COOKIEJAR,$this->cookie_file); //獲取COOKIE並存儲
        $contents = curl_exec($curl);
        curl_close($curl);
    }
    

    public function GetXML($curl_url, $post_argument) {
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $curl_url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);  
        curl_setopt($curl, CURLOPT_NOSIGNAL,1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->timeout);  //等待瀏覽器的回應時間
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($curl, CURLOPT_POST,1); //開啟POST
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_argument);  //傳遞要求參數給伺服器
        curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookie_file);
        $info = curl_getinfo($curl);
        $this->html = curl_exec($curl);
        if(!$this->html){
            echo 'Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url'];
        }
        $curl_errno = curl_errno($curl);  
        $curl_error = curl_error($curl);
        if($curl_errno > 0){  
            echo "cURL Error ($curl_errno): $curl_error\n";  
        }
        curl_close($curl);

    }
}



?>