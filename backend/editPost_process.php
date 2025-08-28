<?php
include '../connection/dbConnect.php'; // Database connection
include '../backend/login_process.php'; // Include login process for session management

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email'])) {
    header("Location: LoginPage.php");
    exit();
}

// Profile picture with fallback
$profile_picture = isset($_SESSION['profilePicture']) ? $_SESSION['profilePicture'] : 'Image/Alumni.png';

// ✅ Get postId from POST first, fallback to GET
$post_id = $_POST['postId'] ?? ($_GET['postId'] ?? null);

if (!$post_id) {
    echo "Invalid post.";
    exit();
}

// Make sure the logged-in alumni owns the post
$query = "SELECT * FROM tbl_alumnipost WHERE id = $post_id AND alumniId = {$_SESSION['id']}";
$result = $conn->query($query);
$post = $result->fetch_assoc();

if (!$post) {
    echo "Post not found or access denied.";
    exit();
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $updated_content = $conn->real_escape_string($_POST['postContent']);

    $update_query = "UPDATE tbl_alumnipost SET postContent = '$updated_content' WHERE id = $post_id";
    if ($conn->query($update_query)) {

        // Handle new image uploads
        if (!empty($_FILES['new_images']['name'][0])) {
            $uploadDir = '../imagepost/';
            foreach ($_FILES['new_images']['tmp_name'] as $key => $tmp_name) {
                $fileName = basename($_FILES['new_images']['name'][$key]);
                $targetPath = $uploadDir . time() . '_' . $fileName;

                if (move_uploaded_file($tmp_name, $targetPath)) {
                    $conn->query("INSERT INTO tbl_imagepost (postId, imagePath) VALUES ($post_id, '$targetPath')");
                }
            }
        }

        header("Location: accountProfilePage.php"); // Redirect back to profile
        exit();
    } else {
        echo "Error updating post.";
    }
}
?>
