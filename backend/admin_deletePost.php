<?php
session_start();
include '../connection/dbConnect.php';

// ✅ Get postId from POST
$post_id = $_GET['id'] ?? null;


if (!$post_id || !isset($_SESSION['id'])) {
    echo "Invalid request.";
    exit();
}

// ✅ Check if the post belongs to the logged-in alumni
$query = "SELECT * FROM tbl_alumnipost WHERE id = ? AND alumniId = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $post_id, $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Post not found or access denied.";
    exit();
}

// ✅ Delete post images first (if any)
$img_query = "SELECT imagePath FROM tbl_imagepost WHERE postId = ?";
$stmt_img = $conn->prepare($img_query);
$stmt_img->bind_param("i", $post_id);
$stmt_img->execute();
$img_result = $stmt_img->get_result();

while ($img = $img_result->fetch_assoc()) {
    if (file_exists($img['imagePath'])) {
        unlink($img['imagePath']); // delete from server
    }
}

// ✅ Delete images from DB
$delete_images = "DELETE FROM tbl_imagepost WHERE postId = ?";
$stmt_del_img = $conn->prepare($delete_images);
$stmt_del_img->bind_param("i", $post_id);
$stmt_del_img->execute();

// ✅ Delete post
$delete_post = "DELETE FROM tbl_alumnipost WHERE id = ?";
$stmt_del_post = $conn->prepare($delete_post);
$stmt_del_post->bind_param("i", $post_id);

if ($stmt_del_post->execute()) {
    header("Location: ../frontend/accountProfilePage.php");
    exit();
} else {
    echo "Error deleting post.";
}
?>
