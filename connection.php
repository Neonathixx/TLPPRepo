<?php

    $host = "153.92.10.173";
    $user = "u943893873_Hiam";
    $password = "LittlePawPatissier1";
    $database = "u943893873_TLPPTest";

    $conn = new mysqli($host, $user, $password, $database);

    if($conn->connect_error){
        die("Connection failed: " . $conn->connect_error);
    
    }

?>