<?php

namespace App\Models;

class Revenue extends BaseModel
{
    protected $table = 'revenue';

    public function getRevenueByPackage()
    {
        $sql = "SELECT p.name as package_name, COUNT(*) as total_registrations, 
                SUM(p.price) as total_revenue 
                FROM membership_registrations mr 
                JOIN packages p ON mr.package_id = p.id 
                GROUP BY p.id, p.name";

        return $this->db->query($sql)->fetchAll();
    }
}
