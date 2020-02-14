<?php
//WIP
//class needed to start connection to database server
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    public function __construct($database="", $username="", $password="",$host="localhost") {
        $this->host = $host;
        $this->db_name = $database;
        $this->username = $username;
        $this->password = $password;
        $this->conn = @mysqli_connect($this->host, $this->username, $this->password,$this->db_name); //connect to database
    }

    public function __destruct() {
        // check if link exists
        // if it does close it
        if(mysqli_connect_errno()==0){
            mysqli_close($this->conn);
        }
    }

    public function getLink() {
        return $this->conn; //returns the link needed to execute queries
    }
}
?>