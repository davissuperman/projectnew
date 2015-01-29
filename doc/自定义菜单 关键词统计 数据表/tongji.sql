/*
SQLyog 企业版 - MySQL GUI v8.14 
MySQL - 5.5.24 : Database - pinv_new
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`pinv` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;

USE `pinv`;

/*Table structure for table `tp_user_keyword_click` */

DROP TABLE IF EXISTS `tp_user_keyword_click`;

CREATE TABLE `tp_user_keyword_click` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `kid` int(11) unsigned DEFAULT NULL,
  `keyword` varchar(100) NOT NULL COMMENT '关键词',
  `matched` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否匹配到：1匹配到，0未匹配到',
  `token` char(16) NOT NULL COMMENT '商家token',
  `openid` char(28) NOT NULL COMMENT '用户标识',
  `click_time` int(10) unsigned NOT NULL COMMENT '点击时间',
  `click_date` date NOT NULL COMMENT '点击日期(Y-m-d)',
  `click_hour` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '点击时间(小时)',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `keyword` (`keyword`)
) ENGINE=MyISAM AUTO_INCREMENT=69 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tp_user_keyword_click` */

insert  into `tp_user_keyword_click`(`id`,`kid`,`keyword`,`matched`,`token`,`openid`,`click_time`,`click_date`,`click_hour`) values (1,222,'西城区',0,'sdfsdfds','sdfasdfasdf',113131313,'2014-03-26',1),(2,NULL,'会员卡',0,'rggfsk1394161441','haiyan',1395331682,'2014-03-27',1),(3,NULL,'国二招',0,'gzkxgh','haiyan',1395392877,'2014-04-01',1),(4,NULL,'首页',0,'ssdfsdf','haiyan',1395393594,'2014-03-31',1),(5,NULL,'天气',0,'ssdfsdf','haiyan',1395366267,'2014-03-31',1),(6,NULL,'天气',0,'ssdfsdf','haiyan',1395366316,'2014-03-27',1),(7,NULL,'会员',0,'meimei','haiyan',1395366580,'2014-04-01',1),(8,NULL,'特产',0,'meimei','haiyan',1395367906,'2014-03-31',1),(9,NULL,'订房',0,'meimei','haiyan',1395367916,'2014-03-27',1),(10,NULL,'酒店',0,'meimei','haiyan',1395367925,'2014-04-01',1),(11,NULL,'酒店',0,'meimei','haiyan',1395368356,'2014-04-01',1),(12,NULL,'欢迎您致电010-66180806直接客房预订。',0,'meimei','haiyan',1395369679,'2014-03-27',1),(13,NULL,'欢迎您致电010-66180806直接客房预订。',0,'meimei','haiyan',1395369709,'2014-04-01',1),(14,NULL,'房间',1,'meimei','haiyan',1395369804,'2014-03-31',1),(15,NULL,'房间',0,'meimei','haiyan',1395369804,'2014-04-01',1),(16,NULL,'酒店',0,'meimei','haiyan',1395369830,'2014-04-01',1),(17,NULL,'酒店',0,'meimei','haiyan',1395369830,'2014-03-25',1),(18,NULL,'首页',0,'meimei','haiyan',1395369837,'2014-03-31',1),(19,NULL,'首页',1,'meimei','haiyan',1395369837,'2014-03-25',1),(20,NULL,'附近',0,'meimei','haiyan',1395369858,'2014-03-25',1),(21,NULL,'附近',0,'meimei','haiyan',1395369858,'2014-03-25',1),(22,NULL,'今天',1,'meimei','haiyan',1395369874,'2014-03-28',1),(23,NULL,'今天',0,'meimei','haiyan',1395369874,'2014-03-31',1),(24,NULL,'今天',1,'meimei','haiyan',1395369924,'2014-03-28',1),(25,NULL,'很好中国旅行',0,'meimei','haiyan',1395369943,'2014-03-31',1),(26,NULL,'很好中国旅行',0,'meimei','haiyan',1395370373,'2014-03-28',1),(28,NULL,'首页',0,'xosooe1384219311','haiyan51',1395370538,'2014-03-28',1),(29,NULL,'业务模块',1,'xosooe1384219311','haiyan51',1395370554,'2014-03-31',1),(30,NULL,'会员卡',0,'xosooe1384219311','haiyan51',1395370563,'2014-03-20',1),(31,NULL,'相册',0,'xosooe1384219311','haiyan51',1395370576,'2014-03-20',1),(32,NULL,'刮刮乐',0,'xosooe1384219311','haiyan51',1395370584,'2014-03-20',1),(33,NULL,'相册',0,'xosooe1384219311','miaomiao',1396349283,'2014-03-28',1),(34,NULL,'首页',1,'xosooe1384219311','miaomiao',1396349297,'2014-03-08',1),(35,NULL,'首页',0,'xosooe1384219311','miaomiao',1396349435,'2014-03-08',1),(36,NULL,'首页',0,'xosooe1384219311','miaomiao',1396349532,'2014-03-08',1),(37,NULL,'首页',1,'xosooe1384219311','miaomiao',1396349558,'2014-03-20',1),(38,NULL,'首页',0,'xosooe1384219311','miaomiao',1396349583,'2014-03-20',1),(39,NULL,'首页',0,'xosooe1384219311','miaomiao',1396349594,'2014-03-28',1),(40,NULL,'汽车',0,'xosooe1384219311','miaomiao',1396349604,'2014-03-20',1),(41,NULL,'首页',0,'xosooe1384219311','miaomiao',1396349625,'2014-03-28',1),(42,NULL,'首页',0,'xosooe1384219311','miaomiao',1396350047,'2014-03-08',1),(43,NULL,'首页',0,'xosooe1384219311','miaomiao',1396350060,'2014-03-08',1),(44,NULL,'汽车',1,'xosooe1384219311','miaomiao',1396350079,'2014-03-12',1),(45,NULL,'汽车',0,'xosooe1384219311','miaomiao',1396350766,'2014-03-12',1),(46,NULL,'汽车',0,'xosooe1384219311','miaomiao',1396351079,'2014-03-12',1),(47,NULL,'汽车',0,'xosooe1384219311','miaomiao',1396351402,'2014-03-08',1),(48,NULL,'汽车',0,'xosooe1384219311','miaomiao',1396351818,'2014-03-12',1),(49,NULL,'汽车',0,'xosooe1384219311','miaomiao',1396351993,'2014-03-12',1),(50,NULL,'汽车',0,'xosooe1384219311','miaomiao',1396352041,'2014-03-08',1),(51,NULL,'汽车',0,'xosooe1384219311','miaomiao',1396354928,'2014-03-12',1),(52,NULL,'首页',0,'xosooe1384219311','miaomiao',1396356304,'2014-03-26',1),(53,NULL,'首页',0,'xosooe1384219311','miaomiao',1396356304,'2014-03-26',1),(54,NULL,'汽车',0,'xosooe1384219311','miaomiao',1396356312,'2014-03-29',1),(55,NULL,'车主关怀',0,'xosooe1384219311','miaomiao',1396357363,'2014-03-29',1),(56,NULL,'预约试驾',0,'xosooe1384219311','miaomiao',1396357381,'2014-03-29',1),(57,NULL,'车主关怀',0,'xosooe1384219311','miaomiao',1396357405,'2014-03-30',1),(58,NULL,'预约试驾',0,'xosooe1384219311','miaomiao',1396357431,'2014-03-30',1),(59,NULL,'预约保养',0,'xosooe1384219311','miaomiao',1396357451,'2014-03-31',1),(60,NULL,'预约保养',0,'xosooe1384219311','miaomiao',1396357525,'2014-03-31',1),(61,NULL,'车主关怀',0,'xosooe1384219311','miaomiao',1396357537,'2014-03-31',1),(62,NULL,'车主关怀',0,'xosooe1384219311','miaomiao',1396357566,'2014-03-23',1),(63,NULL,'预约试驾',0,'xosooe1384219311','miaomiao',1396357583,'2014-03-23',1),(64,NULL,'预约试驾',0,'xosooe1384219311','miaomiao',1396357772,'2014-03-23',1),(65,NULL,'预约试驾',0,'xosooe1384219311','miaomiao',1396357842,'2014-03-22',1),(66,NULL,'首页',0,'xosooe1384219311','miaomiao',1396357857,'2014-03-22',1),(67,NULL,'预约试驾',0,'xosooe1384219311','miaomiao',1396357882,'2014-03-22',1),(68,NULL,'预约保养',0,'xosooe1384219311','miaomiao',1396357911,'2014-03-22',1);

/*Table structure for table `tp_user_menu_click` */

DROP TABLE IF EXISTS `tp_user_menu_click`;

CREATE TABLE `tp_user_menu_click` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `mid` int(11) unsigned NOT NULL COMMENT '菜单id',
  `mpid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '父级菜单id',
  `title` varchar(30) NOT NULL COMMENT '菜单名称',
  `keyword` varchar(100) DEFAULT NULL COMMENT '关键词',
  `url` varchar(150) DEFAULT NULL,
  `token` char(16) NOT NULL,
  `openid` char(28) NOT NULL COMMENT '用户wecha_id',
  `click_time` int(10) unsigned NOT NULL COMMENT '点击时间',
  `is_reached` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否到达',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tp_user_menu_click` */

