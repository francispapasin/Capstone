<?php

include '../connection/dbConnect.php'; // Ensure you have a database connection

$userID = isset($_GET['id']) ? $_GET['id'] : null;

// Fetch user details from database
$query = "SELECT fullName, course, batch, profilePicture, currentJob, location, description 
            FROM tbl_alumniAccount 
            WHERE id = '$userID'";

$result = mysqli_query($conn, $query);
if ($row = mysqli_fetch_assoc($result)) {
    $full_name = $row['fullName'];
    $course = $row['course'];
    $batch = $row['batch'];
    $profile_picture = !empty($row['profilePicture']) ? $row['profilePicture'] : "Image/Alumni.jpg";
    $job = !empty($row['currentJob']) ? $row['currentJob'] : "N/A";
    $location = !empty($row['location']) ? $row['location'] : "N/A";
    $description = !empty($row['description']) ? $row['description'] : "No description available.";

} else {
    echo "User not found.";
    exit();
}

// Fetch posts of the viewed alumni
$post_query = "SELECT p.id AS postId, a.fullName, p.postContent, p.postDate 
FROM tbl_alumnipost p
JOIN tbl_alumniaccount a ON p.alumniId = a.id
WHERE p.alumniId = $userID
ORDER BY p.postDate DESC";

$post_result = $conn->query($post_query);
?>