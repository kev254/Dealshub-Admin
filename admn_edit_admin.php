<?php
session_start();
$vid = $_SESSION['vid'];
if (!isset($vid) || $vid === null || $vid === "") {
    header("Location: auth");
    exit;
}

include("includes/data.php");

// Get admin ID from URL
$admin_id = isset($_GET['aid']) ? base64_decode($_GET['aid']) : 0;
if (!isset($admin_id)) {
    header("Location: admn_list");
    exit;
}

// Fetch admin details
$stmt = $conn->prepare("
    SELECT users.id, users.name, users.email, admins.role_id, users.is_verified 
    FROM users 
    JOIN admins ON users.id = admins.user_id 
    WHERE users.id = ?
");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

if (!$admin) {
    header("Location: admn_list");
    exit;
}

// Fetch roles
$stmt = $conn->prepare("SELECT id, name FROM admin_roles");
$stmt->execute();
$result = $stmt->get_result();
$roles = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role_id = intval($_POST['role_id']);
    $password = trim($_POST['password']);
    $is_verified = isset($_POST['is_verified']) ? 1 : 0; // Checkbox for verification status
    $hash_password = !empty($password) ? md5($password) : null;

    // Validate inputs
    if (empty($name) || empty($email) || $role_id == 0) {
        $error = "All fields except password are required.";
    } else {
        // Check if email already exists for another user
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $admin_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "This email: $email is already in use.";
        } else {
            // Update users table (Name, Email, Password, is_verified)
            if ($hash_password) {
                $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ?, is_verified = ? WHERE id = ?");
                $stmt->bind_param("sssii", $name, $email, $hash_password, $is_verified, $admin_id);
            } else {
                $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, is_verified = ? WHERE id = ?");
                $stmt->bind_param("ssii", $name, $email, $is_verified, $admin_id);
            }

            if ($stmt->execute()) {
                // Update admins table (Role)
                $stmt = $conn->prepare("UPDATE admins SET role_id = ? WHERE user_id = ?");
                $stmt->bind_param("ii", $role_id, $admin_id);
                $stmt->execute();
                $message = "Dear, $name, Congarulations your admin account has been updated\nusername: $email and password is : $password. \nYou can login and do the allowed operations.\nThank you. ";
                saveNotification($conn, 22, "New Admin Account updated",$message,$email, $name);

                $success = "Admin updated successfully!";
                header("refresh:1; url=admn_list");
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

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
                                    <h5>Edit Admin</h5>
                                    <?php if (isset($error)): ?>
                                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                                    <?php endif; ?>
                                    <?php if (isset($success)): ?>
                                        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                                    <?php endif; ?>
                                    <form class="theme-form" method="POST">
                                        <div class="row">
                                            <div class="col-md-12 mb-4">
                                                <label class="form-label">Full Name</label>
                                                <input class="form-control" type="text" name="name" value="<?= htmlspecialchars($admin['name']) ?>" required>
                                            </div>
                                            <div class="col-md-12 mb-4">
                                                <label class="form-label">Email</label>
                                                <input class="form-control" type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required>
                                            </div>
                                            <div class="col-md-12 mb-4">
                                                <label class="form-label">New Password (leave blank to keep current)</label>
                                                <input class="form-control" type="password" name="password">
                                            </div>
                                            <div class="col-md-12 mb-4">
                                                <label class="form-label">Select Role</label>
                                                <select class="form-control" name="role_id" required>
                                                    <option value="">-- Select Role --</option>
                                                    <?php foreach ($roles as $role): ?>
                                                        <option value="<?= $role['id'] ?>" <?= ($admin['role_id'] == $role['id']) ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($role['name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-12 mb-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="is_verified" value="1" <?= ($admin['is_verified'] == 1) ? 'checked' : '' ?>>
                                                    <label class="form-check-label">Verified Account</label>
                                                </div>
                                            </div>
                                            <div class="col-12 text-end">
                                                <button class="btn btn-primary" type="submit">Update Admin</button>
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
