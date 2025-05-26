<?php
session_start();
$id = $_GET['id'] ?? null;
if ($id && isset($_SESSION['keranjang'][$id])) {
    unset($_SESSION['keranjang'][$id]);
}
header("Location: keranjang.php");

