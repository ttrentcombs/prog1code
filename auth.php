<?php
function get_user_by_token($pdo, $token) {
  $stmt = $pdo->prepare("SELECT id, username FROM users WHERE token=?");
  $stmt->execute([$token]);
  return $stmt->fetch(PDO::FETCH_ASSOC);
}
function require_auth($pdo) {
  $headers = function_exists('getallheaders') ? getallheaders() : [];
  $auth = $headers['Authorization'] ?? ($headers['authorization'] ?? '');
  if (stripos($auth, 'Bearer ') === 0) {
    $token = substr($auth, 7);
    $user = get_user_by_token($pdo, $token);
    if ($user) return $user;
  }
  header('WWW-Authenticate: Bearer');
  send_json(["error"=>"Unauthorized"], 401);
}
?>
