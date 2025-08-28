<?php
session_start();
include '../connection/dbConnect.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Collect form inputs
    $last_name   = trim($_POST['last_name']);
    $first_name  = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $gender       = $_POST['gender'];
    $batch       = $_POST['batch'];
    $course      = $_POST['course'];

    // Build fullName
    $fullName = $first_name . " " . $middle_name . " " . $last_name;
    $fullName = trim($fullName); // remove extra spaces

    // Handle file upload
    $upload_dir = "../yearbook/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // create folder if not exists
    }

    $graduationPicture = null;

    if (isset($_FILES['graduationPicture']) && $_FILES['graduationPicture']['error'] === UPLOAD_ERR_OK) {
        $file_tmp  = $_FILES['graduationPicture']['tmp_name'];
        $file_name = time() . "_" . basename($_FILES['graduationPicture']['name']); // unique filename
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file_tmp, $file_path)) {
            $graduationPicture = $file_path;
        } else {
            $_SESSION['error'] = "Failed to upload graduation picture.";
            header("Location: ../frontend/adminManageYearbook.php");
            exit();
        }
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO tbl_yearbook (fullName, gender, batch, course, graduationPicture) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $fullName, $gender, $batch, $course, $graduationPicture);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Alumni added successfully!";
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    // Redirect back
    header("Location: ../frontend/adminManageYearbook.php");
    exit();
} else {
    header("Location: ../frontend/adminManageYearbook.php");
    exit();
}
?>
