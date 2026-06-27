<?php
require_once __DIR__ . '/../config.php';

class FuelBon
{
    public static function getAll($filters = [], $limit = 15, $offset = 0)
    {
        $sql = 'SELECT * FROM fuel_bons WHERE 1=1';
        $params = [];

        if (!empty($filters['date'])) {
            $sql .= ' AND date = :date';
            $params['date'] = $filters['date'];
        }
        if (!empty($filters['month'])) {
            $sql .= ' AND month = :month';
            $params['month'] = $filters['month'];
        }
        if (!empty($filters['vehicle'])) {
            $sql .= ' AND vehicle_registration LIKE :vehicle';
            $params['vehicle'] = '%' . $filters['vehicle'] . '%';
        }
        if (!empty($filters['driver'])) {
            $sql .= ' AND driver_name LIKE :driver';
            $params['driver'] = '%' . $filters['driver'] . '%';
        }

        $sql .= ' ORDER BY date DESC, id DESC LIMIT :limit OFFSET :offset';
        $stmt = pdo()->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function count($filters = [])
    {
        $sql = 'SELECT COUNT(*) FROM fuel_bons WHERE 1=1';
        $params = [];
        if (!empty($filters['date'])) {
            $sql .= ' AND date = :date';
            $params['date'] = $filters['date'];
        }
        if (!empty($filters['month'])) {
            $sql .= ' AND month = :month';
            $params['month'] = $filters['month'];
        }
        if (!empty($filters['vehicle'])) {
            $sql .= ' AND vehicle_registration LIKE :vehicle';
            $params['vehicle'] = '%' . $filters['vehicle'] . '%';
        }
        if (!empty($filters['driver'])) {
            $sql .= ' AND driver_name LIKE :driver';
            $params['driver'] = '%' . $filters['driver'] . '%';
        }
        $stmt = pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public static function getById($id)
    {
        $stmt = pdo()->prepare('SELECT * FROM fuel_bons WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public static function create($data)
    {
        $stmt = pdo()->prepare('INSERT INTO fuel_bons (date, month, bon_number, vehicle_registration, driver_name, amount_spent, remaining_balance, created_at) VALUES (:date, :month, :bon_number, :vehicle_registration, :driver_name, :amount_spent, :remaining_balance, NOW())');
        return $stmt->execute($data);
    }

    public static function update($data)
    {
        $stmt = pdo()->prepare('UPDATE fuel_bons SET date = :date, month = :month, bon_number = :bon_number, vehicle_registration = :vehicle_registration, driver_name = :driver_name, amount_spent = :amount_spent, remaining_balance = :remaining_balance WHERE id = :id');
        return $stmt->execute($data);
    }

    public static function delete($id)
    {
        $stmt = pdo()->prepare('DELETE FROM fuel_bons WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public static function getTotalSpent($filters = [])
    {
        $sql = 'SELECT COALESCE(SUM(amount_spent),0) FROM fuel_bons WHERE 1=1';
        $params = [];
        if (!empty($filters['date'])) {
            $sql .= ' AND date = :date';
            $params['date'] = $filters['date'];
        }
        if (!empty($filters['month'])) {
            $sql .= ' AND month = :month';
            $params['month'] = $filters['month'];
        }
        if (!empty($filters['vehicle'])) {
            $sql .= ' AND vehicle_registration LIKE :vehicle';
            $params['vehicle'] = '%' . $filters['vehicle'] . '%';
        }
        if (!empty($filters['driver'])) {
            $sql .= ' AND driver_name LIKE :driver';
            $params['driver'] = '%' . $filters['driver'] . '%';
        }
        $stmt = pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public static function getTotalBons()
    {
        $stmt = pdo()->query('SELECT COUNT(*) FROM fuel_bons');
        return $stmt->fetchColumn();
    }

    public static function getVehicleCount()
    {
        $stmt = pdo()->query('SELECT COUNT(DISTINCT vehicle_registration) FROM fuel_bons');
        return $stmt->fetchColumn();
    }
}
