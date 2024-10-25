<?php
session_start(); 

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

function verifyAdmin($pdo, $nama, $password) {
    $stmt = $pdo->prepare("SELECT * FROM login_admin WHERE nama = :nama");
    $stmt->bindParam(':nama', $nama);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        $hashedPassword = bin2hex(hash('sha256', $password, true)); 
        if ($hashedPassword === $admin['password']) {
            return $admin; 
        }
    }
    return false; 
}

function verifyUser($pdo, $username, $password) {
    $stmt = $pdo->prepare("SELECT * FROM login_user WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $hashedPassword = bin2hex(hash('sha256', $password, true)); 
        if ($hashedPassword === $user['password']) {
            return $user; 
        }
    }
    return false; 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username_or_name = $_POST['username_or_name'];
    $password = $_POST['password'];
    $admin = verifyAdmin($pdo, $username_or_name, $password);
    $user = verifyUser($pdo, $username_or_name, $password);

    if ($admin) {
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['user_type'] = 'admin'; 
        $_SESSION['nama'] = $admin['nama'];
        $_SESSION['email_admin'] = $admin['email_user']; 
        header('Location: ../admin/admin_dashboard.php'); 
        exit;
    } elseif ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_type'] = 'user'; 
        $_SESSION['username'] = $user['username'];
        $_SESSION['email_user'] = $user['email_user']; 
        header('Location: ../users/user_dashboard.php'); 
        exit;
    } else {
        $_SESSION['login_error'] = "Invalid username or password. Please try again.";
        header(header: 'Location: login.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.0/dist/tailwind.min.css" rel="stylesheet">

</head>
<body class="bg-gray-100 flex justify-center items-center min-h-screen">

    <div class="bg-white shadow-md rounded-lg p-8 max-w-md w-full">
        <h1 class="text-2xl font-bold text-center mb-6">Login</h1>
        <div class="fixed bottom-4 right-4 z-50">
            <?php if (isset($_SESSION['login_error'])): ?>
                <div class="bg-red-500 text-white px-4 py-3 rounded shadow-md flex items-center mb-2">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span><?= $_SESSION['login_error']; unset($_SESSION['login_error']); ?></span>
                </div>
            <?php endif; ?>
        </div>
        <form action="login.php" method="post" class="space-y-4">
            <div>
                <label for="username_or_name" class="block text-sm font-medium text-gray-700">Username or Admin Name</label>
                <input type="text" name="username_or_name" id="username_or_name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter your username" required />
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" id="password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter your password" required />
            </div>

            <div class="flex justify-between items-center">
                <a href="register.php" class="text-sm text-indigo-600 hover:text-indigo-500">New user? Register here</a>
                <a href="forget_password.php" class="text-sm text-indigo-600 hover:text-indigo-500">Forget Password?</a>
                <button type="submit" class="bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-500">Login</button>
            </div>
        </form>

        <?php if (isset($message)) echo "<p class='mt-4 text-red-500 text-center'>$message</p>"; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.0/dist/tailwind.min.js"></script> 
</body>
</html>
