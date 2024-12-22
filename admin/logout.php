<?php
require_once '../includes/auth.php';
session_start();

$auth = new Auth($pdo);
$auth->logout();

header('Location: login.php');
exit; 