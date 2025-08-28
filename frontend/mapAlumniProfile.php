<?php

include '../connection/dbConnect.php'; // Ensure you have a database connection
include '../backend/mapAlumniProfile_process.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $full_name; ?>'s Profile</title>
    <link rel="stylesheet" href="navigation.css">
</head>
<body>
    <?php include 'nav.php'; ?>

    <div class="profile-container">
        <table class="profile-table" align="center">
            <tr>
                <td>
                    <div class="profile-header">
                        <img src="<?php echo $profile_picture; ?>" alt="Profile Picture" class="profile-picture">
                        <div class="profile-info">
                            <p><strong>Name:</strong> <?php echo $full_name; ?></p>
                            <p><strong>Course:</strong> <?php echo $course; ?></p>
                            <p><strong>Batch:</strong> <?php echo $batch; ?></p>
                            <p><strong>Current Job:</strong> <?php echo $job; ?></p>
                            <p><strong>Location:</strong> <?php echo $location; ?></p>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <h3>Description:</h3>
                    <p><?php echo $description; ?></p>
                </td>
            </tr>
        </table>
    </div>

    <footer>
        <p>&copy; 2025 COLM AlumniConnect. All rights reserved.</p>
    </footer>
</body>
</html>