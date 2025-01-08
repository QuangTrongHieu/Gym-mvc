<?php

namespace App\Models;

use App\Models\BaseModel;
use PDOException;

class Package extends BaseModel
{
    protected $table = 'membership_packages';

    public function __construct($db = null)
    {
        parent::__construct($db);
    }

    public function findAll()
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error finding all packages: " . $e->getMessage());
            return [];
        }
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getPackageStatistics()
    {
        $sql = "SELECT 
                    mp.name as package_name,
                    COUNT(DISTINCT mr.userId) as total_users,
                    SUM(mp.price) as total_revenue
                FROM {$this->table} mp
                LEFT JOIN membership_registrations mr ON mp.id = mr.packageId
                WHERE mp.status = 'ACTIVE'
                GROUP BY mp.id, mp.name, mp.price";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getPackageStatisticsNew()
    {
        $sql = "SELECT p.name as package_name, 
                       COUNT(CASE WHEN m.status = 'active' THEN m.id END) as total_users,
                       COALESCE(SUM(CASE WHEN m.status = 'active' THEN p.price ELSE 0 END), 0) as total_revenue
                FROM packages p
                LEFT JOIN membership_registrations m ON p.id = m.package_id
                GROUP BY p.id, p.name";
        
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        try {
            $sql = "INSERT INTO {$this->table} (name, description, duration, price, status) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['name'],
                $data['description'],
                $data['duration'],
                $data['price'],
                $data['status'] ?? 'active'
            ]);
        } catch (PDOException $e) {
            error_log("Error creating package: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data)
    {
        try {
            $sql = "UPDATE {$this->table} SET name = ?, description = ?, duration = ?, price = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $data['name'],
                $data['description'],
                $data['duration'],
                $data['price'],
                $id
            ]);
        } catch (PDOException $e) {
            error_log("Error updating package: " . $e->getMessage());
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log("Error deleting package: " . $e->getMessage());
            return false;
        }
    }

    public function findActivePackages()
    {
        try {
            $stmt = $this->db->query("SELECT * FROM {$this->table} WHERE status = 'ACTIVE' ORDER BY price ASC");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in findActivePackages: " . $e->getMessage());
            return [];
        }
    }

    public function findAllActive()
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY duration ASC");
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error finding active packages: " . $e->getMessage());
            return [];
        }
    }

    public function getMonthlyRevenue()
    {
        $sql = "SELECT 
                    MONTH(mr.startDate) as month,
                    YEAR(mr.startDate) as year,
                    COUNT(DISTINCT mr.userId) as total_users,
                    SUM(mp.price) as total_revenue
                FROM {$this->table} mp
                JOIN membership_registrations mr ON mp.id = mr.packageId
                WHERE YEAR(mr.startDate) = YEAR(CURRENT_DATE)
                GROUP BY YEAR(mr.startDate), MONTH(mr.startDate)
                ORDER BY year ASC, month ASC";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Initialize array for all months
            $monthlyData = [];
            for ($month = 1; $month <= 12; $month++) {
                $monthlyData[$month] = [
                    'month' => $month,
                    'total_users' => 0,
                    'total_revenue' => 0
                ];
            }

            // Fill in actual data
            foreach ($results as $row) {
                $monthlyData[$row['month']] = [
                    'month' => (int)$row['month'],
                    'total_users' => (int)$row['total_users'],
                    'total_revenue' => (float)$row['total_revenue']
                ];
            }

            return array_values($monthlyData);
        } catch (\PDOException $e) {
            error_log("Error getting monthly revenue: " . $e->getMessage());
            return [];
        }
    }
}
