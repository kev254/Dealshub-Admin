<?php
session_start();
$vid = $_SESSION['vid'];
if (!isset($vid) || $vid === null || $vid === "") {
    header("Location: auth");
    exit;
}

include("includes/data.php");
$categories = getCategories();
$sub_categories = getSubCategories();
$bus_types = getBusinessTypes();



// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = "";
    $subcategory_id ="";
    $title = trim($_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $valid_until = mysqli_real_escape_string($conn, $_POST['valid_until']);
    $is_approved = 0;

    // Initialize array for flyer paths and thumbnail
    $flyer_paths = [];
    $thumbnail_image = '';

    // Handle multiple file uploads for flyer_path
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

        // Set the first uploaded image as the thumbnail
        if (count($flyer_paths) > 0) {
            $thumbnail_image = $flyer_paths[0]; // First image in the list
        } else {
            $error = "Please upload at least one flyer image.";
        }
    } else {
        $error = "Flyer images are required.";
    }

    // Server-side validation
    if (empty($title) || empty($start_date) || empty($valid_until) || empty($flyer_paths)) {
        $error = "Please fill out all required fields.";
    } else {
        // Concatenate flyer paths into a comma-separated string
        $flyer_pathsStr = mysqli_real_escape_string($conn, implode(',', $flyer_paths)); // Comma-separated list of paths

        // Insert data into the flyers table
        $stmt = $conn->prepare("INSERT INTO flyers (vendor_id, category_id, subcategory_id, title, flyer_path, description, is_approved, start_date, valid_until, thumbnail_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('iiisssisss', $vid, $category_id, $subcategory_id, $title, $flyer_pathsStr, $description, $is_approved, $start_date, $valid_until, $thumbnail_image);

        if ($stmt->execute()) {
            // Redirect to the flyers page after success
            $success = "Flyer added successfully!";
            header("refresh:1; url=my_offers");
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
        <?php include("partials/header.php"); ?>
        <div class="page-body-wrapper">
            <?php include("partials/sidebar.php"); ?>
            <div class="page-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Create Flyer</h5>
                                    <?php if (isset($error)): ?>
                                        <div class="alert alert-danger"><?= $error ?></div>
                                    <?php endif; ?>
                                    <?php if (isset($success)): ?>
                                        <div class="alert alert-success"><?= $success ?></div>
                                    <?php endif; ?>
                                    <form class="theme-form" method="POST" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Business type <span class="text-danger">*</span></label>
                                                <select name="category_id" class="form-control" required>
                                                    <option>--Select Business Type--</option>
                                                    <?php foreach ($bus_types as $bus_type): ?>
                                                        <option value="<?= $bus_type['id'] ?>"><?= htmlspecialchars($bus_type['name']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                                <input class="form-control" type="text" name="title" required>
                                            </div>
                                            
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                                <textarea class="form-control" name="description"></textarea>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                                <input class="form-control" type="date" name="start_date" required>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">End Date <span class="text-danger">*</span></label>
                                                <input class="form-control" type="date" name="valid_until" required>
                                            </div>
                                            
                                            <div class="col-md-12 mb-4">
                                                <span class="form-label">Flyer Images (Multiple) <span class="text-danger">*</span></label>
                                                <input class="form-control" type="file" name="flyer_path[]" accept="image/*" multiple required>
                                                <small class="text-danger">Recommended size: 1440x2352 pixels</small>
                                                <div id="preview" class="mt-2"></div>
                                            </div>
                                            <div class="col-12 text-end">
                                                <button class="btn btn-primary" type="submit">Create Flyer</button>
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
        document.querySelector('input[name="flyer_path[]"]').addEventListener('change', function(event) {
    const previewContainer = document.getElementById('preview');
    previewContainer.innerHTML = '';
    const files = event.target.files;

    for (const file of files) {
        const reader = new FileReader();

        reader.onload = function(e) {
            const img = new Image();
            img.src = e.target.result;

            img.onload = function() {
                const dimensions = `${img.naturalWidth}x${img.naturalHeight} pixels`;
                
                // Create a container for the image preview and details
                const container = document.createElement('div');
                container.style.display = 'inline-block';
                container.style.margin = '10px';
                container.style.textAlign = 'center';

                // Add the image
                img.classList.add('img-thumbnail', 'mb-2');
                img.style.maxWidth = '150px'
                img.style.height = '200px';
                container.appendChild(img);

                // Add the dimensions
                const sizeText = document.createElement('p');
                sizeText.textContent = dimensions;
                sizeText.style.fontSize = '12px';
                sizeText.style.color = '#555';
                container.appendChild(sizeText);

                previewContainer.appendChild(container);
            };
        };

        reader.readAsDataURL(file);
    }
});

    </script>
</body>

</html>
