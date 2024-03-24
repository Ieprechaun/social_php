<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "son");

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM users WHERE id=$id";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Nếu password không được cung cấp, không cập nhật password
    $password_query = !empty($password) ? ", password='" . password_hash($password, PASSWORD_DEFAULT) . "'" : "";

    $query = "UPDATE users SET username='$username', email='$email' $password_query WHERE id=$id";
    mysqli_query($conn, $query);

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Edit User</h2>
        <form action="edit.php" method="post">
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
            <input type="text" name="username" value="<?php echo $user['username']; ?>" required><br><br>
            <input type="email" name="email" value="<?php echo $user['email']; ?>" required><br><br>
            <input type="password" name="password" placeholder="New Password"><br><br>
            <button type="submit">Update User</button>
        </form>
        <a href="index.php">Back to List</a>
    </div>
</body>
</html>
