<?php

namespace App\Models;

;
use PDO;

class PackageStatistics extends BaseModel {
    public function getPackageStatistics() {
        $sql = "SELECT 
                    mp.name as package_name,
                    COUNT(DISTINCT mr.userId) as total_users,
                    SUM(mp.price) as total_revenue
                FROM membership_packages mp
                LEFT JOIN membership_registrations mr ON mp.id = mr.packageId
                WHERE mp.status = 'ACTIVE'
                GROUP BY mp.id, mp.name, mp.price";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
