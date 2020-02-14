<?php
//WIP
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
include_once "classes/database.php";
include_once "classes/customer.php";
// make database object
$db = new Database("pract_ent", "webgebruiker", "labo2019");
// get database link
$link = $db->getLink();
// check if link was created
if ($link != false) {
    // make a new customer
    $customer = new Customer($link);
    //use read_all method to execute query
    $values = $customer->readAll();
    // check if the query returned something
    $rowcount = mysqli_num_rows($values);
    if ($rowcount > 0) {
        // format data for json encoding
        $cust_array = array();
        $cust_array["customers"] = array();
        $temp = -1;
        while ($cust = mysqli_fetch_array($values)) {
            extract($cust);
            //check if id is still the same
            if ($id != $temp) {
                //if not create a new customer
                $res = array(
                    "id" => $id,
                    "lastname" => $lastname,
                    "firstname" => $firstname,
                    "email" => $email,
                    "addresses" => array(
                        array("street" => $address,
                        "number" => $number,
                        "city" => $city,
                        "postalCode" => $postalCode)
                    )
                );
                array_push($cust_array["customers"], $res);
                $temp=$id;
            } 
            else {
                //if it is the same add a new address to the last customer
                $size = count($cust_array);
                $adr = array(
                    "street" => $address,
                    "number" => $number,
                    "city" => $city,
                    "postalCode" => $postalCode
                );
                array_push($cust_array["customers"][$size - 1]["addresses"], $adr);
            }
        }
        // send data to client
        http_response_code(200);
        $cust_array["success"] = true;
        echo (json_encode($cust_array));
    } else {
        http_response_code(404);
        echo (json_encode(array("message" => "No products found.", "success" => false)));
    }
} else {
    http_response_code(404);
    echo (json_encode(array("message" => "No connection with database.", "success" => false)));
}
