<?php
require_once __DIR__ . '/auth.php';
requireLogin();

require_once __DIR__ . '/models/Budget.php';
require_once __DIR__ . '/models/FuelBon.php';
require_once __DIR__ . '/models/Allocation.php';

$currentBudget = Budget::getCurrentBudget();
$filters = [
    'date' => $_GET['date'] ?? '',
    'month' => $_GET['month'] ?? '',
    'vehicle' => $_GET['vehicle'] ?? '',
    'driver' => $_GET['driver'] ?? '',
];

$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 12;
$offset = ($page - 1) * $limit;
$totalRecords = FuelBon::count($filters);
$bons = FuelBon::getAll($filters, $limit, $offset);
$totalPages = (int)ceil($totalRecords / $limit);
$totalSpent = FuelBon::getTotalSpent();
$totalBons = FuelBon::getTotalBons();
$vehicleCount = FuelBon::getVehicleCount();
$budgetRemaining = $currentBudget ? max(0, $currentBudget['total_budget'] - $totalSpent) : 0;
$allocation = Allocation::getCurrentAllocation();

function formatMoney($value)
{
    return number_format((float)$value, 2, ',', ' ') . ' DH';
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tableau de Suivi du Carburant - Commune</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Tableau de Suivi du Carburant</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link active" href="#">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Déconnexion</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container py-4">
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card border-primary shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h6 class="text-uppercase text-muted">Budget Total</h6>
                            <h3 class="fw-bold"><?= $currentBudget ? formatMoney($currentBudget['total_budget']) : '0 DH' ?></h3>
                            <p class="mb-0 text-muted">Année Budgétaire: <?= $currentBudget ? htmlspecialchars($currentBudget['year']) : 'N/A' ?></p>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#budgetModal">
                                <i class="fa-solid fa-pen-to-square"></i> Modifier
                            </button>
                        </div>
                    </div>
                    <div class="border-top pt-3 mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Dépensé</span>
                            <strong><?= formatMoney($totalSpent) ?></strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Budget Restant</span>
                            <strong><?= formatMoney($budgetRemaining) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-info shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <div>
                            <h6 class="text-uppercase text-muted">Allocation</h6>
                            <h3 class="fw-bold"><?= $allocation ? formatMoney($allocation['amount']) : '0 DH' ?></h3>
                            <p class="mb-0 text-muted">Année allouée: <?= $allocation ? htmlspecialchars($allocation['year']) : 'N/A' ?></p>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#allocationModal">
                                <i class="fa-solid fa-pen-to-square"></i> Edit Amount and Year
                            </button>
                        </div>
                    </div>
                    <div class="border-top pt-3 mt-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Montant alloué</span>
                            <strong><?= $allocation ? formatMoney($allocation['amount']) : '0 DH' ?></strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Année</span>
                            <strong><?= $allocation ? htmlspecialchars($allocation['year']) : 'N/A' ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="card shadow-sm border-success h-100">
                        <div class="card-body text-center">
                            <i class="fa-solid fa-file-invoice-dollar fa-2x text-success mb-3"></i>
                            <h6 class="text-muted">Total Bons</h6>
                            <h4 class="fw-bold"><?= $totalBons ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-danger h-100">
                        <div class="card-body text-center">
                            <i class="fa-solid fa-chart-line fa-2x text-danger mb-3"></i>
                            <h6 class="text-muted">Total Dépensé</h6>
                            <h4 class="fw-bold"><?= formatMoney($totalSpent) ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-warning h-100">
                        <div class="card-body text-center">
                            <i class="fa-solid fa-car fa-2x text-warning mb-3"></i>
                            <h6 class="text-muted">Nombre de Véhicules</h6>
                            <h4 class="fw-bold"><?= $vehicleCount ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3">
            <div>
                <h5 class="card-title mb-0">Suivi des Bons de Carburant</h5>
                <div class="small text-muted">Ajouter, modifier, supprimer et consulter les transactions.</div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bonModal" id="addBonBtn">
                    <i class="fa-solid fa-plus"></i> Nouveau Bon
                </button>
                <button class="btn btn-outline-secondary" onclick="exportExcel()">
                    <i class="fa-solid fa-file-excel"></i> Exporter Excel
                </button>
                <button class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="fa-solid fa-print"></i> Imprimer
                </button>
            </div>
        </div>
        <div class="card-body">
            <form class="row g-3 mb-4" id="filterForm" method="get">
                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($filters['date']) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Mois</label>
                    <select name="month" class="form-select">
                        <option value="">Tous</option>
                        <?php
                        $months = [
                            'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
                        ];
                        foreach ($months as $monthName):
                        ?>
                            <option value="<?= $monthName ?>" <?= $filters['month'] === $monthName ? 'selected' : '' ?>><?= $monthName ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Véhicule</label>
                    <input type="text" name="vehicle" class="form-control" placeholder="Matricule" value="<?= htmlspecialchars($filters['vehicle']) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Chauffeur</label>
                    <input type="text" name="driver" class="form-control" placeholder="Nom chauffeur" value="<?= htmlspecialchars($filters['driver']) ?>">
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-outline-primary">Filtrer</button>
                    <a href="index.php" class="btn btn-outline-secondary">Réinitialiser</a>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Mois</th>
                        <th>N° Bon</th>
                        <th>Véhicule (Matricule)</th>
                        <th>Chauffeur</th>
                        <th>Montant Dépensé</th>
                        <th>Total Dépensé</th>
                        <th>Reste</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($bons)): ?>
                        <tr><td colspan="9" class="text-center py-4">Aucun bon trouvé.</td></tr>
                    <?php else: ?>
                        <?php foreach ($bons as $bon): ?>
                            <tr>
                                <td><?= htmlspecialchars($bon['date']) ?></td>
                                <td><?= htmlspecialchars($bon['month']) ?></td>
                                <td><?= htmlspecialchars($bon['bon_number']) ?></td>
                                <td><?= htmlspecialchars($bon['vehicle_registration']) ?></td>
                                <td><?= htmlspecialchars($bon['driver_name']) ?></td>
                                <td><?= formatMoney($bon['amount_spent']) ?></td>
                                <td><?= formatMoney($bon['amount_spent']) ?></td>
                                <td><?= formatMoney($bon['remaining_balance']) ?></td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary me-1" onclick="editBon(<?= $bon['id'] ?>)">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger" onclick="deleteBon(<?= $bon['id'] ?>)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if ($totalPages > 1): ?>
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $i])) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Budget modal -->
<div class="modal fade" id="budgetModal" tabindex="-1" aria-labelledby="budgetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="budgetForm" action="actions/budget.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="budgetModalLabel">Budget Annuel Carburant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="<?= $currentBudget ? $currentBudget['id'] : '' ?>">
                    <div class="mb-3">
                        <label class="form-label">Année</label>
                        <input type="number" name="year" class="form-control" min="2024" value="<?= $currentBudget ? htmlspecialchars($currentBudget['year']) : date('Y') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Montant Global du Budget</label>
                        <input type="number" step="0.01" name="total_budget" class="form-control" value="<?= $currentBudget ? htmlspecialchars($currentBudget['total_budget']) : '' ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Allocation modal -->
