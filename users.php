<?php
require 'db.php'; require 'util.php';
$stmt = $pdo->query("SELECT id, username, created_at FROM users ORDER BY id DESC");
send_json($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
