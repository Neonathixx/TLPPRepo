<?php

    include 'connection.php';

    if(isset($_POST['register'])){
    $name = $_POST['Name'];
    $email = $_POST['Email'];
    $password = password_hash($_POST['Password'], PASSWORD_DEFAULT);

    $sql = $conn -> query("INSERT INTO Users(FirstName, Email, Password)
                        VALUES ($name, $email, $password)");
    }

?>