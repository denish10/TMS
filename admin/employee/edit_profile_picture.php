<?php
session_start();

require_once __DIR__ . '/../../dbsetting/config.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['users_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

$id = $_GET['id'] ?? 0;
$message = "";

// Fetch employee info
$query = "SELECT fullname, profile_photo FROM users WHERE users_id=$id LIMIT 1";
$result = mysqli_query($conn, $query);
$employee = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['cropped_image'])) {
        $cropped_image = $_POST['cropped_image'];
        $cropped_image = str_replace('data:image/png;base64,', '', $cropped_image);
        $cropped_image = str_replace(' ', '+', $cropped_image);
        $imageData = base64_decode($cropped_image);

        $fileName = time() . "_cropped.png";
        $filePath = "../../assets/uploads/" . $fileName;

        if (file_put_contents($filePath, $imageData)) {
            $update = "UPDATE users SET profile_photo='$fileName' WHERE users_id=$id";
            if (mysqli_query($conn, $update)) {
                $message = "<div class='alert alert-success text-center'>
                              ✅ Profile photo updated successfully. Redirecting...
                            </div>
                            <script>
                              setTimeout(function(){
                                window.location.href='view_employee.php?id=$id';
                              }, 2000);
                            </script>";
            } else {
                $message = "❌ Database update failed: " . mysqli_error($conn);
            }
        } else {
            $message = "❌ Failed to save cropped image.";
        }
    } else {
        $message = "❌ Please select and crop an image.";
    }
}

include HEADER_PATH;
include SIDEBAR_PATH;
?>

<!-- Cropper.js CSS & JS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

<div class="container card p-4 mt-5" style="max-width:500px;">
    <center>
        <h3>Edit Profile Picture</h3>
        <p><strong><?php echo $employee['fullname'] ?? ''; ?></strong></p>

        <!-- Preview Image -->
        <div class="mb-3">
            <img id="preview"
                src="<?php echo !empty($employee['profile_photo'])
                    ? '../../assets/uploads/' . $employee['profile_photo']
                    : '../../assets/default-avatar.png'; ?>"
                class="img-thumbnail rounded-circle"
                style="width:150px; height:150px; object-fit:cover;">
        </div>

        <?php if (!empty($message)) echo $message; ?>

        <!-- Upload Form -->
        <form method="POST" enctype="multipart/form-data" id="uploadForm">
            <input type="file" name="profile_photo" class="form-control mb-3" accept="image/*" id="uploadImage" required>
            <input type="hidden" name="cropped_image" id="croppedImage">
            <button type="submit" class="btn btn-primary mt-2">Upload</button>
            <a href="view_employee.php?id=<?php echo $id; ?>" class="btn btn-secondary mt-2">Cancel</a>
        </form>
    </center>
</div>

<!-- Crop Modal -->
<div id="cropModal"
    style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); text-align:center; padding-top:50px; z-index:9999;">
    <div style="display:inline-block; background:#fff; padding:20px; border-radius:10px;">
        <h4>Crop Image</h4>
        <img id="cropImage" style="max-width:100%; max-height:400px;">
        <br>
        <button id="cropBtn" class="btn btn-success mt-3">Crop & Save</button>
        <button id="closeBtn" class="btn btn-danger mt-3">Cancel</button>
    </div>
</div>

<script>
let cropper;
let cropModal = document.getElementById('cropModal');
let cropImage = document.getElementById('cropImage');
let uploadImage = document.getElementById('uploadImage');
let croppedImageInput = document.getElementById('croppedImage');
let previewImage = document.getElementById('preview');

uploadImage.addEventListener('change', function(event) {
    let file = event.target.files[0];
    if (!file) return;

    let reader = new FileReader();
    reader.onload = function(e) {
        cropImage.src = e.target.result;
        cropModal.style.display = 'block';
        if (cropper) cropper.destroy();
        cropper = new Cropper(cropImage, {
            aspectRatio: 1,
            viewMode: 1,
            autoCropArea: 1,
            minCropBoxWidth: 400,
            minCropBoxHeight: 400
        });
    };
    reader.readAsDataURL(file);
});

document.getElementById('cropBtn').addEventListener('click', function() {
    let canvas = cropper.getCroppedCanvas({ width: 400, height: 400 });
    croppedImageInput.value = canvas.toDataURL('image/png');
    previewImage.src = canvas.toDataURL('image/png');
    cropModal.style.display = 'none';
});

document.getElementById('closeBtn').addEventListener('click', function() {
    cropModal.style.display = 'none';
});
</script>

<?php include FOOTER_PATH; ?>
