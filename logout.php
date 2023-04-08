<?php
    header("Content-Type: text/html; charset = utf-8");
    require_once 'connection.php';
    $link = mysqli_connect($host, $user, $password, $db) or die("Ошибка ".mysqli_error($link));
    setcookie("users_id", "", time() - 3600 * 24 * 30 * 12, "/");
    setcookie("users_hash", "", time() - 3600 * 24 * 30 * 12, "/", null, null, true);
    header("Location: login.php");
    exit();
?>