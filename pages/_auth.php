<?php
session_start();
if (empty($_SESSION['admin_ok'])) {
  $from = urlencode($_SERVER['REQUEST_URI'] ?? '/');
  header("Location: /pages/login.php?from={$from}");
  exit;
}
