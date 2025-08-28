<?php

include '../connection/dbConnect.php';
include '../backend/login_process.php'; // Include the login process to handle session management

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Navigation</title>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body>
      <!-- Navbar -->
<nav class="fixed top-0 left-0 w-full bg-[#41A735] text-white flex justify-between items-center px-6 py-4 shadow-md z-50"> 
  <!-- Left Side -->
  <div class="flex items-center space-x-3">
    <img src="../colm/colmlogo.png" alt="COLM Logo" class="h-10">
    <span class="text-xl font-bold">AlumniConnect</span>
  </div>

  <!-- Right Side -->
  <div class="relative">
    <button onclick="toggleDropdown()" class="flex items-center space-x-2 focus:outline-none">
      <span>Welcome, Admin <?php echo $_SESSION['fullName']?></span>
      <svg class="w-4 h-4 transition-transform" id="dropdownIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
      </svg>
    </button>
    
    <!-- Dropdown -->
    <div id="dropdownMenu" class="absolute right-0 mt-2 w-40 bg-white text-gray-800 rounded-md shadow-lg hidden">
      <a href="../backend/logout_process.php" class="block px-4 py-2 hover:bg-gray-100">Logout</a>
    </div>
  </div>
</nav>

    <!-- Sidebar -->
    <aside class="w-64 bg-gray-900 text-white h-screen p-4 space-y-2 fixed">
      <a href="adminDashboard.php" class="flex items-center px-4 py-3 hover:bg-gray-700 rounded-md transition">
        <i class="fas fa-home mr-3"></i> Dashboard
      </a>
      <a href="adminManageAlumni.php" class="flex items-center px-4 py-3 hover:bg-gray-700 rounded-md transition">
        <i class="fas fa-users mr-3"></i> Manage Alumni
      </a>
      <a href="adminManageStudent.php" class="flex items-center px-4 py-3 hover:bg-gray-700 rounded-md transition">
        <i class="fas fa-users mr-3"></i> Manage Student
      </a>
      <a href="adminManagePost.php" class="flex items-center px-4 py-3 hover:bg-gray-700 rounded-md transition">
        <i class="fas fa-edit mr-3"></i> Manage Alumni Posts
      </a>
      <a href="adminManageAnnouncement.php" class="flex items-center px-4 py-3 hover:bg-gray-700 rounded-md transition">
        <i class="fas fa-bullhorn mr-3"></i> Manage Announcement
      </a>
      <a href="adminManageYearBook.php" class="flex items-center px-4 py-3 hover:bg-gray-700 rounded-md transition">
        <i class="fas fa-bullhorn mr-3"></i> Manage Year Book
      </a>
    </aside>
    
    <script>
  function toggleDropdown() {
    let menu = document.getElementById("dropdownMenu");
    let icon = document.getElementById("dropdownIcon");

    menu.classList.toggle("hidden");
    icon.classList.toggle("rotate-180");
  }

  // ✅ Close dropdown if clicking outside
  window.addEventListener("click", function(e) {
    let menu = document.getElementById("dropdownMenu");
    let button = document.querySelector("button[onclick='toggleDropdown()']");
    if (!button.contains(e.target) && !menu.contains(e.target)) {
      menu.classList.add("hidden");
      document.getElementById("dropdownIcon").classList.remove("rotate-180");
    }
  });
</script>
</body>
</html>