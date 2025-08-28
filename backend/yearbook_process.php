<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../connection/dbConnect.php'; // Ensure you have a database connection

// Get selected batch and course from dropdowns (default to "All")
$selectedBatch = isset($_GET['batch']) ? $_GET['batch'] : 'All';
$selectedCourse = isset($_GET['course']) ? $_GET['course'] : 'All';

// Build the SQL query with filters
$query = "SELECT id, batch, course, fullName, gender, profilePicture FROM tbl_alumniaccount WHERE 1=1";

if ($selectedBatch !== 'All') {
    $query .= " AND batch = '".mysqli_real_escape_string($conn, $selectedBatch)."'";
}
if ($selectedCourse !== 'All') {
    $query .= " AND course = '".mysqli_real_escape_string($conn, $selectedCourse)."'";
}

$query .= " ORDER BY batch DESC, course, fullName";
$result = mysqli_query($conn, $query);

// Organize data by batch and course
$alumniByBatch = [];
while ($row = mysqli_fetch_assoc($result)) {
    $batch = $row['batch'];
    $course = $row['course'];
    $alumniByBatch[$batch][$course][] = $row;
}

?>
