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
$branches = getBranches($vid);  // Function to get branches for the vendor

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $subcategory_id = mysqli_real_escape_string($conn, $_POST['subcategory_id']);
    $name = trim($_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $initial_price = floatval($_POST['initial_price']);
    $valid_until = mysqli_real_escape_string($conn, $_POST['valid_until']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);

    $is_approved = 0;

    // Get selected branches
    $branch_ids = isset($_POST['branch_ids']) ? $_POST['branch_ids'] : [];

    // Handle flyer images (as the flyer_path in the previous example)
    $flyer_paths = [];
    $thumbnail_image = '';

    if (isset($_FILES['flyer_path']) && count($_FILES['flyer_path']['name']) > 0) {
        $uploadDir = "items/";

        $uploadedFiles = $_FILES['flyer_path'];

        for ($i = 0; $i < count($uploadedFiles['name']); $i++) {
            if ($uploadedFiles['error'][$i] == 0) {
                $fileName = basename($uploadedFiles['name'][$i]);
                $filePath = $uploadDir . $fileName;

                if (move_uploaded_file($uploadedFiles['tmp_name'][$i], $filePath)) {
                    $flyer_paths[] = $filePath;
                }
            }
        }

        if (count($flyer_paths) > 0) {
            $thumbnail_image = $flyer_paths[0]; 
            // $error = "Please upload at least one product image.";
        }
    } else {
        $error = "Flyer images are required.";
    }

    // Server-side validation
    if (empty($name) || empty($category_id) || empty($valid_until) || empty($flyer_paths) || empty($branch_ids)) {
        $error = "Please fill out all required fields.";
    } else {
        $flyer_pathsStr = mysqli_real_escape_string($conn, implode(',', $flyer_paths)); // Comma-separated list of paths

        // Convert branch_ids array to a comma-separated string
        $branchesStr = mysqli_real_escape_string($conn, implode(',', $branch_ids));

        // Insert data into the products table
        $stmt = $conn->prepare("INSERT INTO products (vendor_id, category_id, subcategory_id, name, description, price, initial_price, thumbnail_image, images, branche_ids, is_approved, valid_until, start_date) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param('iiissdsssssss', $vid, $category_id, $subcategory_id, $name, $description, $price, $initial_price, $thumbnail_image, $flyer_pathsStr, $branchesStr, $is_approved, $valid_until, $start_date);

        if ($stmt->execute()) {
            $success = "Product added successfully!";
            header("refresh:1; url=my_products");
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
                                    <h5>Create Product</h5>
                                    <?php if (isset($error)): ?>
                                        <div class="alert alert-danger"><?= $error ?></div>
                                    <?php endif; ?>
                                    <?php if (isset($success)): ?>
                                        <div class="alert alert-success"><?= $success ?></div>
                                    <?php endif; ?>
                                    <form class="theme-form" method="POST" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Category</label>
                                                <select name="category_id" class="form-control" required>
                                                    <?php foreach ($categories as $category): ?>
                                                        <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Subcategory</label>
                                                <select name="subcategory_id" class="form-control">
                                                    <option value="">Select Subcategory</option>
                                                    <?php foreach ($sub_categories as $sub_category): ?>
                                                        <option value="<?= $sub_category['id'] ?>"><?= htmlspecialchars($sub_category['name']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Product Name</label>
                                                <input class="form-control" type="text" name="name" required>
                                            </div>
                                            
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Description</label>
                                                <textarea class="form-control" name="description"></textarea>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Price</label>
                                                <input class="form-control" type="number" step="0.01" name="price" required>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Initial Price</label>
                                                <input class="form-control" type="number" step="0.01" name="initial_price" value="0">
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Start Date</label>
                                                <input class="form-control" type="date" name="start_date" required value="<?php echo date("Y-m-d");?>">
                                            </div>
                                            
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">End Date</label>
                                                <input class="form-control" type="date" name="valid_until" required value="<?php echo date("Y-m-d");?>">
                                            </div>
                                            
                                            <div class="col-md-12 mb-4">
                                                <label class="form-label">Branches (Where product is available)</label>
                                                <div>
                                                    <?php foreach ($branches as $branch): ?>
                                                        <label>
                                                            <input type="checkbox" name="branch_ids[]" value="<?= $branch['id'] ?>"> <?= htmlspecialchars($branch['branch_name']) ?>
                                                        </label><br>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-12 mb-4">
                                                <label class="form-label">Product Images (Multiple)</label>
                                                <input class="form-control" type="file" name="flyer_path[]" accept="image/*" multiple required>
                                                <small class="text-danger">Recommended size: 1440x2352 pixels</small>
                                                <div id="preview" class="mt-2"></div>
                                            </div>
                                            
                                            <div class="col-12 text-end">
                                                <button class="btn btn-primary" type="submit">Create Product</button>
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
                        img.style.maxWidth = '150px';
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
