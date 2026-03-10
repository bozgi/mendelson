<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php" . (isset($_GET['id']) ? "?id=" . $_GET['id'] : ""));
    exit;
}
header("Location: dashboard.php");