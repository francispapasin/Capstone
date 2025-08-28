<?php
include '../connection/dbConnect.php'; // Ensure you have a database connection

// Get the course and searchName parameters
$course = isset($_GET['course']) ? trim($_GET['course']) : '';
$searchName = isset($_GET['searchName']) ? trim($_GET['searchName']) : '';

// Start building the query
$query = "SELECT id, fullName, latitude, longitude, profilePicture 
          FROM tbl_alumniaccount 
          WHERE latitude IS NOT NULL AND latitude != '' 
          AND longitude IS NOT NULL AND longitude != ''
          AND latitude REGEXP '^-?[0-9]+(\.[0-9]+)?$' 
          AND longitude REGEXP '^-?[0-9]+(\.[0-9]+)?$'";


// Add filtering by course if provided
if (!empty($course) && $course !== 'All') {
    $query .= " AND course = ?";
}

// Add filtering by name if a search term is provided
if (!empty($searchName)) {
    $query .= " AND fullName LIKE ?";
}

// Order the results by name
$query .= " ORDER BY fullName ASC";

$stmt = $conn->prepare($query);

// Bind parameters for the prepared statement
if (!empty($course) && $course !== 'All' && !empty($searchName)) {
    $searchTerm = "%" . $searchName . "%"; // Wrap the search term with wildcards for LIKE
    $stmt->bind_param("ss", $course, $searchTerm); // Bind course and searchName as strings
} elseif (!empty($course) && $course !== 'All') {
    $stmt->bind_param("s", $course); // Only bind course if searchName is empty
} elseif (!empty($searchName)) {
    $searchTerm = "%" . $searchName . "%"; // Wrap the search term with wildcards for LIKE
    $stmt->bind_param("s", $searchTerm); // Bind only the search term
}

$stmt->execute();
$result = $stmt->get_result();

// Prepare the data to send as JSON
$users = [];

while ($row = $result->fetch_assoc()) {
    $users[] = [
        'id' => intval($row['id']),
        'name' => $row['fullName'],
        'lat' => floatval($row['latitude']),
        'lng' => floatval($row['longitude']),
        'profile_picture' => $row['profilePicture']
    ];
}

// Send the response as JSON
header('Content-Type: application/json');
echo json_encode($users, JSON_PRETTY_PRINT);
?>