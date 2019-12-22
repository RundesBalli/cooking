-- Adminer 4.7.3 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

USE `pr0cooking`;

DELIMITER ;;

DROP EVENT IF EXISTS `Sitzungsbereinigung`;;
CREATE EVENT `Sitzungsbereinigung` ON SCHEDULE EVERY 1 HOUR STARTS '2019-09-21 20:33:20' ON COMPLETION NOT PRESERVE ENABLE COMMENT 'Löscht abgelaufene Sitzungen nach sechs Wochen' DO DELETE FROM `sessions` WHERE `lastactivity` < DATE_SUB(NOW(), INTERVAL 6 WEEK);;

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


DROP VIEW IF EXISTS `best_voted`;
CREATE TABLE `best_voted` (`title` varchar(100), `shortTitle` varchar(64), `a` decimal(6,2));


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


DROP TABLE IF EXISTS `category_items`;
CREATE TABLE `category_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `category_id` int(10) unsigned NOT NULL COMMENT 'Querverweis - Kategorie',
  `item_id` int(10) unsigned NOT NULL COMMENT 'Querverweis - Item',
  `sortIndex` int(10) unsigned NOT NULL DEFAULT '9999999' COMMENT 'Sortierindex',
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_id_item_id` (`category_id`,`item_id`),
  KEY `category_id` (`category_id`),
  KEY `item_id` (`item_id`),
  KEY `sortindex` (`sortIndex`),
  CONSTRAINT `category_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `category_items_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Querverweistabelle';


DROP TABLE IF EXISTS `clicks`;
CREATE TABLE `clicks` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `itemid` int(10) unsigned NOT NULL COMMENT 'Item ID',
  `hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'User-Unique-Hash',
  `ts` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt des Zugriffs',
  PRIMARY KEY (`id`),
  KEY `itemid` (`itemid`),
  KEY `hash` (`hash`),
  KEY `ts` (`ts`),
  CONSTRAINT `clicks_ibfk_1` FOREIGN KEY (`itemid`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabelle mit Klicks';


DROP TABLE IF EXISTS `images`;
CREATE TABLE `images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `itemid` int(10) unsigned NOT NULL COMMENT 'Querverweis - Item',
  `sortIndex` int(10) unsigned NOT NULL DEFAULT '9999999' COMMENT 'Sortierindex',
  `thumb` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Thumbnail = 1, normales Bild = 0',
  `filehash` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Unikat-Hash',
  PRIMARY KEY (`id`),
  UNIQUE KEY `filehash` (`filehash`),
  KEY `itemid` (`itemid`),
  KEY `thumb` (`thumb`),
  CONSTRAINT `images_ibfk_3` FOREIGN KEY (`itemid`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Angezeigter Titel',
  `shortTitle` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Kurzer Titel für die URL',
  `text` text COLLATE utf8mb4_unicode_ci COMMENT 'Text des Eintrags',
  `ingredients` text COLLATE utf8mb4_unicode_ci COMMENT 'Zutaten',
  `persons` int(10) unsigned NOT NULL DEFAULT '1' COMMENT 'Ausgelegt für ... Personen',
  `cost` tinyint(3) unsigned NOT NULL COMMENT 'Querverweis zu meta_cost',
  `difficulty` tinyint(3) unsigned NOT NULL COMMENT 'Querverweis zu meta_difficulty',
  `duration` tinyint(3) unsigned NOT NULL COMMENT 'Querverweis zu meta_duration',
  PRIMARY KEY (`id`),
  UNIQUE KEY `shortTitle` (`shortTitle`),
  KEY `persons` (`persons`),
  KEY `cost` (`cost`),
  KEY `difficulty` (`difficulty`),
  KEY `duration` (`duration`),
  CONSTRAINT `items_ibfk_1` FOREIGN KEY (`cost`) REFERENCES `meta_cost` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `items_ibfk_2` FOREIGN KEY (`difficulty`) REFERENCES `meta_difficulty` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `items_ibfk_3` FOREIGN KEY (`duration`) REFERENCES `meta_duration` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Rezepttabelle';


DROP TABLE IF EXISTS `meta_cost`;
CREATE TABLE `meta_cost` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Bezeichnung',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabelle mit Metadaten: Kosten des Rezepts';

TRUNCATE `meta_cost`;
INSERT INTO `meta_cost` (`id`, `title`) VALUES
(1,	'günstig'),
(2,	'mittel'),
(3,	'teuer');

DROP TABLE IF EXISTS `meta_difficulty`;
CREATE TABLE `meta_difficulty` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Bezeichnung',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabelle mit Metadaten: Schwierigkeit des Rezepts';

TRUNCATE `meta_difficulty`;
INSERT INTO `meta_difficulty` (`id`, `title`) VALUES
(1,	'Anfänger'),
(2,	'Fortgeschrittener'),
(3,	'Profi'),
(4,	'Experte');

DROP TABLE IF EXISTS `meta_duration`;
CREATE TABLE `meta_duration` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Bezeichnung',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabelle mit Metadaten: Dauer des Rezepts';

TRUNCATE `meta_duration`;
INSERT INTO `meta_duration` (`id`, `title`) VALUES
(1,	'bis zu 10 Minuten'),
(2,	'10 bis 15 Minuten'),
(3,	'15 bis 30 Minuten'),
(4,	'30 bis 60 Minuten'),
(5,	'60 bis 90 Minuten'),
(6,	'länger als 90 Minuten');

DROP VIEW IF EXISTS `most_clicked`;
CREATE TABLE `most_clicked` (`itemid` int(10) unsigned, `c` bigint(21), `title` varchar(100), `shortTitle` varchar(64));


DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `userid` int(10) unsigned NOT NULL COMMENT 'User ID',
  `hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Sitzungshash',
  `lastactivity` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Zeitpunkt der letzten Aktivität',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`),
  KEY `hash` (`hash`),
  KEY `lastactivity` (`lastactivity`),
  CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP VIEW IF EXISTS `stats`;
