<?php
include '../connection/dbConnect.php';
session_start();

// Fetch alumni records
$query = "SELECT id, fullName, gender, batch, course, graduationPicture FROM tbl_yearbook ORDER BY fullName ASC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin | Manage Year Book</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <?php include 'adminNavigation.php'; // Include the admin navigation bar ?>
    <!-- Sidebar + Main -->
    <div class="flex mt-16"> <!-- push down from navbar -->
      <!-- Main Content -->
      <main class="ml-64 p-8 w-full">
        <div class="bg-white rounded-xl shadow-md p-6">

          <!-- Page Header -->
          <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-gray-800">Alumni Directory</h2>
            <button id="addAlumniBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
              + Add
            </button>
          </div>

          <!-- Alumni Table -->
          <div class="overflow-x-auto">
            <table class="w-full text-left border border-gray-200 rounded-lg overflow-hidden">
              <thead class="bg-green-600 text-white">
                <tr>
                  <th class="px-4 py-3">Graduation Picture</th>
                  <th class="px-4 py-3">Full Name</th>
                  <th class="px-4 py-3">Gender</th>
                  <th class="px-4 py-3">Batch</th>
                  <th class="px-4 py-3">Course</th>
                  <th class="px-4 py-3">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <?php while ($row = $result->fetch_assoc()) { ?>
                  <tr class="hover:bg-gray-100">
                    <td class="px-4 py-3">
        <?php if (!empty($row['graduationPicture'])) { ?>
          <img src="../yearbook/<?php echo htmlspecialchars($row['graduationPicture']); ?>" 
               alt="Graduation Picture" 
               class="w-16 h-16 object-cover rounded-full border">
        <?php } else { ?>
          <span class="text-gray-400 italic">No Picture</span>
        <?php } ?>
      </td>
                    <td class="px-4 py-3"><?php echo htmlspecialchars($row['fullName']); ?></td>
                    <td class="px-4 py-3"><?php echo htmlspecialchars($row['gender']); ?></td>
                    <td class="px-4 py-3"><?php echo htmlspecialchars($row['batch']); ?></td>
                    <td class="px-4 py-3"><?php echo htmlspecialchars($row['course']); ?></td>
                    <td class="px-4 py-3 text-center">
                        <button 
                            onclick="openEditModal(
                                '<?php echo $row['id']; ?>',
                                '<?php echo htmlspecialchars($row['graduationPicture']); ?>',
                                '<?php echo htmlspecialchars($row['fullName']); ?>',
                                '<?php echo htmlspecialchars($row['gender']); ?>',
                                '<?php echo htmlspecialchars($row['batch']); ?>',
                                '<?php echo htmlspecialchars($row['course']); ?>'
                            )" 
                            class="text-blue-600 hover:text-blue-800">
                            Edit
                        </button>
                    <a href="../backend/admin_deleteYearBook_process.php?id=<?php echo $row['id']; ?>" 
                      class="text-red-600 hover:text-red-800" 
                      onclick="return confirm('Are you sure you want to delete this alumni?');">
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
<!-- Add Alumni Modal -->
  <div id="addAlumniModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-lg p-6 rounded-xl shadow-lg relative">
      <button class="absolute top-3 right-3 text-gray-500 text-xl" onclick="document.getElementById('addAlumniModal').classList.add('hidden')">&times;</button>
      
      <h2 class="text-xl font-bold mb-4">Add New Alumni</h2>
      <form action="../backend/admin_createYearBook.php"  method="POST" enctype="multipart/form-data" class="space-y-4">

        <input type="text" name="last_name" placeholder="Last Name" class="w-full border px-3 py-2 rounded-md" required>
        <input type="text" name="first_name" placeholder="First Name" class="w-full border px-3 py-2 rounded-md" required>
        <input type="text" name="middle_name" placeholder="Middle Name" class="w-full border px-3 py-2 rounded-md">
        <select name="gender" class="w-full border px-3 py-2 rounded-md">
          <option value="Male">Male</option>
          <option value="Female">Female</option>
          <option value="Rather Not Say">Rather Not Say</option>
        </select>
        <select name="batch" class="w-full border px-3 py-2 rounded-md">
          <option value="2023 - 2024">2023 - 2024</option>
          <option value="2024 - 2025">2024 - 2025</option>
        </select>
        <select name="course" class="w-full border px-3 py-2 rounded-md">
          <option value="Bachelor of Science in Accountancy">B.S. in Accountancy</option>
          <option value="Bachelor of Science in Business Administration">B.S. in Business Administration</option>
          <option value="Bachelor of Science in Criminology">B.S. in Criminology</option>
          <option value="Bachelor of Science in Education">B.S. in Education</option>
          <option value="Bachelor of Science in Hospitality Management">B.S. in Hospitality Management</option>
          <option value="Bachelor of Science in Information Technology">B.S. in Information Technology</option>
          <option value="Bachelor of Science in Radiologic Technology">B.S. in Radiologic Technology</option>
          <option value="Bachelor of Science in Tourism Management">B.S. in Tourism Management</option>
        </select>
        <input type="file" name="graduationPicture" placeholder="Graduation Picture" class="w-full border px-3 py-2 rounded-md" required>
        <div class="text-center">
          <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
            Add Student
          </button>
        </div>
      </form>
    </div>
  </div>
  <!-- Edit Alumni Modal -->
