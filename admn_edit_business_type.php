<?php
session_start();
$vid = $_SESSION['vid'];
if (!isset($vid) || $vid === null || $vid === "") {
    header("Location: auth");
    exit;
}

include("includes/data.php");

// Initialize variables
$name = "";
$priority = 1;
$is_edit = false;

// Check if editing an existing business type
if (isset($_GET['id'])) {
    $id = base64_decode($_GET['id']);
    $is_edit = true;

    // Fetch the existing business type
    $stmt = $conn->prepare("SELECT name, priority FROM business_types WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($name, $priority);
    $stmt->fetch();
    $stmt->close();
}

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $priority = intval($_POST['priority']);

    // Server-side validation
    if (empty($name)) {
        $error = "Business type name is required.";
    } else {
        if ($is_edit) {
            // Update existing business type
            $stmt = $conn->prepare("UPDATE business_types SET name = ?, priority = ? WHERE id = ?");
            $stmt->bind_param("sii", $name, $priority, $id);
        } else {
            // Insert new business type
            $stmt = $conn->prepare("INSERT INTO business_types (name, priority, is_approved) VALUES (?, ?, 1)");
            $stmt->bind_param("si", $name, $priority);
        }

        if ($stmt->execute()) {
            $success = $is_edit ? "Business type updated successfully!" : "Business type added successfully!";
            header("refresh:1; url=admn_bus_types");
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
                                    <h5><?= $is_edit ? "Edit" : "Create"; ?> Business Type</h5>
                                    <?php if (isset($error)): ?>
                                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                                    <?php endif; ?>
                                    <?php if (isset($success)): ?>
                                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                                    <?php endif; ?>
                                    <form class="theme-form" method="POST">
                                        <div class="row">
                                            <div class="col-md-12 mb-4">
                                                <label class="form-label">Business Type Name</label>
                                                <input class="form-control" type="text" name="name" value="<?= htmlspecialchars($name) ?>" required>
                                            </div>
                                            <div class="col-md-12 mb-4">
                                                <label class="form-label">Priority</label>
                                                <input class="form-control" type="number" name="priority" value="<?= htmlspecialchars($priority) ?>" required>
                                            </div>
                                            <div class="col-12 text-end">
                                                <button class="btn btn-primary" type="submit"><?= $is_edit ? "Update" : "Create"; ?> Business Type</button>
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
