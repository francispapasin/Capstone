<?php 
    include '../connection/dbConnect.php'; // Database connection

    if (!isset($_GET['id'])) {
        echo "User ID is missing in the URL.";
        exit();
    }

    $userID = (int) $_GET['id'];  // Ensure it's an integer

    // Fetch user details from the database
    $stmt = $conn->prepare("SELECT fullName, course, batch, profilePicture, currentJob, location, description 
                            FROM tbl_alumniaccount WHERE id = ?");
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $full_name = $row['fullName'];
        $profile_picture = !empty($row['profilePicture']) ? "uploads/" . $row['profilePicture'] : "Image/Alumni.jpg";
        // Handle other fields...
    } else {
        echo "User not found.";
        exit();
    }

    $stmt->close();
?>
