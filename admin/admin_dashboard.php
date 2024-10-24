<?php
include '../database/db.php'; 
require '../authentication/authentication.php'; 
checkAuth('admin');

$sql = "
    SELECT e.*, COUNT(er.user_id) AS total_registrants 
    FROM events e 
    LEFT JOIN event_registrations er ON e.id = er.event_id 
    GROUP BY e.id
";
$result = $conn->query($sql);
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

<div class="container mx-auto my-8">
    <div class="flex justify-end mb-4">
        <a href="../events/add_event.php" class="bg-blue-600 text-white px-6 py-2 rounded-md uppercase text-sm font-semibold hover:bg-blue-700 transition-all">
            <i class="fa fa-calendar-plus mr-2"></i>Tambah Event
        </a>
    </div>

    <div class="container mx-auto mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($event = $result->fetch_assoc()): ?>
                <div class="relative bg-cover bg-center rounded-lg shadow-lg transform transition duration-300 hover:scale-105 h-64 md:h-80 lg:h-96" style="background-image: url('<?= htmlspecialchars($event['banner']) ?>');">
                    <div class="bg-black bg-opacity-50 p-4 flex flex-col justify-end h-full">
                        <h3 class="text-white text-lg md:text-xl lg:text-2xl font-bold"><?= htmlspecialchars($event['nama']) ?></h3>
                        <p class="text-white text-sm"><?= htmlspecialchars($event['total_registrants']) ?> / <?= htmlspecialchars($event['max_partisipan']) ?> partisipan</p>
                        <div class="mt-2">
                            <?php if ($event['status'] == 'open'): ?>
                                <span class="text-green-400"><i class="fas fa-door-open"></i> Open</span>
                            <?php elseif ($event['status'] == 'closed'): ?>
                                <span class="text-red-400"><i class="fas fa-door-closed"></i> Closed</span>
                            <?php elseif ($event['status'] == 'canceled'): ?>
                                <span class="text-yellow-400"><i class="fas fa-times-circle"></i> Canceled</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="absolute inset-0" data-bs-toggle="modal" data-bs-target="#eventModal-<?= htmlspecialchars($event['id']) ?>"></div>
                </div>

                <div class="modal fade" id="eventModal-<?= htmlspecialchars($event['id']) ?>" tabindex="-1" aria-labelledby="eventModalLabel-<?= htmlspecialchars($event['id']) ?>" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered w-full max-w-screen-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title font-bold" id="eventModalLabel-<?= htmlspecialchars($event['id']) ?>"><?= htmlspecialchars($event['nama']) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Tanggal:</strong> <?= htmlspecialchars($event['tanggal']) ?></p>
                                <p><strong>Waktu:</strong> <?= htmlspecialchars($event['waktu']) ?></p>
                                <p><strong>Lokasi:</strong> <?= htmlspecialchars($event['lokasi']) ?></p>
                                <p><strong>Deskripsi:</strong> <?= htmlspecialchars($event['deskripsi']) ?></p>
                                <p><strong>Partisipan Terdaftar:</strong> <?= htmlspecialchars($event['total_registrants']) ?> / <?= htmlspecialchars($event['max_partisipan']) ?></p>
                                <p><strong>Event ID:</strong> <?= htmlspecialchars($event['id']) ?></p>
                            </div>
                            <div class="modal-footer">
                                <a href="../events/edit_event.php?id=<?= htmlspecialchars($event['id']) ?>" class="bg-yellow-400 text-white px-4 py-2 rounded hover:bg-yellow-500">Edit</a>
                                <button class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal-<?= htmlspecialchars($event['id']) ?>">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="confirmDeleteModal-<?= htmlspecialchars($event['id']) ?>" tabindex="-1" aria-labelledby="confirmDeleteModalLabel-<?= htmlspecialchars($event['id']) ?>" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmDeleteModalLabel-<?= htmlspecialchars($event['id']) ?>">Konfirmasi Hapus</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Anda yakin ingin menghapus event ini?</p>
                            </div>
                            <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <a href="../events/delete_event.php?id=<?= htmlspecialchars($event['id']) ?>" class="btn btn-danger">Hapus</a>
                                </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="text-center col-span-3 text-gray-500">Tidak ada event yang tersedia.</div>
        <?php endif; ?>
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
