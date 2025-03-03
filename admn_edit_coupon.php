<?php
session_start();
$vid = $_SESSION['vid'];
if(!isset($vid) || $vid===null || $vid===""){
    header("Location: auth");
}
include("includes/data.php");
$categories = getCategories();
$sub_categories = getSubCategories();


// Check if the coupon ID is provided
if (!isset($_GET['cid'])) {
    die("Coupon ID is required.");
}

$coupon_id = base64_decode($_GET['cid']);
$vcid = base64_decode($_GET['vcid']);


// Fetch existing coupon details
$stmt = $conn->prepare("SELECT * FROM coupons WHERE id = ? AND vendor_id = ?");
$stmt->bind_param("ii", $coupon_id, $vcid);
$stmt->execute();
$result = $stmt->get_result();
$coupon = $result->fetch_assoc();

if (!$coupon) {
    die("Coupon not found or you do not have permission to edit it.");
}

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $coupon_code = mysqli_real_escape_string($conn, $_POST['coupon_code']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $discount_percentage = mysqli_real_escape_string($conn, $_POST['discount_percentage']);
    $is_approved = 1; // Default to 0
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $valid_until = mysqli_real_escape_string($conn, $_POST['valid_until']);
    $subcategory_id = mysqli_real_escape_string($conn, $_POST['subcategory_id']);


    // Validation
    if (empty($title) || empty($coupon_code) || empty($start_date) || empty($valid_until)) {
        $error = "Please fill out all required fields.";
    } elseif (!is_numeric($discount_percentage) || $discount_percentage < 0) {
        $error = "Discount percentage must be a valid non-negative number.";
    } elseif ($start_date > $valid_until) {
        $error = "Start date must be before or on the end date.";
    } else {
        // Update the coupon in the database
        $stmt = $conn->prepare("UPDATE coupons SET category_id = ?, title = ?, coupon_code = ?, description = ?, discount_percentage = ?, is_approved = ?, start_date = ?, valid_until = ?, sub_category_id = ? WHERE id = ? AND vendor_id = ?");
        $stmt->bind_param("isssdissiii", $category_id, $title, $coupon_code, $description, $discount_percentage, $is_approved, $start_date, $valid_until, $subcategory_id, $coupon_id, $vcid);

        if ($stmt->execute()) {
            // Success message and redirect
            $success = "Coupon edited successfully!";
            header("refresh:1; url=admn_coupons");
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

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
                                    <h5>Edit Coupon</h5>
                                    <?php if (isset($error)): ?>
                                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                                    <?php endif; ?>
                                    <?php if (isset($success)): ?>
                                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                                    <?php endif; ?>
                                    <form class="theme-form" method="POST">
                                        <div class="row">
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Category</label>
                                                <select name="category_id" class="form-control" required>
                                                    <?php foreach ($categories as $category): ?>
                                                        <option value="<?= $category['id'] ?>" <?= $category['id'] == $coupon['category_id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($category['name']) ?>
                                                        </option>
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
                                                <label class="form-label">Title</label>
                                                <input class="form-control" type="text" name="title" value="<?= htmlspecialchars($coupon['title']) ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Coupon Code</label>
                                                <input class="form-control" type="text" name="coupon_code" value="<?= htmlspecialchars($coupon['coupon_code']) ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Description</label>
                                                <textarea class="form-control" name="description"><?= htmlspecialchars($coupon['description']) ?></textarea>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Discount Percentage</label>
                                                <input class="form-control" type="number" step="0.01" name="discount_percentage" value="<?= htmlspecialchars($coupon['discount_percentage']) ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Start Date</label>
                                                <input class="form-control" type="date" name="start_date" value="<?= htmlspecialchars($coupon['start_date']) ?>" required>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">End Date</label>
                                                <input class="form-control" type="date" name="valid_until" value="<?= htmlspecialchars($coupon['valid_until']) ?>" required>
                                            </div>
                                            <div class="col-12 text-end">
                                                <button class="btn btn-primary" type="submit">Update Coupon</button>
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
