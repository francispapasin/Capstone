<?php

include '../connection/dbConnect.php'; // Ensure you have a database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = $_POST['location'];
    $body = $_POST['body'];

    // Image handling
    $imageName = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "../announcement_images/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true); // create uploads folder if it doesn't exist
        }

        $imageName = basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $imageName;

        // Move uploaded file
        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            header ('Location: ../frontend/adminManageAnnouncement.php');
            //echo "<script>alert('Image upload failed.'); window.history.back();</script>";
            exit;
        }
    }

    $stmt = $conn->prepare("INSERT INTO tbl_announcement (title, date, time, location, body, image) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssss", $title, $date, $time, $location, $body, $imageName);

    if ($stmt->execute()) {
        header ('Location: ../frontend/adminManageAnnouncement.php');
    } else {
        header ('Location: ../frontend/adminManageAnnouncement.php');
        //echo "<script>alert('Failed to add announcement.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
} else {
    header ('../frontend/adminManageAnnouncement.php');
    //echo "<script>alert('Invalid request.'); window.history.back();</script>";
}
?>
