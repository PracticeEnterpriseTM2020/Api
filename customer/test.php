<?php
$postdata = http_build_query(
    array(
        'email' => 'c.c@c.be',
        "city" => "antwerp",
        "street" => "velostraat",
        "number" => "111",
        "postalCode" => "1234"
    )
);

$opts = array('http' =>
    array(
        'method'  => 'POST',
        'header'  => 'Content-Type: application/x-www-form-urlencoded',
        "ignore_errors" => true,
        'content' => $postdata
    )
);

$context  = stream_context_create($opts);

$result = json_decode(file_get_contents('http://localhost/php/project_pe/API/customer/add_address.php', false, $context),true);
  if($result["success"]){
    return 0;
}
else{
    return 1;
}
?>