<?php
include '../database/db.php';
require '../authentication/authentication.php';
checkAuth('admin');

$id = $_GET['id'];

$sql = "SELECT * FROM events WHERE id = $id";
$result = $conn->query($sql);
$event = $result->fetch_assoc();

if (isset($_POST['update'])) {
    $nama = $_POST['nama'];
    $tanggal = $_POST['tanggal'];
    $waktu = $_POST['waktu'];
    $lokasi = $_POST['lokasi'];
    $deskripsi = $_POST['deskripsi'];
    $max_partisipan = $_POST['max_partisipan'];
    $status = $_POST['status'];

    if (!empty($_FILES['banner']['name'])) {
        $target_dir = "../uploads/";

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $banner = $target_dir . basename($_FILES['banner']['name']);
        move_uploaded_file($_FILES['banner']['tmp_name'], $banner);

        $sql = "UPDATE events SET nama='$nama', tanggal='$tanggal', waktu='$waktu', lokasi='$lokasi', deskripsi='$deskripsi', max_partisipan='$max_partisipan', banner='$banner', status='$status' WHERE id=$id";
    } else {
        $sql = "UPDATE events SET nama='$nama', tanggal='$tanggal', waktu='$waktu', lokasi='$lokasi', deskripsi='$deskripsi', max_partisipan='$max_partisipan', status='$status' WHERE id=$id";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: ../admin/admin_dashboard.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-8">
        <h1 class="text-2xl font-bold mb-6">Edit Event</h1>
        <form action="" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md">
            <div class="mb-4">
                <label for="nama" class="block text-sm font-medium text-gray-700">Nama Event</label>
                <input type="text" name="nama" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" value="<?= htmlspecialchars($event['nama']) ?>">
            </div>
            <div class="mb-4">
                <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal</label>
                <input type="date" name="tanggal" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" value="<?= htmlspecialchars($event['tanggal']) ?>">
            </div>
            <div class="mb-4">
                <label for="waktu" class="block text-sm font-medium text-gray-700">Waktu</label>
                <input type="time" name="waktu" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" value="<?= htmlspecialchars($event['waktu']) ?>">
            </div>
            <div class="mb-4">
                <label for="lokasi" class="block text-sm font-medium text-gray-700">Lokasi</label>
                <input type="text" name="lokasi" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" value="<?= htmlspecialchars($event['lokasi']) ?>">
            </div>
            <div class="mb-4">
                <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea name="deskripsi" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2"><?= htmlspecialchars($event['deskripsi']) ?></textarea>
            </div>
            <div class="mb-4">
                <label for="partisipan" class="block text-sm font-medium text-gray-700">Partisipan (Tergabung: <?= htmlspecialchars($event['partisipan_terdaftar']) ?>/<?= htmlspecialchars($event['max_partisipan']) ?>)</label>
                <input type="number" name="max_partisipan" min="0" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2" value="<?= htmlspecialchars($event['max_partisipan']) ?>">
            </div>
            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700">Status Event</label>
                <select name="status" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                    <option value="open" <?= ($event['status'] == 'open') ? 'selected' : '' ?>>Open <i class="fas fa-door-open"></i></option>
                    <option value="closed" <?= ($event['status'] == 'closed') ? 'selected' : '' ?>>Closed <i class="fas fa-door-closed"></i></option>
                    <option value="canceled" <?= ($event['status'] == 'canceled') ? 'selected' : '' ?>>Canceled <i class="fas fa-times-circle"></i></option>
                </select>
            </div>
            <div class="mb-4">
                <label for="banner" class="block text-sm font-medium text-gray-700">Banner Event</label>
                <input type="file" name="banner" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
            </div>
            <button type="submit" name="update" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600">Update</button>
        </form>
    </div>
</body>

</html>