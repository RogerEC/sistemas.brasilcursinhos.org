use brasilcursinhos;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='-03:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

/*!40101 SET character_set_client = utf8mb4 */;

DROP TABLE IF EXISTS `INTERVIEW_SCHEDULES`;
DROP TABLE IF EXISTS `INTERVIEW_TIMES`;
DROP TABLE IF EXISTS `CANDIDATES`;

CREATE TABLE IF NOT EXISTS `CANDIDATES` (
	`idCandidate` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(30) NOT NULL,
    `fullName` VARCHAR(90) NOT NULL,
    `cpf` VARCHAR(11) NOT NULL,
	`email` VARCHAR(128) NOT NULL,
    `phone` VARCHAR(11) NOT NULL,
    `createdAt` DATETIME NOT NULL,
    `updatedAt` DATETIME NOT NULL,
	PRIMARY KEY (`idCandidate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `CANDIDATES`(`name`, `fullName`, `cpf`, `email`, `phone`, `createdAt`, `updatedAt`) VALUES
('Roger', 'Roger Eliodoro Condras', '10118164902', 'rogereliodoro08@gmail.com', '47992071913', NOW(), NOW());

CREATE TABLE IF NOT EXISTS `INTERVIEW_TIMES` (
	`idInterviewTime` INT NOT NULL AUTO_INCREMENT,
	`datetime` DATETIME NOT NULL,
    `day` INT NOT NULL,
	`meet` VARCHAR(40) NOT NULL,
    `createdAt` DATETIME NOT NULL,
    `updatedAt` DATETIME NOT NULL,
	PRIMARY KEY (`idInterviewTime`),
    UNIQUE INDEX `idxDatetime` (`datetime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `INTERVIEW_TIMES`(`datetime`, `day`, `meet`, `createdAt`, `updatedAt`) VALUES
('2025-01-28 09:00:00', 3, 'https://meet.google.com/jwv-rcqm-vme', NOW(), NOW()),
('2025-01-28 10:00:00', 3, 'https://meet.google.com/pmg-bnas-tsc', NOW(), NOW()),
('2025-01-28 11:00:00', 3, 'https://meet.google.com/hbh-mbqm-von', NOW(), NOW()),
('2025-01-28 14:00:00', 3, 'https://meet.google.com/zyp-emhu-yfp', NOW(), NOW()),
('2025-01-28 15:00:00', 3, 'https://meet.google.com/omq-mkfw-ndv', NOW(), NOW()),
('2025-01-28 16:00:00', 3, 'https://meet.google.com/kuo-rowu-fze', NOW(), NOW()),
('2025-01-28 17:00:00', 3, 'https://meet.google.com/ogg-uxgo-qqm', NOW(), NOW()),
('2025-01-28 19:00:00', 3, 'https://meet.google.com/aqv-uojk-oyx', NOW(), NOW()),
('2025-01-28 20:00:00', 3, 'https://meet.google.com/sew-afee-foc', NOW(), NOW()),
('2025-01-28 21:00:00', 3, 'https://meet.google.com/dgc-snrd-enj', NOW(), NOW()),
('2025-01-29 09:00:00', 4, 'https://meet.google.com/jwv-rcqm-vme', NOW(), NOW()),
('2025-01-29 10:00:00', 4, 'https://meet.google.com/pmg-bnas-tsc', NOW(), NOW()),
('2025-01-29 11:00:00', 4, 'https://meet.google.com/hbh-mbqm-von', NOW(), NOW()),
('2025-01-29 14:00:00', 4, 'https://meet.google.com/zyp-emhu-yfp', NOW(), NOW()),
('2025-01-29 15:00:00', 4, 'https://meet.google.com/omq-mkfw-ndv', NOW(), NOW()),
('2025-01-29 16:00:00', 4, 'https://meet.google.com/kuo-rowu-fze', NOW(), NOW()),
('2025-01-29 17:00:00', 4, 'https://meet.google.com/ogg-uxgo-qqm', NOW(), NOW()),
('2025-01-29 19:00:00', 4, 'https://meet.google.com/aqv-uojk-oyx', NOW(), NOW()),
('2025-01-29 20:00:00', 4, 'https://meet.google.com/sew-afee-foc', NOW(), NOW()),
('2025-01-29 21:00:00', 4, 'https://meet.google.com/dgc-snrd-enj', NOW(), NOW()),
('2025-01-30 09:00:00', 5, 'https://meet.google.com/jwv-rcqm-vme', NOW(), NOW()),
('2025-01-30 10:00:00', 5, 'https://meet.google.com/pmg-bnas-tsc', NOW(), NOW()),
('2025-01-30 11:00:00', 5, 'https://meet.google.com/hbh-mbqm-von', NOW(), NOW()),
('2025-01-30 14:00:00', 5, 'https://meet.google.com/zyp-emhu-yfp', NOW(), NOW()),
('2025-01-30 15:00:00', 5, 'https://meet.google.com/omq-mkfw-ndv', NOW(), NOW()),
('2025-01-30 16:00:00', 5, 'https://meet.google.com/kuo-rowu-fze', NOW(), NOW()),
('2025-01-30 17:00:00', 5, 'https://meet.google.com/ogg-uxgo-qqm', NOW(), NOW()),
('2025-01-30 19:00:00', 5, 'https://meet.google.com/aqv-uojk-oyx', NOW(), NOW()),
('2025-01-30 20:00:00', 5, 'https://meet.google.com/sew-afee-foc', NOW(), NOW()),
('2025-01-30 21:00:00', 5, 'https://meet.google.com/dgc-snrd-enj', NOW(), NOW()),
('2025-01-31 09:00:00', 6, 'https://meet.google.com/jwv-rcqm-vme', NOW(), NOW()),
('2025-01-31 10:00:00', 6, 'https://meet.google.com/pmg-bnas-tsc', NOW(), NOW()),
('2025-01-31 11:00:00', 6, 'https://meet.google.com/hbh-mbqm-von', NOW(), NOW()),
('2025-01-31 14:00:00', 6, 'https://meet.google.com/zyp-emhu-yfp', NOW(), NOW()),
('2025-01-31 15:00:00', 6, 'https://meet.google.com/omq-mkfw-ndv', NOW(), NOW()),
('2025-01-31 16:00:00', 6, 'https://meet.google.com/kuo-rowu-fze', NOW(), NOW()),
('2025-01-31 17:00:00', 6, 'https://meet.google.com/ogg-uxgo-qqm', NOW(), NOW()),
('2025-01-31 19:00:00', 6, 'https://meet.google.com/aqv-uojk-oyx', NOW(), NOW()),
('2025-01-31 20:00:00', 6, 'https://meet.google.com/sew-afee-foc', NOW(), NOW()),
('2025-01-31 21:00:00', 6, 'https://meet.google.com/dgc-snrd-enj', NOW(), NOW()),
('2025-02-01 09:00:00', 7, 'https://meet.google.com/jwv-rcqm-vme', NOW(), NOW()),
('2025-02-01 10:00:00', 7, 'https://meet.google.com/pmg-bnas-tsc', NOW(), NOW()),
('2025-02-01 11:00:00', 7, 'https://meet.google.com/hbh-mbqm-von', NOW(), NOW()),
('2025-02-01 14:00:00', 7, 'https://meet.google.com/zyp-emhu-yfp', NOW(), NOW()),
('2025-02-01 15:00:00', 7, 'https://meet.google.com/omq-mkfw-ndv', NOW(), NOW()),
('2025-02-01 16:00:00', 7, 'https://meet.google.com/kuo-rowu-fze', NOW(), NOW()),
('2025-02-01 17:00:00', 7, 'https://meet.google.com/ogg-uxgo-qqm', NOW(), NOW()),
('2025-02-01 19:00:00', 7, 'https://meet.google.com/aqv-uojk-oyx', NOW(), NOW()),
('2025-02-01 20:00:00', 7, 'https://meet.google.com/sew-afee-foc', NOW(), NOW()),
('2025-02-01 21:00:00', 7, 'https://meet.google.com/dgc-snrd-enj', NOW(), NOW()),
('2025-02-03 09:00:00', 2, 'https://meet.google.com/jwv-rcqm-vme', NOW(), NOW()),
('2025-02-03 10:00:00', 2, 'https://meet.google.com/pmg-bnas-tsc', NOW(), NOW()),
('2025-02-03 11:00:00', 2, 'https://meet.google.com/hbh-mbqm-von', NOW(), NOW()),
('2025-02-03 14:00:00', 2, 'https://meet.google.com/zyp-emhu-yfp', NOW(), NOW()),
('2025-02-03 15:00:00', 2, 'https://meet.google.com/omq-mkfw-ndv', NOW(), NOW()),
('2025-02-03 16:00:00', 2, 'https://meet.google.com/kuo-rowu-fze', NOW(), NOW()),
('2025-02-03 17:00:00', 2, 'https://meet.google.com/ogg-uxgo-qqm', NOW(), NOW()),
('2025-02-03 19:00:00', 2, 'https://meet.google.com/aqv-uojk-oyx', NOW(), NOW()),
('2025-02-03 20:00:00', 2, 'https://meet.google.com/sew-afee-foc', NOW(), NOW()),
('2025-02-03 21:00:00', 2, 'https://meet.google.com/dgc-snrd-enj', NOW(), NOW()),
('2025-02-04 09:00:00', 3, 'https://meet.google.com/jwv-rcqm-vme', NOW(), NOW()),
('2025-02-04 10:00:00', 3, 'https://meet.google.com/pmg-bnas-tsc', NOW(), NOW()),
('2025-02-04 11:00:00', 3, 'https://meet.google.com/hbh-mbqm-von', NOW(), NOW()),
('2025-02-04 14:00:00', 3, 'https://meet.google.com/zyp-emhu-yfp', NOW(), NOW()),
('2025-02-04 15:00:00', 3, 'https://meet.google.com/omq-mkfw-ndv', NOW(), NOW()),
('2025-02-04 16:00:00', 3, 'https://meet.google.com/kuo-rowu-fze', NOW(), NOW()),
('2025-02-04 17:00:00', 3, 'https://meet.google.com/ogg-uxgo-qqm', NOW(), NOW()),
('2025-02-04 19:00:00', 3, 'https://meet.google.com/aqv-uojk-oyx', NOW(), NOW()),
('2025-02-04 20:00:00', 3, 'https://meet.google.com/sew-afee-foc', NOW(), NOW()),
('2025-02-04 21:00:00', 3, 'https://meet.google.com/dgc-snrd-enj', NOW(), NOW()),
('2025-02-05 09:00:00', 4, 'https://meet.google.com/jwv-rcqm-vme', NOW(), NOW()),
('2025-02-05 10:00:00', 4, 'https://meet.google.com/pmg-bnas-tsc', NOW(), NOW()),
('2025-02-05 11:00:00', 4, 'https://meet.google.com/hbh-mbqm-von', NOW(), NOW()),
('2025-02-05 14:00:00', 4, 'https://meet.google.com/zyp-emhu-yfp', NOW(), NOW()),
('2025-02-05 15:00:00', 4, 'https://meet.google.com/omq-mkfw-ndv', NOW(), NOW()),
('2025-02-05 16:00:00', 4, 'https://meet.google.com/kuo-rowu-fze', NOW(), NOW()),
('2025-02-05 17:00:00', 4, 'https://meet.google.com/ogg-uxgo-qqm', NOW(), NOW()),
('2025-02-05 19:00:00', 4, 'https://meet.google.com/aqv-uojk-oyx', NOW(), NOW()),
('2025-02-05 20:00:00', 4, 'https://meet.google.com/sew-afee-foc', NOW(), NOW()),
('2025-02-05 21:00:00', 4, 'https://meet.google.com/dgc-snrd-enj', NOW(), NOW()),
('2025-02-06 09:00:00', 5, 'https://meet.google.com/jwv-rcqm-vme', NOW(), NOW()),
('2025-02-06 10:00:00', 5, 'https://meet.google.com/pmg-bnas-tsc', NOW(), NOW()),
('2025-02-06 11:00:00', 5, 'https://meet.google.com/hbh-mbqm-von', NOW(), NOW()),
('2025-02-06 14:00:00', 5, 'https://meet.google.com/zyp-emhu-yfp', NOW(), NOW()),
('2025-02-06 15:00:00', 5, 'https://meet.google.com/omq-mkfw-ndv', NOW(), NOW()),
('2025-02-06 16:00:00', 5, 'https://meet.google.com/kuo-rowu-fze', NOW(), NOW()),
('2025-02-06 17:00:00', 5, 'https://meet.google.com/ogg-uxgo-qqm', NOW(), NOW()),
('2025-02-06 19:00:00', 5, 'https://meet.google.com/aqv-uojk-oyx', NOW(), NOW()),
('2025-02-06 20:00:00', 5, 'https://meet.google.com/sew-afee-foc', NOW(), NOW()),
('2025-02-06 21:00:00', 5, 'https://meet.google.com/dgc-snrd-enj', NOW(), NOW()),
('2025-02-07 09:00:00', 6, 'https://meet.google.com/jwv-rcqm-vme', NOW(), NOW()),
('2025-02-07 10:00:00', 6, 'https://meet.google.com/pmg-bnas-tsc', NOW(), NOW()),
('2025-02-07 11:00:00', 6, 'https://meet.google.com/hbh-mbqm-von', NOW(), NOW()),
('2025-02-07 14:00:00', 6, 'https://meet.google.com/zyp-emhu-yfp', NOW(), NOW()),
('2025-02-07 15:00:00', 6, 'https://meet.google.com/omq-mkfw-ndv', NOW(), NOW()),
('2025-02-07 16:00:00', 6, 'https://meet.google.com/kuo-rowu-fze', NOW(), NOW()),
('2025-02-07 17:00:00', 6, 'https://meet.google.com/ogg-uxgo-qqm', NOW(), NOW()),
('2025-02-07 19:00:00', 6, 'https://meet.google.com/aqv-uojk-oyx', NOW(), NOW()),
('2025-02-07 20:00:00', 6, 'https://meet.google.com/sew-afee-foc', NOW(), NOW()),
('2025-02-07 21:00:00', 6, 'https://meet.google.com/dgc-snrd-enj', NOW(), NOW());

CREATE TABLE IF NOT EXISTS `INTERVIEW_SCHEDULES` (
	`idInterviewSchedule` INT NOT NULL AUTO_INCREMENT,
	`idInterviewTime` INT NOT NULL,
    `idCandidate` INT NOT NULL, 
    `createdAt` DATETIME NOT NULL,
    `updatedAt` DATETIME NOT NULL,
	PRIMARY KEY (`idInterviewSchedule`),
    UNIQUE INDEX  `idxIdCandidate` (`idCandidate`),
    UNIQUE INDEX  `idxIdInterviewTime` (`idInterviewTime`),
    CONSTRAINT `fkInterviewSchedulesInterviewTimes` FOREIGN KEY (`idInterviewTime`) REFERENCES `INTERVIEW_TIMES`(`idInterviewTime`),
    CONSTRAINT `fkInterviewSchedulesCandiodates` FOREIGN KEY (`idCandidate`) REFERENCES `CANDIDATES`(`idCandidate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;



SELECT `

-- Consultas
SELECT it.`idInterviewTime` AS `id`, it.`datetime` AS `datetime` FROM `INTERVIEW_TIMES` it LEFT JOIN `INTERVIEW_SCHEDULES` isc ON (it.`idInterviewTime` = isc.`idInterviewTime`) WHERE isc.`idInterviewTime` IS NULL AND (`datetime` >= '2025-01-30' AND `datetime` < '2025-01-30' + INTERVAL 1 DAY) ORDER BY `datetime` ASC;


SELECT * FROM `INTERVIEW_TIMES` WHERE `datetime` >= '2025-01-30' AND `datetime` < '2025-01-30' + INTERVAL 1 DAY ORDER BY `datetime` ASC;


SELECT `idCandidate` AS `id`, `name`, `fullName`, `email`, `cpf`, `phone` FROM `CANDIDATES` WHERE `idCandidate` = 1 LIMIT 1





