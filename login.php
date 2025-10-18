<?php
require 'db.php'; require 'util.php';
$in = json_input();
$username = $in['username'] ?? null;
$password = $in['password'] ?? null;
if (!$username || !$password) send_json(["error"=>"username and password required"], 422);
$stmt = $pdo->prepare("SELECT * FROM users WHERE username=?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if ($user && password_verify($password, $user['password'])) {
  $token = bin2hex(random_bytes(16));
  $pdo->prepare("UPDATE users SET token=? WHERE id=?")->execute([$token, $user['id']]);
  send_json(["token"=>$token]);
} else {
  send_json(["error"=>"invalid login"], 401);
}
?>
