<?php
$conn = mysqli_connect("localhost", "root", "", "son");

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "DELETE FROM users WHERE id=$id";
    mysqli_query($conn, $query);

    header("Location: index.php");
    exit();
}
?>
