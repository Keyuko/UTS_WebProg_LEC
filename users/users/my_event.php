<?php
include '../database/db.php';
require '../authentication/authentication.php';
checkAuth('user');

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT e.*, COUNT(er.user_id) AS registrations_count 
    FROM events e 
    JOIN event_registrations er ON e.id = er.event_id 
    WHERE er.user_id = ? 
    GROUP BY e.id
");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
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

    <h1 class="text-2xl font-bold text-center my-6">Registered Events</h1>

    <div class="container mx-auto mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($event = $result->fetch_assoc()): ?>
                <div class="relative bg-cover bg-center rounded-lg shadow-lg transform transition duration-300 hover:scale-105 h-64 md:h-80 lg:h-96"
                    style="background-image: url('../uploads/<?= htmlspecialchars($event['banner']) ?>');">

                    <div class="bg-black bg-opacity-50 p-4 flex flex-col justify-end h-full">
                        <h3 class="text-white text-lg md:text-xl lg:text-2xl font-bold"><?= htmlspecialchars($event['nama']) ?></h3>
                        <p class="text-white text-sm"><?= htmlspecialchars($event['registrations_count']) ?> / <?= htmlspecialchars($event['max_partisipan']) ?> partisipan</p>
                        <div class="mt-2">
                            <!-- Status Icon -->
                            <?php if ($event['status'] == 'open'): ?>
                                <span class="text-green-400"><i class="fas fa-door-open"></i> Open</span>
                            <?php elseif ($event['status'] == 'closed'): ?>
                                <span class="text-red-400"><i class="fas fa-door-closed"></i> Closed</span>
                            <?php elseif ($event['status'] == 'canceled'): ?>
                                <span class="text-yellow-400"><i class="fas fa-times-circle"></i> Canceled</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Modal Trigger -->
                    <div class="absolute inset-0" data-bs-toggle="modal" data-bs-target="#eventModal-<?= htmlspecialchars($event['id']) ?>" onclick="setCancelEventId(<?= htmlspecialchars($event['id']) ?>)"></div>
                </div>

                <!-- Modal for Event Details -->
                <div class="modal fade" id="eventModal-<?= htmlspecialchars($event['id']) ?>" tabindex="-1" aria-labelledby="eventModalLabel-<?= htmlspecialchars($event['id']) ?>" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered w-full max-w-screen-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title font-bold" id="eventModalLabel-<?= htmlspecialchars($event['id']) ?>"><?= htmlspecialchars($event['nama']) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="hideCancelConfirmationModal()"></button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Tanggal:</strong> <?= htmlspecialchars($event['tanggal']) ?></p>
                                <p><strong>Waktu:</strong> <?= htmlspecialchars($event['waktu']) ?></p>
                                <p><strong>Lokasi:</strong> <?= htmlspecialchars($event['lokasi']) ?></p>
                                <p><strong>Deskripsi:</strong> <?= htmlspecialchars($event['deskripsi']) ?></p>
                                <p><strong>Partisipan Terdaftar:</strong> <?= htmlspecialchars($event['registrations_count']) ?> / <?= htmlspecialchars($event['max_partisipan']) ?></p>
                            </div>
                            <div class="modal-footer">
                                <button class="bg-red-400 text-white px-4 py-2 rounded hover:bg-red-500" onclick="openCancelConfirmationModal()">Cancel Registration</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="text-center col-span-3 text-gray-500">Kamu belum daftar event!</div>
        <?php endif; ?>
    </div>

    <!-- Confirmation Modal -->
    <div id="cancelConfirmationModal" class="fixed inset-0 flex items-center justify-center z-50 hidden bg-black bg-opacity-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-1/3">
            <h3 class="text-lg font-bold mb-4">Konfirmasi Pembatalan</h3>
            <p>Apakah Anda yakin ingin membatalkan pendaftaran untuk event ini?</p>
            <div class="flex justify-end mt-4">
                <button id="confirmCancelBtn" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">Ya, Batalkan</button>
                <button id="cancelBtn" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 ml-2" onclick="toggleCancelModal()">Batal</button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script>
        let cancelEventId = null;

        function setCancelEventId(eventId) {
            cancelEventId = eventId;
        }

        function openCancelConfirmationModal() {
            const eventModal = document.querySelector(`#eventModal-${cancelEventId}`);
            const modal = bootstrap.Modal.getInstance(eventModal);
            if (modal) {
                modal.hide();
            }
            document.getElementById("cancelConfirmationModal").classList.remove("hidden");
        }

        function toggleCancelModal() {
            document.getElementById("cancelConfirmationModal").classList.add("hidden");
        }

        document.getElementById("confirmCancelBtn").onclick = function() {
            if (cancelEventId) {
                window.location.href = `../events/cancel_registration.php?id=${cancelEventId}`;
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