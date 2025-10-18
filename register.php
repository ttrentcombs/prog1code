<?php
require 'db.php'; require 'util.php';
$in = json_input();
$username = $in['username'] ?? null;
$password = $in['password'] ?? null;
if (!$username || !$password) send_json(["error"=>"username and password required"], 422);
$hash = password_hash($password, PASSWORD_BCRYPT);
try {
  $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?,?)");
  $stmt->execute([$username, $hash]);
  send_json(["message"=>"user created"]);
} catch (PDOException $e) {
  send_json(["error"=>"username may already exist"], 400);
}
?>
