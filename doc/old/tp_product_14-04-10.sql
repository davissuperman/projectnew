SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

DROP TABLE IF EXISTS `tp_product`;

CREATE TABLE IF NOT EXISTS `tp_product` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `catid` mediumint(4) NOT NULL DEFAULT '0',
  `storeid` mediumint(4) NOT NULL DEFAULT '0',
  `name` varchar(150) NOT NULL DEFAULT '',
  `price` float NOT NULL DEFAULT '0',
  `oprice` float NOT NULL DEFAULT '0',
  `discount` float DEFAULT '10',
  `intro` text NOT NULL,
  `token` varchar(50) NOT NULL,
  `keyword` varchar(100) NOT NULL DEFAULT '',
  `etalon` text NOT NULL,
  `dining` tinyint(1) NOT NULL DEFAULT '0',
  `endtime` int(11) NOT NULL DEFAULT '0',
  `index_logo` varchar(255) NOT NULL COMMENT '首页LOGO',
  `logourl` text NOT NULL,
  `time` int(10) NOT NULL DEFAULT '0',
  `s_goods` smallint(2) NOT NULL COMMENT '商品属性',
  `repertory` int(6) NOT NULL,
  `recommend` varchar(255) NOT NULL COMMENT '推荐',
  `present` smallint(5) NOT NULL COMMENT '赠送积分',
  `freight` int(1) NOT NULL,
  `freightprice` float(5,0) NOT NULL,
  `credits` smallint(5) NOT NULL COMMENT '积分购买金额',
  `color` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `catid` (`catid`,`storeid`),
  KEY `catid_2` (`catid`),
  KEY `storeid` (`storeid`),
  KEY `token` (`token`),
  KEY `price` (`price`),
  KEY `oprice` (`oprice`),
  KEY `discount` (`discount`),
  KEY `dining` (`dining`),
  KEY `groupon` (`endtime`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=168 ;


CREATE TABLE IF NOT EXISTS `tp_product_brand` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(255) CHARACTER SET utf8 NOT NULL,
  `url` varchar(255) CHARACTER SET utf8 NOT NULL,
  `text` text CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

DROP TABLE IF EXISTS `tp_product_cat`;

CREATE TABLE IF NOT EXISTS `tp_product_cat` (
  `id` mediumint(4) NOT NULL AUTO_INCREMENT,
  `sid` smallint(11) NOT NULL COMMENT '子分类',
  `token` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL,
  `des` varchar(500) NOT NULL DEFAULT '',
  `parentid` mediumint(4) NOT NULL,
  `logourl` varchar(255) NOT NULL,
  `host` varchar(10) NOT NULL COMMENT '热门',
  `color` varchar(10) NOT NULL DEFAULT 'FFFFFF' COMMENT '颜色',
  `Ename` varchar(50) NOT NULL COMMENT '英文名称',
  `dining` tinyint(1) NOT NULL DEFAULT '0',
  `time` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `parentid` (`parentid`),
  KEY `token` (`token`),
  KEY `dining` (`dining`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=142 ;


CREATE TABLE IF NOT EXISTS `tp_product_express` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) CHARACTER SET utf8 DEFAULT NULL,
  `price` int(10) DEFAULT NULL,
  `token` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `is` mediumint(1) DEFAULT NULL,
  `sort` int(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=108 ;


CREATE TABLE IF NOT EXISTS `tp_product_pic` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `Shopic` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '商城背景大图',
  `Shopics` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '用户LOGO',
  `token` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '用户标示',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;


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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=233 ;


CREATE TABLE IF NOT EXISTS `tp_product_useradress` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自动增长',
  `token` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT '不同用户识别',
  `opengid` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT '用户ID',
  `name` varchar(50) NOT NULL,
  `address` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT '地址',
  `phone` varchar(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
