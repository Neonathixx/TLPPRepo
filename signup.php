<?php

    include 'connection.php';

    if(isset($_POST['submit'])){
    $name = $_POST['Name'];
    $username = $_POST['Username'];
    $email = $_POST['Email'];
    $password = password_hash($_POST['Password'], PASSWORD_DEFAULT);

    $sql = $conn -> prepare("INSERT INTO Users(Name, Username, Email, Password) VALUES (?, ?, ?, ?)");

    $sql->bind_param("ssss", $name, $username, $email, $password);

    }

?>