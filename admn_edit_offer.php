<?php
session_start();
$vid = $_SESSION['vid']; // Vendor ID from session
if (!isset($vid) || $vid === null || $vid === "") {
    header("Location: auth");
    exit;
}

include("includes/data.php");

// Fetch categories and subcategories
$categories = getCategories();
$sub_categories = getSubCategories();

// Validate and fetch flyer by ID and vendor ID
if (isset($_GET['oid'])) {
    $flyer_id = base64_decode($_GET['oid']);
    $stmt = $conn->prepare("SELECT * FROM flyers WHERE id = ?");
    $stmt->bind_param("i", $flyer_id);
    $stmt->execute();
    $flyer = $stmt->get_result()->fetch_assoc();
    $flyer_paths = explode(',', $flyer['flyer_path']);
    $stmt->close();

    if (!$flyer) {
        // Redirect if flyer doesn't exist or doesn't belong to the vendor
        header("Location: admin_offers");
        exit;
    }
} else {
    header("Location: admin_offers");
    exit;
}

// Handle form submission for updating the flyer
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = "";
    $subcategory_id = "";
    $title = trim(htmlspecialchars($_POST['title']));
    $description = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['description']));
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $valid_until = mysqli_real_escape_string($conn, $_POST['valid_until']);
    $is_approved=1;

    // Initialize flyer paths and thumbnail image
    $flyer_paths = $flyer['flyer_path'] ? explode(',', $flyer['flyer_path']) : [];
    $thumbnail_image = $flyer['thumbnail_image'];

    // Handle multiple file uploads
    if (isset($_FILES['flyer_path']) && count($_FILES['flyer_path']['name']) > 0) {
        $uploadDir = "flyers/";

        $uploadedFiles = $_FILES['flyer_path'];

        // Loop through uploaded files
        for ($i = 0; $i < count($uploadedFiles['name']); $i++) {
            if ($uploadedFiles['error'][$i] == 0) {
                $fileName = basename($uploadedFiles['name'][$i]);
                $filePath = $uploadDir . $fileName;

                // Move the uploaded file to the destination folder
                if (move_uploaded_file($uploadedFiles['tmp_name'][$i], $filePath)) {
                    $flyer_paths[] = $filePath;
                }
            }
        }

        // Update the thumbnail image if new files are uploaded
        if (count($flyer_paths) > 0) {
            $thumbnail_image = $flyer_paths[0]; // First image in the list
        }
    }

    // Concatenate flyer paths into a comma-separated string
    $flyer_pathsStr = mysqli_real_escape_string($conn, implode(',', $flyer_paths));

    // Validate required fields
    if (empty($title) || empty($start_date) || empty($valid_until)) {
        $error = "Please fill out all required fields.";
    } else {
        // Update the flyer record in the database
        $stmt = $conn->prepare("UPDATE flyers SET category_id = ?, subcategory_id = ?, title = ?, flyer_path = ?, description = ?, start_date = ?, valid_until = ?, thumbnail_image = ?, is_approved = ? WHERE id = ? ");
        $stmt->bind_param("iissssssii", $category_id, $subcategory_id, $title, $flyer_pathsStr, $description, $start_date, $valid_until, $thumbnail_image, $is_approved, $flyer_id);

        if ($stmt->execute()) {
            $success = "Offer updated successfully!";
            header("refresh:1; url=admn_offers");
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<?php include("partials/head.php"); ?>

<body>
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        <?php include("partials/admn_header.php"); ?>
        <div class="page-body-wrapper">
            <?php include("partials/admn_sidebar.php"); ?>
            <div class="page-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Edit Flyer</h5>
                                    <?php if (isset($error)): ?>
                                        <div class="alert alert-danger"><?= $error ?></div>
                                    <?php endif; ?>
                                    <?php if (isset($success)): ?>
                                        <div class="alert alert-success"><?= $success ?></div>
                                    <?php endif; ?>
                                    <form class="theme-form" method="POST" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Business type</label>
                                                <select name="category_id" class="form-control" required>
                                                    <option>--Select Business Type--</option>
                                                    <?php foreach ($bus_types as $bus_type): ?>
                                                        <option value="<?= $bus_type['id'] ?>"><?= htmlspecialchars($bus_type['name']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Title</label>
                                                <input class="form-control" type="text" name="title" value="<?= htmlspecialchars($flyer['title']) ?>" required>
                                            </div>
                                            
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Description</label>
                                                <textarea class="form-control" name="description"><?= htmlspecialchars($flyer['description']) ?></textarea>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Start Date</label>
                                                <input class="form-control" type="date" name="start_date" value="<?= htmlspecialchars($flyer['start_date']) ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">End Date</label>
                                                <input class="form-control" type="date" name="valid_until" value="<?= htmlspecialchars($flyer['valid_until']) ?>" required>
                                            </div>
                                            
                                            <div class="col-md-12 mb-4">
                                                <label class="form-label">Flyer Images (Multiple)</label>
                                                <input class="form-control" type="file" name="flyer_path[]" accept="image/*" multiple>
                                                <small class="text-danger">Recommended size: 1440x2352 pixels</small>
                                                <div id="preview" class="mt-2">
                                                    
                                                </div>
                                            </div>
                                            <div class="col-12 text-end">
                                                <button class="btn btn-primary" type="submit">Update Flyer</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include("partials/footer.php"); ?>
        </div>
    </div>
    <?php include("partials/js.php"); ?>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const existingImages = <?= json_encode($flyer_paths) ?>; // Array of existing image paths
        const previewContainer = document.getElementById('preview');
        
        // Display existing images
        existingImages.forEach((path) => {
            const img = createImageElement(path);
            previewContainer.appendChild(img);
        });

        // Handle file selection and show preview
        document.querySelector('input[name="flyer_path[]"]').addEventListener('change', function (event) {
            const files = event.target.files;

            // Clear previously added previews for new uploads
            const uploadedPreviews = document.querySelectorAll('.new-upload-preview');
            uploadedPreviews.forEach((preview) => preview.remove());

            // Loop through selected files and display them
            for (const file of files) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    const img = createImageElement(e.target.result, true); // Mark as a new upload
                    previewContainer.appendChild(img);
                };

                reader.readAsDataURL(file);
            }
        });

        // Function to create an image element
        function createImageElement(src, isNew = false) {
            const container = document.createElement('div');
            container.style.display = 'inline-block';
            container.style.margin = '10px';
            container.style.textAlign = 'center';

            const img = new Image();
            img.src = src;
            img.classList.add('img-thumbnail', 'mb-2');
            img.style.maxWidth = '150px';
            img.style.height = '200px';

            if (isNew) {
                img.classList.add('new-upload-preview'); // For new uploads
            }

            container.appendChild(img);
            return container;
        }
    });
</script>

</body>

</html>
