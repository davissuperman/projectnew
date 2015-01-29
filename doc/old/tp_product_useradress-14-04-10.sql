-- phpMyAdmin SQL Dump
-- version 3.3.7
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2014 年 04 月 10 日 11:59
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
-- 表的结构 `tp_product_useradress`
--

CREATE TABLE IF NOT EXISTS `tp_product_useradress` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自动增长',
  `token` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT '不同用户识别',
  `opengid` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `name` varchar(50) NOT NULL,
  `address` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '地址',
  `phone` varchar(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
