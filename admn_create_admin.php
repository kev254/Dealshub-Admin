<?php
session_start();
$vid = $_SESSION['vid'];
if (!isset($vid) || $vid === null || $vid === "") {
    header("Location: auth");
    exit;
}

include("includes/data.php");

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
    $password = trim($_POST['password']);
    $role_id = intval($_POST['role_id']);
    $is_verified = 1;
    $hash_password = md5($password);

    // Validate inputs
    if (empty($name) || empty($email) || empty($_POST['password']) || $role_id == 0) {
        $error = "All fields are required.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "This email: $email  already in use.";
        } else {
            // Insert user into users table
            $adm_role_id=3;
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, is_verified, role_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssii", $name, $email, $hash_password, $is_verified, $adm_role_id);

            if ($stmt->execute()) {
                $user_id = $stmt->insert_id;

                // Insert into admins table
                $stmt = $conn->prepare("INSERT INTO admins (user_id, role_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $user_id, $role_id);
                $stmt->execute();
                $message = "Dear, $name, Congarulations your admin account has been created\nusername: $email and password is : $password. \nYou can login and do the allowed operations.\nThank you. ";
                saveNotification($conn, 22, "New Admin Account created",$message,$email, $name);
                $success = "Admin created successfully!";
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
                                    <h5>Create Admin</h5>
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
                                                <input class="form-control" type="text" name="name" required>
                                            </div>
                                            <div class="col-md-12 mb-4">
                                                <label class="form-label">Email</label>
                                                <input class="form-control" type="email" name="email" required>
                                            </div>
                                            <div class="col-md-12 mb-4">
                                                <label class="form-label">Password</label>
                                                <input class="form-control" type="password" name="password" required>
                                            </div>
                                            <div class="col-md-12 mb-4">
                                                <label class="form-label">Select Role</label>
                                                <select class="form-control" name="role_id" required>
                                                    <option value="">-- Select Role --</option>
                                                    <?php foreach ($roles as $role): ?>
                                                        <option value="<?= $role['id'] ?>"><?= htmlspecialchars($role['name']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-12 text-end">
                                                <button class="btn btn-primary" type="submit">Create Admin</button>
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
