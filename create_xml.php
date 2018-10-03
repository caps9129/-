<?php

    ini_set("memory_limit",-1);

    define("DB_HOST", "140.109.161.93");
    define("DB_USER", "ntpc");
    define("DB_PASS", "ac6tmsks@a");
    define("DB_NAME", "ntpc");

    $DB_Table_Information = "architect_information";
    $DB_Table_Office = "architect_office";
    $DB_Table_Project = "architect_project";
    $DB_Table_Contractor = "building_contractor";

    do{
        printf("1.create architect information xml\n");
        printf("2.create architect office xml\n");
        printf("3.create architect project xml\n");
        printf("4.create building contractor xml\n");
        printf("5.create all xml\n");
        printf("0.EXIT\n");
        $choice = read_chioce();
        if($choice == 1){
            $DataBase = new DBClass();
            $DataBase->select_DB($DB_Table_Information);
            $DataBase->disconnect();
        }
        else if($choice == 2){
            $DataBase = new DBClass();
            $DataBase->select_DB($DB_Table_Office);
            $DataBase->disconnect();
        }
        else if($choice == 3){
            $DataBase = new DBClass();
            $DataBase->select_DB($DB_Table_Project);
            $DataBase->disconnect();
        }
        else if($choice == 4){
            $DataBase = new DBClass();
            $DataBase->select_DB($DB_Table_Contractor);
            $DataBase->disconnect();
        }
        else if($choice == 5){
            $DataBase = new DBClass();
            $DataBase->select_DB($DB_Table_Information);
            $DataBase->select_DB($DB_Table_Office);
            $DataBase->select_DB($DB_Table_Project);
            $DataBase->select_DB($DB_Table_Contractor);
            $DataBase->disconnect();
        }
    }while($choice != 0);
    
    exit;
        

    class DBClass {

        var $conn, $query, $result;
      
        
        public function __construct() {
            $this->connect();
        }
    
        public function disconnect() {
            mysqli_close($this->conn);
        }
    
        public function reconnect() {
            $this->disconnect();
            $this->connect();
        }
    
        public function connect() {
            $this->conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            
            if(!$this->conn){
                die("dbConnect fail". mysqli_connect_error()."\n");
                exit;
            }
            else{
                echo "dbConnect Successful!!<br>\n";
                if (!$this->conn->set_charset("utf8")) {
                    printf("Error loading character set utf8: %s\n", $this->conn->error);
                    
                } else {
                    printf("Current character set: %s\n", $this->conn->character_set_name());
                }  
            }
        }

        public function create_xml($DB_Table_Option, $data, $colnames) {
            
            $fp = fopen($DB_Table_Option.".xml", "w") or die ("Unable to open file!");
            
            fwrite($fp, '<?xml version="1.0" encoding="utf-8" ?>'.PHP_EOL);
           
            fwrite($fp, '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"'.PHP_EOL);
            fwrite($fp, "\t\n".'xmlns:archi="http://archi.sgis.tw/ontology/">'.PHP_EOL);  
            fwrite($fp, PHP_EOL);
            
      
            
            
            while($this->row = $data->fetch_assoc()){    
               
                
                foreach($colnames as $colname){  
                    
                    if($DB_Table_Option == "architect_information"){
                        
                        if($colname == "architect_ID"){
                            fwrite($fp, "\t".'<rdf:Description rdf:about="http://archi.sgis.tw/ontology/'.$this->row[$colname].'">'.PHP_EOL);
                        }
                        else if($colname == "project_ID"){
                            $arr_project = explode(";",$this->row[$colname]);
                            foreach($arr_project as $project_value){
                                if($project_value == "無相關資料"){
                                    fwrite($fp, "\t\t".'<archi:'.$colname.'>'.$project_value.'</archi:'.$colname.'>'.PHP_EOL);
                                }
                                else{
                                    fwrite($fp, "\t\t".'<archi:'.$colname.' rdf:resource="http://archi.sgis.tw/ontology/'.$project_value.'"/>'.PHP_EOL);
                                }
                            }
                        }
                        else{
                            fwrite($fp, "\t\t".'<archi:'.$colname.'>'.$this->row[$colname].'</archi:'.$colname.'>'.PHP_EOL);
                        }
                    }
                    else if($DB_Table_Option == "architect_office"){
                        if($colname == "office_ID"){
                            fwrite($fp, "\t".'<rdf:Description rdf:about="http://archi.sgis.tw/ontology/'.$this->row[$colname].'">'.PHP_EOL);
                        }
                        else if($colname == "architect_ID"){
                            fwrite($fp, "\t\t".'<archi:'.$colname.' rdf:resource="http://archi.sgis.tw/ontology/'.$this->row[$colname].'"/>'.PHP_EOL);
                        }
                        else if($colname == "project_ID"){
                            $arr_project = explode(";",$this->row[$colname]);
                            foreach($arr_project as $project_value){
                                if($project_value == "無相關資料"){
                                    fwrite($fp, "\t\t".'<archi:'.$colname.'>'.$project_value.'</archi:'.$colname.'>'.PHP_EOL);
                                }
                                else{
                                    fwrite($fp, "\t\t".'<archi:'.$colname.' rdf:resource="http://archi.sgis.tw/ontology/'.$project_value.'"/>'.PHP_EOL);
                                }
                            }
                        }
                        else{
                            fwrite($fp, "\t\t".'<archi:'.$colname.'>'.$this->row[$colname].'</archi:'.$colname.'>'.PHP_EOL);
                        }
                    }
                    else if($DB_Table_Option == "building_contractor"){
                        if($colname == "contractor_ID"){
                            fwrite($fp, "\t".'<rdf:Description rdf:about="http://archi.sgis.tw/ontology/'.$this->row[$colname].'">'.PHP_EOL);
                        }
                        else if($colname == "project_ID"){
                            $arr_project = explode(";",$this->row[$colname]);
                            foreach($arr_project as $project_value){
                                if($project_value == "無相關資料"){
                                    fwrite($fp, "\t\t".'<archi:'.$colname.'>'.$project_value.'</archi:'.$colname.'>'.PHP_EOL);
                                }
                                else{
                                    fwrite($fp, "\t\t".'<archi:'.$colname.' rdf:resource="http://archi.sgis.tw/ontology/'.$project_value.'"/>'.PHP_EOL);
                                }
                            }
                        }
                        else{
                            fwrite($fp, "\t\t".'<archi:'.$colname.'>'.$this->row[$colname].'</archi:'.$colname.'>'.PHP_EOL);
                        }

                    }
                    else if($DB_Table_Option == "architect_project"){
                        if($colname == "architect_ID"){
                            //print($this->row['license_type'].'_'.$this->row[$colname]);
                            fwrite($fp, "\t".'<rdf:Description rdf:about="http://archi.sgis.tw/ontology/'.$this->row['license_type'].'_'.$this->row[$colname].'">'.PHP_EOL);
                        }
                        else if($colname == "designer"){
                            if(strpos($this->row[$colname], "證") != false){
                                
                                fwrite($fp, "\t\t".'<archi:'.$colname.' rdf:resource="http://archi.sgis.tw/ontology/'.$this->row[$colname].'"/>'.PHP_EOL);
                            }
                            else{
                                fwrite($fp, "\t\t".'<archi:'.$colname.'>'.$this->row[$colname].'</archi:'.$colname.'>'.PHP_EOL);
                            }
                        }
                        else if($colname == "supervisor"){
                            if(strpos($this->row[$colname], "第") != false){
                                fwrite($fp, "\t\t".'<archi:'.$colname.' rdf:resource="http://archi.sgis.tw/ontology/'.$this->row[$colname].'"/>'.PHP_EOL);
                            }
                            else{
                                fwrite($fp, "\t\t".'<archi:'.$colname.'>'.$this->row[$colname].'</archi:'.$colname.'>'.PHP_EOL);
                            }
                        }
                        else if($colname == "contractor"){
                            if(strpos($this->row[$colname], "_") != false){
                                fwrite($fp, "\t\t".'<archi:'.$colname.' rdf:resource="http://archi.sgis.tw/ontology/'.$this->row[$colname].'"/>'.PHP_EOL);
                            }
                            else{
                                fwrite($fp, "\t\t".'<archi:'.$colname.'>'.$this->row[$colname].'</archi:'.$colname.'>'.PHP_EOL);
                            }
                        }
                        else if($colname == "license_url"){
                            fwrite($fp, "\t\t".'<archi:'.$colname.'>'.urlencode($this->row[$colname]).'</archi:'.$colname.'>'.PHP_EOL);
                        }
                        else if($colname == "year"){
                            if(strpos($this->row[$colname], "&") != false){
                                fwrite($fp, "\t\t".'<archi:'.$colname.'>'.urlencode($this->row[$colname]).'</archi:'.$colname.'>'.PHP_EOL);
                            }
                            else{
                                fwrite($fp, "\t\t".'<archi:'.$colname.'>'.$this->row[$colname].'</archi:'.$colname.'>'.PHP_EOL);
                            }         
                        }
                        else{
                            fwrite($fp, "\t\t".'<archi:'.$colname.'>'.$this->row[$colname].'</archi:'.$colname.'>'.PHP_EOL);
                        }
                    }

                    
                }
                fwrite($fp, "\t".'</rdf:Description>'.PHP_EOL);
                
            }

          
            fwrite($fp, '</rdf:RDF>');
            printf($DB_Table_Option.'.xml create complete'."\n");

            
        }
    
    
        public function select_DB($DB_Table_Option) {
    
            $this->colname = array();
            
            $this->colname = $this->get_column_name($DB_Table_Option); //get table column name
            
            $this->sql = "SELECT * from $DB_Table_Option"; 
            $this->result = $this->conn->query($this->sql);
    
            if(!$this->result){
                
                if(!mysqli_ping($this->conn)){
                    $this->reconnect();
                    $this->update_DB();
                }
                else if($this->rows->num_rows == 0){
                    echo "0 results\n";
                    exit;
                }
                else{
                    echo "SQL Error: " . mysqli_error($this->conn)."\n";
                    exit;
                }
            }
            else{
                $this->create_xml($DB_Table_Option, $this->result, $this->colname);
   
            }
           
        }

        public function get_column_name($DB_Table_Option){
            
            $this->sql = "DESCRIBE $DB_Table_Option";
            $this->result = $this->conn->query($this->sql);
            $this->colname = array();

            if(!$this->result){
                
                if(!mysqli_ping($this->conn)){
                    $this->reconnect();
                    $this->update_DB();
                }
                else if($this->rows->num_rows == 0){
                    echo "0 results\n";
                    exit;
                }
                else{
                    echo "SQL Error: " . mysqli_error($this->conn)."\n";
                    exit;
                }
            }
            else{
                while($this->row = $this->result->fetch_assoc()){
                    //printf($this->row['Field']."\n");
                    array_push($this->colname,$this->row['Field']);
                }
            }

            return $this->colname;
            //print_r($this->colname);

        }

        
    
    }

    function read_chioce(){
    
        $fp1=fopen("php://stdin", "r");
        $input=fgets($fp1, 255);
        fclose($fp1);
    
        return $input;
    }


?>