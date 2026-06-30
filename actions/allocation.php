<?php
require_once __DIR__ . '/../auth.php';
requireLogin();

require_once __DIR__ . '/../models/Allocation.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
$year = filter_input(INPUT_POST, 'year', FILTER_VALIDATE_INT);

if ($amount === false || $amount < 0 || !$year) {
    header('Location: ../index.php');
    exit;
}

Allocation::save($amount, $year);
header('Location: ../index.php');
