<?php
require_once 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$status = isset($_GET['status']) ? (int)$_GET['status'] : null;

if ($id <= 0 || ($status !== 0 && $status !== 1)) {
    header("Location: index.php");
    exit;
}

$stmt = $mysqli->prepare("UPDATE cars SET status = ? WHERE id = ?");
$stmt->bind_param("ii", $status, $id);
$stmt->execute();

header("Location: index.php");
exit;
