<?php
session_start();
include '../connection/dbConnect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = $_POST['id'];
    $fullName = $_POST['fullName'];
    $gender   = $_POST['gender'];
    $batch    = $_POST['batch'];
    $course   = $_POST['course'];

    // Handle graduation picture
    $graduationPicture = null;
    if (isset($_FILES['graduationPicture']) && $_FILES['graduationPicture']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../yearbook/";
        $fileName = time() . "_" . basename($_FILES['graduationPicture']['name']);
        $targetFile = $targetDir . $fileName;

        // Move uploaded file
        if (move_uploaded_file($_FILES['graduationPicture']['tmp_name'], $targetFile)) {
            $graduationPicture = $fileName;
        }
    }

    // Build query (if new picture uploaded, include it in update)
    if ($graduationPicture) {
        $query = "UPDATE tbl_yearbook 
                  SET fullName = ?, gender = ?, batch = ?, course = ?, graduationPicture = ?
                  WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssi", $fullName, $gender, $batch, $course, $graduationPicture, $id);
    } else {
        $query = "UPDATE tbl_yearbook 
                  SET fullName = ?, gender = ?, batch = ?, course = ?
                  WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $fullName, $gender, $batch, $course, $id);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = "Alumni updated successfully.";
    } else {
        $_SESSION['error'] = "Error updating alumni: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    header("Location: ../frontend/adminManageYearbook.php");
    exit();
}
?>
