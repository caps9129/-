<?php



ini_set('memory_limit', '-1');
ini_set('max_execution_time','0');


define("DB_HOST", "db.sgis.tw");
define("DB_USER", "cStore");
define("DB_PASS", "dVuPKqFRSm2eUob8");
define("DB_NAME", "conStore");



 
// $DataBase = new DBClass();

class DBClass {

    var $conn, $query, $result, $sql, 
        $select_result = array(),
        $select_modal_result = array(),
        $temp_select_modal_result = array(),
        $json_select_result;
       
  
    
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
            if (!$this->conn->set_charset("utf8")) {
                printf("Error loading character set utf8: %s\n", $this->conn->error);
            } else {
                print("dbConnect successful\n");
            }  
        }
    }



    public function select(){

        if(!mysqli_ping($this->conn)){
            $this->reconnect();
            $this->select();
        }
        
        $this->query = "SELECT ID, type, user, description, address, status, selected, log FROM `report` order by log DESC";
        $this->result = $this->conn->query($this->query);

        if($this->result->num_rows <= 0){
            
            if(!mysqli_ping($this->conn)){
                $this->reconnect();
                $this->select();
            }
            else if($this->result->num_rows == 0){
                echo "0 results\n";
                exit;
            }
            else{
                echo "SQL Error: " . mysqli_error($this->conn)."\n";
                exit;
            }
        }

        $count = 0;

        while($row = $this->result->fetch_assoc()){
  
            array_push($this->select_result, $row);
         
            
        }

        //return $log;
        //return $this->select_result;
    }
    //insert record
    public function insert($data, $DB_table) {

        if(!mysqli_ping($this->conn)){
            $this->reconnect();
            $this->insert($data, $DB_table);
        }
   
        //print_r($data);

        if($DB_table == "7-11"){

            $this->sql = "INSERT INTO `$DB_table` (id, name, lng, lat, telno, faxno, address, service, start_time, end_time)
            VALUES (N'$data[0]', N'$data[1]', N'$data[2]', N'$data[3]', N'$data[4]', N'$data[5]', N'$data[6]', N'$data[7]', N'$data[8]', N'$data[8]')";

            $this->query = "UPDATE `$DB_table` SET name = N'$data[1]', lng = N'$data[2]', lat = N'$data[3]', telno = N'$data[4]', 
            faxno = N'$data[5]', address = N'$data[6]', service = N'$data[7]', end_time = N'$data[8]' where id = N'$data[0]'";
        }
        else if($DB_table == "family"){
            
            $data[0] = $data['SERID'];

            if($data['twoice'] == "")
            $data['twoice'] = 'NA';

            $this->sql = "INSERT INTO `$DB_table` (id, name, lng, lat, telno, postel, address, service, pkey, oldpkey, post, twoice, start_time, end_time)
            VALUES (N'$data[SERID]', N'$data[NAME]', N'$data[px]', N'$data[py]', N'$data[TEL]', N'$data[POSTel]', N'$data[addr]', N'$data[all]', N'$data[pkey]', N'$data[oldpkey]', N'$data[post]', N'$data[twoice]', N'$data[datetime]', N'$data[datetime]')";

            $this->query = "UPDATE `$DB_table` SET name = N'$data[NAME]', lng = N'$data[px]', lat = N'$data[py]', telno = N'$data[TEL]', postel = N'$data[POSTel]',
            address = N'$data[addr]', service = N'$data[all]', pkey = N'$data[pkey]', oldpkey = N'$data[oldpkey]', post = N'$data[post]', twoice = N'$data[twoice]', end_time = N'$data[datetime]' where id= N'$data[SERID]'";

        }
        else if($DB_table == "ok-mart"){
            
            $this->sql = "INSERT INTO `$DB_table` (id, name, lng, lat, telno, address, service, start_time, end_time)
            VALUES (N'$data[3]', N'$data[0]', N'$data[6]', N'$data[5]', N'$data[2]', N'$data[1]', N'$data[4]', N'$data[7]', N'$data[7]')";

            $this->query = "UPDATE `$DB_table` SET name = N'$data[0]', lng = N'$data[6]', lat = N'$data[5]', telno = N'$data[2]',
            address = N'$data[1]', service = N'$data[4]', end_time = N'$data[7]' where id= N'$data[3]'";

        }
        if(!mysqli_query($this->conn, $this->sql)){
            
            
            if(strpos(mysqli_error($this->conn),"key 'PRIMARY'")!==false){
                

                if(mysqli_query($this->conn, $this->query)){
                    echo "Update in ".$DB_table.": ".$data[0]." complete<br>\n";
                }
                else{
                    
                    $this->insert($data, $DB_table);
                }
            }
            else{
                echo "SQL Error: " . mysqli_error($this->conn)."\n";
            }
        }
        else{
            echo "Insert in ".$DB_table.": ".$data[0]." complete<br>\n";
        }

        
    }

    public function search($arr_post) {

        if(!mysqli_ping($this->conn)){
            $this->reconnect();
            $this->select();
        }

        $this->query = "SELECT * FROM $arr_post[0] WHERE `address` LIKE '%$arr_post[1]%'";
        $this->result = $this->conn->query($this->query);

        // print($this->query);

        if($this->result->num_rows <= 0){
            
            if(!mysqli_ping($this->conn)){
                $this->reconnect();
                $this->select();
            }
            else if($this->result->num_rows == 0){
                echo "0 results\n";
                exit;
            }
            else{
                echo "SQL Error: " . mysqli_error($this->conn)."\n";
                exit;
            }
        }

        while($row = $this->result->fetch_assoc()){
  
            array_push($this->select_result, $row);
            
        }

        $this->json_select_result = json_encode($this->select_result, JSON_UNESCAPED_UNICODE);
        
        echo $this->json_select_result;

    }



}



?>