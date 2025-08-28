<?php 
    include '../connection/dbConnect.php';
    include '../backend/login_process.php'; // Includes the login process

    // Handle post errors from post_process.php
    $post_error = "";
    if (isset($_SESSION['post_error'])) {
        $post_error = $_SESSION['post_error'];
        unset($_SESSION['post_error']); // Clear error message after displaying
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Wall</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100">
    <?php include 'nav.php'; // Include the navigation bar ?>



    <div class="mt-25 max-w-4xl mx-auto px-4">



    <div id="suggestions" class="bg-white border rounded-lg shadow mt-1 hidden"></div>
    </form>
        <!-- Post Form -->
        <?php if (isset($_SESSION['email']) && isset($_SESSION['account_type']) && $_SESSION['account_type'] === 'Alumni'): ?>

            <div class="bg-white shadow-md rounded-lg p-6 mb-6">
                <div class="flex items-center space-x-2">
                    <img src="<?php echo isset($_SESSION['profilePicture']) ? $_SESSION['profilePicture'] : 'Image/small_alumni.JPG'; ?>" 
                         alt="Profile" class="h-10 w-10 rounded-full">
                    <h5 class="font-semibold">
                        <a href="accountProfilePage.php">
                            <?php echo isset($_SESSION['fullName']) ? htmlspecialchars($_SESSION['fullName']) : ''; ?>
                        </a>
                    </h5>
                </div>
                <form action="../backend/post_process.php" method="POST" enctype="multipart/form-data" class="mt-4">
                    <textarea name="postContent" rows="3" placeholder="What's on your mind?" 
                              class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                    <div class="mt-2">
                        <input type="file" name="postImage[]" multiple accept="image/*" class="border rounded-lg p-2">
                    </div>
                    <button type="submit" name="submitPost" 
                            class="mt-4 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                        Post
                    </button>
                </form>

            </div>
        <?php else: ?>

        <?php endif; ?>

        <!-- Display Post Error -->
        <?php if (!empty($post_error)) : ?>
            <p class="text-red-600 text-center mt-2"><?php echo $post_error; ?></p>
        <?php endif; ?>

        <!-- Display Posts -->
        <div class="bg-white shadow-md rounded-lg p-6 mt-6">
            <h2 class="text-xl font-semibold mb-4">Posts</h2>
            <?php
            // Check if user is logged in
            $is_logged_in = isset($_SESSION['email']);

            // Modify SQL to limit posts if not logged in
            $query = "SELECT p.id AS post_id, a.id AS alumniId, a.fullName, p.postContent, p.postDate 
                      FROM tbl_alumnipost p
                      JOIN tbl_alumniaccount a ON p.alumniId = a.id
                      ORDER BY p.postDate DESC";

            if (!$is_logged_in) {
                $query .= " LIMIT 10"; // Show only 10 for guests
            }

            $result = $conn->query($query);
            $post_count = 0;

            while ($row = $result->fetch_assoc()):
                $post_count++;
                $alumni_id = $row['alumniId'];  
                $user_query = "SELECT profilePicture FROM tbl_alumniaccount WHERE id = $alumni_id";
                $user_result = $conn->query($user_query);
                $user = $user_result->fetch_assoc();
                $profile_picture = !empty($user['profilePicture']) ? $user['profilePicture'] : 'Image/Alumni.png';
            ?>
                <div class="mb-6 border-b pb-4">
                    <div class="flex items-center mb-2">
                        <a href="profile.php?id=<?php echo $alumni_id; ?>">
                            <img src="<?php echo $profile_picture; ?>" alt="Profile Picture" class="h-10 w-10 rounded-full">
                        </a>
                        <h6 class="ml-3 font-semibold">
                            <a href="profile.php?id=<?php echo $alumni_id; ?>">
                                <?php echo htmlspecialchars($row['fullName']); ?>
                            </a>
                        </h6>
                    </div>
                    <p class="mb-2"><?php echo htmlspecialchars($row['postContent']); ?></p>
                    <small class="text-gray-500">Posted on: 
                        <?php
                            $postedDate = new DateTime($row['postDate'], new DateTimeZone('Asia/Manila'));
                            echo $postedDate->format('F j, Y g:i A');
                        ?>
                    </small>

                    <!-- Images -->
                    <div class="mt-3 flex flex-wrap gap-3">
                        <?php
                        $post_id = $row['post_id'];
                        $img_query = "SELECT imagePath FROM tbl_imagepost WHERE postId = $post_id";
                        $img_result = $conn->query($img_query);

                        if ($img_result->num_rows > 0):
                            while ($img = $img_result->fetch_assoc()):
                        ?>
                            <img src="<?php echo $img['imagePath']; ?>" alt="Post Image" 
                                 class="w-48 h-auto rounded-lg shadow-md">
                        <?php endwhile; else: ?>
                            <p class="text-gray-400 text-sm">No images attached.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>

            <!-- Show "Login to see more" for guests -->
            <?php if (!$is_logged_in && $post_count >= 10): ?>
                <div class="text-center mt-4">
                    <a href="loginPage.php" class="inline-block border border-green-600 text-green-600 px-4 py-2 rounded-lg hover:bg-green-600 hover:text-white transition">
                        Login to see more posts
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <script>
document.getElementById('search').addEventListener('input', function() {
    let query = this.value.trim();
    let suggestionsBox = document.getElementById('suggestions');

    if (query.length < 2) {
        suggestionsBox.innerHTML = '';
        suggestionsBox.classList.add('hidden');
        return;
    }

    fetch('../backend/search_suggestions.php?q=' + encodeURIComponent(query))
        .then(res => res.json())
        .then(data => {
            if (data.length > 0) {
                suggestionsBox.innerHTML = data.map(item => 
                    `<div class="p-2 hover:bg-green-100 cursor-pointer"
                         onclick="document.getElementById('search').value='${item}'; suggestionsBox.classList.add('hidden');">
                        ${item}
                     </div>`
                ).join('');
                suggestionsBox.classList.remove('hidden');
            } else {
                suggestionsBox.innerHTML = '<div class="p-2 text-gray-500">No results</div>';
                suggestionsBox.classList.remove('hidden');
            }
        });
});
</script>

</body>
</html>
