<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'session_config.php';
include 'connection.php';

if(isset($_POST['submit'])){
    $name = $_POST['Name'];
    $username = $_POST['Username'];
    $email = $_POST['Email'];
    $password = password_hash($_POST['Password'], PASSWORD_DEFAULT);

    // ✅ Check if email already exists
    $check = $conn->prepare("SELECT Email FROM Users WHERE Email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if($check->num_rows > 0){
        // Email already taken
        header("Location: account.html?error=email_exists");
        exit();
    } else {
        // Email is free, proceed with insert
        $sql = $conn->prepare("INSERT INTO Users(Name, Username, Email, Password) VALUES (?, ?, ?, ?)");
        $sql->bind_param("ssss", $name, $username, $email, $password);

        if($sql->execute()){
            header("Location: account.html?success=1");
            exit();
        } else {
            echo "Error: " . $sql->error;
        }

        $sql->close();
    }

    $check->close();
}
?>