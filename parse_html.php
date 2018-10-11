<?php

class HTMLParseClass {

    var $html_file, 
        $node_value,
        $str_h1,
        $str_info,
        $arr_storeinfo = array(),
        $arr_county = array();

    public function __construct($html_file){    
        $this->html_file = str_get_html($html_file);
    }

    public function ParseStoreINFO(){

        $this->arr_storeinfo = array();
        
    

        if($this->html_file){


            $table = $this->html_file->find('table', 0);

            foreach($table->find('tr') as $row){

                foreach($row->find('th') as $name){
                    array_push($this->arr_storeinfo, $name->plaintext);
                    // var_dump($name->plaintext);
                }

                foreach($row->find('td') as $cell) {
                    $total_service = "";
                    foreach($cell->find('a') as $addr){
                        array_push($this->arr_storeinfo, $addr->plaintext);
                        // var_dump($addr->plaintext);
                    }

                }
                                    
                foreach($row->find('td img') as $service){
                        
                    if($service->title){

                        if($total_service == ""){
                            $total_service =$total_service.$service->title;
                        }
                        else{
                            $total_service =$total_service."、".$service->title;
                    
                        }
                    }
                }  
                if($total_service){
                    array_push($this->arr_storeinfo, $total_service);
                }
                else{
                    array_push($this->arr_storeinfo, "NA");
                }
            
            
         
                foreach($row->find('td[width=120]') as $tel){
                    // var_dump($cell->plaintext);
                    array_push($this->arr_storeinfo, $tel->plaintext);
                }
                

            }
                
            

        }
    }  

    public function ParseCounty(){

        $this->arr_storeinfo = array();
        $this->arr_county = array();

        if($this->html_file){


            $result = $this->html_file->find('select[id=AREA]');
            foreach($result as $element){

                $options = $element->find('option');
               
                foreach($options as $options_value) {

                    array_push($this->arr_county, $options_value->plaintext);

                    // echo $options_value->plaintext . '<br>';
                } 
            }
                
            

        }
    }

    public function ParseHTML($arr_node){
        // print($this->html_file);
        $this->arr_storeinfo = array();
        // var_dump(empty($this->arr_storeinfo));
        if($this->html_file){
            $count = 0;
            foreach($this->html_file->find($arr_node[0]) as $element){
                $this->str_h1 = (string)$element->plaintext;
                $this->str_h1 = substr($this->str_h1, 0, -9);
                $this->str_h1 = rtrim($this->str_h1);
                // print($this->str_h1."\n");
                if(strlen($this->str_h1)){
                    array_push($this->arr_storeinfo, $this->str_h1);
                    foreach($this->html_file->find($arr_node[1]) as $ulelement){
                        
                        foreach($ulelement->find($arr_node[2]) as $lielement){
                            $this->str_info = (string)$lielement->plaintext;
                            $this->str_info = $this->DeleteHtml($this->str_info);
                            // print($this->str_info."\n");
                            if(preg_match('/([\x{4e00}-\x{9fa5}]+)([\W]+)([^X]+)/u' ,$this->str_info ,$matches)){
                                array_push($this->arr_storeinfo, $matches[3]);
                            }
                            else{
                                if($count == 4){

                                }
                                else
                                    array_push($this->arr_storeinfo, 'NA');
                            }
                            $count ++;
                            // print($this->str_info."\n");
                            // print($count."\n");
                        }
                    }
                }
                else{

                }
                
            }
        }
        

    }

    public function DeleteHtml($str){
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

    


}




?>