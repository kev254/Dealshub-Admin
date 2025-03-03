<?php
session_start();
$vid = $_SESSION['vid'];
if (!isset($vid) || $vid === null || $vid === "") {
    header("Location: auth");
    exit;
}

include("includes/data.php");

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category_id = intval($_POST['category_id']);
    $is_approved=1;

    // Server-side validation
    if (empty($name)) {
        $error = "Sub-category name is required.";
    } elseif (empty($category_id)) {
        $error = "Parent category is required.";
    } else {
        // Insert sub-category into the database
        $stmt = $conn->prepare("INSERT INTO sub_categories (name, category_id, vendor_id, is_approved) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siii", $name,  $category_id, $vid, $is_approved);

        if ($stmt->execute()) {
            $success = "Sub-category added successfully!";
            header("refresh:1; url=admn_sub_categories");
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch categories for the parent category dropdown
$categories = getCategories(); // This helper fetches all categories
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
                                    <h5>Create Sub-category</h5>
                                    <?php if (isset($error)): ?>
                                        <div class="alert alert-danger"><?= $error ?></div>
                                    <?php endif; ?>
                                    <?php if (isset($success)): ?>
                                        <div class="alert alert-success"><?= $success ?></div>
                                    <?php endif; ?>
                                    <form class="theme-form" method="POST">
                                        <div class="row">
                                            <div class="col-md-12 mb-4">
                                                <label class="form-label">Sub-category Name</label>
                                                <input class="form-control" type="text" name="name" required>
                                            </div>
                                            <div class="col-md-12 mb-4">
                                                <label class="form-label">Select Parent Category</label>
                                                <select class="form-control" name="category_id" required>
                                                    <option value="">Select Category</option>
                                                    <?php foreach ($categories as $category): ?>
                                                        <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-12 text-end">
                                                <button class="btn btn-primary" type="submit">Create Sub-category</button>
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
