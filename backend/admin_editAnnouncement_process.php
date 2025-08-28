<?php
include "../connection/dbConnect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['edit_id'];
    $title = $_POST['title'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $location = $_POST['location'];
    $body = $_POST['body'];

    // Fetch current image filename from DB
    $getQuery = "SELECT image FROM tbl_announcement WHERE id = '$id'";
    $result = mysqli_query($conn, $getQuery);
    $row = mysqli_fetch_assoc($result);
    $currentImage = $row['image'];

    // Check if a new image was uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageName = $_FILES['image']['name'];
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageExt = pathinfo($imageName, PATHINFO_EXTENSION);
        $newImageName = uniqid("ANN_", true) . '.' . $imageExt;
        $imagePath = 'announcement_image/' . $newImageName;

        // Move uploaded file
        if (move_uploaded_file($imageTmpName, $imagePath)) {
            // Optionally delete old image
            if (!empty($currentImage) && file_exists("announcement_image/$currentImage")) {
                unlink("announcement_image/$currentImage");
            }
        } else {
            echo "<script>alert('Image upload failed.'); window.location.href='../frontend/adminManageAnnouncement.php';</script>";
            exit();
        }
    } else {
        // No new image uploaded, use old one
        $newImageName = $currentImage;
    }

    // Update query
    $updateQuery = "UPDATE tbl_announcement SET 
        title = '$title',
        date = '$date',
        time = '$time',
        location = '$location',
        body = '$body',
        image = '$newImageName'
        WHERE id = '$id'";

    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>alert('Announcement updated successfully.'); window.location.href='../frontend/adminManageAnnouncement.php';</script>";
    } else {
        echo "<script>alert('Error updating announcement.'); window.location.href='../frontend/adminManageAnnouncement.php';</script>";
    }
}
?>
