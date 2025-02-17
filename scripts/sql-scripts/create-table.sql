/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Exportiere Struktur von Tabelle d041e0f6.energy_price
CREATE TABLE IF NOT EXISTS `energy_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp_from` datetime NOT NULL,
  `timestamp_to` datetime NOT NULL,
  `out_cent_price_per_wh` decimal(8,6) NOT NULL DEFAULT 0.000000,
  `in_cent_price_per_wh` decimal(8,6) NOT NULL DEFAULT 0.000000,
  `custom_value` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `timefrom_timeto` (`timestamp_from`,`timestamp_to`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle d041e0f6.hourly_energy_data
CREATE TABLE IF NOT EXISTS `hourly_energy_data` (
  `timestamp_from` datetime NOT NULL,
  `timestamp_to` datetime NOT NULL,
  `value_for_x_quarter_hours` smallint(6) NOT NULL DEFAULT 0,
  `out_cent_price_per_wh` decimal(8,6) DEFAULT NULL,
  `in_cent_price_per_wh` decimal(8,6) DEFAULT NULL,
  `em_total_power` decimal(9,2) DEFAULT NULL,
  `em_total_over_zero` decimal(9,2) DEFAULT NULL,
  `em_total_under_zero` decimal(7,2) DEFAULT NULL,
  `pm1_total_power` decimal(9,2) DEFAULT NULL,
  `pm2_total_power` decimal(9,2) DEFAULT NULL,
  `pm3_total_power` decimal(9,2) DEFAULT NULL,
  `em_missing_rows` int(11) DEFAULT NULL,
  `pm1_missing_rows` int(11) DEFAULT NULL,
  `pm2_missing_rows` int(11) DEFAULT NULL,
  `pm3_missing_rows` int(11) DEFAULT NULL,
  `count_rows` int(11) DEFAULT NULL,
  `custom_value` tinyint(1) DEFAULT 0,
	PRIMARY KEY (`timestamp_from`) USING BTREE,
	INDEX `timestamp_em_total_power` (`timestamp_from`, `em_total_power`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- Daten-Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle d041e0f6.real_time_energy_data
CREATE TABLE IF NOT EXISTS `real_time_energy_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` datetime DEFAULT NULL,
  `interval_in_seconds` tinyint(4) DEFAULT NULL,
  `em_total_power` decimal(7,2) DEFAULT NULL,
  `pm1_total_power` decimal(7,2) DEFAULT NULL,
  `pm2_total_power` decimal(7,2) DEFAULT NULL,
  `pm3_total_power` decimal(7,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_timestamp_data` (`timestamp`) USING BTREE,
  KEY `idx_timestamp_em_total_power` (`timestamp`,`em_total_power`) USING BTREE,
  KEY `idx_timestamp_pm1_total_power` (`timestamp`,`pm1_total_power`) USING BTREE,
  KEY `idx_timestamp_pm2_total_power` (`timestamp`,`pm2_total_power`) USING BTREE,
  KEY `idx_timestamp_pm3_total_power` (`timestamp`,`pm3_total_power`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `key_value_store` (
	`scope` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb3_general_ci',
	`store_key` VARCHAR(50) NULL DEFAULT NULL COLLATE 'utf8mb3_general_ci',
	`value` VARCHAR(250) NULL DEFAULT NULL COLLATE 'utf8mb3_general_ci',
	`json_data` MEDIUMTEXT NULL DEFAULT NULL COLLATE 'utf8mb3_general_ci',
	`notice` MEDIUMTEXT NULL DEFAULT NULL COLLATE 'utf8mb3_general_ci',
	`updated` DATETIME NULL DEFAULT NULL,
	`inserted` DATETIME NULL DEFAULT NULL,
	UNIQUE INDEX `scope_key_unique` (`scope`, `store_key`) USING BTREE,
	INDEX `key` (`store_key`) USING BTREE
) COLLATE='utf8mb3_general_ci' ENGINE=InnoDB
;

-- Daten-Export vom Benutzer nicht ausgewählt

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
