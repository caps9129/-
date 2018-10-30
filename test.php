<?php

class test
{

    public $host;
    public $user;
    public $password;
    public $dbname;

    public $conn = null;

    public function __construct()
    {
        $this->host = "db.sgis.tw";
        $this->user = "cStore";
        $this->password = "dVuPKqFRSm2eUob8";
        $this->dbname   = 'conStore';

        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->dbname);

        if ($this->conn->connect_error) {
            die("连接失败: " . $this->conn->connect_error);
        }
        $this->conn->query('set names utf8mb4');


    }

    public function mysql()
    {
        $sql = 'INSERT INTO info(id,content) VALUES(1,"😄呵呵😁")';

        if ($this->conn->query($sql) === TRUE) {
            $rest = $this->conn->insert_id;
        } else {
            $errorInfo = $this->conn->error;
            $rest = $errorInfo;
        }
        print_r($rest);
    }

}

$mongoData = new test();

$mongoData->mysql();

?>

