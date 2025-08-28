<?php
session_start();
include '../connection/dbConnect.php'; 
include '../backend/login_process.php'; 
include '../backend/editPost_process.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Post</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
        background-color: #e9ebee;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .user-info {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }
    .user-info img {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 15px;
    }
    .image-preview {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .image-preview .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        width: 100px;
        height: 100px;
        position: relative;
    }
    .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .image-preview button {
        position: absolute;
        top: 5px;
        right: 5px;
        font-size: 12px;
        padding: 2px 6px;
    }
    .btn-facebook {
        background-color: #1877f2;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 10px 20px;
        font-weight: 500;
    }
    .btn-facebook:hover {
        background-color: #155db2;
    }
    .btn-cancel {
        background-color: #f0f2f5;
        border: none;
        padding: 10px 20px;
        margin-left: 10px;
        border-radius: 6px;
        color: #050505;
    }
    .file-upload-info {
        font-size: 0.875rem;
        color: #6c757d;
    }
  </style>
</head>
<body>
<?php include 'nav.php'; ?>

<div class="modal fade" id="editPostModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="post" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title">Edit Post</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        
        <div class="modal-body">
            <div class="user-info mb-3">
                <img src="<?php echo $profile_picture; ?>" alt="Profile Picture">
                <div>
                    <strong><?php echo $_SESSION['fullName']; ?></strong><br>
                    <small>Edit your post</small>
                </div>
            </div>

            <!-- ✅ match backend name -->
            <div class="mb-3">
                <textarea name="postContent" class="form-control" rows="4" placeholder="What's on your mind?"><?php echo htmlspecialchars($post['postContent']); ?></textarea>
            </div>

            <div class="mb-3">
                <label for="new_images" class="form-label">Add Photos:</label>
                <input type="file" name="new_images[]" class="form-control" multiple accept="image/*">
                <small class="file-upload-info">Upload multiple images (JPG, PNG, etc.)</small>
            </div>

            <div class="mb-3 image-preview">
                <?php
                $img_query = "SELECT * FROM tbl_imagepost WHERE postId = $post_id";
                $img_result = $conn->query($img_query);
                if ($img_result->num_rows > 0) {
                    while ($img = $img_result->fetch_assoc()) {
                        echo '<div class="card">';
                        echo '<img src="' . $img['imagePath'] . '" alt="Post Image">';
                        echo '<form method="post" action="delete_post_image.php" onsubmit="return confirm(\'Are you sure?\');">';
                        echo '<input type="hidden" name="imageId" value="' . $img['id'] . '">';
                        echo '<input type="hidden" name="postId" value="' . $post_id . '">';
                        echo '<button type="submit" class="btn btn-sm btn-danger">X</button>';
                        echo '</form>';
                        echo '</div>';
                    }
                } else {
                    echo "<p>No images uploaded.</p>";
                }
                ?>
            </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-facebook">Save</button>
          <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var modal = new bootstrap.Modal(document.getElementById('editPostModal'));
    modal.show();
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
