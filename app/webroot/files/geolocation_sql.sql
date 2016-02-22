# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 188.226.168.159 (MySQL 5.5.31-0ubuntu0.12.04.2)
# Database: geolocation template
# Generation Time: 2014-11-17 16:38:04 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table added_venues
# ------------------------------------------------------------

CREATE TABLE `added_venues` (
  `venue_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `inserted` datetime NOT NULL,
  KEY `users_venues` (`user_id`),
  KEY `add_venues_venues` (`venue_id`),
  CONSTRAINT `add_venues_venues` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `users_venues` FOREIGN KEY (`user_id`) REFERENCES `movement_geolocation`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table likes
# ------------------------------------------------------------

CREATE TABLE `likes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `venue_id` int(11) DEFAULT NULL,
  `review_id` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table reports
# ------------------------------------------------------------

CREATE TABLE `reports` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `venue_id` int(11) DEFAULT NULL,
  `review_id` int(11) DEFAULT NULL,
  `report_text` varchar(2000) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table reviews
# ------------------------------------------------------------

CREATE TABLE `reviews` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `venue_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `q1` double DEFAULT NULL,
  `q2` double DEFAULT NULL,
  `q3` double DEFAULT NULL,
  `q4` double DEFAULT NULL,
  `review_text` varchar(400) DEFAULT NULL,
  `flag` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(4) DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `venue` (`venue_id`),
  KEY `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table venue_categories
# ------------------------------------------------------------

CREATE TABLE `venue_categories` (
  `category_id` int(11) unsigned NOT NULL,
  `venue_id` int(11) unsigned NOT NULL,
  KEY `category` (`category_id`),
  KEY `venues` (`venue_id`),
  CONSTRAINT `category` FOREIGN KEY (`category_id`) REFERENCES `movement_geolocation`.`categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `venues` FOREIGN KEY (`venue_id`) REFERENCES `venues` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table venues
# ------------------------------------------------------------

CREATE TABLE `venues` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `foursquare_id` varchar(120) DEFAULT '',
  `name` varchar(300) DEFAULT NULL,
  `latitude` decimal(13,10) NOT NULL,
  `longitude` decimal(13,10) NOT NULL,
  `address` varchar(300) DEFAULT NULL,
  `city` varchar(300) DEFAULT NULL,
  `state` varchar(300) DEFAULT NULL,
  `postcode` varchar(50) DEFAULT NULL,
  `country` varchar(200) DEFAULT NULL,
  `flag` tinyint(4) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;