<?php 
session_start();
$vid = $_SESSION['vid'];
if (!isset($vid) || $vid === null || $vid === "") {
    header("Location: auth");
    exit();
}

include("includes/data.php");

$business_types = getBusinessTypes();

if (isset($_GET['del_id'])) {
    $cat_id = base64_decode($_GET['del_id']);
    $stmt = $conn->prepare("DELETE FROM business_types WHERE id = ?");
    $stmt->bind_param("i", $cat_id);
    if ($stmt->execute()) {
        $success = "Business type deleted successfully!";
        header("refresh:1; url=admn_bus_types");
    }
}

if (isset($_GET['action']) && isset($_GET['act_id'])) {
    $action = base64_decode($_GET['action']);
    $act_id = base64_decode($_GET['act_id']);
    $is_approved = ($action === "activate") ? 1 : 0;
    $stmt = $conn->prepare("UPDATE business_types SET is_approved = ? WHERE id = ?");
    $stmt->bind_param("ii", $is_approved, $act_id);
    if ($stmt->execute()) {
        $success = "Business type updated successfully!";
        header("refresh:1; url=admn_bus_types");
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
                        <div class="col-sm-12">
                            <div class="card card-table">
                                <div class="card-body">
                                    <div class="title-header option-title">
                                        <h5>All Business Types</h5>
                                        <a href="admn_create_business_type" class="btn btn-theme">
                                            <i data-feather="plus-square"></i>Create Business Type
                                        </a>
                                    </div>
                                    <div class="table-responsive category-table">
                                        <table class="table theme-table" id="table_id">
                                            <thead>
                                                <tr>
                                                    <th>Business Type Name</th>
                                                    <th>Priority</th>
                                                    <th>Date Created</th>
                                                    <th>Status</th>
                                                    <th>Options</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($business_types as $type): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($type['name']) ?></td>
                                                        <td><?= $type['priority'] ?? 'N/A' ?></td>
                                                        <td><?= $type['created_at'] ?></td>
                                                        <td>
                                                            <?php if ($type['is_approved'] == 1): ?>
                                                                <span class="badge bg-success">Active</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-warning">Inactive</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <ul>
                                                                <li>
                                                                    <a href="admn_edit_business_type?id=<?= base64_encode($type['id']) ?>">
                                                                        <i class="ri-pencil-line"></i>
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a href="?del_id=<?= base64_encode($type['id']) ?>" 
                                                                       onclick="return confirm('Are you sure you want to delete this business type?')">
                                                                        <i class="ri-delete-bin-line"></i>
                                                                    </a>
                                                                </li>
                                                                <?php if ($type['is_approved'] == 0): ?>
                                                                    <li>
                                                                        <a href="?action=<?= base64_encode('activate') ?>&act_id=<?= base64_encode($type['id']) ?>">
                                                                            <i class="ri-checkbox-circle-line text-success"></i>
                                                                        </a>
                                                                    </li>
                                                                <?php else: ?>
                                                                    <li>
                                                                        <a href="?action=<?= base64_encode('deactivate') ?>&act_id=<?= base64_encode($type['id']) ?>">
                                                                            <i class="ri-close-circle-line text-danger"></i>
                                                                        </a>
                                                                    </li>
                                                                <?php endif; ?>
                                                            </ul>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container-fluid">
                    <?php include("partials/footer.php"); ?>
                </div>
            </div>
        </div>
    </div>
    <?php include("partials/js.php");?>
</body>
</html>