<?php
session_start();
$vid = $_SESSION['vid'];
if (!isset($vid) || $vid === null || $vid === "") {
    header("Location: auth");
    exit;
}

include("includes/data.php");

// Fetch support tickets for the admin
$tickets = getSupportTickets();

if (isset($_GET['action']) && isset($_GET['act_id'])) {
    $action = base64_decode($_GET['action']);
    $act_id = base64_decode($_GET['act_id']);

    // Validate action
    if (!in_array($action, ['activate', 'deactivate'])) {
        die("Invalid action.");
    }

    // Set the status based on the action
    $status = ($action === "activate") ? "Open" : "Closed";

    // Update the ticket status securely
    $stmt = $conn->prepare("UPDATE support_tickets SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $act_id);

    if ($stmt->execute()) {
        $success = "Ticket status updated successfully!";
        header("refresh:1; url=admn_tickets");
    } else {
        $error = "Failed to update ticket status. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">

<?php include("partials/head.php"); ?>

<body>
    <!-- tap on top start -->
    <div class="tap-top">
        <span class="lnr lnr-chevron-up"></span>
    </div>
    <!-- tap on tap end -->

    <!-- page-wrapper Start-->
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        <!-- Page Header Start-->
        <?php include("partials/admn_header.php"); ?>
        <!-- Page Header Ends-->

        <!-- Page Body Start-->
        <div class="page-body-wrapper">
            <!-- Page Sidebar Start-->
            <?php include("partials/admn_sidebar.php"); ?>
            <!-- Page Sidebar Ends-->

            <!-- index body start -->
            <div class="page-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card card-table">
                                <div class="card-body">
                                    <?php if (isset($success)): ?>
                                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                                    <?php elseif (isset($error)): ?>
                                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                                    <?php endif; ?>
                                    
                                    <div class="title-header option-title d-sm-flex d-block">
                                        <h5>Tickets List</h5>
                                    </div>
                                    <div>
                                        <div class="table-responsive">
                                            <table class="table all-package theme-table table-product" id="table_id">
                                                <thead>
                                                    <tr>
                                                        <th>Ticket Number</th>
                                                        <th>Vendor Name</th>
                                                        <th>Phone</th>
                                                        <th>Subject</th>
                                                        <th>Message</th>
                                                        <th>Status</th>
                                                        <th>Option</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($tickets)): ?>
                                                        <?php foreach ($tickets as $ticket): ?>
                                                            <tr>
                                                                <td class="td-price"><?= htmlspecialchars($ticket['ticket_number']) ?></td>
                                                                <td class=""><?= htmlspecialchars($ticket['vendor_name']) ?></td>
                                                                <td class=""><?= htmlspecialchars($ticket['phone']) ?></td>
                                                                <td class=""><?= htmlspecialchars($ticket['subject']) ?></td>
                                                                <td class=""><?= htmlspecialchars($ticket['description']) ?></td>
                                                                <td class="<?= $ticket['status'] == "Open" ? 'success' : 'danger' ?>">
                                                                    <span><?= $ticket['status'] == "Open" ? 'Open' : 'Closed' ?></span>
                                                                </td>
                                                                <td>
                                                                    <ul>
                                                                        <li>
                                                                            <a href="admn_tickets?action=<?= base64_encode($ticket['status'] == "Open" ? 'deactivate' : 'activate') ?>&act_id=<?= base64_encode($ticket['id']) ?>" 
                                                                               onclick="return confirm('Are you sure you want to <?= $ticket['status'] == "Open" ? 'close' : 'reopen' ?> this ticket?')">
                                                                                <i class="ri-<?= $ticket['status'] == "Open" ? 'close-line text-danger' : 'check-line text-success' ?>"></i>
                                                                            </a>
                                                                        </li>
                                                                    </ul>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="7" class="text-center">No tickets found.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Container-fluid Ends-->

                <!-- Footer Start-->
                <?php include("partials/footer.php"); ?>
                <!-- Footer End-->
            </div>
        </div>
    </div>
    <!-- page-wrapper End-->
    <?php include("partials/js.php"); ?>
</body>

</html>
