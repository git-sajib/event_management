<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function logout() {
    session_destroy();
    redirect('login.php');
}
?>