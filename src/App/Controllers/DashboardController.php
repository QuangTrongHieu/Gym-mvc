<?php

namespace App\Controllers;

use App\Models\Revenue;
use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    private $revenueModel;

    public function __construct()
    {
        parent::__construct();
        $this->revenueModel = new Revenue();
    }

    public function index()
    {
        $revenueData = $this->revenueModel->getRevenueByPackage();
        
        $this->view('admin/dashboard', [
            'revenueData' => $revenueData
        ]);
    }
} 