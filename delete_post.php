<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['post_id']) && isset($_POST['action'])) {
    if ($_POST['action'] == 'delete') {
        $post_id = $_POST['post_id'];
        // Thực hiện xóa bài đăng và các comment liên quan trong cơ sở dữ liệu
        $conn = mysqli_connect("localhost", "root", "", "chominzon");
        $query_delete_post = "DELETE FROM `posts` WHERE `id` = '$post_id'";
        mysqli_query($conn, $query_delete_post);

        // Chuyển hướng trang sau khi xóa bài đăng
        header("Location: ".$_SERVER['HTTP_REFERER']);
        exit();
    }
}
?>
