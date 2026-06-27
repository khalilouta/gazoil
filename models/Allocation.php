<?php
require_once __DIR__ . '/../config.php';

class Allocation
{
    public static function ensureTable()
    {
        $stmt = pdo()->prepare(
            'CREATE TABLE IF NOT EXISTS budget_allocations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                amount DECIMAL(12,2) NOT NULL,
                year INT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4'
        );
        return $stmt->execute();
    }

    public static function getCurrentAllocation()
    {
        self::ensureTable();
        $stmt = pdo()->prepare('SELECT * FROM budget_allocations ORDER BY year DESC, created_at DESC LIMIT 1');
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function save($amount, $year)
    {
        self::ensureTable();
        $existing = self::getCurrentAllocation();

        if ($existing && (int)$existing['year'] === (int)$year) {
            $stmt = pdo()->prepare('UPDATE budget_allocations SET amount = :amount, year = :year, updated_at = NOW() WHERE id = :id');
            return $stmt->execute(['id' => $existing['id'], 'amount' => $amount, 'year' => $year]);
        }

        $stmt = pdo()->prepare('INSERT INTO budget_allocations (amount, year, created_at, updated_at) VALUES (:amount, :year, NOW(), NOW())');
        return $stmt->execute(['amount' => $amount, 'year' => $year]);
    }
}
