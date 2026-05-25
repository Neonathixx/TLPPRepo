<?php

    include 'connection.php';
    
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    if(isset($_POST['submit'])){
    $name = $_POST['Name'];
    $username = $_POST['Username'];
    $email = $_POST['Email'];
    $password = password_hash($_POST['Password'], PASSWORD_DEFAULT);

    $sql = $conn -> prepare("INSERT INTO Users(Name, Username, Email, Password) VALUES (?, ?, ?, ?)");

    if(!$sql){
        die("Prepare failed: " . $conn->error);
    }

    $sql->bind_param("ssss", $name, $username, $email, $password);

     if($sql->execute()){
        echo "User registered successfully!";
    } else {
        echo "Error: " . $sql->error;
    }

    $sql->close();
    }

?>