<?php
require_once __DIR__ . '/../auth.php';
requireLogin();

require_once __DIR__ . '/../models/FuelBon.php';
require_once __DIR__ . '/../models/Budget.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../index.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: ../index.php');
    exit;
}

$bon = FuelBon::getById($id);
if ($bon) {
    FuelBon::delete($id);
}

header('Location: ../index.php');