CREATE TABLE `stats` (`cat_count` bigint(21), `item_count` bigint(21), `click_count` bigint(21), `clicks_today` bigint(21));


DROP TABLE IF EXISTS `votes`;
CREATE TABLE `votes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Laufende ID',
  `itemid` int(10) unsigned NOT NULL COMMENT 'Item ID',
  `hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'User-Unique-Hash',
  `ts` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Zeitpunkt des Votes',
  `stars` tinyint(1) unsigned NOT NULL COMMENT 'Anzahl der Sterne',
  PRIMARY KEY (`id`),
  KEY `itemid` (`itemid`),
  KEY `hash` (`hash`),
  KEY `ts` (`ts`),
  KEY `stars` (`stars`),
  CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`itemid`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`hash`) REFERENCES `clicks` (`hash`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabelle mit Votes';


DROP TABLE IF EXISTS `best_voted`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `best_voted` AS select `items`.`title` AS `title`,`items`.`shortTitle` AS `shortTitle`,round(avg(`votes`.`stars`),2) AS `a` from (`votes` left join `items` on((`votes`.`itemid` = `items`.`id`))) group by `votes`.`itemid` order by round(avg(`votes`.`stars`),2) desc limit 15;

DROP TABLE IF EXISTS `most_clicked`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `most_clicked` AS select `clicks`.`itemid` AS `itemid`,count(`clicks`.`id`) AS `c`,`items`.`title` AS `title`,`items`.`shortTitle` AS `shortTitle` from (`clicks` left join `items` on((`items`.`id` = `clicks`.`itemid`))) group by `clicks`.`itemid` order by count(`clicks`.`id`) desc limit 15;

DROP TABLE IF EXISTS `stats`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `stats` AS select (select count(`categories`.`id`) from `categories`) AS `cat_count`,(select count(`items`.`id`) from `items`) AS `item_count`,(select count(`clicks`.`id`) from `clicks`) AS `click_count`,(select count(`clicks`.`id`) from `clicks` where (`clicks`.`ts` > cast(curdate() as datetime))) AS `clicks_today`;

-- 2019-12-22 17:10:13
