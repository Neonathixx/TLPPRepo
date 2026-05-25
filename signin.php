<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'connection.php';

if (isset($_POST['submit'])) {
    $identifier = $_POST['Identifier'];
    $password   = $_POST['Password'];

    $sql = $conn->prepare("SELECT UserID, Name, Username, Email, Password FROM Users WHERE Email = ? OR Username = ?");
    $sql->bind_param("ss", $identifier, $identifier);
    $sql->execute();
    $result = $sql->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['Password'])) {
            $_SESSION['user_id']  = $user['UserID'];
            $_SESSION['name']     = $user['Name'];
            $_SESSION['username'] = $user['Username'];
            $_SESSION['email']    = $user['Email'];

            // ✅ Instead of redirecting, show debug info
            echo "Session ID: " . session_id() . "<br>";
            echo "User ID in session: " . $_SESSION['user_id'] . "<br>";
            echo "Name: " . $_SESSION['name'] . "<br>";
            echo "<a href='check_session.php'>Now click here to check session</a>";
            exit();
        } else {
            echo "Wrong password!";
        }
    } else {
        echo "User not found!";
    }
}