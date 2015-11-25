# ************************************************************
# Sequel Pro SQL dump
# Version 4499
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: debian-vm (MySQL 5.6.23-1~dotdeb.3-log)
# Database: moocplanner
# Generation Time: 2015-11-25 21:45:28 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table course
# ------------------------------------------------------------

DROP TABLE IF EXISTS `course`;

CREATE TABLE `course` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `start_date` date NOT NULL,
  `standard_module_hours` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `course` WRITE;
/*!40000 ALTER TABLE `course` DISABLE KEYS */;

INSERT INTO `course` (`id`, `name`, `start_date`, `standard_module_hours`)
VALUES
	(1,'IKTLMOOC','2016-01-03',30);

/*!40000 ALTER TABLE `course` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table module
# ------------------------------------------------------------

DROP TABLE IF EXISTS `module`;

CREATE TABLE `module` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` int(11) unsigned NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `module_hours` int(11) DEFAULT NULL,
  `module_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `module_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `module` WRITE;
/*!40000 ALTER TABLE `module` DISABLE KEYS */;

INSERT INTO `module` (`id`, `course_id`, `name`, `module_hours`, `module_order`)
VALUES
	(1,1,'Modul 1',NULL,1),
	(4,1,'Modul 2',NULL,2),
	(5,1,'Modul 3',NULL,3),
	(6,1,'Modul 4',NULL,4),
	(7,1,'Modul 5',NULL,5),
	(8,1,'Modul 6',NULL,6),
	(9,1,'Modul 7',NULL,7),
	(10,1,'Modul 8',NULL,8),
	(11,1,'Modul 9',NULL,9),
	(12,1,'Modul 10',NULL,10),
	(13,1,'Modul 11',NULL,11),
	(14,1,'Modul 12',NULL,12),
	(15,1,'Modul 13',NULL,13),
	(16,1,'Modul 14',NULL,14),
	(17,1,'Modul 15',NULL,15);

/*!40000 ALTER TABLE `module` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table plan
# ------------------------------------------------------------

DROP TABLE IF EXISTS `plan`;

CREATE TABLE `plan` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `module_id` int(11) unsigned NOT NULL,
  `start_date` date NOT NULL,
  `hours` tinyint(2) unsigned NOT NULL,
  `repeatable` tinyint(2) unsigned DEFAULT NULL,
  `repeat_days` set('1','2','3','4','5','6','7') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `module_id` (`module_id`),
  CONSTRAINT `plan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `plan_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `module` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;

INSERT INTO `user` (`id`, `name`)
VALUES
	(1,'Are'),
	(2,'Bjørn');

/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table user_course
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_course`;

CREATE TABLE `user_course` (
  `user_id` int(11) unsigned NOT NULL,
  `course_id` int(11) unsigned NOT NULL,
  `standard_module_hours` int(11) unsigned DEFAULT NULL,
  `start_date` date NOT NULL,
  PRIMARY KEY (`user_id`,`course_id`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `user_course_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_course_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `user_course` WRITE;
/*!40000 ALTER TABLE `user_course` DISABLE KEYS */;

INSERT INTO `user_course` (`user_id`, `course_id`, `standard_module_hours`, `start_date`)
VALUES
	(1,1,NULL,'2015-10-10');

/*!40000 ALTER TABLE `user_course` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table user_module
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_module`;

CREATE TABLE `user_module` (
  `user_id` int(11) unsigned NOT NULL,
  `module_id` int(11) unsigned NOT NULL,
  `module_hours` int(11) unsigned DEFAULT NULL,
  `completed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`module_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
