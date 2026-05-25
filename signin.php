<?php

require_once 'session_config.php';
include 'connection.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['submit'])) {
    $identifier = $_POST['Identifier'];
    $password   = $_POST['Password'];

    echo "Identifier received: " . $identifier . "<br>";

    $sql = $conn->prepare("SELECT UserID, Name, Username, Email, Password FROM Users WHERE Email = ? OR Username = ?");
    $sql->bind_param("ss", $identifier, $identifier);
    $sql->execute();
    $result = $sql->get_result();

    echo "Rows found: " . $result->num_rows . "<br>";

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        echo "Password from DB: " . $user['Password'] . "<br>";
        echo "Password verify result: " . (password_verify($password, $user['Password']) ? 'TRUE' : 'FALSE') . "<br>";

        if (password_verify($password, $user['Password'])) {
            $_SESSION['user_id']  = $user['UserID'];
            $_SESSION['name']     = $user['Name'];
            $_SESSION['username'] = $user['Username'];
            $_SESSION['email']    = $user['Email'];

            echo "Session set! user_id = " . $_SESSION['user_id'];
        } else {
            echo "Password mismatch!";
        }
    } else {
        echo "User not found!";
    }
}
?>