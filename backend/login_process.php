<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../connection/dbConnect.php'; // Ensure you have a database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare the SQL statement for admin login
    $stmt = $conn->prepare("SELECT * FROM tbl_adminaccount WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    // Admin login check
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc(); // fetch the row first

        if ($password === $row['password']) { // compare entered password
            $_SESSION['fullName'] = $row['fullName'];
            $_SESSION['email'] = $row['email']; // safer to use from DB
            $_SESSION['role'] = "Admin"; // You can use this for access control

            header("Location: ../frontend/adminDashboard.php"); // Redirect to admin dashboard
            exit();
        } else {
            // Invalid password
            header("Location: ../frontend/loginPage.php?error=invalid_password");
            exit();
        }
    }

    // Prepare the SQL statement for alumni login
    $stmt = $conn->prepare("SELECT * FROM tbl_alumniaccount WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    // Alumni login check
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc(); // fetch the row first

        if ($password === $row['password']) { 
            $_SESSION['id'] = $row['id'];
            $_SESSION['fullName'] = $row['fullName'];
            $_SESSION['email'] = $row['email']; 
            $_SESSION['gender'] = $row['gender'];
            $_SESSION['batch'] = $row['batch']; 
            $_SESSION['course'] = $row['course']; 
            $_SESSION['profilePicture'] = $row['profilePicture']; 
            $_SESSION['currentJob'] = $row['currentJob']; 
            $_SESSION['description'] = $row['description']; 
            $_SESSION['location'] = $row['location']; 
            $_SESSION['account_type'] = "Alumni"; 

            header("Location: ../frontend/homePage.php"); // Redirect to admin dashboard
            exit();
        } else {
            // Invalid password
            header("Location: ../frontend/loginPage.php?error=invalid_password");
            exit();
        }
    }

    // Prepare the SQL statement for student login
    $stmt = $conn->prepare("SELECT * FROM tbl_studentaccount WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    // Alumni login check
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc(); // fetch the row first

        if ($password === $row['password']) { // compare entered password
            $_SESSION['fullName'] = $row['fullName'];
            $_SESSION['email'] = $row['email']; // safer to use from DB
            $_SESSION['course'] = $row['course']; // Assuming you want to store the course as well
            $_SESSION['profilePicture'] = $row['profilePicture']; // Assuming you want to store the profile picture
            $_SESSION['account_type'] = "Student"; // You can use this for access control

            header("Location: ../frontend/homePage.php"); // Redirect to admin dashboard
            exit();
        } else {
            // Invalid password
            header("Location: ../frontend/loginPage.php?error=invalid_password");
            exit();
        }
    }
}
?>