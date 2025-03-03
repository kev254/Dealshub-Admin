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
    $business_type_id = isset($_POST['btid']) && $_POST['btid'] !== '0' ? intval($_POST['btid']) : null;
    $is_approved=1;

    // Server-side validation
    if (empty($name)) {
        $error = "Category name is required.";
    }else {
        // Insert category into the database
        $stmt = $conn->prepare("INSERT INTO categories (name, is_approved, business_type_id,vendor_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("siis", $name, $is_approved, $business_type_id, $vid);

        if ($stmt->execute()) {
            $success = "Category added successfully!";
            header("refresh:1; url=admn_categories");
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch categories for the parent dropdown
$categories = getCategories();
$business_types = getBusinessTypes();
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
                                    <h5>Create Category</h5>
                                    <?php if (isset($error)): ?>
                                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                                    <?php endif; ?>
                                    <?php if (isset($success)): ?>
                                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                                    <?php endif; ?>
                                    <form class="theme-form" method="POST">
                                        <div class="row">
                                            <div class="col-md-12 mb-4">
                                                <label class="form-label">Category Name</label>
                                                <input class="form-control" type="text" name="name" required>
                                            </div>
                                            <div class="col-md-12 mb-4" hidden>
                                                <label class="form-label">Business Type Associated with the Category (Optional)</label>
                                                <select class="form-control" name="btid" required>
                                                    <option value="0">--Select Business Type---</option>
                                                    <?php
                                                    foreach ($business_types as $business_type) {
                                                        echo '<option value="' . htmlspecialchars($business_type['id']) . '">' . htmlspecialchars($business_type['name']) . '</option>';
                                                    }
                                                    ?>
                                                </select>
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

