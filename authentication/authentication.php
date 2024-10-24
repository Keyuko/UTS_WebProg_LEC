<?php
session_start(); 

function checkAuth($requiredRole = null) {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
    
    if ($requiredRole && $_SESSION['user_type'] !== $requiredRole) {
        header('Location: user_dashboard.php'); 
        exit();
    }
}
?>
