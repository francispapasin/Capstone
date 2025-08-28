<?php
include '../connection/dbConnect.php';
include '../backend/login_process.php';

// Fallback profile picture
$profile_picture = !empty($_SESSION['profilePicture']) ? $_SESSION['profilePicture'] : 'Image/Alumni.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AlumniConnect</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50">
  <!-- Navbar -->
  <nav class="flex items-center justify-between bg-white shadow-md px-8 py-3 fixed top-0 left-0 right-0 z-50">
    
    <!-- Logo + Brand -->
    <div class="flex items-center space-x-3">
      <img src="../colm/COLMLogo.png" alt="COLM Logo" class="h-12 w-12">
      <a href="homePage.php" class="text-2xl font-bold text-green-700">AlumniConnect</a>
    </div>

    <!-- Navigation + Profile -->
    <div class="flex items-center space-x-8">
      <!-- Links -->
      <ul class="flex space-x-6 text-lg font-medium text-gray-600">
        <li><a href="homePage.php" class="hover:text-green-600 transition">Home</a></li>
        <li><a href="alumniWall.php" class="hover:text-green-600 transition">Alumni Wall</a></li>
        <li><a href="alumniMap.php" class="hover:text-green-600 transition">Alumni Map</a></li>
        <li><a href="yearBook.php" class="hover:text-green-600 transition">Year Book</a></li>
      </ul>

      <!-- Profile Section -->
      <?php if (isset($_SESSION['fullName'])): ?>
      <div class="relative">
        <button id="dropdownButton" class="flex items-center space-x-2 focus:outline-none hover:bg-gray-100 px-3 py-1 rounded-lg transition">
          <img src="<?php echo $profile_picture; ?>" 
               alt="Profile Picture" 
               class="h-10 w-10 rounded-full border border-gray-300 object-cover">
          <span class="font-medium text-gray-700"><?php echo $_SESSION['fullName']; ?></span>
          <svg id="dropdownArrow" class="h-4 w-4 text-gray-600 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>

        <!-- Dropdown Menu -->
        <div id="dropdownMenu" class="hidden absolute right-0 mt-2 w-48 bg-white border rounded-xl shadow-lg py-2 z-50">
          <a href="accountProfilePage.php" class="block px-4 py-2 text-gray-700 hover:bg-green-50 hover:text-green-600 transition">View Profile</a>
          <a href="../backend/logout_process.php" class="block px-4 py-2 text-gray-700 hover:bg-green-50 hover:text-green-600 transition">Logout</a>
        </div>
      </div>
      <?php else: ?>
      <a href="loginPage.php" class="text-lg font-medium text-gray-600 hover:text-green-600 transition">Login</a>
      <?php endif; ?>
    </div>
  </nav>

  <!-- Dropdown Script -->
  <script>
    const dropdownBtn = document.getElementById('dropdownButton');
    const dropdownMenu = document.getElementById('dropdownMenu');
    const dropdownArrow = document.getElementById('dropdownArrow');

    if (dropdownBtn) {
      dropdownBtn.addEventListener('click', () => {
        dropdownMenu.classList.toggle('hidden');
        dropdownArrow.classList.toggle('rotate-180');
      });

      document.addEventListener('click', (e) => {
        if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
          dropdownMenu.classList.add('hidden');
          dropdownArrow.classList.remove('rotate-180');
        }
      });
    }
  </script>
</body>
</html>
