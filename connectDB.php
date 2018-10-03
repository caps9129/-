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

    public function select_modal(){
        $this->select();
        foreach($this->select_result as $select_index => $select_result){
            $column = 0;
            if($select_result['selected'] == 0){   
                array_push($this->select_modal_result, $select_result);
            }
        }
        $this->temp_select_modal_result = $this->select_modal_result;

    }    

    public function update_modal($arr_post){
        if(!mysqli_ping($this->conn)){
            $this->reconnect();
            $this->update_modal($arr_post);
        }
        foreach($arr_post as $post_value){
            $this->query = "UPDATE `report` SET selected = '1' where `ID` = N'$post_value[ID]'";
            $this->conn->query($this->query);
        }
    }

    public function update_unclosed($arr_post){
        
        if(!mysqli_ping($this->conn)){
            $this->reconnect();
            $this->update_unclosed($arr_post);
        }
        
        foreach($arr_post as $post_value){
            $this->query = "UPDATE `report` SET status = '1' where `id` = N'$post_value'";
            $this->conn->query($this->query);
        }
    }

    public function update_closed($arr_post){
        
        if(!mysqli_ping($this->conn)){
            $this->reconnect();
            $this->update_closed($arr_post);
        }
        
        foreach($arr_post as $post_value){
            $this->query = "UPDATE `report` SET status = '0' where `id` = N'$post_value'";
            $this->conn->query($this->query);
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

    public function post_insert($arr_postdata) {

        if(!mysqli_ping($this->conn)){
            $this->reconnect();
            $this->post_insert($arr_postdata);
        }

        $this->query = "INSERT INTO `report` (ID, type, user, description, address, lan, lat, status)
        VALUES (N'$arr_postdata[0]', N'$arr_postdata[1]', N'$arr_postdata[2]', N'$arr_postdata[3]', N'$arr_postdata[7]', N'$arr_postdata[4]', N'$arr_postdata[5]', N'$arr_postdata[6]')";
        
        if(!mysqli_query($this->conn, $this->query)){

            if(strpos(mysqli_error($this->conn),"key 'PRIMARY'")!==false){
                
                $this->query = "valueUPDATE `report` SET type = N'$arr_postdata[1]', user = N'$arr_postdata[2]', description = N'$arr_postdata[3]',address = N'$arr_postdata[7]', lan= N'$arr_postdata[4]', 
                lat = N'$arr_postdata[5]', status = N'$arr_postdata[6]' where ID= N'$arr_postdata[0]'";

                if(mysqli_query($this->conn, $this->query)){
                    echo "Update ".$arr_postdata[0]." complete<br>\n";
                }
                else{
                    $this->post_insert($arr_postdata);
                }
            }
            else{
                echo "SQL Error: " . mysqli_error($this->conn)."\n";

            }

        }
        else{
            echo "Insert ".$arr_postdata[0]." complete<br>\n";
        }
    }
    
    //insert record
    public function insert($data) {

        if(!mysqli_ping($this->conn)){
            $this->reconnect();
            $this->insert($data);
        }
   
      

        $this->sql = "INSERT INTO `7-11` (id, name, lng, lat, telno, faxno, address, service, start_time, end_time)
        VALUES (N'$data[0]', N'$data[1]', N'$data[2]', N'$data[3]', N'$data[4]', N'$data[5]', N'$data[6]', N'$data[7]', N'$data[8]', N'$data[8]')";

        if(!mysqli_query($this->conn, $this->sql)){
            
            if(strpos(mysqli_error($this->conn),"key 'PRIMARY'")!==false){
                
                $this->query = "UPDATE `7-11` SET name = N'$data[1]', lng = N'$data[2]', lat = N'$data[3]', telno= N'$data[4]', 
                faxno = N'$data[5]', address = N'$data[6]', service = N'$data[7]', end_time = N'$data[8]' where id= N'$data[0]'";

                if(mysqli_query($this->conn, $this->query)){
                    echo "Update in 7-11: ".$data[0]." complete<br>\n";
                }
                else{
                    $this->insert($data);
                }
            }
            else{
                echo "SQL Error: " . mysqli_error($this->conn)."\n";
            }
        }
        else{
            echo "Insert in 7-11: ".$data[0]." complete<br>\n";
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