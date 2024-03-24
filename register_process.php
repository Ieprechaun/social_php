<?php
$conn = mysqli_connect("localhost", "root", "", "chominzon");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phonenumber = isset($_POST['phonenumber']) ? $_POST['phonenumber'] : '';
    $avatarurl = isset($_POST['avatarurl']) ? $_POST['avatarurl'] : '';
    $birthday = $_POST['birthday'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $query = "INSERT INTO users (username, email, phonenumber, avatarurl, birthday, password) VALUES ('$username', '$email', '$phonenumber', '$avatarurl', '$birthday', '$password')";
    mysqli_query($conn, $query);

    header("Location: login.php");
    exit();
}
?>
