<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "chominzon");
$username = $_SESSION['username'];
$query = "SELECT * FROM `users` WHERE `username`='$username'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Xử lý đăng bài mới
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['content'])) {
    if (!empty($_POST['content'])) {
        $content = $_POST['content'];

        // Xử lý tải ảnh lên máy chủ
        if (isset($_FILES['image'])) {
            $file_name = $_FILES['image']['name'];
            $file_tmp = $_FILES['image']['tmp_name'];
            $file_type = $_FILES['image']['type'];
            $file_size = $_FILES['image']['size'];

            // Di chuyển tệp tải lên vào thư mục lưu trữ của bạn
            move_uploaded_file($file_tmp, "uploads/" . $file_name);

            // Câu truy vấn INSERT vào bảng posts với thời gian đăng là thời gian hiện tại
            $query_post = "INSERT INTO `posts` (`content`, `image`, `user_id`, `created_at`, `likes_count`) VALUES ('$content', '$file_name', '{$user['id']}', NOW(), 0)";
            mysqli_query($conn, $query_post);
        } else {
            // Câu truy vấn INSERT vào bảng posts với thời gian đăng là thời gian hiện tại (không có ảnh)
            $query_post = "INSERT INTO `posts` (`content`, `user_id`, `created_at`, `likes_count`) VALUES ('$content', '{$user['id']}', NOW(), 0)";
            mysqli_query($conn, $query_post);
        }

        // Sau khi đăng bài, làm mới trang để hiển thị bài mới
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}

// Xử lý like và unlike bài đăng
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && isset($_POST['post_id'])) {
        $post_id = $_POST['post_id'];
        $user_id = $user['id'];

        if ($_POST['action'] == 'like') {
            // Kiểm tra xem đã tồn tại like từ user_id cho post_id này chưa
            $query_check_like = "SELECT * FROM `likes` WHERE `user_id` = '$user_id' AND `post_id` = '$post_id'";
            $result_check_like = mysqli_query($conn, $query_check_like);
            if(mysqli_num_rows($result_check_like) == 0) {
                // Thêm dữ liệu like vào bảng likes
                $query_like = "INSERT INTO `likes` (`user_id`, `post_id`, `liked_date`) VALUES ('$user_id', '$post_id', NOW())";
                mysqli_query($conn, $query_like);

                // Cập nhật số lượng like của bài đăng trong bảng posts
                $query_update_likes = "UPDATE `posts` SET `likes_count` = `likes_count` + 1 WHERE `id` = '$post_id'";
                mysqli_query($conn, $query_update_likes);
            }
        } elseif ($_POST['action'] == 'unlike') {
            // Xóa dữ liệu like từ user_id cho post_id này
            $query_unlike = "DELETE FROM `likes` WHERE `user_id` = '$user_id' AND `post_id` = '$post_id'";
            mysqli_query($conn, $query_unlike);

            // Cập nhật số lượng like của bài đăng trong bảng posts
            $query_update_likes = "UPDATE `posts` SET `likes_count` = `likes_count` - 1 WHERE `id` = '$post_id'";
            mysqli_query($conn, $query_update_likes);
        }

        // Chuyển hướng trang để tránh việc gửi lại yêu cầu POST khi người dùng làm mới trang
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment_content']) && isset($_POST['tweet_id'])) {
    $comment_content = $_POST['comment_content'];
    $tweet_id = $_POST['tweet_id'];

    if (!empty($comment_content)) {
        // Thực hiện câu truy vấn INSERT để lưu comment vào bảng comments
        $query_add_comment = "INSERT INTO `comments` (`user_id`, `tweet_id`, `content`, `commented_date`) VALUES ('{$user['id']}', '$tweet_id', '$comment_content', NOW())";
        mysqli_query($conn, $query_add_comment);
    }
}

// Truy vấn lấy tất cả bài đăng từ tất cả người dùng
$query_posts = "SELECT p.*, COUNT(l.like_id) AS likes_count FROM `posts` p LEFT JOIN `likes` l ON p.id = l.post_id GROUP BY p.id ORDER BY p.created_at DESC";
$result_all_posts = mysqli_query($conn, $query_posts);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Home</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo $user['username']; ?>!</h2>
        <!-- Biểu mẫu đăng bài mới -->
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
            <textarea name="content" placeholder="What's on your mind?" rows="4"></textarea><br><br>
            <input type="file" name="image"><br><br>
            <button type="submit">Post</button>
        </form>

        <h3>All Posts</h3>
        <?php
        // Hiển thị tất cả bài đăng và cho phép like và comment
        while($row = mysqli_fetch_assoc($result_all_posts)) {
            // Lấy thông tin người đăng
            $user_query = "SELECT username FROM `users` WHERE `id` = '{$row['user_id']}'";
            $user_result = mysqli_query($conn, $user_query);
            $user_row = mysqli_fetch_assoc($user_result);
            $author_username = $user_row['username'];
            echo "<div class='post'>";
            echo "<p>Posted by: " . $author_username . "</p>"; // Hiển thị username của người đăng
            echo "<p>" . $row['content'] . "</p>";
            // Hiển thị hình ảnh nếu có
            if (!empty($row['image'])) {
                echo "<img src='uploads/" . $row['image'] . "' alt='Posted Image'>";
            }
            echo "<p>Posted at: " . date('Y-m-d H:i:s', strtotime($row['created_at'])) . "</p>";
            echo "<p>Likes: " . $row['likes_count'] . "</p>";
            // Nút like hoặc unlike
            echo "<form method='post' action='".$_SERVER['PHP_SELF']."'>";
            echo "<input type='hidden' name='post_id' value='".$row['id']."'>";
            // Kiểm tra xem người dùng đã like bài viết này chưa
            $query_check_like = "SELECT * FROM `likes` WHERE `user_id` = '{$user['id']}' AND `post_id` = '{$row['id']}'";
            $result_check_like = mysqli_query($conn, $query_check_like);
            if(mysqli_num_rows($result_check_like) > 0) {
                // Nếu đã like, hiển thị nút unlike
                echo "<button type='submit' name='action' value='unlike'>Unlike</button>";
            } else {
                // Nếu chưa like, hiển thị nút like
                echo "<button type='submit' name='action' value='like'>Like</button>";
            }
            echo "</form>";
            // Form comment
            echo "<form method='post' action='".$_SERVER['PHP_SELF']."'>";
            echo "<input type='hidden' name='tweet_id' value='".$row['id']."'>";
            echo "<input type='text' name='comment_content' placeholder='Write a comment'>";
            echo "<button type='submit'>Comment</button>";
            echo "</form>";
            // Hiển thị tất cả comment của bài đăng này
            $query_comments = "SELECT c.*, u.username FROM `comments` c INNER JOIN `users` u ON c.user_id = u.id WHERE `tweet_id` = '{$row['id']}' ORDER BY c.commented_date ASC";
            $result_comments = mysqli_query($conn, $query_comments);
            echo "<div class='comments'>";
            while ($comment = mysqli_fetch_assoc($result_comments)) {
                echo "<p><strong>".$comment['username']."</strong>: ".$comment['content']."</p>";
            }
            echo "</div>";
            echo "</div>";

            echo "<form method='post' action='profile.php'>";
            echo "<input type='hidden' name='retweet_content' value='".htmlspecialchars($row['content'])."'>";
            echo "<input type='hidden' name='retweet_image' value='".htmlspecialchars($row['image'])."'>";
            echo "<button type='submit'>Retweet</button>";
            echo "</form>";
        }
        ?>
        <a href="profile.php">Go to Profile</a>
    </div>
</body>
</html>

            
