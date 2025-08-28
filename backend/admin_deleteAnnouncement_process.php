<?php
include "../connection/dbConnect.php";
session_start();

// ✅ Check if ID is passed
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Invalid request.'); window.location.href='../frontend/adminManageAnnouncement.php';</script>";
    exit();
}

$id = intval($_GET['id']); // Prevent SQL injection

// ✅ Step 1: Get image filename before deleting
$stmt = $conn->prepare("SELECT image FROM tbl_announcement WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    echo "<script>alert('Announcement not found.'); window.location.href='../frontend/adminManageAnnouncement.php';</script>";
    exit();
}

$image = $row['image'];

// ✅ Step 2: Delete announcement from DB
$deleteStmt = $conn->prepare("DELETE FROM tbl_announcement WHERE id = ?");
$deleteStmt->bind_param("i", $id);

if ($deleteStmt->execute()) {
    // ✅ Step 3: Delete image file if exists
    if (!empty($image) && file_exists("../backend/announcement_image/" . $image)) {
        unlink("../backend/announcement_image/" . $image);
    }
    echo "<script>alert('Announcement deleted successfully.'); window.location.href='../frontend/adminManageAnnouncement.php';</script>";
} else {
    echo "<script>alert('Error deleting announcement.'); window.location.href='../frontend/adminManageAnnouncement.php';</script>";
}
?>
