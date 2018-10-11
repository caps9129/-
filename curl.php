<?php


class CurlClass {

    var $cookie_file = "valid.tmp",
        $timeout = 60 * 10000,
        $html;

    public function __construct($cookie_url) {
        print("cookie write in......\n");
        //$this->GetCookie($cookie_url);
    }

    public function GetCookie($cookie_url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $cookie_url); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //以輸出文件的方式取代直接輸出
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->timeout);  //等待瀏覽器的回應時間
        curl_setopt($curl,CURLOPT_COOKIEFILE,$this->cookie_file); 
        curl_setopt($curl,CURLOPT_COOKIEJAR,$this->cookie_file); //獲取COOKIE並存儲
        $contents = curl_exec($curl);
        curl_close($curl);
    }
    

    public function GetHTML($curl_url, $post_argument = null, $referer = false) {
        
        if($post_argument != null){
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_POST,1); //開啟POST
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST,'POST');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $post_argument);  //傳遞要求參數給伺服器
        }
        else{
            $curl = curl_init($curl_url);
        }
        
        curl_setopt($curl, CURLOPT_URL, $curl_url);
        curl_setopt($curl,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.95 Safari/537.36');
        curl_setopt($curl, CURLOPT_HEADER, false);
        //允許網頁跳轉
        curl_setopt($curl,CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($curl,CURLOPT_AUTOREFERER,1);
        //關閉SSL錯誤警告
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,2);
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
        if($referer)
        curl_setopt($curl, CURLOPT_REFERER, $referer);

        if(isset($_SESSION['ckfile'])){
            curl_setopt($ch,CURLOPT_COOKIEFILE, $_SESSION['ckfile']); 
            curl_setopt($ch,CURLOPT_COOKIEJAR, $_SESSION['ckfile']);
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);  
        curl_setopt($curl, CURLOPT_NOSIGNAL,1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->timeout);  //等待瀏覽器的回應時間
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
        

       
        
        $info = curl_getinfo($curl);
        $this->html = curl_exec($curl);
        if(!$this->html){
            echo 'Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url']."\n";
            usleep(100000);
            $this->GetHTML($curl_url, $post_argument);
        }
        $curl_errno = curl_errno($curl);  
        $curl_error = curl_error($curl);
        if($curl_errno > 0){  
            echo "cURL Error ($curl_errno): $curl_error\n";  
            // $this->GetHTML($curl_url, $post_argument);
        }
        curl_close($curl);

    }
}



?>