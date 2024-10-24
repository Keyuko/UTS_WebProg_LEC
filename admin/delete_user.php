<?php
include '../database/db.php'; 
require '../authentication/authentication.php'; 
checkAuth('admin');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    $conn->begin_transaction();

    try {
        $sqlDeleteRegistrations = "DELETE FROM event_registrations WHERE user_id = ?";
        $stmtDeleteRegistrations = $conn->prepare($sqlDeleteRegistrations);
        $stmtDeleteRegistrations->bind_param("i", $id);
        $stmtDeleteRegistrations->execute();

        $sqlDeleteUser = "DELETE FROM login_user WHERE id = ?";
        $stmtDeleteUser = $conn->prepare($sqlDeleteUser);
        $stmtDeleteUser->bind_param("i", $id);

        if ($stmtDeleteUser->execute()) {
            $conn->commit();
            header("Location: user_management.php?success=User and their event registrations deleted successfully");
            exit();
        } else {
            throw new Exception("Error deleting user: " . $conn->error);
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request";
}
?>