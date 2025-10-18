<?php
require 'db.php'; require 'util.php'; require 'auth.php';
$me = require_auth($pdo);
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'GET') {
  $stmt = $pdo->prepare("SELECT b.id, b.qty, b.total, b.created_at, c.title, c.event_date, v.name AS venue_name, v.city
                         FROM bookings b
                         JOIN concerts c ON b.concert_id=c.id
                         JOIN venues v ON c.venue_id=v.id
                         WHERE b.user_id=? ORDER BY b.id DESC");
  $stmt->execute([$me['id']]);
  send_json($stmt->fetchAll(PDO::FETCH_ASSOC));
}
if ($method === 'POST') {
  $in = json_input();
  $concert_id = $in['concert_id'] ?? null;
  $qty = isset($in['qty']) ? intval($in['qty']) : 1;
  if (!$concert_id) send_json(["error"=>"concert_id required"], 422);
  $stmt = $pdo->prepare("SELECT price FROM concerts WHERE id=?");
  $stmt->execute([$concert_id]);
  $c = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$c) send_json(["error"=>"concert not found"], 404);
  $total = floatval($c['price']) * $qty;
  $pdo->prepare("INSERT INTO bookings (user_id, concert_id, qty, total) VALUES (?,?,?,?)")->execute([$me['id'], $concert_id, $qty, $total]);
  send_json(["message"=>"booking created","id"=>$pdo->lastInsertId(),"total"=>$total], 201);
}
send_json(["error"=>"method not allowed"], 405);
?>
