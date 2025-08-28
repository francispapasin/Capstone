<?php

include '../connection/dbConnect.php'; // Ensure you have a database connection

// Fetch existing profile data from the database
$user_id = $_SESSION['id']; // Assuming the user's ID is stored in the session
$query = "SELECT * FROM tbl_alumniaccount WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $profile_picture = !empty($row['profilePicture']) ? $row['profilePicture'] : 'Image/Alumni.png';
    $current_job = $row['currentJob'] ?? '';
    $location = $row['location'] ?? '';
    $description = $row['description'] ?? '';
} else {
    // Default values if no profile exists
    $profile_picture = 'Image/Alumni.png';
    $current_job = '';
    $location = '';
    $description = '';
}
?>