<div class="modal fade" id="allocationModal" tabindex="-1" aria-labelledby="allocationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="actions/allocation.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="allocationModalLabel">Allocation Manuelle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Montant alloué</label>
                        <input type="number" step="0.01" name="amount" class="form-control" value="<?= $allocation ? htmlspecialchars($allocation['amount']) : '' ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Année manuelle</label>
                        <input type="number" name="year" class="form-control" min="2024" placeholder="Ex: 2026" value="<?= $allocation ? htmlspecialchars($allocation['year']) : '' ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bon modal -->
<div class="modal fade" id="bonModal" tabindex="-1" aria-labelledby="bonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="bonForm" action="actions/bon.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="bonModalLabel">Ajouter un Bon de Carburant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body row g-3">
                    <input type="hidden" name="id" id="bonId">
                    <div class="col-md-6">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" id="bonDate" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Mois</label>
                        <select name="month" id="bonMonth" class="form-select" required>
                            <option value="">Sélectionner</option>
                            <?php foreach ($months as $monthName): ?>
                                <option value="<?= $monthName ?>"><?= $monthName ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">N° Bon</label>
                        <input type="text" name="bon_number" id="bonNumber" class="form-control" placeholder="Référence" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Véhicule (Matricule)</label>
                        <input type="text" name="vehicle_registration" id="bonVehicle" class="form-control" placeholder="AA-123-BB" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Chauffeur</label>
                        <input type="text" name="driver_name" id="bonDriver" class="form-control" placeholder="Nom du chauffeur" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Montant Dépensé</label>
                        <input type="number" step="0.01" name="amount_spent" id="bonAmount" class="form-control" placeholder="0.00" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="assets/js/app.js"></script>
</body>
</html>
