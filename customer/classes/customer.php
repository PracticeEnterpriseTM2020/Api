<?php
//WIP
//class customer
class Customer
{
    private $lastname;
    private $firstname;
    private $email;
    private $password;
    private $number;
    private $postalCode;
    private $city;
    private $address;
    private $conn;

    public function __construct($connection = "", $addr = "", $number = "", $city = "", $postalCode = "", $email = "", $lastname = "", $firstname = "", $pass = "")
    {
        // constructor for customer
        $this->conn = $connection;
        $this->lastname = mysqli_escape_string($this->conn, $lastname);
        $this->firstname = mysqli_escape_string($this->conn, $firstname);
        $this->email = mysqli_escape_string($this->conn, $email);
        $this->password = mysqli_escape_string($this->conn, $pass);
        $this->address = mysqli_escape_string($this->conn, $addr);
        $this->number = mysqli_escape_string($this->conn, $number);
        $this->city = mysqli_escape_string($this->conn, $city);
        $this->postalCode = mysqli_escape_string($this->conn, $postalCode);
    }

    public function __set($name, $value)
    {
        $val = mysqli_escape_string($this->conn, $value);
        switch ($name) {
            case "id":
                $this->id = $val;
                break;
            case "lastname":
                $this->lastname = $val;
                break;
            case "firstname":
                $this->firstname = $val;
                break;
            case 'email':
                $this->email = $val;
                break;
            case 'city':
                $this->city = $val;
                break;
            case 'address':
                $this->address = $val;
                break;
            case 'number':
                $this->number = $val;
                break;
            case 'postalCode':
                $this->postalCode = $val;
                break;
        }
    }

    public function __get($name)
    {
        switch ($name) {
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
            case 'city':
                return $this->city;
                break;
            case 'address':
                return $this->address;
                break;
            case 'number':
                return $this->number;
                break;
            case 'postalCode':
                return $this->postalCode;
                break;
        }
    }
    public function addAddress()
    {
        $query = "INSERT INTO address(address,number,city,postalCode,custId) SELECT ?,?,?,?,(SELECT id FROM customer WHERE customer.email = ?) FROM DUAL WHERE NOT EXISTS (SELECT * FROM address WHERE address=? and number=? and city=? and postalCode=?)";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, "sssssssss", $this->address, $this->number, $this->city, $this->postalCode, $this->email, $this->address, $this->number, $this->city, $this->postalCode);
        mysqli_stmt_execute($stmt);
        //returns true if query was successfull
        if (mysqli_stmt_error($stmt) == "" && mysqli_affected_rows($this->conn)>0) {
            return true;
        } else {
            return false;
        }
    }

    public function readOne()
    {
        // query reads one customer
        $stmt = mysqli_prepare($this->conn, "SELECT * FROM `customer` LEFT JOIN address on customer.id = address.custId WHERE email =?");
        mysqli_stmt_bind_param($stmt, "s", $this->email);
        mysqli_stmt_execute($stmt);
        $val = mysqli_stmt_get_result($stmt);
        // get only associative data
        $row = mysqli_fetch_array($val, MYSQLI_ASSOC);
        if ($row != null) {
            extract($row);
            // format data first row for json encoding
            $res["customer"] = array(
                "id" => $id,
                "lastname" => $lastname,
                "firstname" => $firstname,
                "email" => $email,
                "password" => $pass,
                "addresses" => array(
                    array(
                        "street" => $address,
                        "number" => $number,
                        "city" => $city,
                        "postalCode" => $postalCode
                    )
                )
            );
            // format all other rows (addresses)
            while ($cust = mysqli_fetch_array($val)) {
                extract($cust);
                $adr = array(
                    "street" => $address,
                    "number" => $number,
                    "city" => $city,
                    "postalCode" => $postalCode
                );
                array_push($res["customer"]["addresses"], $adr);
            }
        } else {
            $res = null;
        }
        // return data
        return $res;
    }
    public function readOneLogin()
    {
        // query reads one customer
        $stmt = mysqli_prepare($this->conn, "SELECT email,pass FROM customer WHERE email=?");
        mysqli_stmt_bind_param($stmt, "s", $this->email);
        mysqli_stmt_execute($stmt);
        $val = mysqli_stmt_get_result($stmt);
        // get only associative data
        $row = mysqli_fetch_array($val, MYSQLI_ASSOC);
        if ($row != null) {
            extract($row);
            // format data for json encoding
            $res["customer"] = array(
                "email" => $email,
                "password" => $pass
            );
        } else {
            $res = null;
        }
        // return data
        return $res;
    }


    public function readAll()
    {
        // gets all customers
        $query = "SELECT customer.id,customer.firstname,customer.lastname,customer.email,address.address,address.number,address.city,address.postalCode FROM customer left join address on customer.id = address.custId ORDER BY customer.id";
        $val = mysqli_query($this->conn, $query);
        // return query result
        return $val;
    }

    public function readAllLogin()
    {
        // gets all customers
        $query = "SELECT email,pass from customer";
        $val = mysqli_query($this->conn, $query);
        // return query result
        return $val;
    }

    public function create()
    {
        // makes a new customer WIP
        $stmt = mysqli_prepare($this->conn, "INSERT INTO customer (firstname,lastname,email,pass) VALUES (?,?,?,?)");
        mysqli_stmt_bind_param($stmt, "ssss", $this->firstname, $this->lastname, $this->email, $this->password);
        mysqli_stmt_execute($stmt);
        //returns true if query was successfull
        if (mysqli_stmt_error($stmt) == "") {
            return true;
        } else {
            return false;
        }
    }
}
