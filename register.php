<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $company = $_POST['company'] ?? '';
    $role = $_POST['role'] ?? ''; // Get the selected role

    $result = registerUser($name, $email, $password, $role, $phone, $address, $company);

    if ($result === true) {
        $success = "Registration successful! Please <a href='login.php' class='text-blue-600 hover:underline'>login</a> to continue.";
    } else {
        $errors[] = $result;
    }
}

$title = "Register";
include 'includes/header.php';
?>

<!-- Registration Section -->
<section class="bg-gray-100 min-h-screen flex items-center justify-center py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
            <h2 class="text-3xl font-bold text-blue-600 text-center mb-6">Create an Account</h2>

            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 text-red-800 p-4 rounded-md mb-6">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100 text-green-800 p-4 rounded-md mb-6">
                    <?= $success ?>
                </div>
            <?php else: ?>
                <form method="POST" action="register.php" class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-gray-700 font-semibold mb-2">Full Name</label>
                        <input type="text" id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" class="w-full p-3 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    </div>

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

                    <!-- Role Selection -->
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Register As</label>
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="role" value="job_seeker" class="mr-2" required <?= isset($_POST['role']) && $_POST['role'] === 'job_seeker' ? 'checked' : '' ?>>
                                <span>Job Seeker</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="role" value="employer" class="mr-2" required <?= isset($_POST['role']) && $_POST['role'] === 'employer' ? 'checked' : '' ?>>
                                <span>Employer</span>
                            </label>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-gray-700 font-semibold mb-2">Phone Number (Optional)</label>
                        <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" class="w-full p-3 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-gray-700 font-semibold mb-2">Address (Optional)</label>
                        <input type="text" id="address" name="address" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>" class="w-full p-3 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Company (for Employers) -->
                    <div>
                        <label for="company" class="block text-gray-700 font-semibold mb-2">Company Name (Optional, for Employers)</label>
                        <input type="text" id="company" name="company" value="<?= htmlspecialchars($_POST['company'] ?? '') ?>" class="w-full p-3 rounded-md border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" class="w-full bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-user-plus mr-2"></i> Register
                        </button>
                    </div>
                </form>

                <p class="text-center text-gray-600 mt-6">
                    Already have an account? <a href="login.php" class="text-blue-600 hover:underline">Login here</a>
                </p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>