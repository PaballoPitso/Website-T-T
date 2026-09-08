<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include 'db.php';

$id = $_GET['id'];
$type = $_GET['type'];

if ($type === 'registration') {
    $conn->query("DELETE FROM directors WHERE registration_id = $id");
    $conn->query("DELETE FROM registrations WHERE id = $id");
} elseif ($type === 'director') {
    $conn->query("DELETE FROM directors WHERE id = $id");
}

header("Location: index.php");
exit;
