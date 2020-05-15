-- Adminer 4.7.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

USE `pr0cooking`;

DELIMITER ;;

DROP EVENT IF EXISTS `Sitzungsbereinigung Admin`;;
CREATE EVENT `Sitzungsbereinigung Admin` ON SCHEDULE EVERY 6 HOUR STARTS '2020-04-01 00:00:00' ON COMPLETION NOT PRESERVE ENABLE COMMENT 'Löscht abgelaufene Admin-Sitzungen nach sechs Wochen' DO DELETE FROM `accountSessions` WHERE `lastActivity` < DATE_SUB(NOW(), INTERVAL 6 WEEK);;

DROP EVENT IF EXISTS `Sitzungsbereinigung User`;;
CREATE EVENT `Sitzungsbereinigung User` ON SCHEDULE EVERY 6 HOUR STARTS '2020-04-01 00:00:00' ON COMPLETION NOT PRESERVE ENABLE COMMENT 'Löscht abgelaufene User-Sitzungen nach sechs Wochen' DO DELETE FROM `userSessions` WHERE `lastActivity` < DATE_SUB(NOW(), INTERVAL 6 WEEK);;

DELIMITER ;

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `username` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Username',
  `password` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Passworthash',
  `salt` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Salt',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Administratorzugänge';


DROP TABLE IF EXISTS `accountSessions`;
CREATE TABLE `accountSessions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `userId` int(10) unsigned NOT NULL COMMENT 'User ID',
  `hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Sitzungshash',
  `lastActivity` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Aktivität',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `hash` (`hash`),
  KEY `lastactivity` (`lastActivity`),
  CONSTRAINT `accountSessions_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Administratorsitzungen';


DROP TABLE IF EXISTS `adminLog`;
CREATE TABLE `adminLog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `userId` int(10) unsigned DEFAULT NULL COMMENT 'Querverweis - accounts.id',
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt des Eintrags',
  `logLevel` int(10) unsigned NOT NULL COMMENT 'Querverweis - logLevel.id',
  `itemId` int(10) unsigned DEFAULT NULL COMMENT 'Querverweis - items.id, oder NULL bei User-/Systemaktion oder Irrelevanz',
  `categoryId` int(10) unsigned DEFAULT NULL COMMENT 'Querverweis - categories.id, oder NULL bei User-/Systemaktion oder Irrelevanz',
  `text` text COLLATE utf8mb4_unicode_ci COMMENT 'Logtext (optional)',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `timestamp` (`timestamp`),
  KEY `logLevel` (`logLevel`),
  KEY `itemId` (`itemId`),
  KEY `categoryId` (`categoryId`),
  CONSTRAINT `adminLog_ibfk_3` FOREIGN KEY (`logLevel`) REFERENCES `logLevel` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `adminLog_ibfk_5` FOREIGN KEY (`itemId`) REFERENCES `items` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `adminLog_ibfk_6` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `adminLog_ibfk_7` FOREIGN KEY (`userId`) REFERENCES `accounts` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='AdminLog';


DROP VIEW IF EXISTS `bestVoted`;
CREATE TABLE `bestVoted` (`title` varchar(100), `shortTitle` varchar(64), `a` decimal(6,2));


DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Angezeigter Titel',
  `shortTitle` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Kurzer Titel für die URL',
  `sortIndex` int(10) unsigned NOT NULL DEFAULT '9999999' COMMENT 'Sortierindex',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Beschreibung der Kategorie',
  `shortDescription` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Kurze Beschreibung der Kategorie',
  PRIMARY KEY (`id`),
  UNIQUE KEY `shortTitle` (`shortTitle`),
  KEY `sortindex` (`sortIndex`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Kategorientabelle';


DROP TABLE IF EXISTS `categoryItems`;
CREATE TABLE `categoryItems` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `categoryId` int(10) unsigned NOT NULL COMMENT 'Querverweis - categories.id',
  `itemId` int(10) unsigned NOT NULL COMMENT 'Querverweis - items.id',
  `sortIndex` int(10) unsigned NOT NULL DEFAULT '9999999' COMMENT 'Sortierindex',
  PRIMARY KEY (`id`),
  UNIQUE KEY `categoryId_itemId` (`categoryId`,`itemId`),
  KEY `categoryId` (`categoryId`),
  KEY `itemId` (`itemId`),
  KEY `sortindex` (`sortIndex`),
  CONSTRAINT `categoryItems_ibfk_2` FOREIGN KEY (`itemId`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `categoryItems_ibfk_3` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Querverweistabelle';


DROP TABLE IF EXISTS `clicks`;
CREATE TABLE `clicks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `itemId` int(10) unsigned NOT NULL COMMENT 'Item ID',
  `hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unique-User-Identifier',
  `ts` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt des Zugriffs',
  PRIMARY KEY (`id`),
  KEY `itemId` (`itemId`),
  KEY `hash` (`hash`),
  KEY `ts` (`ts`),
  CONSTRAINT `clicks_ibfk_1` FOREIGN KEY (`itemId`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `clicks_ibfk_2` FOREIGN KEY (`itemId`) REFERENCES `items` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabelle mit Klicks';


DROP TABLE IF EXISTS `favs`;
CREATE TABLE `favs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `userId` int(10) unsigned NOT NULL COMMENT 'Querverweis - users.id',
  `itemId` int(10) unsigned NOT NULL COMMENT 'Querverweis - items.id',
  `ts` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt des Eintrages',
  PRIMARY KEY (`id`),
  UNIQUE KEY `userId_itemId` (`userId`,`itemId`),
  KEY `userId` (`userId`),
  KEY `itemId` (`itemId`),
  CONSTRAINT `favs_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `favs_ibfk_2` FOREIGN KEY (`itemId`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Favoritentabelle';


DROP TABLE IF EXISTS `images`;
CREATE TABLE `images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `itemId` int(10) unsigned NOT NULL COMMENT 'Querverweis - items.id',
  `sortIndex` int(10) unsigned NOT NULL DEFAULT '9999999' COMMENT 'Sortierindex',
  `thumb` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Thumbnail = 1, normales Bild = 0',
  `fileHash` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unikat-Hash',
  PRIMARY KEY (`id`),
  UNIQUE KEY `filehash` (`fileHash`),
  KEY `itemId` (`itemId`),
  KEY `thumb` (`thumb`),
  CONSTRAINT `images_ibfk_3` FOREIGN KEY (`itemId`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `images_ibfk_4` FOREIGN KEY (`itemId`) REFERENCES `items` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabelle mit Bildzuordnungen';


DROP TABLE IF EXISTS `itemIngredients`;
CREATE TABLE `itemIngredients` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `itemId` int(10) unsigned NOT NULL COMMENT 'Querverweis - items.id',
  `ingredientId` int(10) unsigned NOT NULL COMMENT 'Querverweis - metaIngredients.id',
  `unitId` int(10) unsigned DEFAULT NULL COMMENT 'Querverweis - metaUnits.id',
  `quantity` double(10,2) unsigned DEFAULT NULL COMMENT 'Menge der Einheit',
  PRIMARY KEY (`id`),
  UNIQUE KEY `itemId_ingredientId` (`itemId`,`ingredientId`),
  KEY `ingredientId` (`ingredientId`),
  KEY `unitId` (`unitId`),
  CONSTRAINT `itemIngredients_ibfk_1` FOREIGN KEY (`itemId`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `itemIngredients_ibfk_2` FOREIGN KEY (`ingredientId`) REFERENCES `metaIngredients` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `itemIngredients_ibfk_3` FOREIGN KEY (`unitId`) REFERENCES `metaUnits` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Querverweistabelle - Zutaten/Mengeneinheiten/Rezepte';


DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Angezeigter Titel',
  `shortTitle` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Kurzer Titel für die URL',
  `text` text COLLATE utf8mb4_unicode_ci COMMENT 'Text des Eintrags',
  `persons` int(10) unsigned NOT NULL DEFAULT '1' COMMENT 'Ausgelegt für ... Personen',
  `cost` tinyint(3) unsigned NOT NULL COMMENT 'Querverweis zu metaCost',
  `difficulty` tinyint(3) unsigned NOT NULL COMMENT 'Querverweis zu metaDifficulty',
  `workDuration` tinyint(3) unsigned NOT NULL COMMENT 'Querverweis zu metaDuration',
  `totalDuration` tinyint(3) unsigned NOT NULL COMMENT 'Querverweis zu metaDuration',
  PRIMARY KEY (`id`),
  UNIQUE KEY `shortTitle` (`shortTitle`),
  KEY `persons` (`persons`),
  KEY `cost` (`cost`),
  KEY `difficulty` (`difficulty`),
  KEY `workDuration` (`workDuration`),
  KEY `totalDuration` (`totalDuration`),
  CONSTRAINT `items_ibfk_1` FOREIGN KEY (`cost`) REFERENCES `metaCost` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `items_ibfk_2` FOREIGN KEY (`difficulty`) REFERENCES `metaDifficulty` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `items_ibfk_3` FOREIGN KEY (`workDuration`) REFERENCES `metaDuration` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `items_ibfk_5` FOREIGN KEY (`totalDuration`) REFERENCES `metaDuration` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Rezepttabelle';


DROP TABLE IF EXISTS `logLevel`;
CREATE TABLE `logLevel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Meldungsart',
  `color` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'HexCode der Meldungsfarbe',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

TRUNCATE `logLevel`;
INSERT INTO `logLevel` (`id`, `title`, `color`) VALUES
(1,	'User-/Systemaktion',	'888888'),
(2,	'Neuanlage',	'e108e9'),
(3,	'Bearbeitung',	'ff9900'),
(4,	'Löschung',	'c52b2f'),
(5,	'Vote',	'1db992'),
(6,	'Favoriten',	'5bb91c'),
(7,	'Sortierung',	'bfbc06'),
(8,	'Zuweisung',	'008fff');

DROP TABLE IF EXISTS `metaCost`;
CREATE TABLE `metaCost` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Bezeichnung',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabelle mit Metadaten: Kosten des Rezepts';

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabelle mit Metadaten: Schwierigkeit des Rezepts';

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabelle mit Metadaten: Dauer des Rezepts';

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
  `searchable` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0=nicht suchbar; 1=suchbar',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabelle mit Metadaten: Zutaten';


DROP TABLE IF EXISTS `metaUnits`;
CREATE TABLE `metaUnits` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Titel der Einheit',
  `short` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Kurzform der Einheit',
  `spacer` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Leerzeichen zwischen Menge und Maßeinheit, ja oder nein',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`),
  UNIQUE KEY `short` (`short`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabelle mit Metadaten: Einheiten';


DROP VIEW IF EXISTS `mostClicked`;
CREATE TABLE `mostClicked` (`itemId` int(10) unsigned, `c` bigint(21), `title` varchar(100), `shortTitle` varchar(64));


DROP VIEW IF EXISTS `stats`;
CREATE TABLE `stats` (`catCount` bigint(21), `itemCount` bigint(21), `clickCount` bigint(21), `clicksToday` bigint(21));


DROP TABLE IF EXISTS `userLog`;
CREATE TABLE `userLog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `userId` int(10) unsigned DEFAULT NULL COMMENT 'Querverweis - users.id',
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt des Eintrags',
  `logLevel` int(10) unsigned NOT NULL COMMENT 'Querverweis - logLevel.id',
  `itemId` int(10) unsigned DEFAULT NULL COMMENT 'Querverweis - items.id, oder NULL bei User-/Systemaktion oder Irrelevanz',
  `text` text COLLATE utf8mb4_unicode_ci COMMENT 'Logtext (optional)',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `timestamp` (`timestamp`),
  KEY `logLevel` (`logLevel`),
  KEY `itemId` (`itemId`),
  CONSTRAINT `userLog_ibfk_2` FOREIGN KEY (`logLevel`) REFERENCES `logLevel` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `userLog_ibfk_4` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `userLog_ibfk_5` FOREIGN KEY (`itemId`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='UserLog';


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `username` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'pr0gramm Username',
  `pr0grammUserId` int(10) unsigned NOT NULL COMMENT 'pr0gramm User-ID',
  `accessToken` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Header-Token für die oAuth Anfrage',
  `lastSynced` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Ban-Abfrage',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `pr0grammUserId` (`pr0grammUserId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Usertabelle';


DROP TABLE IF EXISTS `userSessions`;
CREATE TABLE `userSessions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `userId` int(10) unsigned NOT NULL COMMENT 'Querverweis - users.id',
  `sessionHash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'sessionHash',
  `lastActivity` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Aktivität',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `sessionHash` (`sessionHash`),
  KEY `lastActivity` (`lastActivity`),
  CONSTRAINT `userSessions_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sitzungstabelle';


DROP TABLE IF EXISTS `votes`;
CREATE TABLE `votes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `itemId` int(10) unsigned NOT NULL COMMENT 'Item ID',
  `userId` int(10) unsigned NOT NULL COMMENT 'Querverweis - users.id',
  `ts` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt des Votes',
  `stars` tinyint(1) unsigned NOT NULL COMMENT 'Anzahl der Sterne',
  PRIMARY KEY (`id`),
  UNIQUE KEY `itemId_userId` (`itemId`,`userId`),
  KEY `itemId` (`itemId`),
  KEY `ts` (`ts`),
  KEY `stars` (`stars`),
  KEY `userId` (`userId`),
  CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`itemId`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `votes_ibfk_4` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabelle mit Votes';


DROP TABLE IF EXISTS `bestVoted`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `bestVoted` AS select `items`.`title` AS `title`,`items`.`shortTitle` AS `shortTitle`,round(avg(`votes`.`stars`),2) AS `a` from (`votes` left join `items` on((`votes`.`itemId` = `items`.`id`))) group by `votes`.`itemId` order by round(avg(`votes`.`stars`),2) desc limit 15;

DROP TABLE IF EXISTS `mostClicked`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `mostClicked` AS select `clicks`.`itemId` AS `itemId`,count(`clicks`.`id`) AS `c`,`items`.`title` AS `title`,`items`.`shortTitle` AS `shortTitle` from (`clicks` left join `items` on((`items`.`id` = `clicks`.`itemId`))) group by `clicks`.`itemId` order by count(`clicks`.`id`) desc limit 15;

DROP TABLE IF EXISTS `stats`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `stats` AS select (select count(`categories`.`id`) from `categories`) AS `catCount`,(select count(`items`.`id`) from `items`) AS `itemCount`,(select count(`clicks`.`id`) from `clicks`) AS `clickCount`,(select count(`clicks`.`id`) from `clicks` where (`clicks`.`ts` > cast(curdate() as datetime))) AS `clicksToday`;

-- 2020-05-15 19:20:12
