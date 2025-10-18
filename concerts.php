<?php
require 'db.php'; require 'util.php'; require 'auth.php';
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'GET') {
  if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT c.*, v.name AS venue_name, v.city FROM concerts c JOIN venues v ON c.venue_id=v.id WHERE c.id=?");
    $stmt->execute([$_GET['id']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) send_json(["error"=>"not found"], 404);
    send_json($row);
  } else {
    $stmt = $pdo->query("SELECT c.id,c.title,c.event_date,c.price,v.name AS venue_name,v.city FROM concerts c JOIN venues v ON c.venue_id=v.id ORDER BY c.event_date ASC");
    send_json($stmt->fetchAll(PDO::FETCH_ASSOC));
  }
}
if ($method === 'POST') {
  require_auth($pdo);
  $in = json_input();
  if (!isset($in['title']) || !isset($in['venue_id']) || !isset($in['event_date']) || !isset($in['price'])) {
    send_json(["error"=>"title, venue_id, event_date, price required"], 422);
  }
  $stmt = $pdo->prepare("INSERT INTO concerts (title, venue_id, event_date, price) VALUES (?,?,?,?)");
  $stmt->execute([$in['title'], $in['venue_id'], $in['event_date'], $in['price']]);
  send_json(["message"=>"concert created","id"=>$pdo->lastInsertId()], 201);
}
if ($method === 'PUT') {
  require_auth($pdo);
  parse_str($_SERVER['QUERY_STRING'] ?? '', $q);
  if (!isset($q['id'])) send_json(["error"=>"id required"], 422);
  $in = json_input();
  $stmt = $pdo->prepare("UPDATE concerts SET title=COALESCE(?,title), venue_id=COALESCE(?,venue_id), event_date=COALESCE(?,event_date), price=COALESCE(?,price) WHERE id=?");
  $stmt->execute([$in['title'] ?? null, $in['venue_id'] ?? null, $in['event_date'] ?? null, $in['price'] ?? null, $q['id']]);
  send_json(["message"=>"concert updated"]);
}
if ($method === 'DELETE') {
  require_auth($pdo);
  parse_str($_SERVER['QUERY_STRING'] ?? '', $q);
  if (!isset($q['id'])) send_json(["error"=>"id required"], 422);
  $stmt = $pdo->prepare("DELETE FROM concerts WHERE id=?");
  $stmt->execute([$q['id']]);
  send_json(["message"=>"concert deleted"]);
}
send_json(["error"=>"method not allowed"], 405);
?>
