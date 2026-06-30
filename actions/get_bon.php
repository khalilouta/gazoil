<?php
require_once __DIR__ . '/../auth.php';
requireLoginJson();

require_once __DIR__ . '/../models/FuelBon.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    echo json_encode(['error' => 'ID invalide']);
    exit;
}

$bon = FuelBon::getById($id);
if (!$bon) {
    echo json_encode(['error' => 'Bon non trouvé']);
    exit;
}

header('Content-Type: application/json');
echo json_encode($bon);
