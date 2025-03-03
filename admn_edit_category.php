<?php
session_start();
$vid = $_SESSION['vid'];
if (!isset($vid) || $vid === null || $vid === "") {
    header("Location: auth");
    exit;
}

include("includes/data.php");

// Get category ID from query string
if (!isset($_GET['cid']) || empty($_GET['cid'])) {
    header("Location: admn_categories");
    exit;
}

$category_id = base64_decode($_GET['cid']);
$vcid = base64_decode($_GET['vcid']);


// Fetch the category details
$stmt = $conn->prepare("SELECT c.id, c.name, c.business_type_id, bt.name as b_name FROM categories c JOIN business_types bt ON c.business_type_id=bt.id  WHERE c.id = ? AND vendor_id=?");
$stmt->bind_param("ii", $category_id, $vcid);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();
$stmt->close();

if (!$category) {
    header("Location: admn_categories");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $business_type_id = intval($_POST['business_type_id']);
    $priority = intval($_POST['priority']);
    $is_approved = 1;

    // Server-side validation
    if (empty($name)) {
        $error = "Category name is required.";
    } elseif (empty($business_type_id)) {
        $error = "Business type is required.";
    } else {
        // Update category in the database
        $stmt = $conn->prepare("UPDATE categories SET name = ?, is_approved = ?, business_type_id = ?, priority = ? WHERE id = ?");
        $stmt->bind_param("siiii", $name, $is_approved, $business_type_id, $priority, $category_id);

        if ($stmt->execute()) {
            $success = "Category updated successfully!";
            header("refresh:1; url=admn_categories");
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
                                    <h5>Edit Category</h5>
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
                                                <input class="form-control" type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
                                            </div>
                                            
                                            <div class="col-md-12 mb-4" hidden>
                                                <label class="form-label">Business Type</label>
                                                <select name="business_type_id" class="form-control" required>
                                                   
                                                        <option value="<?= $category['business_type_id'] ?>" ?>
                                                            <?= htmlspecialchars($category['b_name']) ?>
                                                        </option>
                                                    
                                                </select>
                                            </div>
                                            <div class="col-md-12 mb-4">
                                                <label class="form-label">Priority</label>
                                                <input class="form-control" type="number" name="priority">
                                            </div>
                                            <div class="col-12 text-end">
                                                <button class="btn btn-primary" type="submit">Update Category</button>
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
