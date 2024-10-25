<?php
include '../database/db.php';
require '../authentication/authentication.php';
checkAuth('admin');

$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'registration_date';
$order_direction = isset($_GET['order_direction']) && $_GET['order_direction'] == 'asc' ? 'asc' : 'desc';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT er.*, e.nama AS event_name 
        FROM event_registrations er 
        JOIN events e ON er.event_id = e.id 
        WHERE er.username LIKE '%$search%' 
        OR e.nama LIKE '%$search%' 
        ORDER BY $order_by $order_direction";
$result = $conn->query($sql);

if ($result->num_rows === 0 && !empty($search)) {
    $_SESSION['no_results'] = "Tidak ada hasil ditemukan untuk '$search'";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Registrants</title>
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

    <div class="container mx-auto mt-8 px-4">
        <h2 class="text-2xl font-bold text-center mb-6">Daftar Registran Event</h2>

        <?php if (isset($_SESSION['no_results'])): ?>
            <div class="fixed bottom-4 right-4 bg-red-500 text-white px-4 py-3 rounded shadow-md flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span><?= $_SESSION['no_results'];
                        unset($_SESSION['no_results']); ?></span>
            </div>
        <?php endif; ?>

        <div class="text-right mb-4">
            <a href="../reports/download_all_data.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Download Semua Data Registran
            </a>
        </div>

        <form method="GET" action="" class="mb-4 flex flex-col md:flex-row md:items-center">
            <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari Username atau Event" class="border px-4 py-2 rounded-lg mb-2 md:mb-0 md:mr-2 flex-1">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Cari</button>
        </form>

        <div class="bg-white shadow-md rounded-lg p-6 overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="px-4 py-2">
                            <a href="?order_by=user_id&order_direction=<?php echo $order_direction == 'asc' ? 'desc' : 'asc'; ?>">
                                User ID <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th class="px-4 py-2">
                            <a href="?order_by=username&order_direction=<?php echo $order_direction == 'asc' ? 'desc' : 'asc'; ?>">
                                Username <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th class="px-4 py-2">
                            <a href="?order_by=email_user&order_direction=<?php echo $order_direction == 'asc' ? 'desc' : 'asc'; ?>">
                                Email <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th class="px-4 py-2">
                            <a href="?order_by=event_id&order_direction=<?php echo $order_direction == 'asc' ? 'desc' : 'asc'; ?>">
                                Event ID <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th class="px-4 py-2">
                            <a href="?order_by=event_name&order_direction=<?php echo $order_direction == 'asc' ? 'desc' : 'asc'; ?>">
                                Nama Event <i class="fas fa-sort"></i>
                            </a>
                        </th>
                        <th class="px-4 py-2">
                            <a href="?order_by=registration_date&order_direction=<?php echo $order_direction == 'asc' ? 'desc' : 'asc'; ?>">
                                Tanggal Registrasi <i class="fas fa-sort"></i>
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-100">
                            <td class="border px-4 py-2"><?= htmlspecialchars($row['user_id']); ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($row['username']); ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($row['email_user']); ?></td> <!-- Ensure email_user is displayed -->
                            <td class="border px-4 py-2"><?= htmlspecialchars($row['event_id']); ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($row['event_name']); ?></td>
                            <td class="border px-4 py-2"><?= htmlspecialchars($row['registration_date']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <script>
            function toggleDropdown() {
                const dropdown = document.getElementById('profileDropdown');
                dropdown.classList.toggle('hidden');
            }

            
        </script>
</body>

</html>