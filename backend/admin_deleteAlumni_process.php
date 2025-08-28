<?php
include '../connection/dbConnect.php';
session_start();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // sanitize

    // ✅ Prepare delete query
    $query = "DELETE FROM tbl_alumniaccount WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Alumni account deleted successfully.'); window.location.href='../frontend/adminManageAlumni.php';</script>";
    } else {
        echo "<script>alert('Error deleting alumni account.'); window.location.href='../frontend/adminManageAlumni.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request.'); window.location.href='../frontend/adminManageAlumni.php';</script>";
}

$conn->close();
?>
