<?php
include '../connection/dbConnect.php';
include '../backend/login_process.php';
include '../backend/yearbook_process.php';

$profile_picture = isset($_SESSION['profilePicture']) ? $_SESSION['profilePicture'] : 'Image/Alumni.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Year Book</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">
  <?php include 'nav.php'; ?>

  <div class="mt-32 max-w-6xl mx-auto px-4">
    
    <!-- Page Title -->
    <h2 class="text-3xl font-bold text-green-700 mb-6">Alumni Directory</h2>

    <!-- Filters -->
    <form method="GET" class="flex flex-wrap items-center gap-6 bg-white p-4 rounded-xl shadow mb-10">
      <!-- Batch -->
      <div>
        <label for="batch" class="block text-sm font-semibold text-gray-600 mb-1">Choose Batch</label>
        <select name="batch" id="batch" onchange="this.form.submit()" 
                class="w-48 px-3 py-2 border rounded-lg shadow-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
          <option value="All" <?= $selectedBatch == 'All' ? 'selected' : '' ?>>All</option>
          <?php 
          $batchQuery = mysqli_query($conn, "SELECT DISTINCT batch FROM tbl_alumniaccount ORDER BY batch DESC");
          while ($batchRow = mysqli_fetch_assoc($batchQuery)): ?>
            <option value="<?php echo $batchRow['batch']; ?>" <?= $selectedBatch == $batchRow['batch'] ? 'selected' : '' ?>>
              <?php echo htmlspecialchars($batchRow['batch']); ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>

      <!-- Course -->
      <div>
        <label for="course" class="block text-sm font-semibold text-gray-600 mb-1">Choose Course</label>
        <select name="course" id="course" onchange="this.form.submit()" 
                class="w-64 px-3 py-2 border rounded-lg shadow-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
          <option value="All" <?= $selectedCourse == 'All' ? 'selected' : '' ?>>All</option>
          <?php 
          $courseQuery = mysqli_query($conn, "SELECT DISTINCT course FROM tbl_alumniaccount ORDER BY course");
          while ($courseRow = mysqli_fetch_assoc($courseQuery)): ?>
            <option value="<?php echo $courseRow['course']; ?>" <?= $selectedCourse == $courseRow['course'] ? 'selected' : '' ?>>
              <?php echo htmlspecialchars($courseRow['course']); ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
    </form>

    <!-- Alumni Display -->
    <?php if (!empty($alumniByBatch)): ?>
      <?php foreach ($alumniByBatch as $batch => $courses): ?>
        
        <h2 class="text-2xl font-semibold text-green-600 mb-4">Batch: <?php echo htmlspecialchars($batch); ?></h2>

        <?php foreach ($courses as $course => $alumniList): ?>
          <h3 class="text-xl font-medium text-gray-700 mb-3 ml-4">Course: <?php echo htmlspecialchars($course); ?></h3>

          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6 mb-10">
            <?php foreach ($alumniList as $alumni): 
              $fullName = htmlspecialchars($alumni['fullName']);
              $profilePic = !empty($alumni['profilePicture']) ? $alumni['profilePicture'] : 'Image/Alumni.png';
            ?>
              <a href="profile.php?id=<?php echo $alumni['id']; ?>" 
                 class="bg-white rounded-xl shadow hover:shadow-lg p-4 flex flex-col items-center transition">
                <img src="<?php echo $profilePic; ?>" 
                     alt="Profile Picture" 
                     class="w-28 h-28 object-cover rounded-full border-2 border-green-500">
                <p class="mt-3 text-sm font-medium text-gray-800 text-center"><?php echo $fullName; ?></p>
              </a>
            <?php endforeach; ?>
          </div>

        <?php endforeach; ?>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-gray-500 italic">No alumni found for this selection.</p>
    <?php endif; ?>

  </div>
</body>
</html>
