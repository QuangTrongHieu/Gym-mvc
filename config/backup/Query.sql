-- Xóa database nếu tồn tại và tạo mới
DROP DATABASE IF EXISTS `gym-mvc`;
CREATE DATABASE `gym-mvc`;
USE `gym-mvc`;

-- Thiết lập môi trường
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Bảng Users
CREATE TABLE users (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `avatar` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `dateOfBirth` date NOT NULL,
  `sex` enum('Male','Female','Other') NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `membershipStatus` enum('ACTIVE','EXPIRED','SUSPENDED') NOT NULL DEFAULT 'EXPIRED',
  `eRole` enum('ADMIN','TRAINER','MEMBER', 'USER') NOT NULL DEFAULT 'MEMBER',
  `status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_username` (`username`),
  UNIQUE KEY `unique_email` (`email`),
  UNIQUE KEY `unique_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Bảng Admins
CREATE TABLE admins (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `eRole` enum('ADMIN','TRAINER','MEMBER', 'USER') NOT NULL DEFAULT 'ADMIN',
  `status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_admin_username` (`username`),
  UNIQUE KEY `unique_admin_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Bảng Trainers
CREATE TABLE trainers (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `avatar` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `fullName` varchar(255) NOT NULL,
  `dateOfBirth` date NOT NULL,
  `sex` enum('Male','Female','Other') NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `specialization` text NOT NULL,
  `experience` int(11) NOT NULL,
  `certification` text NOT NULL,
  `salary` decimal(10,0) NOT NULL,
  `password` varchar(255) NOT NULL,
  `eRole` enum('ADMIN','TRAINER','MEMBER', 'USER') NOT NULL DEFAULT 'TRAINER',
  `status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_trainer_username` (`username`),
  UNIQUE KEY `unique_trainer_email` (`email`),
  UNIQUE KEY `unique_trainer_phone` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. Bảng Membership Packages
CREATE TABLE membership_packages (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `duration` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `maxFreeze` int(11) DEFAULT 0,
  `benefits` text NOT NULL,
  `status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5. Bảng PT Packages
CREATE TABLE pt_packages (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `sessions` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `validity` int(11) NOT NULL,
  `status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 6. Bảng Payments
CREATE TABLE payments (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `amount` decimal(15,2) NOT NULL,
  `paymentMethod` enum('CASH_ON_DELIVERY','QR_TRANSFER') NOT NULL,
  `qrImage` varchar(255) DEFAULT NULL,
  `refNo` varchar(255) DEFAULT NULL,
  `paymentStatus` enum('PENDING','COMPLETED','FAILED','REFUNDED') NOT NULL DEFAULT 'PENDING',
  `paymentDate` datetime DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 7. Bảng Membership Registrations
CREATE TABLE membership_registrations (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `packageId` int(11) NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `freezeCount` int(11) DEFAULT 0,
  `freezeDays` int(11) DEFAULT 0,
  `status` enum('ACTIVE','EXPIRED','FROZEN','CANCELLED') NOT NULL DEFAULT 'ACTIVE',
  `paymentId` int(11) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `packageId` (`packageId`),
  KEY `paymentId` (`paymentId`),
  CONSTRAINT `membership_registrations_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`),
  CONSTRAINT `membership_registrations_ibfk_2` FOREIGN KEY (`packageId`) REFERENCES `membership_packages` (`id`),
  CONSTRAINT `membership_registrations_ibfk_3` FOREIGN KEY (`paymentId`) REFERENCES `payments` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 8. Bảng PT Registrations
CREATE TABLE pt_registrations (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `trainerId` int(11) NOT NULL,
  `packageId` int(11) NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `remainingSessions` int(11) NOT NULL,
  `status` enum('ACTIVE','COMPLETED','EXPIRED','CANCELLED') NOT NULL DEFAULT 'ACTIVE',
  `paymentId` int(11) NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `trainerId` (`trainerId`),
  KEY `packageId` (`packageId`),
  KEY `paymentId` (`paymentId`),
  CONSTRAINT `pt_registrations_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`),
  CONSTRAINT `pt_registrations_ibfk_2` FOREIGN KEY (`trainerId`) REFERENCES `trainers` (`id`),
  CONSTRAINT `pt_registrations_ibfk_3` FOREIGN KEY (`packageId`) REFERENCES `pt_packages` (`id`),
  CONSTRAINT `pt_registrations_ibfk_4` FOREIGN KEY (`paymentId`) REFERENCES `payments` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 9. Bảng Training Sessions
CREATE TABLE training_sessions (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ptRegistrationId` int(11) NOT NULL,
  `sessionDate` datetime NOT NULL,
  `status` enum('SCHEDULED','COMPLETED','CANCELLED','NO_SHOW') NOT NULL DEFAULT 'SCHEDULED',
  `notes` text DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ptRegistrationId` (`ptRegistrationId`),
  CONSTRAINT `training_sessions_ibfk_1` FOREIGN KEY (`ptRegistrationId`) REFERENCES `pt_registrations` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 10. Bảng Check-ins
CREATE TABLE check_ins (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `checkInTime` datetime NOT NULL,
  `checkOutTime` datetime DEFAULT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  CONSTRAINT `check_ins_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 11. Bảng Progress Tracking
CREATE TABLE progress_tracking (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `weight` decimal(5,2) NOT NULL,
  `height` decimal(5,2) NOT NULL,
  `bodyFat` decimal(5,2) DEFAULT NULL,
  `muscle` decimal(5,2) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `measurementDate` date NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `updatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  CONSTRAINT `progress_tracking_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 12. Bảng Equipment
CREATE TABLE equipment (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `purchaseDate` date NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `status` enum('ACTIVE','MAINTENANCE','BROKEN','RETIRED') NOT NULL,
  `lastMaintenanceDate` date DEFAULT NULL,
  `nextMaintenanceDate` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 13. Bảng Maintenance Logs
CREATE TABLE maintenance_logs (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `equipmentId` int(11) NOT NULL,
  `maintenanceDate` date NOT NULL,
  `description` text NOT NULL,
  `cost` decimal(15,2) DEFAULT NULL,
  `performedBy` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `equipmentId` (`equipmentId`),
  CONSTRAINT `maintenance_logs_ibfk_1` FOREIGN KEY (`equipmentId`) REFERENCES `equipment` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 14. Bảng Announcements
CREATE TABLE announcements (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `type` enum('NEWS','PROMOTION','MAINTENANCE','OTHER') NOT NULL,
  `startDate` datetime NOT NULL,
  `endDate` datetime DEFAULT NULL,
  `status` enum('DRAFT','PUBLISHED','ARCHIVED') NOT NULL,
  `createdAt` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 15. Bảng Promotions
CREATE TABLE promotions (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `discountType` enum('PERCENTAGE','FIXED_AMOUNT') NOT NULL,
  `discountValue` decimal(15,2) NOT NULL,
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `applicableType` enum('MEMBERSHIP','PT','ALL') NOT NULL,
  `status` enum('ACTIVE','INACTIVE') NOT NULL DEFAULT 'ACTIVE',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 16. Bảng Schedules
CREATE TABLE schedules (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trainerId` int(11) NOT NULL,
  `dayOfWeek` tinyint(4) NOT NULL,
  `startTime` time NOT NULL,
  `endTime` time NOT NULL,
  `status` enum('ACTIVE','OFF') NOT NULL DEFAULT 'ACTIVE',
  PRIMARY KEY (`id`),
  KEY `trainerId` (`trainerId`),
  CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`trainerId`) REFERENCES `trainers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 17. Bảng Remember Tokens
CREATE TABLE remember_tokens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY unique_token (token)
);

-- 18. Bảng Equipment Images
CREATE TABLE equipment_images (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `equipment_id` int(11) NOT NULL,
    `image_path` varchar(255) NOT NULL,
    `is_primary` boolean NOT NULL DEFAULT false,
    `sort_order` int(11) NOT NULL DEFAULT 999,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `equipment_id` (`equipment_id`),
    CONSTRAINT `equipment_images_ibfk_1` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample package data
INSERT INTO membership_packages (name, description, duration, price, status) VALUES 
('Gói Cơ Bản', 'Phù hợp cho người mới bắt đầu tập luyện. Bao gồm sử dụng các thiết bị cơ bản và tham gia lớp tập theo nhóm.', 1, 500000, 'active'),
('Gói Tiêu Chuẩn', 'Gói phổ biến với đầy đủ quyền truy cập vào tất cả các thiết bị và lớp học. Bao gồm 2 buổi PT miễn phí.', 3, 1200000, 'active'),
('Gói Premium', 'Trải nghiệm tập luyện cao cấp với huấn luyện viên cá nhân hàng tuần và quyền ưu tiên đặt lớp.', 6, 2500000, 'active'),
('Gói VIP', 'Gói cao cấp nhất với huấn luyện viên riêng, chế độ dinh dưỡng cá nhân hóa và các đặc quyền VIP.', 12, 4500000, 'active');

-- Tạo tài khoản mặc định cho Admin
INSERT INTO admins (username, email, password, eRole) VALUES
('admin', 'admin@gmail.com', '$2y$10$41A7b7y96Icmxa/CbhAAuezZYbsd3A7.YY51zIxbRWpT..a.EYnB.', 'ADMIN');
-- Password: 123456

-- Thêm indexes
ALTER TABLE users
ADD INDEX idx_user_search (fullName, phone, email, membershipStatus);

ALTER TABLE trainers
ADD INDEX idx_trainer_search (fullName, specialization(100), status);

ALTER TABLE membership_registrations
ADD INDEX idx_membership_status (userId, status, startDate, endDate);

ALTER TABLE pt_registrations 
ADD INDEX idx_pt_status (userId, trainerId, status, startDate, endDate);

ALTER TABLE training_sessions
ADD INDEX idx_session_date (sessionDate, status);

ALTER TABLE check_ins
ADD INDEX idx_checkin_time (checkInTime, checkOutTime);

-- Triggers
DELIMITER $$

-- Trigger cập nhật trạng thái thành viên
CREATE TRIGGER after_membership_registration_update 
AFTER UPDATE ON membership_registrations
FOR EACH ROW 
BEGIN
    IF NEW.status != OLD.status THEN
        UPDATE users 
        SET membershipStatus = CASE
            WHEN NEW.status = 'ACTIVE' THEN 'ACTIVE'
            WHEN NEW.status = 'FROZEN' THEN 'SUSPENDED'
            ELSE 'EXPIRED'
        END
        WHERE id = NEW.userId;
    END IF;
END $$

-- Trigger cập nhật số buổi tập còn lại
CREATE TRIGGER after_training_session_update
AFTER UPDATE ON training_sessions
FOR EACH ROW
BEGIN
    IF NEW.status = 'COMPLETED' AND OLD.status != 'COMPLETED' THEN
        UPDATE pt_registrations
        SET remainingSessions = remainingSessions - 1
        WHERE id = NEW.ptRegistrationId;
    END IF;
END $$

DELIMITER ;

-- Views
CREATE VIEW membership_revenue_report AS
SELECT 
    DATE_FORMAT(mr.createdAt, '%Y-%m') as month_year,
    mp.name as package_name,
    COUNT(mr.id) as total_registrations,
    SUM(p.amount) as total_revenue
FROM membership_registrations mr
JOIN membership_packages mp ON mr.packageId = mp.id
JOIN payments p ON mr.paymentId = p.id
WHERE p.paymentStatus = 'COMPLETED'
GROUP BY DATE_FORMAT(mr.createdAt, '%Y-%m'), mp.id
ORDER BY DATE_FORMAT(mr.createdAt, '%Y-%m') DESC, SUM(p.amount) DESC;

CREATE VIEW trainer_performance AS
SELECT
    t.id as trainer_id,
    t.fullName as trainer_name,
    COUNT(DISTINCT ptr.userId) as total_clients,
    COUNT(ts.id) as total_sessions,
    COUNT(CASE WHEN ts.status = 'COMPLETED' THEN 1 END) as completed_sessions,
    COUNT(CASE WHEN ts.status = 'CANCELLED' THEN 1 END) as cancelled_sessions,
    COUNT(CASE WHEN ts.status = 'NO_SHOW' THEN 1 END) as no_show_sessions
FROM trainers t
LEFT JOIN pt_registrations ptr ON t.id = ptr.trainerId
LEFT JOIN training_sessions ts ON ptr.id = ts.ptRegistrationId
GROUP BY t.id;

-- Bật lại foreign key checks
SET FOREIGN_KEY_CHECKS = 1;
COMMIT;