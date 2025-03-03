<?php
session_start();
$vid = $_SESSION['vid'];
if (!isset($vid) || $vid === null || $vid === "") {
    header("Location: auth");
}
include("includes/data.php");

$vendors = getVendors($vid);
$v_email = $vendors[0]['email'];
$v_name = $vendors[0]['vendor_name'];

$error = $success = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $priority = mysqli_real_escape_string($conn, $_POST['priority']);

    if (empty($subject) || empty($description) || empty($priority)) {
        $error = "All fields are required.";
    } else {
        // Insert into support_tickets
        $ticket_number = uniqid("DHK_");
        $sql = "INSERT INTO support_tickets (vendor_id, ticket_number, subject, description, priority, status) 
                VALUES ('$vid', '$ticket_number', '$subject', '$description', '$priority', 'Open')";

        if (mysqli_query($conn, $sql)) {
            // Immediately invoke the notification function
            $notification_title = "New Support Ticket Created -#$ticket_number ";
            $notification_message = "Your support ticket (#$ticket_number) has been created successfully.\n\nMessage: $description.\n\nPriority: $priority";
            
            if (saveNotification($conn, $vid, $notification_title, $notification_message,$v_email,$v_name)) {
                $success = "Ticket created and notification sent successfully!";
            } else {
                $error = "Ticket created but failed to send notification.";
            }
        } else {
            $error = "Failed to create ticket. Please try again.";
        }
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
        <?php include("partials/header.php"); ?>

        <!-- Page Header Ends-->

        <!-- Page Body Start-->
        <div class="page-body-wrapper">
            <?php include("partials/sidebar.php"); ?>

            <!-- Ticket Section Start -->
            <div class="page-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="card card-table">
                                <!-- Table Start -->
                                <div class="card-body">
                                    <div class="title-header option-title">
                                        <h5>Support Ticket</h5>
                                    </div>
                                    <div>
                                        <?php if (!empty($error)) : ?>
                                            <div class="alert alert-danger"><?= $error ?></div>
                                        <?php endif; ?>
                                        <?php if (!empty($success)) : ?>
                                            <div class="alert alert-success"><?= $success ?></div>
                                        <?php endif; ?>

                                        <!-- Support Ticket Form -->
                                        <form method="POST" class="theme-form theme-form-2">
                                            <div class="mb-4">
                                                <label class="form-label-title">Subject</label>
                                                <input type="text" name="subject" class="form-control"
                                                       placeholder="Enter ticket subject" required>
                                            </div>

                                            <div class="mb-4">
                                                <label class="form-label-title">Description</label>
                                                <textarea name="description" class="form-control"
                                                          placeholder="Describe the issue" rows="5" required></textarea>
                                            </div>

                                            <div class="mb-4">
                                                <label class="form-label-title">Priority</label>
                                                <select name="priority" class="form-control" required>
                                                    <option value="Low">Low</option>
                                                    <option value="Medium">Medium</option>
                                                    <option value="High">High</option>
                                                </select>
                                            </div>

                                            <div>
                                                <button type="submit" class="btn btn-primary">Submit Ticket</button>
                                            </div>
                                        </form>
                                        <!-- End Support Ticket Form -->
                                    </div>
                                </div>
                                <!-- Table End -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Start -->
                <?php include("partials/footer.php"); ?>
                <!-- Footer End -->
            </div>
            <!-- Ticket Section End -->
        </div>
        <!-- Page Body End-->

        <!-- page-wrapper End-->
        <?php include("partials/js.php"); ?>
    </div>
</body>

</html>
