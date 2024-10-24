<?php
include '../database/db.php'; 
require '../authentication/authentication.php'; 
checkAuth('admin');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_nama = $_POST['nama'];
    $new_email_admin = $_POST['email_admin'];

    if (!empty($_POST['password'])) {
        $new_password = $_POST['password'];
        $hashedPassword = bin2hex(hash('sha256', $new_password, true));
    }

    if (!empty($new_password)) {
        $sql = "UPDATE login_admin SET nama = ?, email_admin = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $new_nama, $new_email_admin, $hashedPassword, $user_id);
    } else {
        $sql = "UPDATE login_admin SET nama = ?, email_admin = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $new_nama, $new_email_admin, $user_id);
    }

    if ($stmt === false) {
        die("Error preparing the query: " . $conn->error);
    }

    $stmt->execute();
    $stmt->close();

    header("Location: view_profile_admin.php");
    exit;
}

$sql = "SELECT nama, email_admin FROM login_admin WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing the query: " . $conn->error);
}

$stmt->bind_param("i", $user_id); 
$stmt->execute();
$stmt->bind_result($nama, $email_admin);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.0/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body class="bg-gray-100">
<nav class="bg-gray-800">
    <div class="container mx-auto flex justify-between items-center py-4">
        <div class="flex items-center">
            <a href="admin_dashboard.php">
                <img src="../assets/logobagusbanget.png" alt="Logo" class="w-10 h-10 mr-3">
            </a>
            <a href="view_registrants.php" class="text-white mx-4 hover:text-gray-400">View Registrants</a>
            <a href="user_management.php" class="text-white mx-4 hover:text-gray-400">User Management</a>
        </div>

        <!-- Profile Dropdown -->
        <div class="relative inline-block text-left">
            <button onclick="toggleDropdown()" class="text-white focus:outline-none">
                <i class="fas fa-user-secret"></i>
            </button>
            <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-20">
                <a href="view_profile_admin.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-user"></i> View Profile
                </a>
                <a href="edit_profile_admin.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-edit"></i> Edit Profile
                </a>
                <a href="../authentication/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-sign-out-alt"></i> Log Out
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="container mx-auto my-10">
    <div class="bg-white p-8 rounded-lg shadow-lg">
        <h1 class="text-2xl font-bold text-gray-800 mb-4">Edit Profil Admin</h1>
        <form method="POST" action="">
            <div class="mb-4">
                <label for="nama" class="block text-lg font-semibold text-gray-700">Nama:</label>
                <input type="text" id="nama" name="nama" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo htmlspecialchars($nama); ?>" required>
            </div>
            <div class="mb-4">
                <label for="email_admin" class="block text-lg font-semibold text-gray-700">Email:</label>
                <input type="email" id="email_admin" name="email_admin" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo htmlspecialchars($email_admin); ?>" required>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-lg font-semibold text-gray-700">New Password (Optional):</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Leave blank if not changing">
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-blue-600 transition duration-300">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
<script>
    function toggleDropdown() {
        var dropdown = document.getElementById("profileDropdown");
        dropdown.classList.toggle("hidden");
    }

    window.onclick = function(event) {
        if (!event.target.matches('.fas')) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (!openDropdown.classList.contains('hidden')) {
                    openDropdown.classList.add('hidden');
                }
            }
        }
    };
</script>
</body>
</html>
