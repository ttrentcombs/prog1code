<?php
require 'db.php'; require 'util.php'; require 'auth.php';
$me = require_auth($pdo);
send_json($me);
?>
