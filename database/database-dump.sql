use brasilcursinhos;
-- Sanvando estados das variáveis do sistema antes da criação das tabelas
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

-- DUMP TABELAS DO BANCO

-- Excluí as tabelas caso já existam
DROP TABLE IF EXISTS `PRESENCE_IN_ACTIVITIES`;
DROP TABLE IF EXISTS `EVENT_ACTIVITIES`;
DROP TABLE IF EXISTS `EVENT_PARTICIPANTS`;
DROP TABLE IF EXISTS `EVENTS`;
DROP TABLE IF EXISTS `PERSONAL_INFORMATIONS`;
DROP TABLE IF EXISTS `VERIFICATION_CODES`;
DROP TABLE IF EXISTS `USERS`;
DROP TABLE IF EXISTS `STATUS`;
DROP TABLE IF EXISTS `TYPES`;

-- Define o charset padrão para UTF-8 de 4 bytes
/*!40101 SET character_set_client = utf8mb4 */;

-- Criação das tabelas

CREATE TABLE IF NOT EXISTS `TYPES` (
    `idType` INT NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(5) NOT NULL,
    `name` VARCHAR(32) NOT NULL,
    `role` VARCHAR(32) NOT NULL,
    `createdAt` DATETIME NOT NULL,
    `updatedAt` DATETIME NOT NULL,
    PRIMARY KEY (`idType`),
    UNIQUE INDEX  `idxTypesRoleCode` (`role`, `code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `TYPES` WRITE;
/*!40000 ALTER TABLE `TYPES` DISABLE KEYS */;
INSERT INTO `TYPES` VALUES(1, 'ADMIN', 'Administrador', 'USER', NOW(), NOW());
INSERT INTO `TYPES` VALUES(2, 'U', 'Usuário', 'USER', NOW(), NOW());
/*!40000 ALTER TABLE `TYPES` ENABLE KEYS */;
UNLOCK TABLES;

CREATE TABLE IF NOT EXISTS `STATUS` (
    `idStatus` INT NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(5) NOT NULL,
    `name` VARCHAR(36) NOT NULL,
    `role` VARCHAR(32) NOT NULL,
    `createdAt` DATETIME NOT NULL,
    `updatedAt` DATETIME NOT NULL,
    PRIMARY KEY (`idStatus`),
    UNIQUE INDEX  `idxStatusRoleCode` (`role`, `code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `STATUS` WRITE;
/*!40000 ALTER TABLE `STATUS` DISABLE KEYS */;
INSERT INTO `STATUS` VALUES(1, 'A', 'Ativo', 'USER', NOW(), NOW());
INSERT INTO `STATUS` VALUES(2, 'I', 'Inativo', 'USER', NOW(), NOW());
INSERT INTO `STATUS` VALUES(3, 'B', 'Bloqueado', 'USER', NOW(), NOW());
INSERT INTO `STATUS` VALUES(4, 'D', 'Desligado', 'USER', NOW(), NOW());
INSERT INTO `STATUS` VALUES(5, 'E', 'Enviado', 'VERIFICATION_CODE', NOW(), NOW());
INSERT INTO `STATUS` VALUES(6, 'C', 'Confirmado', 'VERIFICATION_CODE', NOW(), NOW());
INSERT INTO `STATUS` VALUES(7, 'EX', 'Expirado', 'VERIFICATION_CODE', NOW(), NOW());
INSERT INTO `STATUS` VALUES(8, 'NE', 'Não Enviado', 'VERIFICATION_CODE', NOW(), NOW());
INSERT INTO `STATUS` VALUES(9, 'A', 'Ativo', 'EVENT', NOW(), NOW());
INSERT INTO `STATUS` VALUES(10, 'I', 'Inativo', 'EVENT', NOW(), NOW());
INSERT INTO `STATUS` VALUES(11, 'E', 'Encerrado', 'EVENT', NOW(), NOW());
/*!40000 ALTER TABLE `STATUS` ENABLE KEYS */;
UNLOCK TABLES;

CREATE TABLE IF NOT EXISTS `USERS` (
    `idUser` INT NOT NULL AUTO_INCREMENT,
    `cpf` VARCHAR(11) NOT NULL,
    `email` VARCHAR(128) NOT NULL,
    `username` VARCHAR(32) NULL DEFAULT NULL,
    `registration` VARCHAR(8) NOT NULL,
    `passwordHash` VARCHAR(256) NOT NULL,
    `idType` INT NOT NULL,
    `idStatus` INT NOT NULL,
    `accessToken` VARCHAR(64) NULL DEFAULT NULL,
    `tokenDatetime` DATETIME NULL DEFAULT NULL,
    `lastAccess` DATETIME NULL DEFAULT NULL,
    `lastIpAddress` VARBINARY(16) NULL DEFAULT NULL,
    `accessAttempts` INT NOT NULL DEFAULT 0,
    `createdAt` DATETIME NOT NULL,
    `updatedAt` DATETIME NOT NULL,
    PRIMARY KEY (`idUser`),
    UNIQUE INDEX `idxCpf` (`cpf`),
    UNIQUE INDEX `idxEmail` (`email`),
    UNIQUE INDEX `idxUsername` (`username`),
    UNIQUE INDEX `idxRegistration` (`registration`),
    CONSTRAINT `fkUsersIdType` FOREIGN KEY (`idType`) REFERENCES `TYPES`(`idType`),
    CONSTRAINT `fkUsersIdStatus` FOREIGN KEY (`idStatus`) REFERENCES `STATUS`(`idStatus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `VERIFICATION_CODES` (
    `idVerificationCode` INT NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(128) NOT NULL,
    `sentAt` DATETIME NOT NULL,
    `confirmedAt` DATETIME NULL DEFAULT NULL,
    `idStatus` INT NOT NULL,
    `idUser` INT NOT NULL,
    PRIMARY KEY (`idVerificationCode`),
    CONSTRAINT `fkVerificationCodesIdStatus` FOREIGN KEY (`idStatus`) REFERENCES `STATUS`(`idStatus`),
    CONSTRAINT `fkVerificationCodesIdUser` FOREIGN KEY (`idUser`) REFERENCES `USERS`(`idUser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `VERIFICATION_CODES` WRITE;
/*!40000 ALTER TABLE `VERIFICATION_CODES` DISABLE KEYS */;
/*!40000 ALTER TABLE `VERIFICATION_CODES` ENABLE KEYS */;
UNLOCK TABLES;

CREATE TABLE IF NOT EXISTS `PERSONAL_INFORMATIONS` (
	`idPersonalInformation` INT NOT NULL AUTO_INCREMENT,
	`firstName` VARCHAR(64) NOT NULL,
	`lastName` VARCHAR(64) NOT NULL,
    `birthDate` DATE NULL DEFAULT NULL,
    `phone` VARCHAR(16) NULL DEFAULT NULL,
    `createdAt` DATETIME NOT NULL,
    `updatedAt` DATETIME NOT NULL,
    `idUser` INT UNIQUE NOT NULL,
	PRIMARY KEY (`idPersonalInformation`),
    CONSTRAINT `fkPersonalInformationIdUser` FOREIGN KEY (`idUser`) REFERENCES `USERS`(`idUser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `EVENTS` (
	`idEvent` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(64) NOT NULL,
	`startDate` DATE NOT NULL,
    `finalDate` DATE NOT NULL,
    `idStatus` INT UNIQUE NOT NULL,
    `createdAt` DATETIME NOT NULL,
    `updatedAt` DATETIME NOT NULL,
	PRIMARY KEY (`idEvent`),
    CONSTRAINT `fkEventsIdStatus` FOREIGN KEY (`idStatus`) REFERENCES `USERS`(`idStatus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `EVENT_ACTIVITIES` (
	`idEventActivity` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(64) NOT NULL,
    `local` VARCHAR(65) NOT NULL,
	`startDatetime` DATETIME NOT NULL,
    `finalDatetime` DATETIME NOT NULL,
    `createdAt` DATETIME NOT NULL,
    `updatedAt` DATETIME NOT NULL,
    `idEvent` INT UNIQUE NOT NULL,
	PRIMARY KEY (`idEventActivity`),
    CONSTRAINT `fkEventActivitiesIdEvent` FOREIGN KEY (`idEvent`) REFERENCES `EVENTS`(`idEvent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `EVENT_PARTICIPANTS` (
	`idEventParticipant` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(64) NOT NULL,
    `cpf` VARCHAR(11) NOT NULL,
    `eventRegistration` VARCHAR(8) NOT NULL,
	`email` VARCHAR(128) UNIQUE NOT NULL,
    `phone` VARCHAR(16) NOT NULL,
    `cup` VARCHAR(64) NOT NULL,
    `eventCode` VARCHAR(16) NULL DEFAULT NULL,
    `createdAt` DATETIME NOT NULL,
    `updatedAt` DATETIME NOT NULL,
	PRIMARY KEY (`idEventParticipant`),
    UNIQUE INDEX `idxCpf` (`cpf`),
    UNIQUE INDEX `idxEventCode` (`eventCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `PRESENCE_IN_ACTIVITIES` (
	`idPresenceInActivity` INT NOT NULL AUTO_INCREMENT,
	`idEventActivity` INT NOT NULL,
    `idEventParticipant` INT NOT NULL,
    `idUser` INT NOT NULL,
    `createdAt` DATETIME NOT NULL,
	PRIMARY KEY (`idPresenceInActivity`),
    CONSTRAINT `fkPresenceInActivitiesIdEventActivity` FOREIGN KEY (`idEventActivity`) REFERENCES `EVENT_ACTIVITIES`(`idEventActivity`),
    CONSTRAINT `fkPresenceInActivitiesIdEventParticipant` FOREIGN KEY (`idEventParticipant`) REFERENCES `EVENT_PARTICIPANTS`(`idEventParticipant`),
    CONSTRAINT `fkPresenceInActivitiesIdUser` FOREIGN KEY (`idUser`) REFERENCES `USERS`(`idUser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Restaura as variáveis originais do sistema
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;