<?php
include '../database/db.php';
require '../authentication/authentication.php';
checkAuth('admin');

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    // Start transaction
    $conn->begin_transaction();

    try {
        // Prepare statement to delete from login_user
        $sqlUser = "DELETE FROM login_user WHERE id = ?";
        $stmtUser = $conn->prepare($sqlUser);
        $stmtUser->bind_param("i", $id);
        $stmtUser->execute();

        // Prepare statement to delete from user_registrations
        $sqlRegistration = "DELETE FROM user_registrations WHERE user_id = ?";
        $stmtRegistration = $conn->prepare($sqlRegistration);
        $stmtRegistration->bind_param("i", $id);
        $stmtRegistration->execute();

        // Commit transaction if both deletions were successful
        $conn->commit();
        header("Location: user_management.php?success=User deleted successfully");
        exit();
    } catch (Exception $e) {
        // Rollback transaction if there was an error
        $conn->rollback();
        echo "Error deleting user: " . $e->getMessage();
    } finally {
        // Close statements
        if (isset($stmtUser)) {
            $stmtUser->close();
        }
        if (isset($stmtRegistration)) {
            $stmtRegistration->close();
        }
    }
} else {
    echo "Invalid request";
}

$conn->close();
?>

