<?php
include '../connection/dbConnect.php';
session_start();

// Fetch alumni posts
$postQuery = mysqli_query($conn, "
    SELECT 
        p.id,
        p.postContent,
        p.postDate,
        a.fullName,
        GROUP_CONCAT(i.imagePath ORDER BY i.id ASC) AS images
    FROM tbl_alumnipost p
    JOIN tbl_alumniaccount a ON p.alumniId = a.id
    LEFT JOIN tbl_imagepost i ON p.id = i.postId
    GROUP BY p.id
    ORDER BY p.id DESC
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Manage Alumni Posts</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

  <?php include 'adminNavigation.php'; ?>

  <!-- Sidebar + Main -->
  <div class="flex mt-16"> <!-- push down from navbar -->

    <!-- Main Content -->
    <main class="ml-64 p-8 w-full">
      <div class="bg-white rounded-xl shadow-md p-6">

        <!-- Page Header -->
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-semibold text-gray-800">Alumni Posts</h2>
        </div>

        <!-- Alumni Posts Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-left border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-green-600 text-white">
              <tr>
                <th class="px-4 py-3">Alumni Name</th>
                <th class="px-4 py-3">Post Content</th>
                <th class="px-4 py-3">Images</th>
                <th class="px-4 py-3">Date Posted</th>
                <th class="px-4 py-3 text-center">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <?php while ($post = mysqli_fetch_assoc($postQuery)) { ?>
                <tr class="hover:bg-gray-100">
                  <td class="px-4 py-3 font-medium text-gray-900">
                    <?php echo htmlspecialchars($post['fullName']); ?>
                  </td>
                  <td class="px-4 py-3 text-gray-700">
                    <?php echo htmlspecialchars($post['postContent']); ?>
                  </td>
                  <td class="px-4 py-3">
                    <?php
                      $imageQuery = mysqli_query($conn, "SELECT * FROM tbl_imagepost WHERE postId = {$post['id']}");
                      if (mysqli_num_rows($imageQuery) > 0) {
                          echo "<div class='flex flex-wrap gap-2'>";
                          while ($image = mysqli_fetch_assoc($imageQuery)) {
                              echo "<img src='{$image['imagePath']}' 
                                        alt='Post Image' 
                                        class='expandable-image w-20 h-20 object-cover rounded-lg shadow cursor-pointer hover:scale-105 transition-transform'>";
                          }
                          echo "</div>";
                      }
                    ?>
                  </td>
                  <td class="px-4 py-3 text-gray-600">
                    <?php echo htmlspecialchars($post['postDate']); ?>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <a href="../backend/admin_deletePost.php?id=<?php echo $post['id']; ?>"
                      onclick="return confirm('Are you sure you want to delete this post?')"
                      class="inline-block px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg shadow">
                      Delete
                    </a>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </main>
  </div>

  <!-- Lightbox Modal -->
  <div id="imageModal" class="fixed inset-0 hidden bg-black bg-opacity-70 justify-center items-center z-50">
    <span class="absolute top-6 right-10 text-white text-3xl font-bold cursor-pointer" onclick="closeModal()">&times;</span>
    <img id="expandedImage" class="max-h-[80%] max-w-[90%] rounded-xl shadow-2xl border-4 border-white">
  </div>

  <script>
    // Open modal with clicked image
    var images = document.querySelectorAll('.expandable-image');
    images.forEach(function(image) {
      image.onclick = function() {
        var modal = document.getElementById("imageModal");
        var modalImg = document.getElementById("expandedImage");
        modal.classList.remove("hidden");
        modal.classList.add("flex");
        modalImg.src = this.src;
      };
    });

    function closeModal() {
      var modal = document.getElementById("imageModal");
      modal.classList.add("hidden");
    }

    window.onclick = function(event) {
      var modal = document.getElementById("imageModal");
      if (event.target === modal) {
        closeModal();
      }
    }
  </script>
</body>
</html>
