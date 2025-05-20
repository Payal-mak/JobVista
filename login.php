<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    // Redirect based on role
    if (isJobSeeker()) {
        header("Location: job_seeker/dashboard.php");
    } elseif (isEmployer()) {
        header("Location: employer/dashboard.php");
    } elseif (isAdmin()) {
        header("Location: admin/dashboard.php");
    }
    exit();
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $result = loginUser($email, $password);

    if ($result === true) {
        // Redirect based on role after successful login
        if (isJobSeeker()) {
            header("Location: job_seeker/dashboard.php");
        } elseif (isEmployer()) {
            header("Location: employer/dashboard.php");
        } elseif (isAdmin()) {
            header("Location: admin/dashboard.php");
        }
        exit();
    } else {
        $errors[] = $result;
    }
}

$title = "Login";
include 'includes/header.php';
?>

<!-- Login Section -->
<section class="bg-gray-100 min-h-screen flex items-center justify-center py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
            <h2 class="text-3xl font-bold text-blue-600 text-center mb-6">Login to Your Account</h2>

            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 text-red-800 p-4 rounded-md mb-6">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" class="space-y-6">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-gray-700 font-semibold mb-2">Email Address</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" class="w-full p-3 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-gray-700 font-semibold mb-2">Password</label>
                    <input type="password" id="password" name="password" class="w-full p-3 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" class="w-full bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login
                    </button>
                </div>
            </form>

            <p class="text-center text-gray-600 mt-6">
                Don't have an account? <a href="register.php" class="text-blue-600 hover:underline">Register here</a>
            </p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>