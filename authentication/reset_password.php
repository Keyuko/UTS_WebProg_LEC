<?php
require 'authentication.php';

$host = 'localhost';
$dbname = 'event_management';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error connecting to database: " . $e->getMessage();
    exit();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: forget_password.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT id, email_user AS email, 'user' AS role FROM login_user WHERE id = :id
    UNION ALL
    SELECT id, email_admin AS email, 'admin' AS role FROM login_admin WHERE id = :id
");
$stmt->bindParam(':id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit();
}

$user_email = $user['email'];
$role = $user['role'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error = "Passwords do not match. Please try again.";
    } else {
        $hashedPassword = bin2hex(hash('sha256', $new_password, true));

        if ($role === 'user') {
            $stmt = $pdo->prepare("UPDATE login_user SET password = :password WHERE id = :id");
        } else {
            $stmt = $pdo->prepare("UPDATE login_admin SET password = :password WHERE id = :id");
        }
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $user_id);

        if ($stmt->execute()) {
            $success = "Password reset successful. You can now log in.";
            session_destroy();
        } else {
            $error = "Failed to reset password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.0/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 flex justify-center items-center min-h-screen">
    <div class="bg-white shadow-md rounded-lg p-8 max-w-lg w-full">
        <h1 class="text-2xl font-bold text-center mb-6">Reset Password</h1>
        <?php if (isset($error)): ?>
            <p class="text-red-500 text-center mb-4"><?php echo $error; ?></p>
            <a href="forget_password.php" class="text-blue-600 hover:text-blue-500">Back to forgot password</a>
        <?php elseif (isset($success)): ?>
            <p class="text-green-500 text-center mb-4"><?php echo $success; ?></p>
            <a href="login.php" class="text-blue-600 hover:text-blue-500">Login here</a>
        <?php else: ?>
            <form action="reset_password.php" method="post" class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user_email); ?>" readonly class="border border-gray-300 rounded-lg p-2 w-full bg-gray-200 cursor-not-allowed" />
                </div>
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                    <input type="password" name="new_password" id="new_password" required class="border border-gray-300 rounded-lg p-2 w-full" />
                </div>
                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" required class="border border-gray-300 rounded-lg p-2 w-full" />
                </div>
                <div class="mt-6">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Reset Password</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>
