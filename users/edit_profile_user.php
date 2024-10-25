<?php
include '../database/db.php';
require '../authentication/authentication.php';
checkAuth('user');

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = $_POST['username'];
    $new_email = $_POST['email_user'];

    if (!empty($_POST['password'])) {
        $new_password = $_POST['password'];
        $hashedPassword = bin2hex(hash('sha256', $new_password, true));

        $sql = "UPDATE login_user SET username = ?, email_user = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $new_username, $new_email, $hashedPassword, $user_id);
    } else {
        $sql = "UPDATE login_user SET username = ?, email_user = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $new_username, $new_email, $user_id);
    }

    $stmt->execute();
    $stmt->close();

    $sql_update_registrations = "UPDATE event_registrations SET username = ?, email_user = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql_update_registrations);
    $stmt->bind_param("ssi", $new_username, $new_email, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: view_profile_user.php");
    exit;
}

$sql = "SELECT username, email_user FROM login_user WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email_user);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.0/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body class="bg-gray-100">
    <nav class="bg-gray-800">
        <div class="container mx-auto flex justify-between items-center py-4">
            <div class="flex items-center">
                <a href="user_dashboard.php">
                    <img src="../assets/logobagusbanget.png" alt="Logo" class="w-10 h-10 mr-3">
                </a>
                <a href="my_event.php" class="text-white mx-4 hover:text-gray-400">My Events</a>
            </div>

            <!-- Profile Dropdown -->
            <div class="relative inline-block text-left">
                <button onclick="toggleDropdown()" class="text-white focus:outline-none">
                    <i class="fas fa-user-secret"></i>
                </button>
                <div id="profileDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-20">
                    <a href="view_profile_user.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-user"></i> View Profile
                    </a>
                    <a href="edit_profile_user.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                    <a href="../authentication/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-sign-out-alt"></i> Log Out
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto my-10">
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Edit Profil Pengguna</h1>
            <form method="POST" action="">
                <div class="mb-6">
                    <label for="username" class="block text-lg font-medium text-gray-700">Username:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150 ease-in-out p-2">
                </div>

                <div class="mb-6">
                    <label for="email_user" class="block text-lg font-medium text-gray-700">Email:</label>
                    <input type="email" id="email_user" name="email_user" value="<?php echo htmlspecialchars($email_user); ?>" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150 ease-in-out p-2">
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-lg font-medium text-gray-700">New Password (Optional):</label>
                    <input type="password" id="password" name="password" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 transition duration-150 ease-in-out p-2" placeholder="Leave blank if not changing">
                </div>

                <button type="submit" class="w-full inline-block bg-blue-500 text-white px-4 py-3 rounded-lg shadow-lg hover:bg-blue-600 transition duration-300">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    <script>
        function toggleDropdown() {
            var dropdown = document.getElementById("profileDropdown");
            dropdown.classList.toggle("hidden");
        }
    </script>
</body>

</html>