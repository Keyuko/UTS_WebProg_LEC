<?php
include '../database/db.php';
require '../authentication/authentication.php';
checkAuth('user');
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    $sql = "
        SELECT events.nama, events.tanggal, events.id, events.banner, events.deskripsi, events.waktu, events.lokasi, events.max_partisipan,
               (SELECT COUNT(*) FROM event_registrations WHERE event_id = events.id) AS registrations_count 
        FROM event_registrations 
        JOIN events ON event_registrations.event_id = events.id 
        WHERE event_registrations.user_id = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $user_sql = "SELECT username FROM login_user WHERE id = ?";
    $user_stmt = $conn->prepare($user_sql);
    $user_stmt->bind_param('i', $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();
    $user = $user_result->fetch_assoc();
} else {
    echo "User not selected.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Event History</title>
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
                <a href="user_dashboard.php" class="text-white mx-4 hover:text-gray-400">Home</a>
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

    <div class="container mx-auto mt-8">
        <h1 class="text-2xl font-bold text-center mb-6"><?= htmlspecialchars($user['username']) ?>'s Event History</h1>
        <div class="bg-white shadow-md rounded-lg p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($event = $result->fetch_assoc()): ?>
                        <div class="relative bg-cover bg-center rounded-lg shadow-lg transform transition duration-300 hover:scale-105 h-64 md:h-80 lg:h-96"
                            style="background-image: url('../uploads/<?= htmlspecialchars($event['banner']) ?>');">
                            <div class="bg-black bg-opacity-50 p-4 flex flex-col justify-end h-full">
                                <h3 class="text-white text-lg md:text-xl lg:text-2xl font-bold"><?= htmlspecialchars($event['nama']) ?></h3>
                                <p class="text-white text-sm"><?= htmlspecialchars($event['registrations_count']) ?> / <?= htmlspecialchars($event['max_partisipan']) ?> participants</p>
                            </div>
                            <div class="absolute inset-0" data-bs-toggle="modal" data-bs-target="#eventModal-<?= htmlspecialchars($event['id']) ?>"></div>
                        </div>

                        <!-- Modal for Event Details -->
                        <div class="modal fade" id="eventModal-<?= htmlspecialchars($event['id']) ?>" tabindex="-1" aria-labelledby="eventModalLabel-<?= htmlspecialchars($event['id']) ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title event-name font-bold" id="eventModalLabel-<?= htmlspecialchars($event['id']) ?>"><?= htmlspecialchars($event['nama']) ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p><strong>Date:</strong> <?= htmlspecialchars($event['tanggal']) ?></p>
                                        <p><strong>Time:</strong> <?= htmlspecialchars($event['waktu']) ?></p>
                                        <p><strong>Location:</strong> <?= htmlspecialchars($event['lokasi']) ?></p>
                                        <p><strong>Description:</strong> <?= htmlspecialchars($event['deskripsi']) ?></p>
                                        <p><strong>Registered Participants:</strong> <?= htmlspecialchars($event['registrations_count']) ?> / <?= htmlspecialchars($event['max_partisipan']) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-center text-gray-500">Kamu belum daftar event!</p>
                <?php endif; ?>
            </div>
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