<div id="editAlumniModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
  <div class="bg-white w-full max-w-lg p-6 rounded-xl shadow-lg relative">
    <button class="absolute top-3 right-3 text-gray-500 text-xl" 
            onclick="document.getElementById('editAlumniModal').classList.add('hidden')">&times;</button>
    
    <h2 class="text-xl font-bold mb-4">Edit Alumni</h2>
    <form id="editAlumniForm" action="../backend/admin_editYearBook.php" method="POST" enctype="multipart/form-data" class="space-y-4">
      <input type="hidden" name="id" id="edit_id">

      <div>
        <label class="block text-sm font-medium">Full Name</label>
        <input type="text" name="fullName" id="edit_fullName" class="w-full border px-3 py-2 rounded-md" required>
      </div>

      <div>
        <label class="block text-sm font-medium">Gender</label>
        <select name="gender" id="edit_gender" class="w-full border px-3 py-2 rounded-md" required>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
          <option value="Rather Not Say">Rather Not Say</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-medium">Batch</label>
        <input type="text" name="batch" id="edit_batch" class="w-full border px-3 py-2 rounded-md" required>
      </div>

      <div>
        <label class="block text-sm font-medium">Course</label>
        <input type="text" name="course" id="edit_course" class="w-full border px-3 py-2 rounded-md" required>
      </div>

      <div>
        <label class="block text-sm font-medium">Graduation Picture</label>
        <input type="file" name="graduationPicture" id="edit_graduationPicture" class="w-full border px-3 py-2 rounded-md">
      </div>

      <div class="text-center">
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
          Save Changes
        </button>
      </div>
    </form>
  </div>
</div>

  <script>
    // Modal toggle
    document.getElementById("addAlumniBtn").onclick = () => {
      document.getElementById("addAlumniModal").classList.remove("hidden");
    };
  </script>
  <script>
function openEditModal(id, graduationPicture, fullName, gender, batch, course) {
  document.getElementById("edit_id").value = id;
  document.getElementById("edit_fullName").value = fullName;
  document.getElementById("edit_batch").value = batch;
  document.getElementById("edit_course").value = course;

  // Set gender dropdown
  const genderSelect = document.getElementById("edit_gender");
  for (let option of genderSelect.options) {
    option.selected = option.value === gender;
  }

  document.getElementById("editAlumniModal").classList.remove("hidden");
}
</script>

</body>
</html>
