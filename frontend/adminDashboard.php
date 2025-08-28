<?php 
session_start();

include '../connection/dbConnect.php'; 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "Admin") {
    header("Location: ../frontend/loginPage.php");
    exit();
} elseif (isset($_SESSION["role"]) && $_SESSION["role"] !== "Admin") {
    header("Location: ../frontend/loginPage.php");
    exit();
}

// Query counts
$query = "SELECT COUNT(*) AS total_alumni FROM tbl_alumniaccount";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$total_alumni = $row['total_alumni']; 

$query_pending = "SELECT COUNT(*) AS total_announcement FROM tbl_announcement";
$result_pending = $conn->query($query_pending);
$row_pending = $result_pending->fetch_assoc();
$total_announcement = $row_pending['total_announcement']; 

$query_posts = "SELECT COUNT(*) AS total_post FROM tbl_alumnipost ";
$result_posts = $conn->query($query_posts);
$row_posts = $result_posts->fetch_assoc();
$total_post = $row_posts['total_post'];

// Query counts
$query = "SELECT COUNT(*) AS total_student FROM tbl_studentaccount";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$total_student = $row['total_student']; 

// Alumni per course
$query_courses = "SELECT course, COUNT(*) AS count FROM tbl_alumniaccount GROUP BY course";
$result_courses = $conn->query($query_courses);
$courses = [];
$course_counts = [];
while ($row_course = $result_courses->fetch_assoc()) {
    $courses[] = $row_course['course'];
    $course_counts[] = $row_course['count'];
}

// Alumni per batch
$query_batches = "SELECT batch, COUNT(*) AS count FROM tbl_alumniaccount GROUP BY batch ORDER BY batch ASC";
$result_batches = $conn->query($query_batches);
$batches = [];
$batch_counts = [];
while ($row_batch = $result_batches->fetch_assoc()) {
    $batches[] = $row_batch['batch'];
    $batch_counts[] = $row_batch['count'];
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 font-sans">

    <?php include 'adminNavigation.php'; // Include the admin navigation bar ?>

  <div class="flex mt-16">

    <!-- Main Content -->
    <main class="ml-64 p-6 w-full">
      <!-- Dashboard Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-md text-center">
          <h2 class="text-xl font-semibold">Total Alumni</h2>
          <p class="text-3xl font-bold text-green-600 mt-2"><?php echo $total_alumni; ?></p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md text-center">
          <h2 class="text-xl font-semibold">Total Student</h2>
          <p class="text-3xl font-bold text-green-600 mt-2"><?php echo $total_student; ?></p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md text-center">
          <h2 class="text-xl font-semibold">Total Alumni Post</h2>
          <p class="text-3xl font-bold text-green-600 mt-2"><?php echo $total_post; ?></p>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md text-center">
          <h2 class="text-xl font-semibold">Total Announcements</h2>
          <p class="text-3xl font-bold text-green-600 mt-2"><?php echo $total_announcement; ?></p>
        </div>
      </div>

      <!-- Charts -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
        <div class="bg-white p-6 rounded-xl shadow-md">
          <h2 class="text-lg font-semibold mb-4 text-center">Alumni Per Course</h2>
          <canvas id="courseChart"></canvas>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-md">
          <h2 class="text-lg font-semibold mb-4 text-center">Alumni Per Batch</h2>
          <canvas id="batchChart"></canvas>
        </div>
      </div>
    </main>
  </div>

  <script>
    var ctx = document.getElementById('courseChart').getContext('2d');
    var courseChart = new Chart(ctx, {
      type: 'pie',
      data: {
        labels: <?php echo json_encode($courses); ?>,
        datasets: [{
          data: <?php echo json_encode($course_counts); ?>,
          backgroundColor: ['#4CAF50', '#2196F3', '#FF9800', '#E91E63', '#9C27B0', '#3F51B5', '#009688']
        }]
      }
    });

    var batchCtx = document.getElementById('batchChart').getContext('2d');
    var batchChart = new Chart(batchCtx, {
      type: 'bar',
      data: {
        labels: <?php echo json_encode($batches); ?>,
        datasets: [{
          data: <?php echo json_encode($batch_counts); ?>,
          backgroundColor: '#4CAF50'
        }]
      }
    });
  </script>
</body>
</html>
