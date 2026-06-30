<?php
require_once __DIR__ . '/../auth.php';
requireLogin();

require_once __DIR__ . '/../models/Budget.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$id = $_POST['id'] ?? null;
$year = filter_input(INPUT_POST, 'year', FILTER_VALIDATE_INT);
$total_budget = filter_input(INPUT_POST, 'total_budget', FILTER_VALIDATE_FLOAT);

if (!$year || $total_budget === false || $total_budget < 0) {
    header('Location: ../index.php');
    exit;
}

if ($id) {
    Budget::update($id, $year, $total_budget);
} else {
    Budget::create($year, $total_budget);
}

header('Location: ../index.php');
