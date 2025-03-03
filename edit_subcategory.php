<?php
session_start();
$vid = $_SESSION['vid'];
if (!isset($vid) || $vid === null || $vid === "") {
    header("Location: auth");
    exit;
}

include("includes/data.php");

// Get sub-category ID from query string
if (!isset($_GET['sid']) || empty($_GET['sid'])) {
    header("Location: sub_categories");
    exit;
}

$sub_category_id = base64_decode($_GET['sid']);

// Fetch the sub-category details
$stmt = $conn->prepare("SELECT sc.id, sc.name, sc.category_id, c.name as category_name FROM sub_categories sc JOIN categories c ON sc.category_id=c.id WHERE sc.id = ? AND sc.vendor_id = ?");
$stmt->bind_param("ii", $sub_category_id, $vid);
$stmt->execute();
$result = $stmt->get_result();
$sub_category = $result->fetch_assoc();
$stmt->close();

if (!$sub_category) {
    header("Location: sub_categories");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category_id = intval($_POST['category_id']); // Parent category selected

    // Server-side validation
    if (empty($name)) {
        $error = "Sub-category name is required.";
    } elseif (empty($category_id)) {
        $error = "Parent category is required.";
    } else {
        // Update sub-category in the database
        $stmt = $conn->prepare("UPDATE sub_categories SET name = ?, category_id = ? WHERE id = ? AND vendor_id = ?");
        $stmt->bind_param("siii", $name, $category_id, $sub_category_id, $vid);

        if ($stmt->execute()) {
            $success = "Sub-category updated successfully!";
            header("refresh:1; url=sub_categories");
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
        <?php include("partials/header.php"); ?>
        <div class="page-body-wrapper">
            <?php include("partials/sidebar.php"); ?>
            <div class="page-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <h5>Edit Sub-category</h5>
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
                                                <input class="form-control" type="text" name="name" value="<?= htmlspecialchars($sub_category['name']) ?>" required>
                                            </div>
                                           
                                            <div class="col-md-12 mb-4">
                                                <label class="form-label">Select Parent Category</label>
                                                <select class="form-control" name="category_id" required>
                                                    <option>---Select Category---</option>
                                                    <?php foreach ($categories as $category): ?>
                                                        <option value="<?= $category['id'] ?>" <?= $sub_category['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($category['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="col-12 text-end">
                                                <button class="btn btn-primary" type="submit">Update Sub-category</button>
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
