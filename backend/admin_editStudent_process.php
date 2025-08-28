<?php
include "../connection/dbConnect.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id       = $_POST['id'];
    $fullName = trim($_POST['fullName']);
    $email    = trim($_POST['email']);
    $course   = $_POST['course'];

    // ✅ Validate input
    if (empty($id) || empty($fullName) || empty($email) || empty($course)) {
        echo "<script>alert('All fields are required.'); window.location.href='../frontend/adminStudentList.php';</script>";
        exit();
    }

    // ✅ Update query
    $query = "UPDATE tbl_studentaccount 
              SET fullName = ?, email = ?, course = ?
              WHERE id = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo "<script>alert('Database error.'); window.location.href='../frontend/adminManageStudent.php';</script>";
        exit();
    }

    $stmt->bind_param("sssi", $fullName, $email, $course, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Student updated successfully.'); window.location.href='../frontend/adminManageStudent.php';</script>";
    } else {
        echo "<script>alert('Error updating student.'); window.location.href='../frontend/adminManageStudent.php';</script>";
    }

    $stmt->close();
}
$conn->close();
?>
