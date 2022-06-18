-- Adminer 4.8.1 MySQL 5.5.5-10.1.48-MariaDB-0+deb9u2 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP DATABASE IF EXISTS `cooking`;
CREATE DATABASE `cooking` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `cooking`;

DELIMITER ;;

CREATE EVENT `Sitzungsbereinigung Admin` ON SCHEDULE EVERY 1 HOUR STARTS '2022-03-01 00:00:00' ON COMPLETION NOT PRESERVE ENABLE COMMENT 'Löscht abgelaufene Administratorsitzungen nach sechs Wochen' DO DELETE FROM `sessions` WHERE `lastActivity` < DATE_SUB(NOW(), INTERVAL 6 WEEK);;

DELIMITER ;

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Username',
  `password` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Passwordhash',
  `salt` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Passwordsalt',
  `lastActivity` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Aktivität',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Administratorzugänge';

TRUNCATE `accounts`;

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Angezeigter Titel',
  `shortTitle` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Kurzer Titel für die URL',
  `sortIndex` int(10) unsigned NOT NULL DEFAULT '9999999' COMMENT 'Sortierindex',
  `description` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Kurzbeschreibung',
  PRIMARY KEY (`id`),
  UNIQUE KEY `shortTitle` (`shortTitle`),
  KEY `sortIndex` (`sortIndex`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Kategorien';

TRUNCATE `categories`;

DROP TABLE IF EXISTS `categoryItems`;
CREATE TABLE `categoryItems` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `categoryId` int(10) unsigned NOT NULL COMMENT 'Querverweis categories.id',
  `itemId` int(10) unsigned NOT NULL COMMENT 'Querverweis items.id',
  `sortIndex` int(10) unsigned NOT NULL COMMENT 'Sortierindex',
  PRIMARY KEY (`id`),
  UNIQUE KEY `categoryId_itemId` (`categoryId`,`itemId`),
  KEY `itemId` (`itemId`),
  CONSTRAINT `categoryItems_ibfk_1` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `categoryItems_ibfk_2` FOREIGN KEY (`itemId`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Querverweistabelle items/categories';

TRUNCATE `categoryItems`;

DROP TABLE IF EXISTS `clicks`;
CREATE TABLE `clicks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `itemId` int(10) unsigned NOT NULL COMMENT 'Querverweis items.id',
  `uuid` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique-User-Identifier',
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt des Zugriffs',
  PRIMARY KEY (`id`),
  KEY `itemId` (`itemId`),
  KEY `uuid` (`uuid`),
  KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Aufrufe eines Rezepts';

TRUNCATE `clicks`;

DROP TABLE IF EXISTS `featured`;
CREATE TABLE `featured` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `itemId` int(10) unsigned NOT NULL COMMENT 'Querverweis items.id',
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt des Eintrages',
  PRIMARY KEY (`id`),
  KEY `itemId` (`itemId`),
  KEY `timestamp` (`timestamp`),
  CONSTRAINT `featured_ibfk_1` FOREIGN KEY (`itemId`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Vorgestellte Rezepte auf der Startseite';

TRUNCATE `featured`;

DELIMITER ;;

CREATE TRIGGER `featured-logTrigger` AFTER DELETE ON `featured` FOR EACH ROW
INSERT INTO `log` (`logLevel`, `itemId`, `text`) VALUES(1, OLD.`itemId`, CONCAT("Featured Eintrag gelöscht."));;

DELIMITER ;

DROP TABLE IF EXISTS `images`;
CREATE TABLE `images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `itemId` int(10) unsigned NOT NULL COMMENT 'Querverweis items.id',
  `sortIndex` int(10) unsigned NOT NULL DEFAULT '9999999' COMMENT 'Sortierindex',
  `thumb` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Bild = 0; Thumbnail = 1',
  `fileHash` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unikat-Hash für Dateinamen',
  `description` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Beschreibung des Bildes',
  PRIMARY KEY (`id`),
  UNIQUE KEY `fileHash` (`fileHash`),
  KEY `itemId` (`itemId`),
  KEY `thumb` (`thumb`),
  CONSTRAINT `images_ibfk_1` FOREIGN KEY (`itemId`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bilddaten für Rezepte';

TRUNCATE `images`;

DROP TABLE IF EXISTS `itemIngredients`;
CREATE TABLE `itemIngredients` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `itemId` int(10) unsigned NOT NULL COMMENT 'Querverweis items.id',
  `ingredientId` int(10) unsigned NOT NULL COMMENT 'Querverweis metaIngredients.id',
  `unitId` int(10) unsigned DEFAULT NULL COMMENT 'Querverweis metaUnits.id',
  `quantity` double(10,2) unsigned DEFAULT NULL COMMENT 'Menge der Einheit',
  `optional` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1=optional;0=erforderlich',
  PRIMARY KEY (`id`),
  UNIQUE KEY `itemId_ingredientId` (`itemId`,`ingredientId`),
  KEY `ingredientId` (`ingredientId`),
  KEY `unitId` (`unitId`),
  CONSTRAINT `itemIngredients_ibfk_1` FOREIGN KEY (`itemId`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `itemIngredients_ibfk_2` FOREIGN KEY (`ingredientId`) REFERENCES `metaIngredients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `itemIngredients_ibfk_3` FOREIGN KEY (`unitId`) REFERENCES `metaUnits` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Querverweistabelle items/metaIngredients/metaUnits';

TRUNCATE `itemIngredients`;

DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Angezeigter Titel',
  `shortTitle` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Kurzer Titel für die URL',
  `author` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Autor für Gastbeiträge',
  `authorURL` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Link zum Autor des Gastbeitrages',
  `text` mediumtext COLLATE utf8mb4_unicode_ci COMMENT 'Text des Rezepts',
  `persons` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'Ausgelegt für ... Personen',
  `cost` tinyint(3) unsigned NOT NULL COMMENT 'Querverweis metaCost.id',
  `difficulty` tinyint(3) unsigned NOT NULL COMMENT 'Querverweis metaDifficulty.id',
  `workDuration` tinyint(3) unsigned NOT NULL COMMENT 'Querverweis metaDuration.id',
  `totalDuration` tinyint(3) unsigned NOT NULL COMMENT 'Querverweis metaDuration.id',
  `createdAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der Anlage',
  `lastChanged` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Änderung',
  PRIMARY KEY (`id`),
  UNIQUE KEY `shortTitle` (`shortTitle`),
  KEY `persons` (`persons`),
  KEY `cost` (`cost`),
  KEY `difficulty` (`difficulty`),
  KEY `workDuration` (`workDuration`),
  KEY `totalDuration` (`totalDuration`),
  KEY `author` (`author`),
  CONSTRAINT `items_ibfk_2` FOREIGN KEY (`cost`) REFERENCES `metaCost` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `items_ibfk_3` FOREIGN KEY (`difficulty`) REFERENCES `metaDifficulty` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `items_ibfk_4` FOREIGN KEY (`workDuration`) REFERENCES `metaDuration` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `items_ibfk_5` FOREIGN KEY (`totalDuration`) REFERENCES `metaDuration` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Rezepte';

TRUNCATE `items`;

DROP TABLE IF EXISTS `log`;
CREATE TABLE `log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `accountId` int(10) unsigned DEFAULT NULL COMMENT 'Querverweis accounts.id oder NULL bei Systemaktion',
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt des Eintrages',
  `logLevel` int(10) unsigned NOT NULL COMMENT 'Querverweis logLevel.id',
  `itemId` int(10) unsigned DEFAULT NULL COMMENT 'Querverweis items.id oder NULL bei Systemaktion oder Irrelevanz',
  `categoryId` int(10) unsigned DEFAULT NULL COMMENT 'Querverweis categories.id oder NULL bei Systemaktion oder Irrelevanz',
  `text` text COLLATE utf8mb4_unicode_ci COMMENT 'Logtext (optional)',
  PRIMARY KEY (`id`),
  KEY `accountId` (`accountId`),
  KEY `logLevel` (`logLevel`),
  KEY `categoryId` (`categoryId`),
  KEY `itemId` (`itemId`),
  CONSTRAINT `log_ibfk_3` FOREIGN KEY (`accountId`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `log_ibfk_5` FOREIGN KEY (`logLevel`) REFERENCES `logLevel` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `log_ibfk_6` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `log_ibfk_7` FOREIGN KEY (`itemId`) REFERENCES `items` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Administratorlog';

TRUNCATE `log`;

DROP TABLE IF EXISTS `logLevel`;
CREATE TABLE `logLevel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Meldungsart',
  `color` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'HexCode der Meldungsfarbe',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='logLevel Einträge für das adminLog';

TRUNCATE `logLevel`;
INSERT INTO `logLevel` (`id`, `title`, `color`) VALUES
(1,	'User-/Systemaktion',	'888888'),
(2,	'Neuanlage',	'e108e9'),
(3,	'Bearbeitung',	'ff9900'),
(4,	'Löschung',	'c52b2f'),
(5,	'Sortierung',	'bfbc06'),
(6,	'Zuweisung',	'008fff');

DROP TABLE IF EXISTS `metaCost`;
CREATE TABLE `metaCost` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Bezeichnung',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Metadaten: Kosten des Rezepts';

TRUNCATE `metaCost`;
INSERT INTO `metaCost` (`id`, `title`) VALUES
(1,	'günstig'),
(2,	'mittel'),
(3,	'teuer');

DROP TABLE IF EXISTS `metaDifficulty`;
CREATE TABLE `metaDifficulty` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Bezeichnung',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Metadaten: Schwierigkeit des Rezepts';

TRUNCATE `metaDifficulty`;
INSERT INTO `metaDifficulty` (`id`, `title`) VALUES
(1,	'Anfänger'),
(2,	'Fortgeschrittener'),
(3,	'Profi'),
(4,	'Experte');

DROP TABLE IF EXISTS `metaDuration`;
CREATE TABLE `metaDuration` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Bezeichnung',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Metadaten: Dauer des Rezepts';

TRUNCATE `metaDuration`;
INSERT INTO `metaDuration` (`id`, `title`) VALUES
(1,	'bis zu 10 Minuten'),
(2,	'10 bis 15 Minuten'),
(3,	'15 bis 30 Minuten'),
(4,	'30 bis 60 Minuten'),
(5,	'60 bis 90 Minuten'),
(6,	'länger als 90 Minuten');

DROP TABLE IF EXISTS `metaIngredients`;
CREATE TABLE `metaIngredients` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `title` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Bezeichnung der Zutat',
  `titlePlural` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Bezeichnung der Zutat wenn Menge != 1',
  `searchable` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0=nicht suchbar; 1=suchbar',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`),
  UNIQUE KEY `titlePlural` (`titlePlural`),
  KEY `searchable` (`searchable`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Metadaten: Zutaten';


DROP TABLE IF EXISTS `metaUnits`;
CREATE TABLE `metaUnits` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Titel der Einheit',
  `titlePlural` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Titel der Einheit wenn Menge != 1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`),
  UNIQUE KEY `titlePlural` (`titlePlural`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Metadaten: Einheiten';


DROP VIEW IF EXISTS `mostClicked`;
CREATE TABLE `mostClicked` (`itemId` int(10) unsigned, `c` bigint(21), `title` varchar(100), `shortTitle` varchar(64));


DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `accountId` int(10) unsigned NOT NULL COMMENT 'Querverweis accounts.id',
  `hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Sessionidentifikation',
  `lastActivity` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Aktivität',
  PRIMARY KEY (`id`),
  KEY `accountId` (`accountId`),
  CONSTRAINT `sessions_ibfk_2` FOREIGN KEY (`accountId`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Administratorsitzungen';

TRUNCATE `sessions`;

DROP VIEW IF EXISTS `stats`;
CREATE TABLE `stats` (`catCount` bigint(21), `itemCount` bigint(21), `clickCount` bigint(21), `clicksToday` bigint(21));


DROP TABLE IF EXISTS `mostClicked`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `mostClicked` AS select `clicks`.`itemId` AS `itemId`,count(`clicks`.`id`) AS `c`,`items`.`title` AS `title`,`items`.`shortTitle` AS `shortTitle` from (`clicks` left join `items` on((`items`.`id` = `clicks`.`itemId`))) group by `clicks`.`itemId` order by count(`clicks`.`id`) desc limit 15;

DROP TABLE IF EXISTS `stats`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `stats` AS select (select count(`categories`.`id`) from `categories`) AS `catCount`,(select count(`items`.`id`) from `items`) AS `itemCount`,(select count(`clicks`.`id`) from `clicks`) AS `clickCount`,(select count(`clicks`.`id`) from `clicks` where (`clicks`.`timestamp` > cast(curdate() as datetime))) AS `clicksToday`;

-- 2022-06-18 22:21:23
