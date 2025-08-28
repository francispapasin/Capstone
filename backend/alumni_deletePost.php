<?php
session_start();
include '../connection/dbConnect.php';

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: ../pages/loginPage.php");
    exit();
}

// Check if postId is provided
if (!isset($_POST['postId']) || empty($_POST['postId'])) {
    echo "Invalid request.";
    exit();
}

$postId = $_POST['postId'];
$userId = $_SESSION['id'];

// Verify that the post belongs to the logged-in user
$stmt = $conn->prepare("SELECT id FROM tbl_alumnipost WHERE id = ? AND alumniId = ?");
$stmt->bind_param("ii", $postId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "You are not authorized to delete this post.";
    exit();
}

// Delete associated images first
$imgStmt = $conn->prepare("SELECT imagePath FROM tbl_imagepost WHERE postId = ?");
$imgStmt->bind_param("i", $postId);
$imgStmt->execute();
$imgResult = $imgStmt->get_result();

while ($img = $imgResult->fetch_assoc()) {
    if (file_exists("../" . $img['imagePath'])) {
        unlink("../" . $img['imagePath']); // Delete the image file
    }
}

// Delete images from the database
$delImgStmt = $conn->prepare("DELETE FROM tbl_imagepost WHERE postId = ?");
$delImgStmt->bind_param("i", $postId);
$delImgStmt->execute();

// Delete the post
$delPostStmt = $conn->prepare("DELETE FROM tbl_alumnipost WHERE id = ?");
$delPostStmt->bind_param("i", $postId);
if ($delPostStmt->execute()) {
    header("Location: ../frontend/accountProfilePage.php?msg=Post deleted successfully");
    exit();
} else {
    echo "Error deleting post. Please try again.";
}
?>
