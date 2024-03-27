<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['post_id']) && isset($_POST['action'])) {
    if ($_POST['action'] == 'edit') {
        $post_id = $_POST['post_id'];
        // Truy vấn để lấy thông tin của bài đăng để chỉnh sửa
        $conn = mysqli_connect("localhost", "root", "", "chominzon");
        $query_get_post = "SELECT * FROM `posts` WHERE `id` = '$post_id'";
        $result_get_post = mysqli_query($conn, $query_get_post);
        $post_data = mysqli_fetch_assoc($result_get_post);

        // Chuyển hướng đến trang chỉnh sửa bài đăng và truyền dữ liệu bài đăng qua URL hoặc session
        // Ví dụ: header("Location: edit_post_form.php?post_id=".$post_id);
        // hoặc
        // $_SESSION['edit_post_data'] = $post_data;
        // header("Location: edit_post_form.php");
        exit();
    }
}
?>