insert  into `tp_user_menu_click`(`id`,`mid`,`mpid`,`title`,`keyword`,`url`,`token`,`openid`,`click_time`,`is_reached`) values (26,0,0,'','刮刮乐',NULL,'xosooe1384219311','haiyan51',1395370616,0),(27,0,0,'','会员卡',NULL,'xosooe1384219311','haiyan51',1395370628,0),(28,0,0,'','会员卡',NULL,'xosooe1384219311','haiyan51',1395370707,0),(29,0,0,'','会员卡',NULL,'xosooe1384219311','haiyan51',1395371087,0),(30,0,0,'','行业案例',NULL,'xosooe1384219311','haiyan51',1395371241,1),(31,0,0,'','行业案例',NULL,'xosooe1384219311','haiyan51',1395371284,1),(32,0,0,'','行业案例',NULL,'xosooe1384219311','haiyan51',1395371311,1),(33,0,0,'','行业案例',NULL,'xosooe1384219311','haiyan51',1395371546,1),(34,0,0,'','行业案例',NULL,'xosooe1384219311','haiyan51',1395372111,0),(35,0,0,'','http://www.pinv.cc',NULL,'xosooe1384219311','haiyan',1395372324,0),(36,0,0,'行业案例','','http://www.pinv.cc','xosooe1384219311','haiyan',1395372347,1),(37,150,0,'行业案例','','http://www.pinv.cc','xosooe1384219311','haiyan',1395372427,0),(38,143,0,'会员卡','会员卡',NULL,'xosooe1384219311','haiyan',1395374183,0),(39,143,140,'会员卡','会员卡',NULL,'xosooe1384219311','haiyan',1395381913,0),(40,143,140,'会员卡','会员卡',NULL,'xosooe1384219311','haiyan',1395381914,1),(41,148,145,'大转盘','大转盘',NULL,'xosooe1384219311','haiyan',1395382097,0),(42,292,140,'360度全景','全景',NULL,'xosooe1384219311','haiyan',1395382127,0),(43,150,0,'行业案例','','http://www.pinv.cc','xosooe1384219311','haiyan',1395382181,1);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
