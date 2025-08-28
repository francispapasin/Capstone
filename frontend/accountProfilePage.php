<?php  
session_start();
include '../connection/dbConnect.php'; 
include '../backend/login_process.php'; 

if (!isset($_SESSION['email'])) {
    header("Location: loginPage.php");
    exit();
}


// Profile picture with fallback
$profile_picture = !empty($_SESSION['profilePicture']) ? $_SESSION['profilePicture'] : 'Image/Alumni.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $_SESSION['fullName']; ?>'s Profile</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">

  <?php include 'nav.php'; ?>

  <!-- Profile Section -->
  <div class="max-w-6xl mx-auto mt-40 px-6 w-820">
    <div class="bg-white rounded-2xl shadow-lg p-8 flex flex-col md:flex-row items-start gap-10">
      
      <!-- Profile Picture -->
      <div class="flex-shrink-0">
        <img src="<?php echo $profile_picture; ?>" 
             class="w-40 h-40 rounded-full border-4 border-gray-200 shadow-lg object-cover">
      </div>

      <!-- User Info -->
      <div class="flex-1">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
          <div>
            <h1 class="text-3xl font-bold text-gray-800"><?php echo $_SESSION['fullName']; ?></h1>
          </div>
          <a href="createAlumniProfile.php" 
             class="mt-4 md:mt-0 bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg shadow transition">
            Edit Profile
          </a>
        </div>

        <!-- About -->
        <div class="mt-6 border-t pt-4">
          <h3 class="font-semibold text-xl mb-2">About Me</h3>
          <p class="text-gray-700 leading-relaxed">
            <?php echo !empty($_SESSION['description']) ? $_SESSION['description'] : "No description available."; ?>
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
          <li>📚 Course: <?php echo $_SESSION['course']; ?></li>
          <li>🎓 Batch: <?php echo $_SESSION['batch']; ?></li>
          <li>💼 Job: <?php echo $_SESSION['currentJob'] ?? 'N/A'; ?></li>
          <li>📍 Location: <?php echo $_SESSION['location'] ?? 'N/A'; ?></li>
        </ul>
      </div>
    </div>

    <!-- Posts Section -->
    <div class="md:col-span-2 space-y-6">
      <?php
      $user_id = $_SESSION['id'];
      $query = "SELECT p.id AS postId, a.id AS alumniId, a.fullName, 
                       p.postContent, p.postDate
                FROM tbl_alumnipost p
                JOIN tbl_alumniaccount a ON p.alumniId = a.id
                WHERE p.alumniId = $user_id
                ORDER BY p.postDate DESC";
      $result = $conn->query($query);

      while ($row = $result->fetch_assoc()):
        $alumni_id = $row['alumniId'];
        $user_query = "SELECT profilePicture FROM tbl_alumniaccount WHERE id = $alumni_id";
        $user_result = $conn->query($user_query);
        $user = $user_result->fetch_assoc();
        $profile_picture = !empty($user['profilePicture']) ? $user['profilePicture'] : 'Image/Alumni.png';
        $postedDate = new DateTime($row['postDate'], new DateTimeZone('Asia/Manila'));
      ?>
        <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition">
          <!-- Post Header -->
          <div class="flex items-center gap-3 mb-3">
            <a href="accountProfilePage.php?id=<?php echo $alumni_id; ?>">
              <img src="<?php echo $profile_picture; ?>" 
                   class="w-12 h-12 rounded-full object-cover">
            </a>
            <div>
              <a href="accountProfilePage.php?id=<?php echo $alumni_id; ?>" 
                 class="font-semibold text-gray-800 hover:underline">
                <?php echo htmlspecialchars($row['fullName']); ?>
              </a>
              <p class="text-xs text-gray-500">Posted on <?php echo $postedDate->format('F j, Y g:i A'); ?></p>
            </div>
          </div>

          <!-- Post Content -->
          <p class="mb-4 text-gray-700"><?php echo htmlspecialchars($row['postContent']); ?></p>

          <!-- Images -->
          <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mb-3">
            <?php
            $post_id = $row['postId'];
            $img_query = "SELECT imagePath FROM tbl_imagepost WHERE postId = $post_id";
            $img_result = $conn->query($img_query);
            if ($img_result->num_rows > 0):
              while ($img = $img_result->fetch_assoc()): ?>
                <img src="<?php echo $img['imagePath']; ?>" alt="Post Image" 
                     class="w-full h-40 rounded-lg object-cover">
              <?php endwhile;
            else: ?>
              <p class="text-sm text-gray-400 col-span-2 text-center">No images attached.</p>
            <?php endif; ?>
          </div>

          <!-- Action Buttons -->
          <?php if ($row['alumniId'] == $_SESSION['id']): ?>
            <div class="flex gap-2">
              <!-- Edit Button (opens modal) -->
              <button type="button" 
                      class="px-3 py-1 text-sm bg-yellow-500 hover:bg-yellow-600 text-white rounded-md"
                      onclick="openEditModal('<?php echo $row['postId']; ?>', `<?php echo htmlspecialchars($row['postContent']); ?>`)">
                Edit
              </button>

              <form method="post" action="../backend/alumni_deletePost.php" onsubmit="return confirm('Are you sure you want to delete this post?');">
  <input type="hidden" name="postId" value="<?php echo $row['postId']; ?>">
  <button type="submit" 
          class="px-3 py-1 text-sm bg-red-600 hover:bg-red-700 text-white rounded-md">
    Delete
  </button>
</form>

            </div>
          <?php endif; ?>

        </div>
      <?php endwhile; ?>
    </div>
  </div>
  <!-- Edit Post Modal -->
  <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-lg p-6 rounded-xl shadow-lg relative">
      <h2 class="text-xl font-semibold mb-4">Edit Post</h2>

      <!-- Close Button -->
      <button onclick="closeEditModal()" 
              class="absolute top-3 right-3 text-gray-600 hover:text-gray-800 text-lg">&times;</button>

      <form method="POST" action="alumniEditPost.php" enctype="multipart/form-data" class="space-y-4">
        <input type="hidden" name="postId" id="editPostId">

        <!-- Post Content -->
        <textarea name="postContent" id="editPostContent" rows="4"
                  class="w-full border border-gray-300 rounded-lg p-3 focus:ring-2 focus:ring-green-500"></textarea>

        <!-- Upload New Images -->
        <div>
          <label class="block text-sm font-medium text-gray-700">Add Images (optional)</label>
          <input type="file" name="new_images[]" multiple
                class="mt-2 block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 
                        file:rounded-lg file:border-0 file:text-sm file:font-semibold 
                        file:bg-green-600 file:text-white hover:file:bg-green-700">
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
          <button type="submit" 
                  class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow">
            Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Footer -->
  <footer class="text-center mt-12 py-6 text-gray-500 text-sm">
    &copy; 2025 COLM AlumniConnect. All rights reserved.
  </footer>
<script>
  function openEditModal(postId, postContent) {
    document.getElementById('editPostId').value = postId;
    document.getElementById('editPostContent').value = postContent;
    document.getElementById('editModal').classList.remove('hidden');
  }

  function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
  }
</script>

</body>
</html>
