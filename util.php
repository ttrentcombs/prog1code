<?php
function json_input() {
  $raw = file_get_contents('php://input');
  $data = json_decode($raw, true);
  return $data ? $data : [];
}
function send_json($obj, $code = 200) {
  header('Content-Type: application/json');
  http_response_code($code);
  echo json_encode($obj);
  exit;
}
?>
