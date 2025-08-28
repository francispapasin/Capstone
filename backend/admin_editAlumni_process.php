<?php
include "../connection/dbConnect.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id          = $_POST['id'];
    $last_name   = trim($_POST['last_name']);
    $first_name  = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $email       = trim($_POST['email']);
    $batch       = $_POST['batch'];
    $course      = $_POST['course'];

    // ✅ Validate
    if (empty($id) || empty($last_name) || empty($first_name) || empty($email) || empty($batch) || empty($course)) {
        echo "<script>alert('All required fields must be filled out.'); window.location.href='../frontend/adminAlumniList.php';</script>";
        exit();
    }

    // ✅ Update alumni record
    $query = "UPDATE tbl_alumniaccount 
              SET last_name = ?, first_name = ?, middle_name = ?, email = ?, batch = ?, course = ? 
              WHERE id = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo "<script>alert('Database error.'); window.location.href='../frontend/adminAlumniList.php';</script>";
        exit();
    }

    $stmt->bind_param("ssssssi", $last_name, $first_name, $middle_name, $email, $batch, $course, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Alumni updated successfully.'); window.location.href='../frontend/adminManageAlumni.php';</script>";
    } else {
        echo "<script>alert('Error updating alumni.'); window.location.href='../frontend/adminAlumniList.php';</script>";
    }

    $stmt->close();
}
$conn->close();
?>
