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
DROP TABLE IF EXISTS `PRESENCE_IN_VOTINGS`;
DROP TABLE IF EXISTS `VOTINGS`;
DROP TABLE IF EXISTS `CUPS`;
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
INSERT INTO `STATUS` VALUES(9, 'A', 'Ativo', 'CUP', NOW(), NOW());
INSERT INTO `STATUS` VALUES(10, 'I', 'Inativo', 'CUP', NOW(), NOW());
INSERT INTO `STATUS` VALUES(11, 'S', 'Suspenso', 'CUP', NOW(), NOW());
INSERT INTO `STATUS` VALUES(12, 'D', 'Desligado', 'CUP', NOW(), NOW());
INSERT INTO `STATUS` VALUES(13, 'A', 'Aberta', 'VOTING', NOW(), NOW());
INSERT INTO `STATUS` VALUES(14, 'F', 'Fechada', 'VOTING', NOW(), NOW());
INSERT INTO `STATUS` VALUES(15, 'E', 'Encerrada', 'VOTING', NOW(), NOW());
INSERT INTO `STATUS` VALUES(16, 'A', 'Ativo', 'EVENT', NOW(), NOW());
INSERT INTO `STATUS` VALUES(17, 'I', 'Inativo', 'EVENT', NOW(), NOW());
INSERT INTO `STATUS` VALUES(18, 'E', 'Encerrado', 'EVENT', NOW(), NOW());

/*!40000 ALTER TABLE `STATUS` ENABLE KEYS */;
UNLOCK TABLES;

