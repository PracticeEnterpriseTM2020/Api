<?php
// WIP
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    include_once "classes/database.php";
    include_once "classes/customer.php";
    // create a database object
    $db = new Database("pract_ent","webgebruiker","labo2019");
    // get link to the database from database object
    $link=$db->get_link();
    // if the link is successfull make a customer
    if($link != false){
        if(isset($_POST['email'])){
            $customer = new Customer($link);
            $customer->__set("email",$_POST['email']);
        }
        else{
            // stop the code and send an error
            die(json_encode(array("message"=>"missing argument: email","success"=>false)));
        }
        // execute read one (returns an array)
        $json = $customer->read_one();
        // check if the searched customer exists
        if($json["customer"]["firstname"] != null){
            $json["success"] = true;
            http_response_code(200);
            echo(json_encode($json));
        }
        else{
            // if not return error
            http_response_code(404);
            echo(json_encode(array("message" => "No products found.","success"=>false)));
        }
    }
    else{
        // if connection with database is not successfull send error
        http_response_code(404);
        echo(json_encode(array("message" => "No connection with database.","success"=>false)));
    }
?>