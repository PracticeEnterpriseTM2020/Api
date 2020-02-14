<?php
//WIP
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    include_once "classes/database.php";
    include_once "classes/customer.php";
    $db = new Database("pract_ent","webgebruiker","labo2019");
    $link=$db->getLink();
    if($link != false){
        // check if POST values are set
        if(isset($_POST["email"]) 
        && isset($_POST["firstname"]) 
        && isset($_POST["lastname"]) 
        && isset($_POST["password"])){
            // check for valid email
            if(!filter_var($_POST["email"],FILTER_VALIDATE_EMAIL)){
                die(json_encode(array("message"=>"email not valid", "success"=>false)));
            }
            // make customer object
            $cust = new Customer($link, $_POST["lastname"], $_POST["firstname"], $_POST["email"], $_POST["password"]);
            // use create method returns true if successfully added customer
            if($cust->create()){
                http_response_code(201);
                echo(json_encode(array("message" => "Successfully added customer.","success"=>true)));        
            }
            else{
                // else send error
                http_response_code(503);
                echo(json_encode(array("message" => "Failed to add customer.","success"=>false)));
            }
        }
        else{
            // if POST data is not complete send error
            http_response_code(400);
            echo(json_encode(array("message" => "Failed to add customer, data incomplete.","success"=>false)));
        }
    }
    else{
        // if connection with database failed send error
        http_response_code(404);
        echo(json_encode(array("message" => "No connection with database.","success"=>false)));
    }
?>