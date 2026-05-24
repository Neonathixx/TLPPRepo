<?php

    include 'connection.php';

    if(isset($_POST['register'])){
    $name = $_POST['Name'];
    $email = $_POST['Email'];
    $password = password_hash($_POST['Password'], PASSWORD_DEFAULT);

    $sql = $conn -> prepare("INSERT INTO Users(FirstName, Email, Password) VALUES (?, ?, ?)");

    $sql->bind_param("sss", $name, $email, $password);
    $sql->execute();

    if($sql->execute()){
            echo "User registered successfully!";
        } else {
            echo "Error: " . $sql->error;
        }

    $ql->close();

    }

?>