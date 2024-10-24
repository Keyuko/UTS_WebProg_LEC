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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $birthdate = $_POST['birthdate'];

    $stmt = $pdo->prepare("
        SELECT id, 'user' AS role FROM login_user WHERE email_user = :email AND birthdate_user = :birthdate
        UNION ALL
        SELECT id, 'admin' AS role FROM login_admin WHERE email_admin = :email AND birthdate_admin = :birthdate
    ");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':birthdate', $birthdate);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header('Location: reset_password.php');
        exit();
    } else {
        $error = "Email or birthdate is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.0/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">
    <div class="bg-white shadow-md rounded-lg p-8 max-w-lg w-full">
        <h1 class="text-2xl font-bold text-center mb-6">Forget Password</h1>
        <?php if (isset($error)): ?>
            <p class="text-red-500 text-center mb-4"><?php echo $error; ?></p>
        <?php endif; ?>
        <form action="forget_password.php" method="post" class="space-y-4">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" id="email" required class="border border-gray-300 rounded-lg p-2 w-full" />
            </div>
            <div>
                <label for="birthdate" class="block text-sm font-medium text-gray-700">Birthdate</label>
                <input type="date" name="birthdate" id="birthdate" required class="border border-gray-300 rounded-lg p-2 w-full" />
            </div>
            <div class="mt-6">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Verify</button>
            </div>
        </form>
        <div class="text-center mt-6">
            <p class="text-sm">Remember your password? <a href="login.php" class="text-indigo-600 hover:text-indigo-500">Login here</a></p>
        </div>
    </div>
</body>
</html>

