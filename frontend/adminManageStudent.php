<?php
include '../connection/dbConnect.php';
session_start();

// Fetch alumni records
$query = "SELECT id, fullName, email, course FROM tbl_studentaccount ORDER BY fullName ASC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Student List</title>
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
            <h2 class="text-2xl font-semibold text-gray-800">Student List</h2>
            <button id="addAccountBtn" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
              + Add Student
            </button>
          </div>

          <!-- Alumni Table -->
          <div class="overflow-x-auto">
            <table class="w-full text-left border border-gray-200 rounded-lg overflow-hidden">
              <thead class="bg-green-600 text-white">
                <tr>
                  <th class="px-4 py-3">Full Name</th>
                  <th class="px-4 py-3">Email</th>
                  <th class="px-4 py-3">Course</th>
                  <th class="px-4 py-3">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200">
                <?php while ($row = $result->fetch_assoc()) { ?>
                  <tr class="hover:bg-gray-100">
                    <td class="px-4 py-3"><?php echo htmlspecialchars($row['fullName']); ?></td>
                    <td class="px-4 py-3"><?php echo htmlspecialchars($row['email']); ?></td>
                    <td class="px-4 py-3"><?php echo htmlspecialchars($row['course']); ?></td>
                    <td class="px-4 py-3 text-center">
                      <button 
                        onclick="openEditModal(
                          '<?php echo $row['id']; ?>',
                          '<?php echo htmlspecialchars($row['fullName'], ENT_QUOTES); ?>',
                          '<?php echo htmlspecialchars($row['email'], ENT_QUOTES); ?>',
                          '<?php echo htmlspecialchars($row['course'], ENT_QUOTES); ?>'
                        )"
                        class="text-blue-600 hover:text-blue-800">
                        Edit
                      </button>
                      <a href="../backend/admin_deleteStudent_process.php?id=<?php echo $row['id']; ?>" 
                        class="text-red-600 hover:text-red-800" 
                        onclick="return confirm('Are you sure you want to delete this student account?');">
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
  <div id="addAccountModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-lg p-6 rounded-xl shadow-lg relative">
      <button class="absolute top-3 right-3 text-gray-500 text-xl" onclick="document.getElementById('addAccountModal').classList.add('hidden')">&times;</button>
      
      <h2 class="text-xl font-bold mb-4">Add New Student</h2>
      <form action="../backend/admin_createStudent.php" method="POST" class="space-y-4">

        <input type="text" name="last_name" placeholder="Last Name" class="w-full border px-3 py-2 rounded-md" required>
        <input type="text" name="first_name" placeholder="First Name" class="w-full border px-3 py-2 rounded-md" required>
        <input type="text" name="middle_name" placeholder="Middle Name" class="w-full border px-3 py-2 rounded-md">
        <input type="email" name="email" placeholder="COLM Email" class="w-full border px-3 py-2 rounded-md" required>
        <input type="password" name="password" placeholder="Password" class="w-full border px-3 py-2 rounded-md" required>
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

        <div class="text-center">
          <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
            Add Student
          </button>
        </div>
      </form>
    </div>
  </div>


  <!-- Edit Alumni Modal -->
  <div id="editAccountModal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-lg p-6 rounded-xl shadow-lg relative">
      <button class="absolute top-3 right-3 text-gray-500 text-xl" onclick="document.getElementById('editAccountModal').classList.add('hidden')">&times;</button>
      
      <h2 class="text-xl font-bold mb-4">Edit Student</h2>
      <form id="editAccountForm" action="../backend/admin_editStudent_process.php" method="POST" class="space-y-4">
        <input type="hidden" name="id" id="editStudentId">

        <input type="text" name="fullName" id="editFullName" placeholder="Full Name" class="w-full border px-3 py-2 rounded-md" required>

        <input type="email" name="email" id="editEmail" placeholder="COLM Email" class="w-full border px-3 py-2 rounded-md" required>
        <select name="course" id="editCourse" class="w-full border px-3 py-2 rounded-md">
          <option value="Bachelor of Science in Accountancy">B.S. in Accountancy</option>
          <option value="Bachelor of Science in Business Administration">B.S. in Business Administration</option>
          <option value="Bachelor of Science in Criminology">B.S. in Criminology</option>
          <option value="Bachelor of Science in Education">B.S. in Education</option>
          <option value="Bachelor of Science in Hospitality Management">B.S. in Hospitality Management</option>
          <option value="Bachelor of Science in Information Technology">B.S. in Information Technology</option>
          <option value="Bachelor of Science in Radiologic Technology">B.S. in Radiologic Technology</option>
          <option value="Bachelor of Science in Tourism Management">B.S. in Tourism Management</option>
        </select>

        <div class="text-center">
          <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
            Save Changes
          </button>
        </div>
      </form>

  </div>


  <script>
    // Modal toggle
    document.getElementById("addAccountBtn").onclick = () => {
      document.getElementById("addAccountModal").classList.remove("hidden");
    };
  </script>
  <script>
    function openEditModal(id, fullName, email, course) {
      document.getElementById('editAccountModal').classList.remove('hidden');
      document.getElementById('editStudentId').value = id;

      // Split full name into parts (assuming format: Last, First Middle)
      const nameParts = fullName.split(', ');
      const lastName = nameParts[0] || '';
      const firstMiddle = nameParts[1] ? nameParts[1].split(' ') : [];
      const firstName = firstMiddle[0] || '';
      const middleName = firstMiddle.slice(1).join(' ') || '';

      document.getElementById('editFullName').value = fullName;

      document.getElementById('editEmail').value = email;
      document.getElementById('editCourse').value = course;
    }
  </script>
</body>
</html>
