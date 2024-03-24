<?php
$conn = mysqli_connect("localhost", "root", "", "son");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Nếu password không được cung cấp, không cập nhật password
    $password_query = !empty($password) ? ", password='" . password_hash($password, PASSWORD_DEFAULT) . "'" : "";

    $query = "UPDATE users SET username='$username' $password_query WHERE id=$id";
    mysqli_query($conn, $query);

    header("Location: index.php");
    exit();
}
?>
