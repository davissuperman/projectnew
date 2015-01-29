-- phpMyAdmin SQL Dump
-- version 3.3.7
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2014 年 04 月 10 日 12:50
-- 服务器版本: 5.1.61
-- PHP 版本: 5.2.17p1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `pinv`
--

-- --------------------------------------------------------

--
-- 表的结构 `tp_province`
--

CREATE TABLE IF NOT EXISTS `tp_province` (
  `id` bigint(20) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `DateCreated` datetime DEFAULT NULL,
  `DateUpdated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `tp_province`
--

INSERT INTO `tp_province` (`id`, `name`, `DateCreated`, `DateUpdated`) VALUES
(1, '北京市', NULL, NULL),
(2, '天津市', NULL, NULL),
(3, '河北省', NULL, NULL),
(4, '山西省', NULL, NULL),
(5, '内蒙古自治区', NULL, NULL),
(6, '辽宁省', NULL, NULL),
(7, '吉林省', NULL, NULL),
(8, '黑龙江省', NULL, NULL),
(9, '上海市', NULL, NULL),
(10, '江苏省', NULL, NULL),
(11, '浙江省', NULL, NULL),
(12, '安徽省', NULL, NULL),
(13, '福建省', NULL, NULL),
(14, '江西省', NULL, NULL),
(15, '山东省', NULL, NULL),
(16, '河南省', NULL, NULL),
(17, '湖北省', NULL, NULL),
(18, '湖南省', NULL, NULL),
(19, '广东省', NULL, NULL),
(20, '广西壮族自治区', NULL, NULL),
(21, '海南省', NULL, NULL),
(22, '重庆市', NULL, NULL),
(23, '四川省', NULL, NULL),
(24, '贵州省', NULL, NULL),
(25, '云南省', NULL, NULL),
(26, '西藏自治区', NULL, NULL),
(27, '陕西省', NULL, NULL),
(28, '甘肃省', NULL, NULL),
(29, '青海省', NULL, NULL),
(30, '宁夏回族自治区', NULL, NULL),
(31, '新疆维吾尔自治区', NULL, NULL),
(32, '香港特别行政区', NULL, NULL),
(33, '澳门特别行政区', NULL, NULL),
(34, '台湾省', NULL, NULL);
