-- phpMyAdmin SQL Dump
-- version 3.3.7
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2014 年 04 月 10 日 11:55
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
-- 表的结构 `tp_product_order`
--

CREATE TABLE IF NOT EXISTS `tp_product_order` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '用户名',
  `pvid` int(10) DEFAULT NULL COMMENT '城市ID',
  `ctid` int(10) DEFAULT NULL COMMENT '市区ID',
  `regionId` int(10) DEFAULT NULL COMMENT '县级',
  `mobile` varchar(11) CHARACTER SET utf8 DEFAULT NULL COMMENT '手机号',
  `address` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '用户地址',
  `token` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '商户唯一标示',
  `wecha_id` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '用户唯一标示',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=108 ;
