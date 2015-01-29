-- phpMyAdmin SQL Dump
-- version 3.3.7
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2014 年 04 月 10 日 11:57
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
-- 表的结构 `tp_product_shop`
--

CREATE TABLE IF NOT EXISTS `tp_product_shop` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '商品的唯一ID',
  `bid` int(11) DEFAULT NULL COMMENT '商品的编号',
  `dt` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT '商品的价钱',
  `ds` int(2) DEFAULT NULL COMMENT '商品状态',
  `wecha_id` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `oprice` int(11) DEFAULT NULL,
  `payid` int(1) DEFAULT '0',
  `number` varchar(200) CHARACTER SET utf8 DEFAULT NULL COMMENT '订单编号',
  `pc` int(5) DEFAULT NULL,
  `sc` int(3) DEFAULT '1',
  `ex` int(11) DEFAULT NULL,
  `pt` int(2) DEFAULT '1',
  `address` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `moble` varchar(12) CHARACTER SET utf8 DEFAULT NULL,
  `user` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `token` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT 't',
  `il` text CHARACTER SET utf8 COMMENT '商品信息',
  `time` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `wecha_id` (`wecha_id`),
  KEY `token` (`token`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=233 ;
