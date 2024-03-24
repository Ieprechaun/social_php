<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form action="register_process.php" method="post">
            <input type="text" name="username" placeholder="Username" required><br><br>
            <input type="text" name="fullname" placeholder="Fullname" required><br><br>
            <input type="email" name="email" placeholder="Email" required><br><br>
            <input type="text" name="phonenumber" placeholder="Phone"><br><br>
            <input type="text" name="avatarurl" placeholder="Avatar URL"><br><br>
            <input type="date" name="birthday" placeholder="Date of Birth" required><br><br>
            <input type="password" name="password" placeholder="Password" required><br><br>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>
</body>
</html>
