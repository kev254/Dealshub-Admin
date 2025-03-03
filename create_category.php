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
    $business_type_id = intval($_POST['btid']);
    $is_approved=0;

    // Server-side validation
    if (empty($name)) {
        $error = "Category name is required.";
    } elseif (empty($business_type_id)) {
        $error = "Business type is required.";
    } else {
        // Insert category into the database
        $stmt = $conn->prepare("INSERT INTO categories (name, is_approved, business_type_id,vendor_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siis", $name, $is_approved, $business_type_id, $vid);

        if ($stmt->execute()) {
            $success = "Category added successfully!";
            header("refresh:1; url=categories");
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch categories for the parent dropdown
$categories = getCategories(); // Assume this helper fetches all existing categories
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
                                    <h5>Create Category</h5>
                                    <?php if (isset($error)): ?>
                                        <div class="alert alert-danger"><?= $error ?></div>
                                    <?php endif; ?>
                                    <?php if (isset($success)): ?>
                                        <div class="alert alert-success"><?= $success ?></div>
                                    <?php endif; ?>
                                    <form class="theme-form" method="POST">
                                        <div class="row">
                                            <div class="col-md-12 mb-4">
                                                <label class="form-label">Category Name</label>
                                                <input class="form-control" type="text" name="name" required>
                                            </div>
                                            <div class="col-md-12 mb-4" hidden>
                                                <label class="form-label">Parent Category</label>
                                                <input class="form-control" type="text" name="btid" value="<?php echo $loggedbtid;?>">
                                                
                                            </div>
                
                                            <div class="col-12 text-end">
                                                <button class="btn btn-primary" type="submit">Create Category</button>
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
