<?php
session_start();
require_once 'includes/db.php';

if (!isset($_SESSION['admin'])) {
    header("HTTP/1.1 403 Forbidden");
    exit;
}

if (!isset($_GET['id'])) {
    header("HTTP/1.1 400 Bad Request");
    exit;
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM promociones WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$promocion = $result->fetch_assoc();
$stmt->close();

if (!$promocion) {
    header("HTTP/1.1 404 Not Found");
    exit;
}

header('Content-Type: application/json');
echo json_encode($promocion);
?>