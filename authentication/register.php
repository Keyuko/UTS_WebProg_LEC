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

function registerAdmin($pdo, $nama, $password, $secret_key, $email, $birthdate)
{
    if (!isValidSecretKey($pdo, $secret_key)) {
        return "Invalid secret key. You cannot register as an admin.";
    }

    $stmt = $pdo->prepare("SELECT * FROM login_admin WHERE nama = :nama OR email_admin = :email");
    $stmt->bindParam(':nama', $nama);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $existingAdmin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingAdmin) {
        return "Admin with this name or email already exists.";
    }

    $hashedPassword = bin2hex(hash('sha256', $password, true));

    $stmt = $pdo->prepare("INSERT INTO login_admin (nama, password, email_admin, birthdate_admin) VALUES (:nama, :password, :email, :birthdate)");
    $stmt->bindParam(':nama', $nama);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':birthdate', $birthdate);

    if ($stmt->execute()) {
        return "Admin registration successful. Welcome, $nama!";
    } else {
        return "Failed to register. Please try again.";
    }
}

function isValidSecretKey($pdo, $secret_key)
{
    $stmt = $pdo->prepare("SELECT * FROM secret_keys WHERE key_value = :secret_key");
    $stmt->bindParam(':secret_key', $secret_key);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function registerUser($pdo, $username, $password, $email, $birthdate)
{
    $stmt = $pdo->prepare("SELECT * FROM login_user WHERE username = :username OR email_user = :email");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM login_admin WHERE nama = :username OR email_admin = :email");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $existingAdmin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existingUser || $existingAdmin) {
        return "Username or email is already taken. Please choose another one.";
    }

    $hashedPassword = bin2hex(hash('sha256', $password, true));

    $stmt = $pdo->prepare("INSERT INTO login_user (username, password, email_user, birthdate_user) VALUES (:username, :password, :email, :birthdate)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':birthdate', $birthdate);

    if ($stmt->execute()) {
        return "User registration successful. Welcome, $username!";
    } else {
        return "Failed to register. Please try again.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['register_type']) && $_POST['register_type'] == 'admin') {
        $nama = $_POST['name'];
        $password = $_POST['password'];
        $secret_key = $_POST['secret_key'];
        $email = $_POST['email'];
        $birthdate = $_POST['birthdate'];
        $result = registerAdmin($pdo, $nama, $password, $secret_key, $email, $birthdate);

        if (strpos($result, 'successful') !== false) {
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_type'] = 'admin';
            $_SESSION['nama'] = $nama;
            $_SESSION['email_user'] = $email;
            header('Location: login.php');
            exit;
        }
    } else {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $birthdate = $_POST['birthdate'];
        $result = registerUser($pdo, $username, $password, $email, $birthdate);

        if (strpos($result, 'successful') !== false) {
            $stmt = $pdo->prepare("SELECT * FROM login_user WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = 'user';
            $_SESSION['username'] = $username;
            $_SESSION['email_user'] = $user['email_user'];
            header('Location: login.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.0/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 flex justify-center items-center min-h-screen py-12 px-4">

    <div class="bg-white shadow-md rounded-lg p-8 max-w-lg w-full space-y-6">
        <h1 class="text-2xl font-bold text-center mb-4">Register</h1>
        <div class="text-center mb-4 space-x-4">
            <button onclick="toggleForms('admin')" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring">Register as Admin</button>
            <button onclick="toggleForms('user')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:outline-none focus:ring">Register as User</button>
        </div>

        <!-- Admin Registration Form -->
        <div id="adminForm" class="space-y-4 hidden">
            <form action="register.php" method="post">
                <input type="hidden" name="register_type" value="admin" />
                <div>
                    <label for="adminName" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="adminName" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required />
                </div>
                <div>
                    <label for="adminEmail" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="adminEmail" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required />
                </div>
                <div>
                    <label for="adminPassword" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="adminPassword" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required />
                </div>
                <div>
                    <label for="adminBirthdate" class="block text-sm font-medium text-gray-700">Birthdate</label>
                    <input type="date" name="birthdate" id="adminBirthdate" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 mb-4" required />
                </div>
                <div>
                    <label for="adminSecretKey" class="block text-sm font-medium text-gray-700">Secret Key</label>
                    <input type="text" name="secret_key" id="adminSecretKey" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required />
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 mt-4">Register Admin</button>
            </form>
        </div>

        <div id="userForm" class="space-y-4 hidden">
            <form action="register.php" method="post">
                <input type="hidden" name="register_type" value="user" />
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" name="username" id="username" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required />
                </div>
                <div>
                    <label for="userEmail" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" id="userEmail" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required />
                </div>
                <div>
                    <label for="userPassword" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="userPassword" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required />
                </div>
                <div>
                    <label for="userBirthdate" class="block text-sm font-medium text-gray-700">Birthdate</label>
                    <input type="date" name="birthdate" id="userBirthdate" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 mb-4" required />
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 mt-4">Register User</button>
            </form>
        </div>
    </div>
</body>
<script>
    function toggleForms(formType) {
        document.getElementById('adminForm').classList.toggle('hidden', formType !== 'admin');
        document.getElementById('userForm').classList.toggle('hidden', formType !== 'user');
    }

    window.onload = function() {
        toggleForms('user');
    };
</script>

</html>