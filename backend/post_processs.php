<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../connection/dbConnect.php'; // Ensure you have a database connection

// Ensure user is logged in
if (!isset($_SESSION['email'])) {
    die("User not logged in.");
}

// Get the logged-in user's email
$email = $_SESSION['email'];

// Fetch alumni ID
$query = "SELECT id FROM tbl_alumniaccount WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if (!$row) {
    die("Alumni account not found.");
}

$alumni_id = $row['id']; // Get alumni ID

// Ensure that either post content or an image is provided
if (empty($_POST['postContent']) && empty($_FILES['postImage']['name'][0])) {
    die("Either post content or an image is required.");
}

$post_content = $_POST['postContent'];
date_default_timezone_set('Asia/Manila');
$post_date = date("Y-m-d H:i:s"); // Use 24-hour format and no AM/PM


// Insert post into tbl_alumnipost
$query = "INSERT INTO tbl_alumnipost (alumniId, postContent, postDate) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("iss", $alumni_id, $post_content, $post_date);

if (!$stmt->execute()) {
    die("Error inserting post: " . $stmt->error);
}

$post_id = $stmt->insert_id;
$stmt->close();

// Handle Multiple Image Uploads
if (!empty($_FILES['postImage']['name'][0])) {
    $image_folder = "../imagepost";

    foreach ($_FILES['postImage']['tmp_name'] as $key => $tmp_name) {
        $image_name = basename($_FILES['postImage']['name'][$key]);
        $unique_name = time() . "_" . uniqid() . "_" . $image_name;
        $image_path = $image_folder . "/" . $unique_name;

        // Validate file type
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($tmp_name);

        if (!in_array($file_type, $allowed_types)) {
            die("Invalid file type: $image_name. Only JPEG, PNG, and GIF are allowed.");
        }

        // Validate size (max 5MB)
        if ($_FILES['postImage']['size'][$key] > 5 * 1024 * 1024) {
            die("File size too large: $image_name. Max size is 5MB.");
        }

        if (move_uploaded_file($tmp_name, $image_path)) {
            $query = "INSERT INTO tbl_imagepost (postId, imagePath) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("is", $post_id, $image_path);
            $stmt->execute();
            $stmt->close();
        } else {
            die("Failed to upload image: " . $image_name);
        }
    }
}

// Close database connection
$conn->close();

// Redirect to AlumniWall.php after successful post creation
header("Location: ../frontend/alumniWall.php");
exit();
?>