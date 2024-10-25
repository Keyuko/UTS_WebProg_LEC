<?php
include '../database/db.php'; 
require '../authentication/authentication.php';
checkAuth('user');

$user_id = $_SESSION['user_id'];
$sql = "
    SELECT id AS user_id, username, email_user 
    FROM login_user 
    WHERE id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); 
$stmt->execute();
$stmt->bind_result($user_id, $username, $email_user);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Events</title>
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
    <div class="container mx-auto my-10">
        <div class="bg-white p-8 rounded-lg shadow-lg">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Profil Pengguna</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-lg"><strong>ID User:</strong> <?php echo htmlspecialchars($user_id); ?></p>
                    <p class="text-lg"><strong>Username:</strong> <?php echo htmlspecialchars($username); ?></p>
                    <p class="text-lg"><strong>Email:</strong> <?php echo htmlspecialchars($email_user); ?></p>
                </div>
            </div>
            <div class="mt-6">
                <a href="edit_profile_user.php" class="inline-block bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-blue-600 transition duration-300">
                    Edit Profil
                </a>
                <a href="history.php?user_id=<?php echo $user_id; ?>" class="inline-block bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-green-600 transition duration-300">
                    History
                </a>
            </div>

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