<?php
session_start();
include '../connection/dbConnect.php'; // Ensure you have a database connection

if (!isset($_SESSION['id'])) {
    die("User not logged in. Please log in again.");
}

$user_id = $_SESSION['id'];
$latitude = $_POST['latitude'] ?? null;
$longitude = $_POST['longitude'] ?? null;
$location = $_POST['address'] ?? '';
$current_job = $_POST['currentJob'] ?? '';
$description = $_POST['description'] ?? '';

// Handle profile picture upload
$profile_picture_path = null;
if (isset($_FILES['profilePicture']) && $_FILES['profilePicture']['error'] == 0) {
    $target_dir = "../profile/"; // Make sure this folder exists and is writable
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    $filename = basename($_FILES["profilePicture"]["name"]);
    $target_file = $target_dir . time() . "_" . $filename;

    if (move_uploaded_file($_FILES["profilePicture"]["tmp_name"], $target_file)) {
        $profile_picture_path = $target_file;
    } else {
        die("Error uploading the profile picture.");
    }
}

// Prepare SQL with or without image update
if ($profile_picture_path) {
    // SQL query when updating profile picture
    $sql = "UPDATE tbl_alumniaccount 
            SET latitude=?, longitude=?, location=?, currentJob=?, description=?, profilePicture=?
            WHERE id=?";
    $stmt = $conn->prepare($sql);
    // Corrected type definition string
    $stmt->bind_param("ddssssi", $latitude, $longitude, $location, $current_job, $description, $profile_picture_path, $user_id);
} else {
    // SQL query when not updating profile picture
    $sql = "UPDATE tbl_alumniaccount 
            SET latitude=?, longitude=?, location=?, currentJob=?, description=?
            WHERE id=?";
    $stmt = $conn->prepare($sql);
    // Corrected type definition string
    $stmt->bind_param("ddsssi", $latitude, $longitude, $location, $current_job, $description, $user_id);
}

if ($stmt->execute()) {
    // Update session variables with the new profile data
    $query = "SELECT fullName, course, batch, profilePicture, currentJob, location, description 
              FROM tbl_alumniaccount WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $_SESSION['profilePicture'] = $row['profilePicture'];
        $_SESSION['currentJob'] = $row['currentJob'];
        $_SESSION['location'] = $row['location'];
        $_SESSION['description'] = $row['description'];
    }

    $stmt->close();
    $conn->close();
    header("Location: ../frontend/accountProfilePage.php"); // Redirect to the profile page
    exit();
} else {
    die("Error updating profile: " . $stmt->error);
}

?>