CREATE TABLE IF NOT EXISTS `USERS` (
    `idUser` INT NOT NULL AUTO_INCREMENT,
    `cpf` VARCHAR(11) NOT NULL,
    `email` VARCHAR(128) NOT NULL,
    `username` VARCHAR(32) NULL DEFAULT NULL,
    `registration` VARCHAR(8) NOT NULL,
    `passwordHash` VARCHAR(256) NOT NULL DEFAULT '$2y$10$w12meOM5Ktsn338I3mIoIuv9FP96oHwu9M2C4N1QRJ0LWFCZbSNsu',
    `idType` INT NOT NULL DEFAULT 2,
    `idStatus` INT NOT NULL DEFAULT 1,
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

CREATE TABLE IF NOT EXISTS `CUPS` (
    `idCup` INT NOT NULL AUTO_INCREMENT,
    `fullName` VARCHAR(128) NOT NULL,
    `shortName` VARCHAR(64) NOT NULL,
    `username` VARCHAR(32) NOT NULL,
    `idStatus` INT NOT NULL,
    `createdAt` DATETIME NOT NULL,
    `updatedAt` DATETIME NOT NULL,
    PRIMARY KEY (`idCup`),
    CONSTRAINT `fkCupsStatus` FOREIGN KEY (`idStatus`) REFERENCES `STATUS`(`idStatus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9
LOCK TABLES `CUPS` WRITE;
/*!40000 ALTER TABLE `CUPS` DISABLE KEYS */;
INSERT INTO `CUPS`(`idCup`, `fullName`, `shortName`, `username`, `idStatus`, `createdAt`, `updatedAt`) VALUES
(1, 'Brasil Cursinhos', 'Brasil Cursinhos', 'brasilcursinhos', 9, NOW(), NOW()),
(2, 'CASD', 'CASD', 'casd', 9, NOW(), NOW()),
(3, 'CATS', 'CATS', 'cats', 9, NOW(), NOW()),
(4, 'CPM - FMRP', 'CPM - FMRP', 'cpmfmrp', 9, NOW(), NOW()),
(5, 'Cursinho PES', 'Cursinho PES', 'cursinhopes', 9, NOW(), NOW()),
(6, 'Each USP', 'Each USP', 'eachusp', 9, NOW(), NOW()),
(7, 'Edificar', 'Edificar', 'edificar', 9, NOW(), NOW()),
(8, 'Einstein Floripa', 'Einstein Floripa', 'einsteinfloripa', 9, NOW(), NOW()),
(9, 'Face Educa', 'Face Educa', 'faceeduca', 9, NOW(), NOW()),
(10, 'FEA USP', 'FEA USP', 'feausp', 9, NOW(), NOW()),
(11, 'Flavi USP', 'Flavi USP', 'flaviusp', 9, NOW(), NOW()),
(12, 'Galt Vestibulares', 'Galt Vestibulares', 'galt', 9, NOW(), NOW()),
(13, 'Garra', 'Garra', 'garra', 9, NOW(), NOW()),
(14, 'GeraBixo', 'GeraBixo', 'gerabixo', 9, NOW(), NOW()),
(15, 'Hypatia', 'Hypatia', 'hypatia', 9, NOW(), NOW()),
(16, 'Insper', 'Insper', 'insper', 9, NOW(), NOW()),
(17, 'Iny Vestibulares', 'Iny Vestibulares', 'iny', 9, NOW(), NOW()),
(18, 'MarieCurie', 'MarieCurie', 'mariecurie', 9, NOW(), NOW()),
(19, 'MedAprova', 'MedAprova', 'medaprova', 9, NOW(), NOW()),
(20, 'Nubo', 'Nubo', 'nubo', 9, NOW(), NOW()),
(21, 'Paulo Freire', 'Paulo Freire', 'paulofreire', 9, NOW(), NOW()),
(22, 'Poli USP', 'Poli USP', 'poliusp', 9, NOW(), NOW()),
(23, 'PREVEC', 'PREVEC', 'prevec', 9, NOW(), NOW()),
(24, 'UDESC - Bauneário Camburiú', 'UDESC - Bauneário Camburiú', 'udescbc', 9, NOW(), NOW()),
(25, 'UDESC - Laguna', 'UDESC - Laguna', 'udesclaguna', 9, NOW(), NOW()),
(26, 'Vestibular Cidadão', 'Vestibular Cidadão', 'vestibularcidadao', 9, NOW(), NOW());
/*!40000 ALTER TABLE `CUPS` ENABLE KEYS */;
UNLOCK TABLES;

CREATE TABLE IF NOT EXISTS `VOTINGS` (
    `idVoting` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(128) NOT NULL,
    `description` TEXT,
    `code` VARCHAR(32) NOT NULL,
    `link` VARCHAR(128) NOT NULL,
    `datetime` DATETIME NOT NULL,
    `idStatus` INT NOT NULL,
    `createdAt` DATETIME NOT NULL,
    `updatedAt` DATETIME NOT NULL,
    PRIMARY KEY (`idVoting`),
    UNIQUE INDEX `idxVotingsCode` (`code`),
    UNIQUE INDEX `idxVotingsDatetime` (`datetime` DESC),
    CONSTRAINT `fkVotingsStatus` FOREIGN KEY (`idStatus`) REFERENCES `STATUS`(`idStatus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `VOTINGS` WRITE;
/*!40000 ALTER TABLE `VOTINGS` DISABLE KEYS */;
INSERT INTO `votings` (`idVoting`, `name`, `description`, `code`, `link`, `datetime`, `idStatus`, `createdAt`, `updatedAt`) VALUES
(1, 'Assembleia Geral - Brasil Cursinhos', 'Assembleia Geral para aprovação da proposta de alteração estatutária e votação da entrada de um novo CUP na rede.', 'LvMGy5GovumNEVzQToCu9hepJvaKnTOA', 'https://vote.heliosvoting.org/helios/elections/260b1a98-b34b-4f18-98fc-2dce8f3007d5/view', '2023-07-22 10:30:00', 13, NOW(), NOW());
/*!40000 ALTER TABLE `VOTINGS` ENABLE KEYS */;
UNLOCK TABLES;

CREATE TABLE IF NOT EXISTS `PRESENCE_IN_VOTINGS` (
    `idPresenceInVoting` INT NOT NULL AUTO_INCREMENT,
    `fullName` VARCHAR(64) NOT NULL,
    `cpf` VARCHAR(11) NOT NULL,
    `email` VARCHAR(128) NOT NULL,
    `role` VARCHAR(64) NOT NULL,
    `idCup` INT NOT NULL,
    `idVoting` INT NOT NULL,
    `createdAt` DATETIME NOT NULL,
    `updatedAt` DATETIME NOT NULL,
    PRIMARY KEY (`idPresenceInVoting`),
    CONSTRAINT `fkPresenceInVotingsCups` FOREIGN KEY (`idCup`) REFERENCES `CUPS`(`idCup`),
    CONSTRAINT `fkPresenceInVotingsVotings` FOREIGN KEY (`idVoting`) REFERENCES `VOTINGS`(`idVoting`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `VOTINGS` WRITE;
/*!40000 ALTER TABLE `VOTINGS` DISABLE KEYS */;
/*!40000 ALTER TABLE `VOTINGS` ENABLE KEYS */;
UNLOCK TABLES;

CREATE TABLE IF NOT EXISTS `EVENTS` (
	`idEvent` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(64) NOT NULL,
	`startDate` DATE NOT NULL,
    `finalDate` DATE NOT NULL,
    `idStatus` INT UNIQUE NOT NULL,
    `createdAt` DATETIME NOT NULL,
    `updatedAt` DATETIME NOT NULL,
	PRIMARY KEY (`idEvent`),
    CONSTRAINT `fkEventsIdStatus` FOREIGN KEY (`idStatus`) REFERENCES `STATUS`(`idStatus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `EVENT_ACTIVITIES` (
	`idEventActivity` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(64) NOT NULL,
    `local` VARCHAR(64) NOT NULL,
	`startDatetime` DATETIME NOT NULL,
    `finalDatetime` DATETIME NOT NULL,
    `createdAt` DATETIME NOT NULL,
    `updatedAt` DATETIME NOT NULL,
    -- `idEvent` INT UNIQUE NOT NULL,
	PRIMARY KEY (`idEventActivity`)
    -- CONSTRAINT `fkEventActivitiesIdEvent` FOREIGN KEY (`idEvent`) REFERENCES `EVENTS`(`idEvent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `EVENT_PARTICIPANTS` (
	`idEventParticipant` INT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(64) NOT NULL,
    `cpf` VARCHAR(11) NOT NULL,
    -- `eventRegistration` VARCHAR(8) NOT NULL,
	-- `email` VARCHAR(128) UNIQUE NOT NULL,
    -- `phone` VARCHAR(16) NOT NULL,
    `cup` VARCHAR(64) NOT NULL,
    `code` VARCHAR(16) NULL DEFAULT NULL,
    `createdAt` DATETIME NOT NULL,
    `updatedAt` DATETIME NOT NULL,
	PRIMARY KEY (`idEventParticipant`),
    UNIQUE INDEX `idxCpf` (`cpf`),
    UNIQUE INDEX `idxEventCode` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
ALTER TABLE `EVENT_PARTICIPANTS` AUTO_INCREMENT=101;

CREATE TABLE IF NOT EXISTS `PRESENCE_IN_ACTIVITIES` (
	`idPresenceInActivity` INT NOT NULL AUTO_INCREMENT,
	`idEventActivity` INT NOT NULL,
    `idEventParticipant` INT NOT NULL,
    `idUser` INT NOT NULL,
    `createdAt` DATETIME NOT NULL,
    `updatedAt` DATETIME NOT NULL,
	PRIMARY KEY (`idPresenceInActivity`),
    CONSTRAINT `fkPresenceInActivitiesIdEventActivity` FOREIGN KEY (`idEventActivity`) REFERENCES `EVENT_ACTIVITIES`(`idEventActivity`),
    CONSTRAINT `fkPresenceInActivitiesIdEventParticipant` FOREIGN KEY (`idEventParticipant`) REFERENCES `EVENT_PARTICIPANTS`(`idEventParticipant`),
    CONSTRAINT `fkPresenceInActivitiesIdUser` FOREIGN KEY (`idUser`) REFERENCES `USERS`(`idUser`),
    UNIQUE INDEX  `idxActivityParticipant` (`idEventActivity`, `idEventParticipant`)
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