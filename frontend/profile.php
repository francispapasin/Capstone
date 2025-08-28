<?php 
include '../connection/dbConnect.php';
include '../backend/login_process.php'; // Ensure session is started
include '../backend/profile_process.php'; // Include the profile processing script
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $full_name; ?>'s Profile</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

  <?php include 'nav.php'; ?>

  <!-- Profile Section -->
  <div class="max-w-6xl mx-auto mt-40 px-6 w-820">
    <div class="bg-white rounded-2xl shadow-lg p-8 flex flex-col md:flex-row items-start gap-10">
      
      <!-- Profile Picture -->
      <div class="flex-shrink-0">
        <?php
            // Fetch profile picture of this post's author
            $profile_img_query = "SELECT profilePicture FROM tbl_alumniaccount WHERE id = ?";
            $stmt = $conn->prepare($profile_img_query);
            $stmt->bind_param("i", $userID);
            $stmt->execute();
            $img_result = $stmt->get_result();
            $img_data = $img_result->fetch_assoc();

            $author_picture = !empty($img_data['profilePicture']) ? $img_data['profilePicture'] : 'Image/Alumni.png';
        ?>
        <img src="<?php echo htmlspecialchars($author_picture); ?>" 
            alt="Author Profile Picture" 
            class="w-40 h-40 rounded-full object-cover">
      </div>


      <!-- User Info -->
      <div class="flex-1">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-800"><?php echo $full_name; ?></h1>
          </div>
        </div>

        <!-- About -->
        <div class="mt-6 border-t pt-4">
          <h3 class="font-semibold text-xl mb-2">About Me</h3>
          <p class="text-gray-700 leading-relaxed">
            <?php echo !empty($description) ? $description : "No description available."; ?>
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Layout: Quick Info + Posts -->
  <div class="max-w-6xl mx-auto mt-12 px-6 grid grid-cols-1 md:grid-cols-3 gap-8">

    <!-- Quick Info -->
    <div class="md:col-span-1 space-y-6">
      <div class="bg-white p-6 rounded-2xl shadow">
        <h2 class="text-lg font-semibold text-gray-700">Quick Info</h2>
        <ul class="mt-3 space-y-2 text-gray-600 text-sm">
          <li>📚 Course: <?php echo $course; ?></li>
          <li>🎓 Batch: <?php echo $batch; ?></li>
          <li>💼 Job: <?php echo !empty($job) ? $job : 'N/A'; ?></li>
          <li>📍 Location: <?php echo !empty($location) ? $location : 'N/A'; ?></li>
        </ul>
      </div>
    </div>

    <!-- Posts Section -->
    <div class="md:col-span-2 space-y-6">
      <?php if ($post_result->num_rows > 0): ?>
        <?php while ($row = $post_result->fetch_assoc()): ?>
          <?php
            // Fetch profile picture of this post's author
            $profile_img_query = "SELECT profilePicture FROM tbl_alumniaccount WHERE id = $userID";
            $img_result = $conn->query($profile_img_query);
            $img_data = $img_result->fetch_assoc();
            $author_picture = !empty($img_data['profilePicture']) ? $img_data['profilePicture'] : 'Image/Alumni.png';
            $postedDate = new DateTime($row['postDate'], new DateTimeZone('Asia/Manila'));
          ?>
          <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
            <!-- Post Header -->
            <div class="flex items-center gap-3 mb-3">
              <img src="<?php echo $author_picture; ?>" 
                   class="w-12 h-12 rounded-full object-cover">
              <div>
                <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($row['fullName']); ?></span>
                <p class="text-xs text-gray-500">Posted on <?php echo $postedDate->format('F j, Y g:i A'); ?></p>
              </div>
            </div>

            <!-- Post Content -->
            <p class="mb-4 text-gray-700"><?php echo htmlspecialchars($row['postContent']); ?></p>

            <!-- Images -->
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-3">
              <?php
                $post_id = $row['postId'];
                $images_query = "SELECT imagePath FROM tbl_imagepost WHERE postId = $post_id";
                $images_result = $conn->query($images_query);
                if ($images_result->num_rows > 0):
                  while ($img = $images_result->fetch_assoc()): ?>
                    <img src="<?php echo $img['imagePath']; ?>" 
                         alt="Post Image" 
                         class="w-full h-40 rounded-lg object-cover cursor-pointer"
                         onclick="openModal('<?php echo $img['imagePath']; ?>')">
                  <?php endwhile;
                else: ?>
                  <p class="text-sm text-gray-400 col-span-2 text-center">No images attached.</p>
              <?php endif; ?>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="text-center text-gray-500">This alumni has not posted anything yet.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Image Modal -->
  <div id="imageModal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-90 flex items-center justify-center">
    <span class="absolute top-6 right-8 text-white text-4xl font-bold cursor-pointer" onclick="closeModal()">&times;</span>
    <img id="expandedImage" class="max-w-4xl max-h-[90vh] rounded-lg shadow-lg">
  </div>

  <!-- Footer -->
  <footer class="text-center mt-12 py-6 text-gray-500 text-sm">
    &copy; 2025 COLM AlumniConnect. All rights reserved.
  </footer>

  <!-- JS for Modal -->
  <script>
    function openModal(src) {
      document.getElementById("expandedImage").src = src;
      document.getElementById("imageModal").classList.remove("hidden");
    }
    function closeModal() {
      document.getElementById("imageModal").classList.add("hidden");
    }
  </script>

</body>
</html>
