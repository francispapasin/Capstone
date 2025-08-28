<?php
include '../connection/dbConnect.php';
session_start();

// Fetch announcements
$query = mysqli_query($conn, "SELECT * FROM tbl_announcement ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Manage Announcements</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

  <?php include 'adminNavigation.php'; ?>

  <!-- Sidebar + Main -->
  <div class="flex mt-16">

    <!-- Main Content -->
    <main class="ml-64 p-8 w-full">
      <div class="bg-white rounded-xl shadow-md p-6">

        <!-- Page Header -->
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-semibold text-gray-800">Manage Announcements</h2>
          <button onclick="openModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
            + Add Announcement
          </button>
        </div>

        <!-- Announcements Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-left border border-gray-200 rounded-lg overflow-hidden">
            <thead class="bg-green-600 text-white">
              <tr>
                <th class="px-4 py-3">Title</th>
                <th class="px-4 py-3">Date</th>
                <th class="px-4 py-3">Time</th>
                <th class="px-4 py-3">Location</th>
                <th class="px-4 py-3">Body</th>
                <th class="px-4 py-3">Image</th>
                <th class="px-4 py-3 text-center">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <?php while ($row = mysqli_fetch_assoc($query)) { ?>
                <tr class="hover:bg-gray-100">
                  <td class="px-4 py-3 font-medium text-gray-900"><?php echo htmlspecialchars($row['title']); ?></td>
                  <td class="px-4 py-3"><?php echo htmlspecialchars($row['date']); ?></td>
                  <td class="px-4 py-3"><?php echo htmlspecialchars($row['time']); ?></td>
                  <td class="px-4 py-3"><?php echo htmlspecialchars($row['location']); ?></td>
                  <td class="px-4 py-3"><?php echo htmlspecialchars($row['body']); ?></td>
                  <td class="px-4 py-3">
                    <?php if (!empty($row['image'])) { ?>
                      <img src="../announcement_images/<?php echo $row['image']; ?>" class="w-20 h-20 object-cover rounded-md shadow">
                    <?php } ?>
                  </td>
                  <td class="px-4 py-3 text-center">
                    <button 
                      onclick="openEditModal(
                        '<?php echo $row['id']; ?>',
                        '<?php echo htmlspecialchars($row['title']); ?>',
                        '<?php echo $row['date']; ?>',
                        '<?php echo $row['time']; ?>',
                        '<?php echo htmlspecialchars($row['location']); ?>',
                        `<?php echo htmlspecialchars($row['body']); ?>`,
                        '<?php echo $row['image']; ?>'
                      )" 
                      class="text-blue-600 hover:text-blue-800">
                      Edit
                    </button>
                    <a href="../backend/admin_deleteAnnouncement_process.php?id=<?php echo $row['id']; ?>" 
                      class="text-red-600 hover:text-red-800" 
                      onclick="return confirm('Are you sure you want to delete this announcement?');">
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

  <!-- Add Announcement Modal -->
  <div id="announcementModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-lg p-6 rounded-xl shadow-lg relative">
      <button class="absolute top-3 right-3 text-gray-500 text-xl" onclick="closeModal()">&times;</button>
      
      <h2 class="text-xl font-bold mb-4">Add New Announcement</h2>
      <form action="../backend/admin_createAnnouncement.php" method="POST" enctype="multipart/form-data" class="space-y-4">

        <input type="text" name="title" placeholder="Title" class="w-full border px-3 py-2 rounded-md" required>
        
        <input type="date" name="date" class="w-full border px-3 py-2 rounded-md" required>
        
        <input type="time" name="time" class="w-full border px-3 py-2 rounded-md" required>
        
        <input type="text" name="location" placeholder="Location" class="w-full border px-3 py-2 rounded-md" required>

        <textarea name="body" rows="5" placeholder="Announcement body..." class="w-full border px-3 py-2 rounded-md resize-none" required></textarea>
        
        <input type="file" name="image" accept="image/*" class="w-full border px-3 py-2 rounded-md">

        <div class="text-center">
          <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
            Submit
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Edit Announcement Modal -->
<div id="editAnnouncementModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
  <div class="bg-white w-full max-w-lg p-6 rounded-xl shadow-lg relative">
    <button class="absolute top-3 right-3 text-gray-500 text-xl" onclick="closeEditModal()">&times;</button>
    
    <h2 class="text-xl font-bold mb-4">Edit Announcement</h2>
    <form id="editForm" action="../backend/admin_editAnnouncement_process.php" method="POST" enctype="multipart/form-data" class="space-y-4">

      <!-- Hidden ID -->
      <input type="hidden" name="edit_id" id="edit_id">

      <input type="text" name="title" id="edit_title" placeholder="Title" class="w-full border px-3 py-2 rounded-md" required>
      
      <input type="date" name="date" id="edit_date" class="w-full border px-3 py-2 rounded-md" required>
      
      <input type="time" name="time" id="edit_time" class="w-full border px-3 py-2 rounded-md" required>
      
      <input type="text" name="location" id="edit_location" placeholder="Location" class="w-full border px-3 py-2 rounded-md" required>

      <textarea name="body" id="edit_body" rows="5" placeholder="Announcement body..." class="w-full border px-3 py-2 rounded-md resize-none" required></textarea>
      
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Current Image:</label>
        <img id="currentImage" src="" class="w-20 h-20 object-cover rounded-md shadow mb-2">
        <input type="file" name="image" accept="image/*" class="w-full border px-3 py-2 rounded-md">
      </div>

      <div class="text-center">
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
          Update
        </button>
      </div>
    </form>
  </div>
</div>


  <script>
    function openModal() {
      document.getElementById('announcementModal').classList.remove('hidden');
    }
    function closeModal() {
      document.getElementById('announcementModal').classList.add('hidden');
    }
    window.onclick = function(event) {
      let modal = document.getElementById('announcementModal');
      if (event.target == modal) {
        modal.classList.add('hidden');
      }
    }
  </script>

  <script>
  function openEditModal(id, title, date, time, location, body, image) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_title').value = title;
    document.getElementById('edit_date').value = date;
    document.getElementById('edit_time').value = time;
    document.getElementById('edit_location').value = location;
    document.getElementById('edit_body').value = body;
    document.getElementById('currentImage').src = "../backend/announcement_image/" + image;

    document.getElementById('editAnnouncementModal').classList.remove('hidden');
  }

  function closeEditModal() {
    document.getElementById('editAnnouncementModal').classList.add('hidden');
  }

  window.onclick = function(event) {
    let addModal = document.getElementById('announcementModal');
    let editModal = document.getElementById('editAnnouncementModal');
    if (event.target == addModal) addModal.classList.add('hidden');
    if (event.target == editModal) editModal.classList.add('hidden');
  }
</script>

</body>
</html>
