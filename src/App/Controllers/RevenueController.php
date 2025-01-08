<?php

namespace App\Controllers;

use App\Models\Revenue;

class RevenueController extends BaseController
{
    private $revenueModel;

    public function __construct()
    {
        parent::__construct();
        $this->revenueModel = new Revenue();
    }

    public function getRevenueData()
    {
        return $this->revenueModel->getRevenueByPackage();
    }

    public function index()
    {
        $revenueData = $this->getRevenueData();
        $this->view('revenue/index', [
            'title' => 'Thống kê doanh thu theo Gói tập',
            'revenueData' => $revenueData
        ]);
    }

    public function revenueIndex()
    {
        $revenueData = $this->getRevenueData();
        $this->view('revenue/index', [
            'title' => 'Thống kê doanh thu',
            'revenueData' => $revenueData
        ]);
    }
}
