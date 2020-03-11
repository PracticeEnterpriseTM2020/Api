<?php
//WIP
//class customer
class Customer {
    private $lastname;
    private $firstname;
    private $email;
    private $password;
    private $conn;

    public function __construct($connection="",$lastname="",$firstname="",$email="",$pass="") {
        // constructor for customer
        $this->conn = $connection;
        $this->lastname = mysqli_escape_string($this->conn,$lastname);
        $this->firstname = mysqli_escape_string($this->conn,$firstname);
        $this->email = mysqli_escape_string($this->conn,$email);
        $this->password = mysqli_escape_string($this->conn,$pass);
    }

    public function __set($name, $value)
    {
        $val = mysqli_escape_string($this->conn,$value);
        switch($name){
            case "id":
            $this->id=$val;
            break;
            case "lastname":
            $this->lastname=$val;
            break;
            case "firstname":
            $this->firstname=$val;
            break;
            case 'email':
            $this->email=$val;
            break;
        }
    }

    public function __get($name)
    {
        switch($name){
            case 'id':
            return $this->id;
            break;
            case 'lastname':
            return $this->lastname;
            break;
            case 'firstname':
            return $this->firstname;
            break;
            case 'email':
            return $this->email;
            break;
        }
    }

    public function read_one() {
        // query reads one customer
        $stmt=mysqli_prepare($this->conn,"SELECT * FROM customer WHERE email=?");
        mysqli_stmt_bind_param($stmt,"s",$this->email);
        mysqli_stmt_execute($stmt);
        $val = mysqli_stmt_get_result($stmt);
        // get only associative data
        $row = mysqli_fetch_array($val,MYSQLI_ASSOC);
        if($row != null){
            extract($row);
            // format data for json encoding
            $res["customer"] = array(
                "id"=>$id,
                "lastname"=>$lastname,
                "firstname"=>$firstname,
                "email"=>$email,
                "password" => $pass);
        }
        else{
            $res=null;
        }
        // return data
        return $res;
    }


    public function read_all() {
        // gets all customers
        $query = "SELECT * from customer";
        $val = mysqli_query($this->conn,$query);
        // return query result
        return $val;
    }
    public function create() {
        // makes a new customer WIP
        $stmt=mysqli_prepare($this->conn,"INSERT INTO customer (firstname,lastname,email,pass) VALUES (?,?,?,?)");
        mysqli_stmt_bind_param($stmt,"ssss",$this->firstname,$this->lastname,$this->email,$this->password);
        mysqli_stmt_execute($stmt);
        //returns true if query was successfull
        if(mysqli_stmt_error($stmt)==""){
            return true;
        }
        else{
            return false;
        }
    }

}
?>