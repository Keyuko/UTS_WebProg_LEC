<?php
include '../database/db.php'; 
require '../authentication/authentication.php'; 

checkAuth('admin');

$sql = "SELECT id, username FROM login_user";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.0/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>

<body class="bg-gray-100 font-sans">
    <nav class="bg-gray-800">
        <div class="container mx-auto flex justify-between items-center py-4">
            <div class="flex items-center">
                <a href="admin_dashboard.php">
                    <img src="../assets/logobagusbanget.png" alt="Logo" class="w-10 h-10 mr-3">
                </a>
                <a href="view_registrants.php" class="text-white mx-4 hover:text-gray-400">View Registrants</a>
                <a href="user_management.php" class="text-white mx-4 hover:text-gray-400">User Management</a>
            </div>
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

    <div class="container mx-auto mt-8">
        <h1 class="text-2xl font-bold text-center mb-6">User Management</h1>
        <div class="bg-white shadow-md rounded-lg p-6 max-w-lg mx-auto">
            <?php if ($result->num_rows > 0): ?>
                <table class="table-auto w-full">
                    <thead>
                        <tr>
                            <th class="px-4 py-2">Username</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="border px-4 py-2"><?= htmlspecialchars($row['username']) ?></td>
                                <td class="border px-4 py-2">
                                    <a href="view_user_events.php?user_id=<?= htmlspecialchars($row['id']) ?>" class="bg-blue-500 text-white px-4 py-1 rounded">View Events</a>
                                    <button class="bg-red-600 text-white px-4 py-1 rounded" onclick="openDeleteConfirmationModal(<?= htmlspecialchars($row['id']) ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No users found.</p>
            <?php endif; ?>
        </div>
    </div>
    <div id="deleteConfirmationModal" class="fixed inset-0 flex items-center justify-center z-50 hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-1/3">
            <h3 class="text-lg font-bold mb-4">Konfirmasi Penghapusan</h3>
            <p>Apakah Anda yakin ingin menghapus pengguna ini?</p>
            <div class="flex justify-end mt-4">
                <button id="confirmDeleteBtn" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Ya, Hapus</button>
                <button onclick="toggleDeleteModal()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 ml-2">Batal</button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>

    <script>
        let deleteUserId = null; 

        function openDeleteConfirmationModal(userId) {
            deleteUserId = userId;
            document.getElementById("deleteConfirmationModal").classList.remove("hidden");
        }

        function toggleDeleteModal() {
            document.getElementById("deleteConfirmationModal").classList.add("hidden");
        }

        document.getElementById("confirmDeleteBtn").onclick = function() {
            if (deleteUserId) {
                window.location.href = `delete_user.php?id=${deleteUserId}`;
            }
        };

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