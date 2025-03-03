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

// Get product id (pid) from query parameter
$pid = isset($_GET['pid']) ? base64_decode($_GET['pid']) : null;

if ($pid === null) {
    // If no pid is provided, redirect or show an error
    header("Location: my_products");
    exit;
}

// Fetch product details to edit
$products = getProducts($vid, null, null, $pid);

$product = $products[0];

if (!$product) {
    // If product does not exist or does not belong to the vendor, redirect or show an error
    header("Location: my_products");
    exit;
}

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $subcategory_id = mysqli_real_escape_string($conn, $_POST['subcategory_id']);
    $name = trim($_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $initial_price = floatval($_POST['initial_price']);
    $valid_until = mysqli_real_escape_string($conn, $_POST['valid_until']);
    $is_approved = 0;

    // Get selected branches
    $branch_ids = isset($_POST['branch_ids']) ? $_POST['branch_ids'] : [];

    // Handle flyer images (as the flyer_path in the previous example)
    $flyer_paths = explode(',', $product['images']);
     // Keep the original images if not uploading new ones
    $thumbnail_image = $product['thumbnail_image']; // Keep the original thumbnail if not uploading new ones
    

    if (isset($_FILES['flyer_path']) && is_array($_FILES['flyer_path']['name']) && count($_FILES['flyer_path']['name']) > 0) {

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
            $thumbnail_image = $flyer_paths[0]; // Set first uploaded image as the thumbnail
        } else {
            $error = "Please upload at least one product image.";
        }
    }

    // Server-side validation
    if (empty($name) || empty($category_id) || empty($valid_until) || empty($flyer_paths)) {
        $error = "Please fill out all required fields.";
    } else {
        $flyer_pathsStr = mysqli_real_escape_string($conn, implode(',', $flyer_paths)); // Comma-separated list of paths
        $branchesStr = mysqli_real_escape_string($conn, implode(',', $branch_ids)); // Comma-separated list of branch IDs

        // Update data in the products table
        $stmt = $conn->prepare("UPDATE products 
                                SET category_id = ?, subcategory_id = ?, name = ?, description = ?, price = ?, initial_price = ?, thumbnail_image = ?, images = ?, branche_ids = ?, is_approved = ?, valid_until = ? 
                                WHERE id = ? AND vendor_id = ?");
        $stmt->bind_param('iisssdssssssi', $category_id, $subcategory_id, $name, $description, $price, $initial_price, $thumbnail_image, $flyer_pathsStr, $branchesStr, $is_approved, $valid_until, $pid, $vid);

        if ($stmt->execute()) {
            $success = "Product updated successfully!";
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
                                    <h5>Edit Product</h5>
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
                                                        <option value="<?= $category['id'] ?>" <?= $category['id'] == $product['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($category['name']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Subcategory</label>
                                                <select name="subcategory_id" class="form-control">
                                                    <option value="">Select Subcategory</option>
                                                    <?php foreach ($sub_categories as $sub_category): ?>
                                                        <option value="<?= $sub_category['id'] ?>" <?= $sub_category['id'] == $product['subcategory_id'] ? 'selected' : '' ?>><?= htmlspecialchars($sub_category['name']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Product Name</label>
                                                <input class="form-control" type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
                                            </div>
                                            
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Description</label>
                                                <textarea class="form-control" name="description"><?= htmlspecialchars($product['description']) ?></textarea>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Price</label>
                                                <input class="form-control" type="number" step="0.01" name="price" value="<?= $product['price'] ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Initial Price</label>
                                                <input class="form-control" type="number" step="0.01" name="initial_price" value="<?= $product['initial_price'] ?>">
                                            </div>
                                            
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Valid Until</label>
                                                <input class="form-control" type="date" name="valid_until" value="<?= $product['valid_until'] ?>" required>
                                            </div>
                                            
                                            <div class="col-md-12 mb-4">
                                                <label class="form-label">Branches (Where product is available)</label>
                                                <div>
                                                    <?php foreach ($branches as $branch): ?>
                                                        <label>
                                                            <input type="checkbox" name="branch_ids[]" value="<?= $branch['id'] ?>" <?= in_array($branch['id'], explode(',', $product['branche_ids'])) ? 'checked' : '' ?>> <?= htmlspecialchars($branch['branch_name']) ?>
                                                        </label><br>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-12 mb-4">
                                                <label class="form-label">Product Images (Multiple)</label>
                                                <input class="form-control" type="file" name="flyer_path[]" accept="image/*" multiple>
                                                <small class="text-danger">Recommended size: 1440x2352 pixels</small>
                                                <div id="preview" class="mt-2"></div>
                                            </div>
                                            
                                            <div class="col-12 text-end">
                                                <button class="btn btn-primary" type="submit">Update Product</button>
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
</body>

</html>
