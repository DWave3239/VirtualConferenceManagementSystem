-- --------------------------------------------------------
-- Host:                         azusa.zapto.org
-- Server Version:               10.1.48-MariaDB-0+deb9u1 - Raspbian 9.11
-- Server Betriebssystem:        debian-linux-gnueabihf
-- HeidiSQL Version:             11.3.0.6295
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Exportiere Struktur von Tabelle bacc.auths
CREATE TABLE IF NOT EXISTS `auths` (
  `authId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`authId`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf32;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle bacc.auths2segments
CREATE TABLE IF NOT EXISTS `auths2segments` (
  `authId` int(11) NOT NULL,
  `segmentId` int(11) NOT NULL,
  PRIMARY KEY (`authId`,`segmentId`),
  KEY `FK_auths2segments_segments` (`segmentId`),
  CONSTRAINT `FK_auths2segments_auths` FOREIGN KEY (`authId`) REFERENCES `auths` (`authId`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_auths2segments_segments` FOREIGN KEY (`segmentId`) REFERENCES `segments` (`segmentId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle bacc.auths2tickets
CREATE TABLE IF NOT EXISTS `auths2tickets` (
  `authId` int(11) NOT NULL,
  `ticketId` int(11) NOT NULL,
  PRIMARY KEY (`authId`,`ticketId`),
  KEY `FK_auths2tickets_tickets` (`ticketId`),
  CONSTRAINT `FK_auths2tickets_auths` FOREIGN KEY (`authId`) REFERENCES `auths` (`authId`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_auths2tickets_tickets` FOREIGN KEY (`ticketId`) REFERENCES `tickets` (`ticketId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle bacc.auths2tracks
CREATE TABLE IF NOT EXISTS `auths2tracks` (
  `authId` int(11) NOT NULL,
  `trackId` int(11) NOT NULL,
  PRIMARY KEY (`authId`,`trackId`),
  KEY `FK_auths2tracks_tracks` (`trackId`),
  CONSTRAINT `FK_auths2tracks_auths` FOREIGN KEY (`authId`) REFERENCES `auths` (`authId`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_auths2tracks_tracks` FOREIGN KEY (`trackId`) REFERENCES `tracks` (`trackId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf32 ROW_FORMAT=COMPACT;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle bacc.changelog
CREATE TABLE IF NOT EXISTS `changelog` (
  `userId` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle bacc.news
CREATE TABLE IF NOT EXISTS `news` (
  `newsId` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `displayAfter` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`newsId`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle bacc.notifications
CREATE TABLE IF NOT EXISTS `notifications` (
  `notificationId` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `displayAfter` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`notificationId`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 ROW_FORMAT=COMPACT;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle bacc.papers
CREATE TABLE IF NOT EXISTS `papers` (
  `paperId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `name` text NOT NULL,
  `country` varchar(100) NOT NULL,
  `uniqueId` varchar(100) NOT NULL,
  `segmentId` int(11) DEFAULT NULL,
  PRIMARY KEY (`paperId`),
  KEY `FK_papers_users` (`userId`),
  KEY `FK_papers_segments` (`segmentId`),
  CONSTRAINT `FK_papers_segments` FOREIGN KEY (`segmentId`) REFERENCES `segments` (`segmentId`),
  CONSTRAINT `FK_papers_users` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf32;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle bacc.papers2users
CREATE TABLE IF NOT EXISTS `papers2users` (
  `paperId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  PRIMARY KEY (`paperId`,`userId`),
  KEY `FK_papers2users_users` (`userId`),
  CONSTRAINT `FK_papers2users_papers` FOREIGN KEY (`paperId`) REFERENCES `papers` (`paperId`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_papers2users_users` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle bacc.segments
CREATE TABLE IF NOT EXISTS `segments` (
  `segmentId` int(11) NOT NULL AUTO_INCREMENT,
  `timeslotId` int(11) NOT NULL,
  `name` text NOT NULL,
  `subtitle` text,
  `chairId` int(11) DEFAULT NULL,
  `individual_link` varchar(50) DEFAULT NULL,
  `delay` time DEFAULT NULL,
  PRIMARY KEY (`segmentId`),
  KEY `FK_segments_timeslots` (`timeslotId`),
  KEY `FK_segments_users` (`chairId`),
  CONSTRAINT `FK_segments_timeslots` FOREIGN KEY (`timeslotId`) REFERENCES `timeslots` (`timeslotId`),
  CONSTRAINT `FK_segments_users` FOREIGN KEY (`chairId`) REFERENCES `users` (`userId`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf32;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle bacc.segments2tracks
CREATE TABLE IF NOT EXISTS `segments2tracks` (
  `segmentId` int(11) NOT NULL,
  `trackId` int(11) NOT NULL,
  PRIMARY KEY (`trackId`,`segmentId`),
  KEY `FK_segments2tracks_segments` (`segmentId`),
  CONSTRAINT `FK_segments2tracks_segments` FOREIGN KEY (`segmentId`) REFERENCES `segments` (`segmentId`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_segments2tracks_tracks` FOREIGN KEY (`trackId`) REFERENCES `tracks` (`trackId`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf32;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle bacc.tickets
CREATE TABLE IF NOT EXISTS `tickets` (
  `ticketId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`ticketId`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf32;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle bacc.timeslots
CREATE TABLE IF NOT EXISTS `timeslots` (
  `timeslotId` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `time_from` time NOT NULL,
  `time_until` time NOT NULL,
  PRIMARY KEY (`timeslotId`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf32;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle bacc.tracks
CREATE TABLE IF NOT EXISTS `tracks` (
  `trackId` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `link` varchar(500) NOT NULL,
  `sequence` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'display order',
  PRIMARY KEY (`trackId`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf32;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle bacc.users
CREATE TABLE IF NOT EXISTS `users` (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(255) NOT NULL DEFAULT '0',
  `auth_level` int(11) NOT NULL,
  `ticketId` int(11) NOT NULL,
  PRIMARY KEY (`userId`) USING BTREE,
  KEY `FK_users_tickets` (`ticketId`) USING BTREE,
  CONSTRAINT `FK_users_tickets` FOREIGN KEY (`ticketId`) REFERENCES `tickets` (`ticketId`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf32;

-- Daten Export vom Benutzer nicht ausgewählt

-- Exportiere Struktur von Tabelle bacc.webhookEvents
CREATE TABLE IF NOT EXISTS `webhookEvents` (
  `eventId` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `eventName` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `roomName` varchar(150) NOT NULL DEFAULT '',
  `data` text CHARACTER SET utf8 NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`eventId`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4;

-- Daten Export vom Benutzer nicht ausgewählt

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
