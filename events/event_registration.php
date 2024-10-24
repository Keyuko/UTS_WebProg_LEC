<?php
include '../database/db.php';
require '../authentication/authentication.php';
checkAuth('user');

if (isset($_POST['event_id'])) {
    $event_id = intval($_POST['event_id']);
    $user_id = $_SESSION['user_id'];
    $check_sql = "SELECT * FROM event_registrations WHERE user_id = ? AND event_id = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("ii", $user_id, $event_id);
    $stmt->execute();
    $check_result = $stmt->get_result();

    if ($check_result->num_rows > 0) {
        $_SESSION['registration_alert'] = "Anda sudah terdaftar dalam event ini!";
        header("Location: ../users/user_dashboard.php");
        exit();
    }

    $event_sql = "SELECT * FROM events WHERE id = ?";
    $event_stmt = $conn->prepare($event_sql);
    $event_stmt->bind_param("i", $event_id);
    $event_stmt->execute();
    $event_result = $event_stmt->get_result();

    if ($event_result->num_rows === 1) {
        $event = $event_result->fetch_assoc();

        if ($event['status'] == 'closed' || $event['status'] == 'canceled') {
            $_SESSION['registration_alert'] = "Registrasi tidak dapat dilakukan karena event ini sudah ditutup atau dibatalkan.";
            header("Location: ../users/user_dashboard.php");
            exit();
        }

        $count_sql = "SELECT COUNT(*) as participant_count FROM event_registrations WHERE event_id = ?";
        $count_stmt = $conn->prepare($count_sql);
        $count_stmt->bind_param("i", $event_id);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $count_row = $count_result->fetch_assoc();
        $participant_count = $count_row['participant_count'];

        $count_sql = "SELECT COUNT(*) as participant_count FROM event_registrations WHERE event_id = ?";
        $count_stmt = $conn->prepare($count_sql);
        $count_stmt->bind_param("i", $event_id);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $count_row = $count_result->fetch_assoc();
        $participant_count = $count_row['participant_count'];

        if ($participant_count >= $event['max_partisipan']) {
            $_SESSION['registration_alert'] = "Registrasi tidak dapat dilakukan karena jumlah maksimum partisipan telah tercapai.";
            header("Location: ../users/user_dashboard.php");
            exit();
        }

        $register_sql = "INSERT INTO event_registrations (user_id, username, event_id, email_user, registration_date) VALUES (?, ?, ?, ?, NOW())";
        $username = $_SESSION['username'];
        $email_user = $_SESSION['email_user'];
        $register_stmt = $conn->prepare($register_sql);
        $register_stmt->bind_param("isss", $user_id, $username, $event_id, $email_user);

        if ($register_stmt->execute()) {
            $_SESSION['registration_success'] = "Registrasi berhasil untuk event: " . htmlspecialchars($event['nama']);
        } else {
            $_SESSION['registration_error'] = "Gagal melakukan registrasi. Silakan coba lagi.";
        }
    } else {
        $_SESSION['registration_error'] = "Event tidak ditemukan.";
    }
} else {
    $_SESSION['registration_error'] = "ID event tidak valid.";
}

header("Location: ../users/user_dashboard.php");
exit();
