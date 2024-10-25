<?php
include '../database/db.php';
require '../authentication/authentication.php';
checkAuth('admin');

// Check if form is submitted before any HTML output
if (isset($_POST['submit'])) {
    $nama = $_POST['nama'];
    $tanggal = $_POST['tanggal'];
    $waktu = $_POST['waktu'];
    $lokasi = $_POST['lokasi'];
    $deskripsi = $_POST['deskripsi'];
    $max_partisipan = $_POST['max_partisipan'];

    $banner = '';
    if (!empty($_FILES['banner']['name'])) {
        $target_dir = "../uploads/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $banner = $target_dir . basename($_FILES['banner']['name']);
        $imageFileType = strtolower(pathinfo($banner, PATHINFO_EXTENSION));

        if (getimagesize($_FILES['banner']['tmp_name']) === false || $_FILES['banner']['size'] > 2000000 || !in_array($imageFileType, ['jpg', 'png', 'jpeg'])) {
            echo "<div class='alert alert-danger' role='alert'>File tidak valid!</div>";
            exit();
        }

        if (!move_uploaded_file($_FILES['banner']['tmp_name'], $banner)) {
            echo "<div class='alert alert-danger' role='alert'>Terjadi kesalahan saat mengunggah file.</div>";
            exit();
        }
    }

    $sql = "INSERT INTO events (nama, tanggal, waktu, lokasi, deskripsi, max_partisipan, banner) 
            VALUES ('$nama', '$tanggal', '$waktu', '$lokasi', '$deskripsi', '$max_partisipan', '$banner')";

    if ($conn->query($sql) === TRUE) {
        // Redirect with success status
        header("Location: ../admin/admin_dashboard.php?status=event_added");
        exit();
    } else {
        echo "<div class='alert alert-danger' role='alert'>Error: " . $sql . "<br>" . $conn->error . "</div>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Event Baru</title>
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.0.0/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <!-- Form Section -->
<div class="container mx-auto mt-10">
    <div class="flex justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-lg">
            <h2 class="text-2xl font-bold text-center text-blue-600 mb-6">
                <i class="fa fa-calendar-plus"></i> Tambah Event Baru
            </h2>
            <form action="add_event.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                <!-- Back Button -->
                <div>
                    <a href="../admin/admin_dashboard.php" class="text-white bg-blue-500 py-2 px-4 rounded-lg hover:bg-700">
                        Back
                    </a>
                </div>
                <div>
                    <label for="nama" class="block text-sm font-medium text-gray-700">Nama Event:</label>
                    <input type="text" id="nama" name="nama" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required placeholder="Masukkan nama event">
                </div>
                <div>
                    <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal:</label>
                    <input type="date" id="tanggal" name="tanggal" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="waktu" class="block text-sm font-medium text-gray-700">Waktu:</label>
                    <input type="time" id="waktu" name="waktu" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required>
                </div>
                <div>
                    <label for="lokasi" class="block text-sm font-medium text-gray-700">Lokasi:</label>
                    <input type="text" id="lokasi" name="lokasi" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required placeholder="Masukkan lokasi event">
                </div>
                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi:</label>
                    <textarea id="deskripsi" name="deskripsi" rows="4" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required placeholder="Deskripsikan event"></textarea>
                </div>
                <div>
                    <label for="max_partisipan" class="block text-sm font-medium text-gray-700">Jumlah Maksimum Partisipan:</label>
                    <input type="number" id="max_partisipan" min="0" name="max_partisipan" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" required placeholder="Masukkan jumlah maksimum partisipan">
                </div>
                <div>
                    <label for="banner" class="block text-sm font-medium text-gray-700">Banner Event (Max:2000000 bytes):</label>
                    <input type="file" id="banner" name="banner" class="mt-1 block w-full px-3 py-2 text-sm border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button type="submit" name="submit" class="bg-blue-600 text-white py-2 px-4 rounded-lg w-full hover:bg-blue-700">Submit</button>
            </form>
        </div>
    </div>
</div>


</body>
</html>
