<?php
require 'db.php'; require 'util.php'; require 'auth.php';
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'GET') {
  if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM venues WHERE id=?");
    $stmt->execute([$_GET['id']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) send_json(["error"=>"not found"], 404);
    send_json($row);
  } else {
    $stmt = $pdo->query("SELECT * FROM venues ORDER BY id DESC");
    send_json($stmt->fetchAll(PDO::FETCH_ASSOC));
  }
}
if ($method === 'POST') {
  require_auth($pdo);
  $in = json_input();
  $name = $in['name'] ?? null;
  $city = $in['city'] ?? null;
  if (!$name || !$city) send_json(["error"=>"name and city required"], 422);
  $stmt = $pdo->prepare("INSERT INTO venues (name, city) VALUES (?,?)");
  $stmt->execute([$name, $city]);
  send_json(["message"=>"venue added","id"=>$pdo->lastInsertId()], 201);
}
send_json(["error"=>"method not allowed"], 405);
?>
