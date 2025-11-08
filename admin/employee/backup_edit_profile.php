<?php
require_once __DIR__ . '/../../dbsetting/config.php';

$id = $_GET['id'] ?? 0;
$message = "";

// Fetch employee info
$query = "SELECT fullname, profile_photo FROM users WHERE users_id=$id LIMIT 1";
$result = mysqli_query($conn, $query);
$employee = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == 0) {
        $uploadDir = "../../assets/uploads/";
        $fileName  = time() . "_" . basename($_FILES['profile_photo']['name']);
        $targetFile = $uploadDir . $fileName;

        // Allow only images
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $targetFile)) {
                // Update DB
                $update = "UPDATE users SET profile_photo='$fileName' WHERE users_id=$id";
                if (mysqli_query($conn, $update)) {
                    $message = "<div class='alert alert-success text-center'>
                                  ✅ Employee details updated successfully. Redirecting...
                                </div>
                                <script>
                                  setTimeout(function(){
                                    window.location.href='view_employee.php?id=$id';
                                  }, 2000);
                                </script>";
                } else {
                    $message = "❌ Error updating photo: " . mysqli_error($conn);
                }
            } else {
                $message = "❌ Failed to upload file.";
            }
        } else {
            $message = "❌ Invalid file type. Allowed: JPG, JPEG, PNG, GIF.";
        }
    } else {
        $message = "❌ Please select a photo.";
    }
}

include HEADER_PATH;
include SIDEBAR_PATH;
?>

<div class="container card p-4 mt-5 edit_profile_picture" style="max-width:500px; ">
  <center>
    <h3>Edit Profile Picture</h3>
    <p><strong><?php echo $employee['fullname'] ?? ''; ?></strong></p>
  <div class="mb-3">
        <img id="preview" 
             src="<?php echo !empty($employee['profile_photo']) 
                        ? '../../assets/uploads/' . $employee['profile_photo'] 
                        : '../../assets/default-avatar.png'; ?>" 
             class="img-thumbnail rounded-circle"
             style="width:150px; height:150px; object-fit:cover;">
      </div>
    <?php if (!empty($message)) echo $message; ?>

    <form method="POST" enctype="multipart/form-data" 
          onsubmit="return confirm('Are you sure you want to update profile of <?php echo addslashes($employee['fullname']); ?>?')">

      <div class="mb-3">
        <input type="file" name="profile_photo" class="form-control" 
               accept="image/*" onchange="previewImage(event)" required>
      </div>

    
    

      <button type="submit" class="btn btn-primary">Upload</button>
      <a href="view_employee.php?id=<?php echo $id; ?>" class="btn btn-secondary">Cancel</a>
    </form>
  </center>
</div>

<script>
let oldPhoto = document.getElementById('preview').src;

function previewImage(event) {
    let preview = document.getElementById('preview');

    if (event.target.files && event.target.files[0]) {
        let reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        };
        reader.readAsDataURL(event.target.files[0]);
    } else {
        
        preview.src = oldPhoto;
    }
}
</script>

<?php include FOOTER_PATH; ?>
