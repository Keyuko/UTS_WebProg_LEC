<?php
include '../database/db.php';
require '../authentication/authentication.php';
checkAuth('admin');

$id = $_GET['id'];

$sql = "DELETE FROM events WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    // Redirect with success status
    header("Location: ../admin/admin_dashboard.php?status=event_deleted");
    exit();
} else {
    echo "Error deleting record: " . $conn->error;
}

$conn->close();
?>
