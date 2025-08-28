<?php
include '../connection/dbConnect.php';
session_start();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // sanitize

    // ✅ Prepare delete query
    $query = "DELETE FROM tbl_studentaccount WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Student account deleted successfully.'); window.location.href='../frontend/adminManageStudent.php';</script>";
    } else {
        echo "<script>alert('Error deleting student account.'); window.location.href='../frontend/adminManageStudent.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request.'); window.location.href='../frontend/adminManageStudent.php';</script>";
}

$conn->close();
?>
