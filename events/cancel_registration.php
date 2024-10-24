<?php
include '../database/db.php';
require '../authentication/authentication.php';
checkAuth('user');

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $event_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("DELETE FROM event_registrations WHERE event_id = ? AND user_id = ?");
    $stmt->bind_param('ii', $event_id, $user_id);

    if ($stmt->execute()) {
        header('Location: ../users/my_event.php');
        exit();
    } else {
        echo "Failed to cancel registration.";
    }
}