<?php

namespace App\Controllers;

use App\Models\Schedule;
use App\Models\User;
use App\Models\Trainer;
use Core\View;

class ScheduleController extends BaseController
{
    private $scheduleModel;
    private $userModel;
    private $trainerModel;

    public function __construct()
    {
        parent::__construct();
        $this->scheduleModel = new Schedule();
        $this->userModel = new User();
        $this->trainerModel = new Trainer();
    }

    public function index()
    {
        // Check access rights
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'trainer'])) {
            $this->redirect('login');
        }

        // Get parameters from URL with validation
        $currentMonth = isset($_GET['month']) ? str_pad($_GET['month'], 2, '0', STR_PAD_LEFT) : date('m');
        $currentYear = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
        $filter_type = isset($_GET['filter_type']) && in_array($_GET['filter_type'], ['all', 'user', 'trainer']) 
            ? $_GET['filter_type'] : 'all';
        $filter_id = isset($_GET['filter_id']) ? (int)$_GET['filter_id'] : null;

        try {
            // Get schedules based on filters
            $schedules = [];
            switch ($filter_type) {
                case 'user':
                    if ($filter_id) {
                        $schedules = $this->scheduleModel->getSchedulesByUser($filter_id, $currentMonth, $currentYear);
                    }
                    break;
                case 'trainer':
                    if ($filter_id) {
                        $schedules = $this->scheduleModel->getSchedulesByTrainer($filter_id, $currentMonth, $currentYear);
                    }
                    break;
                default:
                    $schedules = $this->scheduleModel->getAllSchedulesWithNames($currentMonth, $currentYear);
            }

            // Get users and trainers for filters
            $users = $this->userModel->getAllActiveUsers();
            $trainers = $this->trainerModel->findActiveTrainers();

            // Calculate calendar data
            $firstDay = strtotime("$currentYear-$currentMonth-01");
            $daysInMonth = date('t', $firstDay);
            $startDay = date('w', $firstDay);
            $weeks = ceil(($daysInMonth + $startDay) / 7);

            // Group schedules by date for easier access in view
            $schedulesGrouped = [];
            foreach ($schedules as $schedule) {
                $date = $schedule['training_date'];
                if (!isset($schedulesGrouped[$date])) {
                    $schedulesGrouped[$date] = [];
                }
                $schedulesGrouped[$date][] = $schedule;
            }

            // Render view with admin layout
            $this->view('admin/Schedule/index', [
                'title' => 'Quản lý lịch tập',
                'schedules' => $schedulesGrouped,
                'users' => $users,
                'trainers' => $trainers,
                'currentMonth' => $currentMonth,
                'currentYear' => $currentYear,
                'filter_type' => $filter_type,
                'filter_id' => $filter_id,
                'year' => $currentYear,
                'daysInMonth' => $daysInMonth,
                'startDay' => $startDay,
                'weeks' => $weeks
            ], 'admin');

        } catch (\Exception $e) {
            $_SESSION['error'] = 'Có lỗi xảy ra: ' . $e->getMessage();
            $this->redirect('admin/schedule');
        }
    }

    public function create()
    {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'trainer'])) {
            $this->json(['error' => 'Không có quyền truy cập'], 403);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Validate required fields
                $requiredFields = ['user_id', 'trainer_id', 'training_date', 'start_time', 'end_time'];
                foreach ($requiredFields as $field) {
                    if (empty($_POST[$field])) {
                        throw new \Exception("Vui lòng điền đầy đủ thông tin: {$field}");
                    }
                }

                // Validate date and time format
                if (!strtotime($_POST['training_date'])) {
                    throw new \Exception('Ngày tập không hợp lệ');
                }

                $start = strtotime($_POST['training_date'] . ' ' . $_POST['start_time']);
                $end = strtotime($_POST['training_date'] . ' ' . $_POST['end_time']);
                
                if ($start >= $end) {
                    throw new \Exception('Thời gian kết thúc phải sau thời gian bắt đầu');
                }

                // Check schedule conflict
                if ($this->scheduleModel->checkScheduleConflict(
                    $_POST['trainer_id'],
                    $_POST['training_date'],
                    $_POST['start_time'],
                    $_POST['end_time']
                )) {
                    throw new \Exception('Huấn luyện viên đã có lịch tập trong thời gian này');
                }

                $data = [
                    'user_id' => (int)$_POST['user_id'],
                    'trainer_id' => (int)$_POST['trainer_id'],
                    'training_date' => $_POST['training_date'],
                    'start_time' => $_POST['start_time'],
                    'end_time' => $_POST['end_time'],
                    'notes' => $_POST['notes'] ?? '',
                    'status' => $_POST['status'] ?? 'pending',
                    'created_by' => $_SESSION['user']['id']
                ];

                if (!$this->scheduleModel->create($data)) {
                    throw new \Exception('Không thể tạo lịch tập');
                }

                $_SESSION['success'] = 'Thêm lịch tập thành công';

            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }

            $this->redirect('admin/schedule');
        }
    }

    public function update($id)
    {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'trainer'])) {
            $this->json(['error' => 'Không có quyền truy cập'], 403);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Validate required fields
                $requiredFields = ['user_id', 'trainer_id', 'training_date', 'start_time', 'end_time', 'status'];
                foreach ($requiredFields as $field) {
                    if (empty($_POST[$field])) {
                        throw new \Exception("Vui lòng điền đầy đủ thông tin: {$field}");
                    }
                }

                // Validate date and time format
                if (!strtotime($_POST['training_date'])) {
                    throw new \Exception('Ngày tập không hợp lệ');
                }

                $start = strtotime($_POST['training_date'] . ' ' . $_POST['start_time']);
                $end = strtotime($_POST['training_date'] . ' ' . $_POST['end_time']);
                
                if ($start >= $end) {
                    throw new \Exception('Thời gian kết thúc phải sau thời gian bắt đầu');
                }

                // Check schedule conflict
                if ($this->scheduleModel->checkScheduleConflict(
                    $_POST['trainer_id'],
                    $_POST['training_date'],
                    $_POST['start_time'],
                    $_POST['end_time'],
                    $id
                )) {
                    throw new \Exception('Huấn luyện viên đã có lịch tập trong thời gian này');
                }

                $data = [
                    'user_id' => (int)$_POST['user_id'],
                    'trainer_id' => (int)$_POST['trainer_id'],
                    'training_date' => $_POST['training_date'],
                    'start_time' => $_POST['start_time'],
                    'end_time' => $_POST['end_time'],
                    'notes' => $_POST['notes'] ?? '',
                    'status' => $_POST['status'],
                    'updated_by' => $_SESSION['user']['id'],
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                if (!$this->scheduleModel->update($id, $data)) {
                    throw new \Exception('Không thể cập nhật lịch tập');
                }

                $_SESSION['success'] = 'Cập nhật lịch tập thành công';

            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }

            $this->redirect('admin/schedule');
        }
    }

    public function delete($id)
    {
        if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'trainer'])) {
            $this->json(['error' => 'Không có quyền truy cập'], 403);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Check if schedule exists
                $schedule = $this->scheduleModel->findById($id);
                if (!$schedule) {
                    throw new \Exception('Không tìm thấy lịch tập');
                }

                // Only allow deletion of future schedules
                $scheduleDate = strtotime($schedule['training_date']);
                if ($scheduleDate < strtotime('today')) {
                    throw new \Exception('Không thể xóa lịch tập đã diễn ra');
                }

                if (!$this->scheduleModel->delete($id)) {
                    throw new \Exception('Không thể xóa lịch tập');
                }

                $_SESSION['success'] = 'Xóa lịch tập thành công';

            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }

            $this->redirect('admin/schedule');
        }
    }
}
