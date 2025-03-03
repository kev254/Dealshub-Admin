<?php
include("dbconnect.php");
$images_base_url="https://dealshub.co.ke/";

/**
 * Fetch all approved vendors
 * 
 * @return array The list of approved vendors.
 */
function getVendors($vendor_id = null) {
    global $conn;
    
    $sql = "SELECT v.id as vendor_id, v.name as vendor_name, v.rep_name, v.email, v.phone, v.logo, v.business_type_id, v.status, bt.name as bus_name  FROM vendors v JOIN business_types bt ON bt.id=v.business_type_id";
    if ($vendor_id) {
        $sql .= " WHERE v.id = ?";
    }
    $sql .= " ORDER BY v.id DESC";

    $stmt = $conn->prepare($sql);
    
    if ($vendor_id) {
        $stmt->bind_param("i", $vendor_id); 
    } 

    $stmt->execute();
    $result = $stmt->get_result();
    $vendors = [];
    while ($row = $result->fetch_assoc()) {
        $vendors[] = $row;
    }
    return $vendors;
}


/**
 * Fetch all categories (main categories only, no subcategories)
 * 
 * @return array The list of categories.
 */
function getCategories() {
    global $conn;
    $stmt = $conn->prepare("SELECT c.id, c.name, c.created_at, c.is_approved, c.vendor_id, c.priority FROM categories c  ORDER BY c.id DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    $categories = [];
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    return $categories;
}

function getBusinessTypes() {
    global $conn;
    $stmt = $conn->prepare("SELECT id, name, created_at, priority, is_approved FROM business_types");
    $stmt->execute();
    $result = $stmt->get_result();
    $business_types = [];
    while ($row = $result->fetch_assoc()) {
        $business_types[] = $row;
    }
    return $business_types;
}

function getAdmins() {
    global $conn;
    $stmt = $conn->prepare("
        SELECT 
            admins.id, 
            users.name AS user_name, 
            users.is_verified, 
            admin_roles.name AS role_name, 
            admin_roles.can_add, 
            admin_roles.can_edit, 
            admin_roles.can_delete, 
            admin_roles.can_update, 
            admin_roles.can_create, 
            admins.created_at,
            users.id as user_id
        FROM admins
        JOIN users ON admins.user_id = users.id
        JOIN admin_roles ON admins.role_id = admin_roles.id
        WHERE users.role_id = 3
    ");
    
    $stmt->execute();
    $result = $stmt->get_result();
    $admin_roles = [];
    
    while ($row = $result->fetch_assoc()) {
        $admin_roles[] = $row;
    }
    
    return $admin_roles;
}



/**
 * Fetch subcategories by category ID
 * 
 * @param int $category_id The category ID for which subcategories are to be fetched.
 * @return array The list of subcategories for the given category.
 */
function getSubCategories() {
    global $conn;
    $stmt = $conn->prepare("SELECT sb.id, sb.name, sb.created_at, c.name as cat_name, sb.is_approved, sb.vendor_id FROM sub_categories sb JOIN categories c ON sb.category_id=c.id ");
    // $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $subcategories = [];
    while ($row = $result->fetch_assoc()) {
        $subcategories[] = $row;
    }
    return $subcategories;
}

function getBranches($vendor_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT b.*, v.name as vendor_name, v.phone FROM branches b JOIN vendors v ON b.vendor_id=v.id WHERE vendor_id = ? ");
    $stmt->bind_param("i", $vendor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $branches = [];
    while ($row = $result->fetch_assoc()) {
        $branches[] = $row;
    }
    return $branches;
}

/**
 * Fetch all products or filter by vendor or category
 * 
 * @param int|null $vendor_id The vendor ID (optional).
 * @param int|null $category_id The category ID (optional).
 * @return array The list of products.
 */
function getProducts($vendor_id = null, $category_id = null, $btid=null, $pid=null) {
    global $conn;
    $sql = "SELECT p.*, v.name as vendor_name, c.name as cat_name, v.logo, v.phone, v.email FROM products p JOIN vendors v ON v.id = p.vendor_id JOIN categories c ON p.category_id = c.id";
    
    if ($vendor_id) {
        $sql .= " AND p.vendor_id = ?";
    }
    if ($category_id) {
        $sql .= " AND p.category_id = ?";
    }
    if($btid){
        $sql .= " AND v.business_type_id = ?";
    }
    if($pid){
        $sql .= " AND p.id = ?";
    }
    $sql .= " ORDER BY p.id DESC";
    
    $stmt = $conn->prepare($sql);
    
    if ($vendor_id && $category_id && $btid) {
        $stmt->bind_param("iii", $vendor_id, $category_id, $btid);
    } 
    elseif ($pid && $vendor_id) {
        $stmt->bind_param("ii", $vendor_id,$pid);
    }
    elseif ($vendor_id) {
        $stmt->bind_param("i", $vendor_id);
    } elseif ($category_id) {
        $stmt->bind_param("i", $category_id);
    }
    elseif ($btid) {
        $stmt->bind_param("i", $btid);
    }
    elseif ($pid) {
        $stmt->bind_param("i", $pid);
    }
    
   
    
    $stmt->execute();
    $result = $stmt->get_result();
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
    return $products;
}

/**
 * Fetch all flyers or filter by vendor or category
 * 
 * @param int|null $vendor_id The vendor ID (optional).
 * @param int|null $category_id The category ID (optional).
 * @return array The list of flyers.
 */
function getFlyers($vendor_id = null, $category_id = null) {
    global $conn;
    $sql = "SELECT f.id, f.title, f.flyer_path, f.thumbnail_image, f.valid_until, f.is_approved, v.name as vendor_name, description FROM flyers f JOIN vendors v ON v.id = f.vendor_id";
    
    if ($vendor_id) {
        $sql .= " AND vendor_id = ?";
    }
    if ($category_id) {
        $sql .= " AND category_id = ?";
    }
    $sql .= " ORDER BY f.id DESC";
    
    $stmt = $conn->prepare($sql);
    
    if ($vendor_id && $category_id) {
        $stmt->bind_param("ii", $vendor_id, $category_id);
    } elseif ($vendor_id) {
        $stmt->bind_param("i", $vendor_id);
    } elseif ($category_id) {
        $stmt->bind_param("i", $category_id);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $flyers = [];
    while ($row = $result->fetch_assoc()) {
        $flyers[] = $row;
    }
    return $flyers;
}
function getFlyerById($oid) {
    global $conn;
    $sql = "SELECT f.id, f.title, f.flyer_path, f.thumbnail_image, f.valid_until, v.name as vendor_name, v.phone, v.email, v.logo, description FROM flyers f JOIN vendors v ON v.id = f.vendor_id WHERE is_approved = 1";
    
    if ($oid) {
        $sql .= " AND f.id = ?";
    }
   
    
    $stmt = $conn->prepare($sql);
    
    if ($oid ) {
        $stmt->bind_param("i", $oid);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $flyers = [];
    while ($row = $result->fetch_assoc()) {
        $flyers[] = $row;
    }
    return $flyers;
}


/**
 * Fetch coupons by vendor ID
 * 
 * @param int $vendor_id The vendor ID for which coupons are to be fetched.
 * @return array The list of coupons for the given vendor.
 */
function getCoupons($vendor_id = null, $cid=null) {
    global $conn;
    
    $sql = "SELECT c.id, c.title, c.discount_percentage, c.coupon_code, c.valid_until, c.is_approved, v.id as vendor_id, v.name as vendor_name, v.logo, v.phone, v.email, v.website
            FROM coupons c JOIN vendors v ON c.vendor_id = v.id";
    
    if ($vendor_id !== null) {
        $sql .= " AND c.vendor_id = ?";
    }
    if ($cid !== null) {
        $sql .= " AND c.id = ?";
    }
    $sql .= " ORDER BY c.id DESC";
    $stmt = $conn->prepare($sql);
    
    if ($vendor_id !== null) {
        $stmt->bind_param("i", $vendor_id);
    }
    if ($cid !== null) {
        $stmt->bind_param("i", $cid);
    }
    
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $coupons = [];
    while ($row = $result->fetch_assoc()) {
        $coupons[] = $row;
    }
    
    return $coupons;
}


function reset_pass($email, $role_id = 2) {
    global $conn;
    include("send_mail.php");

    // Check if the email already exists
    $sql = "SELECT id, name, email FROM users WHERE email = ? AND role_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $email, $role_id); // "si" for string and integer
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Fetch user details
        $stmt->bind_result($id, $name, $email);
        $stmt->fetch();

        $reset_code = bin2hex(random_bytes(8)); 

        // Save the reset code to the database
        $update_sql = "UPDATE users SET reset_password_code = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $reset_code, $id);
        $update_stmt->execute();

        $hashed_code = base64_encode($reset_code);

        // Send reset email with the code in the URL
        $reset_url = "https://portal.dealshub.co.ke/resetpass?cuuid=$hashed_code";
        SendPassResetMail($email, $name, "Password Reset has been requested for your account. Use this link to reset your password: \n$reset_url", "Password Reset Link", $reset_url);

        return json_encode([
            'success' => true,
            'message' => 'Email sent! Check inbox or spam folder.'
        ]);
    } else {
        return json_encode([
            'success' => false,
            'message' => 'Account not found. Contact Support.'
        ]);
    }
}


function login($email, $password) {
    global $conn;

    // Retrieve user by email
    $sql = "SELECT id,role_id, vendor_id, is_verified, name, email FROM users WHERE email = ? AND password = ?";
    $login_pass=md5(($password));
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $login_pass);
    $stmt->execute();
    $stmt->store_result();

    // Check if user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $role_id, $vendor_id, $is_verified, $name, $email);
        $stmt->fetch();
        if($is_verified == 1){
            if($role_id===2){
                
                $_SESSION['vid']=$vendor_id;
                header("Location: my_vendor");
                $message = "We have detected new account login to your account";
                saveNotification($conn, $vendor_id, "New account login", $message, $email, $name);
                return json_encode([
                    'success' => true,
                    'message' => 'Vendor Login successful!'
                ]);
            }
            elseif($role_id===3){
                $_SESSION['vid']=$vendor_id;
                header("Location: admn");
                return json_encode([
                    'success' => true,
                    'message' => 'Admin Login successful!'
                ]);
            }
            else{
                return json_encode([
                    'success' => false,
                    'message' => 'Unauthorize Account type!'
                ]);
            }
            

        }
        else{
            return json_encode([
                'success' => false,
                'message' => 'Your account is not verified. Please verify your email.'
            ]);
        }
        
    }
    else{
        return json_encode([
            'success' => false,
            'message' => 'Account not found!. Signup'
        ]);
    }

    
}

// Function to handle vendor application
function createCoupon($vendor_id, $category_id, $title, $coupon_code, $description, $discount_percentage, $valid_until, $is_approved = 0) {
    global $conn;

    // Check if the coupon code already exists
    $sql = "SELECT id FROM coupons WHERE coupon_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $coupon_code);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        return json_encode([
            'success' => false,
            'message' => 'This coupon code is already in use. Please choose a different one.'
        ]);
    }

    // Insert coupon into the database
    $sql = "INSERT INTO coupons (vendor_id, category_id, title, coupon_code, description, discount_percentage, valid_until, is_approved) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisssdsi", $vendor_id, $category_id, $title, $coupon_code, $description, $discount_percentage, $valid_until, $is_approved);

    if ($stmt->execute()) {
        return json_encode([
            'success' => true,
            'message' => 'Coupon created successfully.'
        ]);
    } else {
        return json_encode([
            'success' => false,
            'message' => 'There was an error creating the coupon. Please try again.'
        ]);
    }
}


