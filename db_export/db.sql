-- Database schema and sample data for the grade management application.
-- Sample account password for all users: 123456@
-- Passwords are stored as bcrypt hashes (PHP PASSWORD_BCRYPT).

CREATE DATABASE IF NOT EXISTS `db_quanlydiemdoan`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `db_quanlydiemdoan`;

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `sinhvien` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `ma_sv` VARCHAR(20) NOT NULL,
    `ho_ten` VARCHAR(100) NOT NULL,
    `ngay_sinh` DATE NOT NULL,
    `lop` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_sinhvien_ma_sv` (`ma_sv`)
) ENGINE=InnoDB
  DEFAULT CHARACTER SET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `monhoc` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `ma_mh` VARCHAR(20) NOT NULL,
    `ten_mh` VARCHAR(100) NOT NULL,
    `so_tin_chi` INT NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_monhoc_ma_mh` (`ma_mh`)
) ENGINE=InnoDB
  DEFAULT CHARACTER SET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `diem` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `sinhvien_id` INT NOT NULL,
    `monhoc_id` INT NOT NULL,
    `diem_giua_ky` FLOAT NOT NULL,
    `diem_cuoi_ky` FLOAT NOT NULL,
    `diem_tong_ket` FLOAT NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_diem_sinhvien_monhoc` (`sinhvien_id`, `monhoc_id`),
    CONSTRAINT `fk_diem_sinhvien`
        FOREIGN KEY (`sinhvien_id`) REFERENCES `sinhvien` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `fk_diem_monhoc`
        FOREIGN KEY (`monhoc_id`) REFERENCES `monhoc` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARACTER SET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `taikhoan` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('giang_vien', 'sinh_vien') NOT NULL,
    `sinhvien_id` INT NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_taikhoan_username` (`username`),
    UNIQUE KEY `uq_taikhoan_sinhvien` (`sinhvien_id`),
    CONSTRAINT `chk_taikhoan_role_sinhvien`
        CHECK (
            (`role` = 'giang_vien' AND `sinhvien_id` IS NULL)
            OR (`role` = 'sinh_vien' AND `sinhvien_id` IS NOT NULL)
        ),
    CONSTRAINT `fk_taikhoan_sinhvien`
        FOREIGN KEY (`sinhvien_id`) REFERENCES `sinhvien` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARACTER SET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;

-- Sample students.
INSERT INTO `sinhvien` (`id`, `ma_sv`, `ho_ten`, `ngay_sinh`, `lop`) VALUES
    (1, 'SV001', 'Nguyễn Quốc Bảo', '2005-03-16', '74DCTT27'),
    (2, 'SV002', 'Nguyễn Bá Duy Phương', '2005-04-28', '74DCTT27')
ON DUPLICATE KEY UPDATE
    `ma_sv` = VALUES(`ma_sv`),
    `ho_ten` = VALUES(`ho_ten`),
    `ngay_sinh` = VALUES(`ngay_sinh`),
    `lop` = VALUES(`lop`);

-- Sample courses.
INSERT INTO `monhoc` (`id`, `ma_mh`, `ten_mh`, `so_tin_chi`) VALUES
    (1, 'MH001', 'Cơ sở dữ liệu', 3),
    (2, 'MH002', 'Lập trình PHP', 3),
    (3, 'MH003', 'Cấu trúc dữ liệu và giải thuật', 4)
ON DUPLICATE KEY UPDATE
    `ma_mh` = VALUES(`ma_mh`),
    `ten_mh` = VALUES(`ten_mh`),
    `so_tin_chi` = VALUES(`so_tin_chi`);

-- Sample grades: final grade = midterm * 0.4 + final exam * 0.6.
INSERT INTO `diem` (`id`, `sinhvien_id`, `monhoc_id`, `diem_giua_ky`, `diem_cuoi_ky`, `diem_tong_ket`) VALUES
    (1, 1, 1, 8.0, 8.5, 8.3),
    (2, 1, 2, 7.5, 8.0, 7.8),
    (3, 2, 1, 9.0, 8.0, 8.4),
    (4, 2, 3, 7.0, 8.5, 7.9)
ON DUPLICATE KEY UPDATE
    `sinhvien_id` = VALUES(`sinhvien_id`),
    `monhoc_id` = VALUES(`monhoc_id`),
    `diem_giua_ky` = VALUES(`diem_giua_ky`),
    `diem_cuoi_ky` = VALUES(`diem_cuoi_ky`),
    `diem_tong_ket` = VALUES(`diem_tong_ket`);

-- The bcrypt hash below is generated with PHP PASSWORD_BCRYPT for "123456@".
-- Sample credentials: admin / 123456@, sv001 / 123456@, sv002 / 123456@.
INSERT INTO `taikhoan` (`id`, `username`, `password`, `role`, `sinhvien_id`) VALUES
    (1, 'admin', '$2y$10$nVUG19VMnA9LCQBA33yCEO1oT9uHzurh23ndv3mEk/crEMOhY2cMS', 'giang_vien', NULL),
    (2, 'sv001', '$2y$10$nVUG19VMnA9LCQBA33yCEO1oT9uHzurh23ndv3mEk/crEMOhY2cMS', 'sinh_vien', 1),
    (3, 'sv002', '$2y$10$nVUG19VMnA9LCQBA33yCEO1oT9uHzurh23ndv3mEk/crEMOhY2cMS', 'sinh_vien', 2)
ON DUPLICATE KEY UPDATE
    `username` = VALUES(`username`),
    `password` = VALUES(`password`),
    `role` = VALUES(`role`),
    `sinhvien_id` = VALUES(`sinhvien_id`);