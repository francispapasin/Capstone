<?php
session_start();
include '../connection/dbConnect.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/src/Exception.php';
require '../phpmailer/src/PHPMailer.php';
require '../phpmailer/src/SMTP.php';

// Function to Send Email Notification using PHPMailer
function sendRegistrationEmail($recipientEmail, $full_name) {
    $mail = new PHPMailer();

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'colmalumniad@gmail.com'; // Your Gmail
    $mail->Password   = 'qsrwsimsiwakpfpj';      // App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
    $mail->Port       = 465;

    // Email Content
    $mail->setFrom('colmalumniad@gmail.com', 'Admin Team');
    $mail->addAddress($recipientEmail);

    $mail->isHTML(true);
    $mail->Subject = 'Welcome to COLM AlumniConnect!';
    $mail->Body    = "<h3>Hi $full_name,</h3>
                      <p>Thank you for joining our COLM AlumniConnect. 
                      Your account has been created successfully.</p>";

    if ($mail->send()) {
        return true;
    } else {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $last_name   = trim($_POST['last_name']);
    $first_name  = trim($_POST['first_name']);
    $middle_name = trim($_POST['middle_name']);
    $email       = trim($_POST['email']);
    $password    = trim($_POST['password']);
    $batch       = trim($_POST['batch']);
    $course      = trim($_POST['course']);

    // Validation
    if (empty($last_name) || empty($first_name) || empty($email) || empty($password)) {
        header("Location: ../frontend/adminManageAlumni.php?error=empty_fields");
        exit();
    }

    // Middle initial
    $middle_initial = "";
    if (!empty($middle_name)) {
        $words = explode(" ", $middle_name);
        foreach ($words as $word) {
            if (!empty($word)) {
                $middle_initial = strtoupper(substr($word, 0, 1)) . ".";
                break;
            }
        }
    }

    // Full name format: Last, First M.
    $full_name = "$last_name, $first_name $middle_initial";

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert into DB
    $sql = "INSERT INTO tbl_alumniaccount (fullName, email, password, batch, course) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $full_name, $email, $hashedPassword, $batch, $course);

    if ($stmt->execute()) {
        // ✅ No session overwrite here
        // Alumni must login later using their new account

        // Send Email
        if (sendRegistrationEmail($email, $full_name)) {
            header("Location: ../frontend/adminManageAlumni.php?success=account_created");
        } else {
            header("Location: ../frontend/adminManageAlumni.php?warning=email_failed");
        }
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