function saveNotification($conn, $vendor_id, $title, $message, $user_email, $name) {
    $sql = "INSERT INTO notifications (vendor_id, title, message, is_read) 
            VALUES ('$vendor_id', '$title', '$message', 0)";
    if (mysqli_query($conn, $sql)) {
        //
        include("send_mail.php");
        SendMail($user_email,$name,$message,$title);
        return true;

    } else {
        return false;
    }
}

function getNotificatiosn($vendor_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT n.*, v.name as vendor_name FROM notifications n  JOIN vendors v ON n.vendor_id=v.id WHERE vendor_id = ? ORDER BY n.id DESC LIMIT 10 ");
    $stmt->bind_param("i", $vendor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $notifications = [];
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
    return $notifications;
}

function getSupportTickets() {
    global $conn;
    $stmt = $conn->prepare("SELECT st.*, v.name as vendor_name, v.phone FROM support_tickets st  JOIN vendors v ON st.vendor_id=v.id ORDER BY st.id DESC ");
    // $stmt->bind_param("i", $vendor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ticket = [];
    while ($row = $result->fetch_assoc()) {
        $ticket[] = $row;
    }
    return $ticket;
}

function register_vendor($name, $email, $password, $role_id, $vendor_id) {
    global $conn;
    
    // Check if the email already exists
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    // If the email exists, update the user details
    if ($stmt->num_rows > 0) {
        // Hash the new password if it's provided
        $hashed_password = $password ? md5($password) : null;
        
        // Update the existing user record
        $update_sql = "UPDATE users SET name = ?, password = ?, role_id = ?, vendor_id = ? WHERE email = ?";
        $update_stmt = $conn->prepare($update_sql);
        
        $update_stmt->bind_param("sssis", $name, $hashed_password, $role_id, $vendor_id, $email);

        
        if ($update_stmt->execute()) {
            $message = "Your account details have been updated.\nYour new username is: $email and your password is: $password.\nYou can login and start posting your offers and products. Thank you.\n";
            saveNotification($conn, $vendor_id, "Vendor account updated.", $message, $email, $name);
            // Insert or update the branch as needed
            return json_encode([
                'success' => true,
                'message' => 'Vendor account has been updated successfully.'
            ]);
        } else {
            return json_encode([
                'success' => false,
                'message' => "Error updating user: " . $update_stmt->error
            ]);
        }
    }
    
    // If the email does not exist, proceed with the original registration process
    $hashed_password = md5($password);
    $is_verified = 1;
    
    // Insert the new user into the database
    $sql = "INSERT INTO users (name, email, password, role_id, vendor_id, is_verified) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssiii", $name, $email, $hashed_password, $role_id, $vendor_id, $is_verified);
    
    if ($stmt->execute()) {
        // Insert a new branch with default values (lat=0, long=0, status=1)
        $branch_sql = "INSERT INTO branches (branch_name, latitude, longitude, vendor_id, status) 
                       VALUES ('All Branches', 0, 0, ?, 1)";
        $branch_stmt = $conn->prepare($branch_sql);
        $branch_stmt->bind_param("i", $vendor_id);
        
        if ($branch_stmt->execute()) {
            $message = "Congratulations! Your account has been approved.\nYour username is: $email and password is: $password. You can login and start posting your offers and products. Thank you.\n";
            saveNotification($conn, $vendor_id, "Vendor account approved.", $message, $email, $name);
            return json_encode([
                'success' => true,
                'message' => 'Registration successful! Proceed to sign in.'
            ]);
        } else {
            return json_encode([
                'success' => false,
                'message' => "Error inserting branch: " . $branch_stmt->error
            ]);
        }
    } else {
        return json_encode([
            'success' => false,
            'message' => "Error: " . $stmt->error
        ]);
    }
}




?>
