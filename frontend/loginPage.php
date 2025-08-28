<?php 
    include '../connection/dbConnect.php';
    include '../backend/login_process.php'; // Includes the login process
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COLM-AlumniConnect</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-[#0C846A] min-h-screen flex items-center justify-center">
    
    <!-- Main Container -->
    <div class="w-[80%] max-w-5xl grid grid-cols-1 md:grid-cols-2 bg-[#D3F4DE] rounded-2xl shadow-2xl overflow-hidden">
        
        <!-- Left Section -->
        <div class="flex flex-col items-center justify-center p-10 space-y-6">
            <img src="../colm/COLMLogo.png" alt="COLM Logo" class="w-48 mx-auto">
            <h2 class="text-2xl font-bold text-gray-800 text-center">Taking You Higher!</h2>
            <p class="text-gray-700 text-justify leading-relaxed">
                "At COLM AlumniConnect, we believe that staying connected strengthens our legacy and inspires future generations. 
                Let’s continue to support, uplift, and celebrate each other’s achievements while fostering a community that thrives 
                on unity, growth, and shared success. COLM AlumniConnect ensures that the COLM spirit lives on, continuing to take 
                us higher in our personal and professional journeys. <span class="font-semibold">WE LIVE, WE INSPIRE, AND WE CONNECT!</span>"
            </p>
        </div>

        <!-- Right Section -->
        <div class="flex flex-col justify-center p-10 bg-white rounded-r-2xl">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Welcome to COLM AlumniConnect</h2>
            
            <form action="../backend/login_process.php" method="POST" class="space-y-5">
                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">COLM Email</label>
                    <input type="text" name="email" required 
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-[#0C846A] focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required 
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-[#0C846A] focus:outline-none">
                </div>
                <div class="text-center">
                    <button type="submit" name="login" 
                        class="w-full bg-[#0C846A] text-white font-semibold py-2 rounded-lg shadow-md hover:bg-[#096956] transition">
                        Login
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
