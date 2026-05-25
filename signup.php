<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'connection.php';

if (isset($_POST['submit'])) {
    $name     = $_POST['Name'];
    $username = $_POST['Username'];
    $email    = $_POST['Email'];
    $password = password_hash($_POST['Password'], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT Email FROM Users WHERE Email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        header("Location: account.html?error=email_exists");
        exit();
    }

    $checkUser = $conn->prepare("SELECT Username FROM Users WHERE Username = ?");
    $checkUser->bind_param("s", $username);
    $checkUser->execute();
    $checkUser->store_result();

    if ($checkUser->num_rows > 0) {
        header("Location: account.html?error=username_exists");
        exit();
    }

    $sql = $conn->prepare("INSERT INTO Users (Name, Username, Email, Password) VALUES (?, ?, ?, ?)");
    $sql->bind_param("ssss", $name, $username, $email, $password);

    if ($sql->execute()) {
        header("Location: account.html?success=1");
        exit();
    } else {
        echo "Execute error: " . $sql->error;
    }

    $check->close();
    $checkUser->close();
    $sql->close();
} else {
    echo "POST not received — form submit button may be missing name='submit'";
}
?>