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
            if ( TRUE !== $this->conn->set_charset( 'utf8mb4' ) )
                throw new \Exception( $this->conn->error, $this->conn->errno );
        
            if ( TRUE !== $this->conn->query( 'SET collation_connection = @@collation_database;' ) )
            throw new \Exception( $this->conn->error, $this->conn->errno );
    
            echo 'character_set_name: ', $this->conn->character_set_name(), '<br />', PHP_EOL;
            
            foreach( $this->conn->query( "SHOW VARIABLES LIKE '%_connection';" )->fetch_all() as $setting )
            echo $setting[0], ': ', $setting[1], '<br />', PHP_EOL;

            
        }
    }



    public function select($table_name){

        if(!mysqli_ping($this->conn)){
            $this->reconnect();
            $this->select();
        }
        
        $this->query = "SELECT name, id FROM `$table_name` order by log DESC";
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
    public function insert($data, $DB_table, $comment = false) {

        if(!mysqli_ping($this->conn)){
            $this->reconnect();
            $this->insert($data, $DB_table);
        }
   
        if($comment){

            $this->query = "UPDATE `$DB_table` SET rating = N'$data[0]', comment_count = N'$data[1]', comment = N'$data[2]' where id = N'$data[3]'";
        }

        else if($DB_table == "7-11"){
            
            $this->sql = "INSERT INTO `$DB_table` (id, name, lng, lat, telno, faxno, address, service, start_time, end_time)
            VALUES (N'$data[0]', N'$data[1]門市', N'$data[2]', N'$data[3]', N'$data[4]', N'$data[5]', N'$data[6]', N'$data[7]', N'$data[8]', N'$data[8]')";

            $this->query = "UPDATE `$DB_table` SET name = N'$data[1]門市', lng = N'$data[2]', lat = N'$data[3]', telno = N'$data[4]', 
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
               
            if(count($data) == 12){
            
                $this->sql = "INSERT INTO `$DB_table` (id, name, lng, lat, telno, address, service, start_time, end_time)
                VALUES (N'$data[3]', N'$data[0]', N'$data[10]', N'$data[9]', N'$data[2]', N'$data[1]', N'$data[7]', N'$data[11]', N'$data[11]')";

                $this->query = "UPDATE `$DB_table` SET name = N'$data[0]', lng = N'$data[9]', lat = N'$data[10]', telno = N'$data[2]',
                address = N'$data[1]', service = N'$data[7]', end_time = N'$data[11]' where id= N'$data[3]'";
            }
        }
        else if($DB_table == "hi-life"){

           
            $this->query = "UPDATE `$DB_table` SET rating = N'$data[0]', comment_count = N'$data[1]', comment = N'$data[2]' where id= N'$data[3]'";
        
        
            $this->sql = "INSERT INTO `$DB_table` (id, name, lng, lat, telno, address, service, start_time, end_time)
            VALUES (N'$data[0]', N'$data[1]', N'$data[6]', N'$data[5]', N'$data[4]', N'$data[2]', N'$data[3]', N'$data[7]', N'$data[7]')";

            $this->query = "UPDATE `$DB_table` SET name = N'$data[1]', lng = N'$data[6]', lat = N'$data[5]', telno = N'$data[4]',
            address = N'$data[2]', service = N'$data[3]', end_time = N'$data[7]' where id= N'$data[0]'";
            
        }
        else{
            print("No match table!!!!!!\n");
            exit;
        }
        if($comment){

            // $comment = '[["2j/雨曦","1","他忘記我的熱美式：（","1540078274"],["Elmer Tan","5","人非常多，店員依舊盡心盡力服務~👍","1503491074"],["Andy Hung","4","2016-12 每年跨年都會拆門的門市，沒有內用座位","1482647288"],["kenny gi","4","就7-11 整體佳","1516042378"],["Cliff
            // Kung","5","即使人潮眾多店員依舊努力服務 , 大推","1484040469"]]';
            // $this->query = "UPDATE test SET comment = '$comment' where id = 5000";

            if(mysqli_query($this->conn, $this->query)){
                echo "Update in ".$DB_table.": ".$data[3]." complete<br>\n";
                // exit;
            }
            else{
                // print(mysqli_error($this->conn)."\n");
                // exit;
                $data[2] = mysqli_real_escape_string($this->conn, $data[2]);

                $this->query = "UPDATE `$DB_table` SET rating = N'$data[0]', comment_count = N'$data[1]', comment = '$data[2]'  where id = N'$data[3]'";
                if(mysqli_query($this->conn, $this->query)){
                    echo "Update in ".$DB_table.": ".$data[3]." complete<br>\n";
                }
                else{
                    print(mysqli_error($this->conn)."\n");
                    exit;
                }
                //$this->insert($data, $DB_table, true);
            }

        }

        else if(!mysqli_query($this->conn, $this->sql)){
            
            
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