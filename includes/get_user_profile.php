<?php
require_once 'auth.php';
require_once 'db_connect.php';

if (!isset($_GET['user_id'])) {
    die('User ID not provided');
}

$user_id = (int)$_GET['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('User not found');
}

// Count jobs posted (if employer)
if ($user['role'] === 'employer') {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM jobs WHERE employer_id = ?");
    $stmt->execute([$user_id]);
    $jobs_count = $stmt->fetchColumn();
}

// Count applications (if job seeker)
if ($user['role'] === 'job_seeker') {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $applications_count = $stmt->fetchColumn();
}
?>

<div class="user-profile">
    <div class="profile-header">
        <div class="profile-avatar">
            <i class="fas fa-user-circle"></i>
        </div>
        <div class="profile-info">
            <h3><?= htmlspecialchars($user['name']) ?></h3>
            <p><?= htmlspecialchars($user['email']) ?></p>
            <span class="role-badge <?= $user['role'] ?>">
                <?= ucfirst(str_replace('_', ' ', $user['role'])) ?>
            </span>
            <span class="status-badge <?= $user['status'] ?>">
                <?= ucfirst($user['status']) ?>
            </span>
        </div>
    </div>
    
    <div class="profile-stats">
        <?php if ($user['role'] === 'employer'): ?>
            <div class="stat-item">
                <div class="stat-value"><?= $jobs_count ?></div>
                <div class="stat-label">Jobs Posted</div>
            </div>
        <?php elseif ($user['role'] === 'job_seeker'): ?>
            <div class="stat-item">
                <div class="stat-value"><?= $applications_count ?></div>
                <div class="stat-label">Applications</div>
            </div>
        <?php endif; ?>
        
        <div class="stat-item">
            <div class="stat-value"><?= date('M d, Y', strtotime($user['created_at'])) ?></div>
            <div class="stat-label">Member Since</div>
        </div>
    </div>
    
    <div class="profile-actions">
        <?php if ($user['status'] === 'active'): ?>
            <a href="../admin/manage_users.php?deactivate=<?= $user['id'] ?>" class="btn btn-warning">
                <i class="fas fa-eye-slash"></i> Deactivate
            </a>
            <a href="../admin/manage_users.php?ban=<?= $user['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to ban this user?')">
                <i class="fas fa-ban"></i> Ban User
            </a>
        <?php elseif ($user['status'] === 'inactive'): ?>
            <a href="../admin/manage_users.php?activate=<?= $user['id'] ?>" class="btn btn-success">
                <i class="fas fa-eye"></i> Activate
            </a>
        <?php else: ?>
            <a href="../admin/manage_users.php?unban=<?= $user['id'] ?>" class="btn btn-success">
                <i class="fas fa-check-circle"></i> Unban
            </a>
        <?php endif; ?>
    </div>
</div>  