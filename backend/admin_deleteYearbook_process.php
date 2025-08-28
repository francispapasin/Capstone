<?php
include '../connection/dbConnect.php';
session_start();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // sanitize

    // ✅ Prepare delete query
    $query = "DELETE FROM tbl_yearbook WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('YearBook deleted successfully.'); window.location.href='../frontend/adminManageYearBook.php';</script>";
    } else {
        echo "<script>alert('Error deleting YearBook.'); window.location.href='../frontend/adminManageYearBook.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Invalid request.'); window.location.href='../frontend/adminManageYearBook.php';</script>";
}

$conn->close();
?>
