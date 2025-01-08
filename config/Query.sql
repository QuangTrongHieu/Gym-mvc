-- Create database
DROP DATABASE IF EXISTS `gym`;
CREATE DATABASE `gym`;
USE `gym`;

-- Create tables with proper constraints
-- 1. Bảng Users
CREATE TABLE users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    avatar VARCHAR(255) DEFAULT 'default.jpg',
    username VARCHAR(50) NOT NULL,
    fullName VARCHAR(255) NOT NULL,
    dateOfBirth DATE NOT NULL,
    sex ENUM('Male', 'Female', 'Other') NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    membershipStatus ENUM('ACTIVE', 'EXPIRED', 'SUSPENDED') NOT NULL DEFAULT 'EXPIRED',
    eRole ENUM('ADMIN', 'TRAINER', 'MEMBER', 'USER') NOT NULL DEFAULT 'MEMBER',
    status ENUM('ACTIVE', 'INACTIVE') NOT NULL DEFAULT 'ACTIVE',
    createdAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY unique_username (username),
    UNIQUE KEY unique_email (email),
    UNIQUE KEY unique_phone (phone),
    INDEX idx_role_status (eRole, status),
    INDEX idx_membership (membershipStatus)
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

-- 2. Bảng Trainers
CREATE TABLE trainers (
    id INT(11) NOT NULL AUTO_INCREMENT,
    avatar VARCHAR(255) NOT NULL,
    username VARCHAR(50) NOT NULL,
    fullName VARCHAR(255) NOT NULL,
    dateOfBirth DATE NOT NULL,
    sex ENUM('Male', 'Female', 'Other') NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    specialization TEXT NOT NULL,
    experience INT(11) NOT NULL,
    certification TEXT NOT NULL,
    salary DECIMAL(10, 0) NOT NULL,
    password VARCHAR(255) NOT NULL,
    eRole ENUM('ADMIN', 'TRAINER', 'MEMBER', 'USER') NOT NULL DEFAULT 'TRAINER',
    status ENUM('ACTIVE', 'INACTIVE') NOT NULL DEFAULT 'ACTIVE',
    createdAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY unique_trainer_username (username),
    UNIQUE KEY unique_trainer_email (email),
    UNIQUE KEY unique_trainer_phone (phone)
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
  CONSTRAINT `membership_registrations_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE,
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
  CONSTRAINT `pt_registrations_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE,
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

-- Tạo bảng thiết bị
CREATE TABLE IF NOT EXISTS equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL COMMENT 'Tên thiết bị',
    description TEXT COMMENT 'Mô tả thiết bị',
    image_path VARCHAR(255) COMMENT 'Đường dẫn hình ảnh',
    purchaseDate DATE NOT NULL COMMENT 'Ngày mua',
    price DECIMAL(10,2) NOT NULL COMMENT 'Giá thiết bị',
    status ENUM('active', 'inactive') DEFAULT 'active' COMMENT 'Trạng thái: active-Hoạt động, inactive-Ngừng hoạt động',
    lastMaintenanceDate DATE COMMENT 'Ngày bảo trì gần nhất',
    nextMaintenanceDate DATE COMMENT 'Ngày bảo trì tiếp theo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ngày cập nhật'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu thông tin thiết bị';

-- 12. Bảng Maintenance Logs
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

-- 13. Bảng Announcements
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

-- 14. Bảng Promotions
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

-- 15. Bảng Schedules
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

-- 16. Bảng Remember Tokens
CREATE TABLE remember_tokens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY unique_token (token)
);

-- 17. Bảng Equipment Images
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

CREATE TRIGGER check_trainer_schedule_trigger
BEFORE INSERT ON training_sessions
FOR EACH ROW
BEGIN
    IF EXISTS (
        SELECT 1 FROM training_sessions
        WHERE ptRegistrationId = NEW.ptRegistrationId
        AND sessionDate = NEW.sessionDate
        AND status IN ('SCHEDULED', 'ACTIVE')
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Trainer already has a session at this time';
    END IF;
END $$
DELIMITER ;

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


-- Trigger kiểm tra lịch trình huấn luyện viên
CREATE TRIGGER check_trainer_availability
BEFORE INSERT ON trainer_schedules
FOR EACH ROW
BEGIN
    IF EXISTS (
        SELECT 1 FROM trainer_schedules
        WHERE trainerId = NEW.trainerId 
        AND sessionDate = NEW.sessionDate
        AND status != 'CANCELLED'
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Trainer already has session at this time';
    END IF;
END $$

-- Trigger kiểm tra ngày buổi tập
CREATE TRIGGER check_session_date_trigger
BEFORE INSERT ON training_sessions
FOR EACH ROW
BEGIN
    IF NEW.sessionDate < NOW() THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Session date must be in the future';
    END IF;
END $$

-- Trigger kiểm tra ngày đăng ký thành viên
CREATE TRIGGER check_membership_dates_trigger
BEFORE INSERT ON membership_registrations
FOR EACH ROW
BEGIN
    IF NEW.startDate > NEW.endDate THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Start date must be before or equal to end date';
    END IF;
END $$

-- Trigger kiểm tra lịch trình huấn luyện viên
CREATE TRIGGER check_trainer_schedule_trigger
BEFORE INSERT ON trainer_schedules
FOR EACH ROW
BEGIN
    IF EXISTS (
        SELECT 1 FROM trainer_schedules
        WHERE trainerId = NEW.trainerId 
        AND sessionDate = NEW.sessionDate
        AND status = 'SCHEDULED'
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Trainer already has session at this time';
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

-- 1. Add cascading deletes for user relationships
ALTER TABLE membership_registrations
DROP FOREIGN KEY membership_registrations_ibfk_1,
ADD CONSTRAINT membership_registrations_ibfk_1 
FOREIGN KEY (userId) REFERENCES users(id)
ON DELETE CASCADE;

ALTER TABLE pt_registrations 
DROP FOREIGN KEY pt_registrations_ibfk_1,
ADD CONSTRAINT pt_registrations_ibfk_1
FOREIGN KEY (userId) REFERENCES users(id)
ON DELETE CASCADE;

-- 2. Add trainer schedule validation check
CREATE TABLE trainer_schedules (
    id INT PRIMARY KEY AUTO_INCREMENT,
    trainerId INT NOT NULL,
    memberId INT NOT NULL, 
    sessionDate DATETIME NOT NULL,
    status ENUM('SCHEDULED','COMPLETED','CANCELLED') DEFAULT 'SCHEDULED',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trainerId) REFERENCES users(id),
    FOREIGN KEY (memberId) REFERENCES users(id),
    UNIQUE KEY unique_trainer_schedule (trainerId, sessionDate, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Add membership package validation
ALTER TABLE membership_registrations
ADD CONSTRAINT check_dates 
CHECK (startDate <= endDate);

-- 4. Add training session validation
ALTER TABLE training_sessions 
ADD CONSTRAINT check_session_dates
CHECK (sessionDate >= CURRENT_TIMESTAMP);

-- 1. Drop constraints first
ALTER TABLE training_sessions 
DROP CONSTRAINT IF EXISTS check_session_dates;

ALTER TABLE membership_registrations
DROP CONSTRAINT IF EXISTS check_dates;