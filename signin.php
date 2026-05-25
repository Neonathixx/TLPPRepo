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

            // Verify session was actually set
            session_write_close();

            header("Location: account.html");
            exit();
        } else {
            header("Location: account.html?error=wrong_password");
            exit();
        }
    } else {
        header("Location: account.html?error=user_not_found");
        exit();
    }
}