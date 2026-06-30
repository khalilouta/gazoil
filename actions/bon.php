<?php
require_once __DIR__ . '/../auth.php';
requireLogin();

require_once __DIR__ . '/../models/FuelBon.php';
require_once __DIR__ . '/../models/Budget.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$id = $_POST['id'] ?? null;
$data = [
    'date' => $_POST['date'] ?? '',
    'month' => $_POST['month'] ?? '',
    'bon_number' => trim($_POST['bon_number'] ?? ''),
    'vehicle_registration' => trim($_POST['vehicle_registration'] ?? ''),
    'driver_name' => trim($_POST['driver_name'] ?? ''),
    'amount_spent' => filter_input(INPUT_POST, 'amount_spent', FILTER_VALIDATE_FLOAT),
];

if (empty($data['date']) || empty($data['month']) || empty($data['bon_number']) || empty($data['vehicle_registration']) || empty($data['driver_name']) || $data['amount_spent'] === false || $data['amount_spent'] < 0) {
    header('Location: ../index.php');
    exit;
}

$currentBudget = Budget::getCurrentBudget();
$totalSpent = FuelBon::getTotalSpent();

if (!$currentBudget) {
    header('Location: ../index.php');
    exit;
}

if ($id) {
    $existingBon = FuelBon::getById($id);
    if (!$existingBon) {
        header('Location: ../index.php');
        exit;
    }
    $adjustedSpent = $totalSpent - $existingBon['amount_spent'] + $data['amount_spent'];
    $data['id'] = $id;
} else {
    $adjustedSpent = $totalSpent + $data['amount_spent'];
}

$data['remaining_balance'] = max(0, $currentBudget['total_budget'] - $adjustedSpent);

if ($id) {
    FuelBon::update($data);
} else {
    FuelBon::create($data);
}

header('Location: ../index.php');
