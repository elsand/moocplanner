# ************************************************************
# Sequel Pro SQL dump
# Version 4499
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: debian-vm (MySQL 5.6.23-1~dotdeb.3-log)
# Database: moocplanner
# Generation Time: 2015-11-30 22:26:54 +0000
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
  `is_exam` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '',
  `module_hours` int(11) DEFAULT NULL,
  `module_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`),
  CONSTRAINT `module_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `module` WRITE;
/*!40000 ALTER TABLE `module` DISABLE KEYS */;

INSERT INTO `module` (`id`, `course_id`, `is_exam`, `name`, `module_hours`, `module_order`)
VALUES
	(1,1,0,'Introduksjon',NULL,1),
	(4,1,0,'Pedagogisk bruk av LMS',NULL,2),
	(5,1,0,'Skjermopptak',20,3),
	(6,1,0,'Web 2.0 del 1 - Samskriving og blogging',NULL,4),
	(7,1,0,'Digital vurdering',NULL,5),
	(8,1,0,'Læringsteorier',NULL,6),
	(9,1,0,'Åpent innhold',NULL,7),
	(10,1,0,'Formativ vurdering med digitale tester',NULL,8),
	(11,1,0,'Pedagogisk bruk av video (snarfilm)',NULL,9),
	(12,1,0,'Web 2.0 del 2 - Tenk nytt om undervisning og læring',NULL,10),
	(13,1,0,'Læringsdesign',NULL,11),
	(14,1,0,'Opphavsrett',NULL,12),
	(15,1,0,'Summativ vurdering med digitale tester',NULL,13),
	(16,1,0,'Omvendt klasserom',NULL,14),
	(17,1,0,'PLN og PLE (personlig læringsnettverk/læringsmiljø)',NULL,15);

/*!40000 ALTER TABLE `module` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table session
# ------------------------------------------------------------

DROP TABLE IF EXISTS `session`;

CREATE TABLE `session` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `module_id` int(11) unsigned NOT NULL,
  `start_date` date NOT NULL,
  `hours` tinyint(2) unsigned NOT NULL,
  `repeating` tinyint(2) unsigned DEFAULT NULL,
  `repeat_days` set('1','2','3','4','5','6','7') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `module_id` (`module_id`),
  CONSTRAINT `session_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `session_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `module` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `session` WRITE;
/*!40000 ALTER TABLE `session` DISABLE KEYS */;

INSERT INTO `session` (`id`, `user_id`, `module_id`, `start_date`, `hours`, `repeating`, `repeat_days`)
VALUES
	(1,1,1,'2015-11-02',4,1,'1,2,3,4,5'),
	(2,1,4,'2015-11-11',4,1,'1,2,3,4,5'),
	(3,1,5,'2015-11-20',4,1,'1,2,3,4,5'),
	(5,1,6,'2015-12-02',6,2,'1,3,5'),
	(6,1,7,'2015-12-01',6,2,'2,4'),
	(7,1,8,'2015-12-07',4,NULL,NULL),
	(8,1,9,'2015-12-08',6,NULL,NULL),
	(9,1,9,'2015-12-09',4,NULL,NULL),
	(10,1,9,'2015-12-10',5,NULL,NULL),
	(11,1,10,'2015-12-11',2,1,'1,2,4,5'),
	(12,1,8,'2016-01-11',5,1,'1,2,3'),
	(13,1,11,'2016-01-04',5,1,'3,4,5'),
	(14,1,12,'2016-02-01',5,1,'1,2,3,4,5'),
	(15,1,13,'2016-02-08',5,1,'1,3,5'),
	(16,1,14,'2016-02-09',5,1,'2,4'),
	(17,1,9,'2015-11-30',3,NULL,NULL);

/*!40000 ALTER TABLE `session` ENABLE KEYS */;
UNLOCK TABLES;


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
	(1,1,25,'2015-11-01');

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

LOCK TABLES `user_module` WRITE;
/*!40000 ALTER TABLE `user_module` DISABLE KEYS */;

INSERT INTO `user_module` (`user_id`, `module_id`, `module_hours`, `completed`)
VALUES
	(1,1,NULL,1),
	(1,4,NULL,1),
	(1,12,20,0),
	(1,13,30,0);

/*!40000 ALTER TABLE `user_module` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
