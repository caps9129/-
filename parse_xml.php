<?php

class XMLParseClass {

    var $xml_file, 
        $node_value,
        $arr_townNO = array(),
        $arr_storeinfo = array();

    public function __construct($xml_file){    
        $this->xml_file = simplexml_load_string($xml_file) or die("Error: Cannot create object");
        if ($this->xml_file === false) {
            print("Failed loading XML: \n");
            foreach(libxml_get_errors() as $error) {
                print($error->message ."\n");
            }
        } 
    }

    public function ParseXML($node1, $node2, $commandID){
        $count = 0;
        if($commandID == "GetTown"){
            $this->arr_townNO = array();
            while(isset($this->xml_file->$node1[$count]->$node2)){   
                $this->node_value = $this->xml_file->$node1[$count]->$node2;
                $this->node_value = (string)$this->node_value; // obj to string   
                $this->DeleteHtml($this->node_value);      
                array_push($this->arr_townNO, $this->node_value);
                $count++;
                
            }
        }
        else if($commandID == "SearchStore"){
            $this->arr_storeinfo = array();
                while(isset($this->xml_file->$node1[$count]->POIID)){   
                    foreach($node2 as $node_tag){
                        $this->node_value = $this->xml_file->$node1[$count]->$node_tag;
                        $this->node_value = (string)$this->node_value; // obj to string 
                        $this->DeleteHtml($this->node_value);   
                        array_push($this->arr_storeinfo, $this->node_value);
                    }
                    $count++;
                }
            
        }
        else{
            print("commandID not match!!!\n");
        }
    }

    public function DeleteHtml(&$str){
        $str = trim($str);
        $str = strip_tags($str,"");
        $str = str_replace("\t","",$str);
        $str = str_replace("\r\n","",$str); 
        $str = str_replace("\r","",$str); 
        $str = str_replace("\n","",$str); 
        $str = str_replace(" "," ",$str); 
        $str = str_replace("&nbsp;","",$str);
    }

    


}




?>