<?php
session_start();
$vid = $_SESSION['vid'];
if(!isset($vid) || $vid===null || $vid===""){
    header("Location: auth");
}

include("includes/data.php");
$vid = $_SESSION['vid'];
$categories = getCategories();
$sub_categories = getSubCategories();

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $title = trim($_POST['title']);
    $coupon_code = trim($_POST['coupon_code']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $discount_percentage = mysqli_real_escape_string($conn, $_POST['discount_percentage']);
    $is_approved = 0;
    $start_date = mysqli_real_escape_string($conn, string: $_POST['start_date']);
    $valid_until = mysqli_real_escape_string($conn, $_POST['valid_until']);
    $subcategory_id = mysqli_real_escape_string($conn, $_POST['subcategory_id']);
   

    // Server-side validation
    if (empty($title) || empty($coupon_code) || empty($valid_until) || empty($start_date)) {
        $error = "Please fill out all required fields.";
    } elseif (!is_numeric($discount_percentage) || $discount_percentage < 0) {
        $error = "Discount percentage must be a valid non-negative number.";
    } 
    elseif ($start_date > $valid_until) {
        $error = "Start date must be before or on the end date.";
    }
    else {
        // Insert data into the database
        $stmt = $conn->prepare("INSERT INTO coupons (vendor_id, category_id, title, coupon_code, description, discount_percentage, is_approved,start_date,valid_until, sub_category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssdissi", $vid, $category_id, $title, $coupon_code, $description, $discount_percentage, $is_approved, $start_date, $valid_until,$subcategory_id);

        if ($stmt->execute()) {
            // Redirect to the coupons page after success
            $success = "Coupon added successfully!";
            header("refresh:1; url=my_coupons");
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
                                    <h5>Create Coupon</h5>
                                    <?php if (isset($error)): ?>
                                        <div class="alert alert-danger"><?= $error ?></div>
                                    <?php endif; ?>
                                    <?php if (isset($success)): ?>
                                        <div class="alert alert-success"><?= $success ?></div>
                                    <?php endif; ?>
                                    <form class="theme-form" method="POST">
                                        <div class="row">
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                                <select name="category_id" class="form-control" required>
                                                    <?php foreach ($categories as $category): ?>
                                                        <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Subcategory <span class="text-danger">*</span></label>
                                                <select name="subcategory_id" class="form-control">
                                                    <option value="">Select Subcategory</option>
                                                    <?php foreach ($sub_categories as $sub_category): ?>
                                                        <option value="<?= $sub_category['id'] ?>"><?= htmlspecialchars($sub_category['name']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Title <span class="text-danger">*</span></label>
                                                <input class="form-control" type="text" name="title" required>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Coupon Code <span class="text-danger">*</span></label>
                                                <input class="form-control" type="text" name="coupon_code" required>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                                <textarea class="form-control" name="description"></textarea>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Discount Percentage <span class="text-danger">*</span></label>
                                                <input class="form-control" type="number" step="0.01" name="discount_percentage" required value="1">
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">Start Date <span class="text-danger">*</span></label>
                                                <input class="form-control" type="date" name="start_date" required>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <label class="form-label">End Date <span class="text-danger">*</span></label>
                                                <input class="form-control" type="date" name="valid_until" required>
                                            </div>
                                           
                                            <div class="col-12 text-end">
                                                <button class="btn btn-primary" type="submit">Create Coupon </button>
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
    <?php include("partials/js.php");?>
</body>

</html>
