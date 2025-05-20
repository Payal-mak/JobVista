<?php
// Get current page name for active state highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="dashboard-sidebar">
    <div class="sidebar-header">
        <div class="user-profile">
            <div class="avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="user-info">
                <h4><?= htmlspecialchars($_SESSION['user_name']) ?></h4>
                <p>Employer Account</p>
            </div>
        </div>
    </div>

    <div class="sidebar-menu">
        <ul>
            <li class="<?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
                <a href="dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="<?= ($current_page == 'post_job.php') ? 'active' : '' ?>">
                <a href="post_job.php">
                    <i class="fas fa-plus-circle"></i>
                    <span>Post New Job</span>
                </a>
            </li>
            <li class="<?= ($current_page == 'manage_jobs.php') ? 'active' : '' ?>">
                <a href="manage_jobs.php">
                    <i class="fas fa-briefcase"></i>
                    <span>Manage Jobs</span>
                    <?php if (count($jobs) > 0): ?>
                        <span class="badge"><?= count($jobs) ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="<?= ($current_page == 'applications.php') ? 'active' : '' ?>">
                <a href="applications.php">
                    <i class="fas fa-file-alt"></i>
                    <span>Applications</span>
                    <?php if (count($applications) > 0): ?>
                        <span class="badge"><?= count($applications) ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="<?= ($current_page == 'messages.php') ? 'active' : '' ?>">
                <a href="../messages.php">
                    <i class="fas fa-envelope"></i>
                    <span>Messages</span>
                    <?php if ($unread_messages > 0): ?>
                        <span class="badge"><?= $unread_messages ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="<?= ($current_page == 'profile.php') ? 'active' : '' ?>">
                <a href="../profile.php">
                    <i class="fas fa-user-cog"></i>
                    <span>Profile Settings</span>
                </a>
            </li>
            <li>
                <a href="../../logout.php">Logout</a>
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="sidebar-footer">
        <p>Need help?</p>
        <a href="../contact.php" class="btn-help">
            <i class="fas fa-question-circle"></i> Contact Support
        </a>
    </div>
</div>