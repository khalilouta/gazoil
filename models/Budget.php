<?php
require_once __DIR__ . '/../config.php';

class Budget
{
    public static function getCurrentBudget()
    {
        $stmt = pdo()->prepare('SELECT * FROM budgets ORDER BY year DESC, created_at DESC LIMIT 1');
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function getAllBudgets()
    {
        $stmt = pdo()->query('SELECT * FROM budgets ORDER BY year DESC');
        return $stmt->fetchAll();
    }

    public static function getBudgetByYear($year)
    {
        $stmt = pdo()->prepare('SELECT * FROM budgets WHERE year = :year ORDER BY created_at DESC LIMIT 1');
        $stmt->execute(['year' => $year]);
        return $stmt->fetch();
    }

    public static function create($year, $totalBudget)
    {
        $stmt = pdo()->prepare('INSERT INTO budgets (year, total_budget, created_at) VALUES (:year, :total_budget, NOW())');
        return $stmt->execute(['year' => $year, 'total_budget' => $totalBudget]);
    }

    public static function update($id, $year, $totalBudget)
    {
        $stmt = pdo()->prepare('UPDATE budgets SET year = :year, total_budget = :total_budget, created_at = NOW() WHERE id = :id');
        return $stmt->execute(['id' => $id, 'year' => $year, 'total_budget' => $totalBudget]);
    }
}
