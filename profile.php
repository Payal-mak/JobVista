<?php
require_once 'includes/config.php'; // Include config to get $pdo
require_once 'includes/auth.php';
requireLogin();

$user_id = $_SESSION['user_id'];

// Fetch current user details
try {
    $stmt = $pdo->prepare("SELECT user_name, email, profile_photo FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
} catch (PDOException $e) {
    $_SESSION['message'] = "Database error: " . $e->getMessage();
    $_SESSION['message_type'] = "danger";
    header("Location: index.php");
    exit();
}

if (!$user) {
    $_SESSION['message'] = "User not found.";
    $_SESSION['message_type'] = "danger";
    header("Location: index.php");
    exit();
}

// Handle profile photo upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_photo'])) {
    $upload_dir = 'assets/uploads/profile_photos/';
    
    // Create upload directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            $_SESSION['message'] = "Failed to create upload directory.";
            $_SESSION['message_type'] = "danger";
            header("Location: profile.php");
            exit();
        }
    }

    // Check if directory is writable
    if (!is_writable($upload_dir)) {
        $_SESSION['message'] = "Upload directory is not writable.";
        $_SESSION['message_type'] = "danger";
        header("Location: profile.php");
        exit();
    }

    $file = $_FILES['profile_photo'];
    $file_name = 'user_' . $user_id . '_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
    $file_path = $upload_dir . $file_name;

    // Validate file
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    if (!in_array($file['type'], $allowed_types)) {
        $_SESSION['message'] = "Only JPEG, PNG, and GIF files are allowed.";
        $_SESSION['message_type'] = "danger";
    } elseif ($file['size'] > $max_size) {
        $_SESSION['message'] = "File size exceeds 5MB limit.";
        $_SESSION['message_type'] = "danger";
    } elseif ($file['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['message'] = "File upload error: " . $file['error'];
        $_SESSION['message_type'] = "danger";
    } elseif (move_uploaded_file($file['tmp_name'], $file_path)) {
        // Delete old photo if exists
        if ($user['profile_photo'] && file_exists($user['profile_photo'])) {
            unlink($user['profile_photo']);
        }

        // Update database
        try {
            $stmt = $pdo->prepare("UPDATE users SET profile_photo = ? WHERE id = ?");
            $stmt->execute([$file_path, $user_id]);
            $_SESSION['message'] = "Profile photo updated successfully.";
            $_SESSION['message_type'] = "success";
        } catch (PDOException $e) {
            $_SESSION['message'] = "Database error: " . $e->getMessage();
            $_SESSION['message_type'] = "danger";
        }
    } else {
        $_SESSION['message'] = "Failed to upload profile photo.";
        $_SESSION['message_type'] = "danger";
    }
    header("Location: profile.php");
    exit();
}

include 'includes/header.php';
?>

<!-- Profile Settings Section -->
<section class="min-vh-100 bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white">
                        <h2 class="h4 mb-0">Profile Settings</h2>
                    </div>
                    <div class="card-body">
                        <!-- Profile Photo -->
                        <div class="text-center mb-4">
                            <img src="<?= $user['profile_photo'] ? htmlspecialchars($user['profile_photo']) : 'assets/images/default_profile.jpg' ?>" alt="Profile Photo" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                            <h5><?= htmlspecialchars($user['user_name']) ?></h5>
                            <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>
                        </div>

                        <!-- Upload Profile Photo Form -->
                        <form method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="profile_photo" class="form-label">Upload Profile Photo</label>
                                <input type="file" class="form-control" id="profile_photo" name="profile_photo" accept="image/jpeg,image/png,image/gif" required>
                                <small class="form-text text-muted">Max 5MB. Allowed: JPEG, PNG, GIF.</small>
                            </div>
                            <button type="submit" class="btn btn-primary" style="background-color: #1491ea; border-color: #1491ea;">Upload Photo</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>