<?php   

include '../connection/dbConnect.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body>
    <?php include 'nav.php'; // Include the navigation bar ?>
    <div>
        <img src="../colm/colmpage.png" alt="COLM Logo" class="w-full h-180 mt-16">
    </div>
    <div class="flex justify-center mt-8 mb-8  p-4 rounded-lg shadow-lg">
        <a href="https://www.facebook.com/colmamdlsc" target="_blank" class="h-32 w-32 rounded-full mx-2 mb-4 mr-auto ml-auto"><img src="../department/amd.png" alt="COLM Logo" class="h-32 w-32 rounded-full mx-2 mb-4 mr-auto ml-auto"></a>
        <a href="https://www.facebook.com/profile.php?id=100086284696864" target="_blank" class="h-32 w-32 rounded-full mx-2 mb-4 mr-auto ml-auto"><img src="../department/crim.png" alt="COLM Logo" class="h-32 w-34 rounded-full mx-2 mb-4 mr-auto ml-auto"></a>
        <a href="https://www.facebook.com/COLMCSD" target="_blank" class="h-35 w-35 rounded-full mx-2 mb-4 mr-auto ml-auto"><img src="../department/csd.png" alt="COLM Logo" class="h-32 w-35 rounded-full mx-2 mb-4 mr-auto ml-auto"></a>
        <a href="https://www.facebook.com/profile.php?id=100068650730627" target="_blank" class="h-32 w-32 rounded-full mx-2 mb-4 mr-auto ml-auto"><img src="../department/htm.png" alt="COLM Logo" class="h-32 w-32 rounded-full mx-2 mb-4 mr-auto ml-auto"></a>
        <a href="https://www.facebook.com/Colmroentgenology" target="_blank" class="h-32 w-32 rounded-full mx-2 mb-4 mr-auto ml-auto"><img src="../department/radtech.png" alt="COLM Logo" ></a>      
    </div>
    <div>
        <h1 class="text-4xl font-bold text-center mt-8">Welcome to AlumniConnect</h1>
        <p class="text-lg text-center mt-4">Connecting alumni from College of Our Lady of Mercy Pulilan Inc.</p>
    </div>
    <div>
        <div>
            <h1 class="text-2xl font-bold text-center mt-8">Notable Alumni</h1>
            <div class="flex justify-center mt-4">
                <div class="text-center bg-white shadow-lg rounded-lg p-6 m-4 max-w-sm">
                    <img src="../notable_alumni/ralph.jpg" alt="Alumni 1" class="h-32 w-32 rounded-full mx-2 mb-4 mr-auto ml-auto">
                    <h2 class="text-xl font-semibold mt-2">Ralph Lawrence A. Reyes</h2>
                    <h3 class="text-lg text-gray-600">Batch 2019</h3>
                    <p class="text-gray-500 mt-1">Software Engineer at Tech Innovations</p>
                    <p class="text-gray-500 mt-1">Ralph is a software engineer who has made significant contributions to the tech industry, specializing in AI and machine learning.</p>                 
                </div>
                <div class="text-center bg-white shadow-lg rounded-lg p-6 m-4 max-w-sm">
                    <img src="../notable_alumni/ralph.jpg" alt="Alumni 1" class="h-32 w-32 rounded-full mx-2 mb-4 mr-auto ml-auto">
                    <h2 class="text-xl font-semibold mt-2">Ralph Lawrence A. Reyes</h2>
                    <h3 class="text-lg text-gray-600">Batch 2019</h3>
                    <p class="text-gray-500 mt-1">Software Engineer at Tech Innovations</p>
                    <p class="text-gray-500 mt-1">Ralph is a software engineer who has made significant contributions to the tech industry, specializing in AI and machine learning.</p>                 
                </div>
                <div class="text-center bg-white shadow-lg rounded-lg p-6 m-4 max-w-sm">
                    <img src="../notable_alumni/ralph.jpg" alt="Alumni 1" class="h-32 w-32 rounded-full mx-2 mb-4 mr-auto ml-auto">
                    <h2 class="text-xl font-semibold mt-2">Ralph Lawrence A. Reyes</h2>
                    <h3 class="text-lg text-gray-600">Batch 2019</h3>
                    <p class="text-gray-500 mt-1">Software Engineer at Tech Innovations</p>
                    <p class="text-gray-500 mt-1">Ralph is a software engineer who has made significant contributions to the tech industry, specializing in AI and machine learning.</p>                 
                </div>
            </div>
        </div>
    </div>
    <div>
    <h1 class="text-start ml-20 text-2xl mt-15 font-bold ">What's happening this month?</h1>

    <div class="flex justify-center mt-8">
        <table class="w-full max-w-5xl">
            <?php
            $currentMonth = date('m');
            $currentYear = date('Y');

            $query = "SELECT * FROM tbl_announcement WHERE MONTH(date) = ? AND YEAR(date) = ? ORDER BY date ASC";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ss", $currentMonth, $currentYear);
            $stmt->execute();
            $result = $stmt->get_result();

            $count = 0;
            while ($row = $result->fetch_assoc()) {
                $imagePath = (!empty($row['image'])) ? "../announcement_images/" . $row['image'] : "Image/Announcement.JPG";

                if ($count % 2 == 0) {
                    // Image Left, Text Right
                    echo '
                    <tr class="align-top">
                        <td class="p-4">
                            <img src="' . $imagePath . '" alt="Announcement Image" class="w-85 h-85 object-cover rounded-2xl shadow-lg mx-auto">
                        </td>
                        <td class="p-4 align-top">
                            <div class="text-start">
                                <h2 class="text-2xl font-bold text-gray-800 mt-20">' . htmlspecialchars($row['title']) . '</h2>
    
                                <p class="text-gray-500 mt-2"><strong>Date:</strong> ' . date("F j, Y", strtotime($row['date'])) . '</p>
                                <p class="text-gray-500"><strong>Location:</strong> ' . htmlspecialchars($row['location']) . '</p>
                                <p class="text-gray-500"><strong>Time:</strong> ' . htmlspecialchars($row['time']) . '</p>
                                                            <p class="text-gray-600 mt-2">' . nl2br(htmlspecialchars($row['body'])) . '</p>
                            </div>
                        </td>
                    </tr>';
                } else {
                    // Text Left, Image Right
                    echo '
                    <tr class="align-top">
                        <td class="p-4 align-top">
                            <div class="text-start">
                                <h2 class="text-2xl font-bold text-gray-800 mt-20">' . htmlspecialchars($row['title']) . '</h2>

                                <p class="text-gray-500 mt-2"><strong>Date:</strong> ' . date("F j, Y", strtotime($row['date'])) . '</p>
                                <p class="text-gray-500"><strong>Location:</strong> ' . htmlspecialchars($row['location']) . '</p>
                                <p class="text-gray-500"><strong>Time:</strong> ' . htmlspecialchars($row['time']) . '</p>
                                <p class="text-gray-600 mt-2 indent-8">' . nl2br(htmlspecialchars($row['body'])) . '</p>
                            </div>
                        </td>
                        <td class="p-4">
                            <img src="' . $imagePath . '" alt="Announcement Image" class="w-85 h-85 object-cover rounded-2xl shadow-lg mx-auto">
                        </td>
                    </tr>';
                }
                $count++;
            }

            if ($count === 0) {
                echo "<tr><td colspan='2' class='text-center text-gray-500 p-6'>No announcements this month.</td></tr>";
            }

            $stmt->close();
            ?>
        </table>
    </div>
    <div>
        <h1 class="text-start ml-20 text-2xl mt-15 font-bold ">Alumni Testimonials</h1>
        <div class="flex justify-center mt-4">
            <div class="text-center bg-white shadow-lg rounded-lg p-6 m-4 max-w-sm">
                <img src="../notable_alumni/ralph.jpg" alt="Testimonial 1" class="h-32 w-32 rounded-full mx-2 mb-4 mr-auto ml-auto">
                <h2 class="text-xl font-semibold mt-2">Ralph Lawrence A. Reyes</h2>
                <p class="text-gray-500 mt-1">"AlumniConnect has been a great platform for me to reconnect with my classmates and network with other professionals."</p>
            </div>
            <div class="text-center bg-white shadow-lg rounded-lg p-6 m-4 max-w-sm">
                <img src="../notable_alumni/ralph.jpg" alt="Testimonial 2" class="h-32 w-32 rounded-full mx-2 mb-4 mr-auto ml-auto">
                <h2 class="text-xl font-semibold mt-2">Ralph Lawrence A. Reyes</h2>
                <p class="text-gray-500 mt-1">"I found my dream job through AlumniConnect! The hiring events are fantastic."</p>
            </div>
            <div class="text-center bg-white shadow-lg rounded-lg p-6 m-4 max-w-sm">
                <img src="../notable_alumni/ralph.jpg" alt="Testimonial 3" class="h-32 w-32 rounded-full mx-2 mb-4 mr-auto ml-auto">
                <h2 class="text-xl font-semibold mt-2">Ralph Lawrence A. Reyes</h2>
                <p class="text-gray-500 mt-1">"The alumni wall is a great way to stay updated on what my classmates are doing."</p>
            </div>
        </div>
    </div>
    <footer class="bg-green-700 text-white text-center p-4 mt-8">
        <p>&copy; 2025 AlumniConnect. All rights reserved.</p>
        <p>Contact us at: <a href="mailto:colmalumniad@gmail.com" class="text-lime-400">colmalumniad@gmail.com</a></p>
        <p>Follow us on social media: 
            <a href="#" class="text-lime-400">Facebook</a>, 
            <a href="#" class="text-lime-400">Twitter</a>, 
            <a href="#" class="text-lime-400">Instagram</a>
        </p>
    </footer>
</body>
</html>