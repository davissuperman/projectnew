-- -----------------------------
--  用户表  已经关注的用户和系统对话时获取/刚关注的用户做关注时得到
-- -------------------------------

CREATE TABLE tp_customer_service_fans(
	openid char(28) NOT NULL DEFAULT '' COMMENT '微信openid',	
	token char(16) NOT NULL DEFAULT ''  COMMENT '公众号唯一标识',	
	nickname varchar(20) NOT NULL DEFAULT ''  COMMENT '昵称',	 
	`sex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT ' 2 女 1 男  值为0时是未知',
	province varchar(20) NOT NULL DEFAULT ''  COMMENT '省',	
	city varchar(20) NOT NULL DEFAULT ''  COMMENT '城市',
	headimgurl varchar(150) NOT NULL DEFAULT ''  COMMENT '头像',
	tel varchar(15) NOT NULL DEFAULT ''  COMMENT '电话',
	remark varchar(20) NOT NULL DEFAULT ''  COMMENT '备注',
	gid int(10) unsigned NOT NULL DEFAULT '0' COMMENT'组id',
	subscribe_time int(10) unsigned NOT NULL DEFAULT '0' COMMENT'关注时间',
	PRIMARY KEY (openid),	
	KEY token (token),
	KEY gid(gid)	
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- 商品表
-- ----------------------------
DROP TABLE IF EXISTS `tp_product`;
CREATE TABLE `tp_product` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `catid` mediumint(4) NOT NULL DEFAULT '0' COMMENT '所属类别ID',
  `storeid` mediumint(4) NOT NULL DEFAULT '0' COMMENT '商户ID',
  `name` varchar(150) NOT NULL DEFAULT '' COMMENT '商品名称',
  `price` float NOT NULL DEFAULT '0' COMMENT '活动价格',
  `oprice` float NOT NULL DEFAULT '0' COMMENT '商品原价',
  `discount` float DEFAULT '10' COMMENT '商品折扣',
  `intro` text NOT NULL COMMENT '商品详细信息',
  `token` varchar(50) NOT NULL COMMENT '用户token',
  `keyword` varchar(100) NOT NULL DEFAULT '' COMMENT '回复关键字',
  `etalon` text NOT NULL COMMENT '规格参数',
  `index_logo` varchar(255) NOT NULL COMMENT '首页LOGO',
  `logourl` text NOT NULL COMMENT '商品展示图片',
  `time` int(10) NOT NULL DEFAULT '0' COMMENT '时间',
  `s_goods` smallint(2) NOT NULL COMMENT '商品属性',
  `repertory` int(6) NOT NULL COMMENT '商品库存',
  `recommend` varchar(255) NOT NULL COMMENT '推荐',
  `freight` varchar(20) NOT NULL COMMENT '运费方式',
  `color` varchar(255) NOT NULL COMMENT '商品颜色',
  PRIMARY KEY (`id`),
  KEY `catid` (`catid`,`storeid`),
  KEY `catid_2` (`catid`),
  KEY `storeid` (`storeid`),
  KEY `token` (`token`),
  KEY `price` (`price`),
  KEY `oprice` (`oprice`),
  KEY `discount` (`discount`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
-- ----------------------------
-- 商品订单表
-- ----------------------------
DROP TABLE IF EXISTS `tp_product_shop`;
CREATE TABLE `tp_product_shop` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '商品的唯一ID',
  `bid` int(11) DEFAULT NULL COMMENT '商品的编号',
  `dt` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT '商品的价钱',
  `ds` int(2) DEFAULT NULL COMMENT '商品状态',
  `wecha_id` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '用户唯一标示',
  `oprice` int(11) DEFAULT NULL COMMENT '总价格',
  `payid` int(1) DEFAULT '0' COMMENT '是否付款',
  `number` varchar(200) CHARACTER SET utf8 DEFAULT NULL COMMENT '订单编号',
  `pc` int(5) DEFAULT NULL COMMENT '付款方式',
  `sc` int(3) DEFAULT '1' COMMENT '付款状态',
  `ex` int(11) DEFAULT NULL COMMENT '商品编号',
  `pt` int(2) DEFAULT '1' COMMENT '商品状态',
  `address` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '收货地址',
  `moble` varchar(12) CHARACTER SET utf8 DEFAULT NULL COMMENT '用户手机号',
  `user` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '用户',
  `token` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '商户标示',
  `il` text CHARACTER SET utf8 COMMENT '商品信息',
  `time` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
--
-- LBS 用户位置信息表 `tp_company_location`
--

CREATE TABLE IF NOT EXISTS `tp_company_location` (
  `openid` char(29) NOT NULL DEFAULT '' COMMENT '用户id',
  `token` char(16) NOT NULL DEFAULT '' COMMENT '公众号唯一标识',
  `latitude` decimal(10,7) NOT NULL COMMENT '纬度',
  `longitude` decimal(10,7) NOT NULL COMMENT '经度',
  `lasttime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后更新时间',
  PRIMARY KEY (`openid`),
  KEY `lasttime` (`lasttime`),
  KEY `token` (`token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- LBS 商家统计 `tp_company_nav`
--

CREATE TABLE IF NOT EXISTS `tp_company_nav` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL COMMENT '导航类型：1店铺导航,2活动导航',
  `openid` char(28) NOT NULL DEFAULT '' COMMENT '用户wecha_id',
  `token` char(16) NOT NULL DEFAULT '' COMMENT '商家token',
  `shop_id` int(11) unsigned NOT NULL COMMENT '店铺id',
  `time` int(10) unsigned NOT NULL COMMENT '导航时间(时间戳)',
  `date` date NOT NULL COMMENT '导航日期(Y-m-d)',
  `hour` tinyint(2) unsigned NOT NULL COMMENT '导航时间(h)',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `token` (`token`),
  KEY `shop_id` (`shop_id`),
  KEY `hour` (`hour`),
  KEY `date` (`date`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
--
-- LBS 商家信息表 `tp_company`
--
CREATE TABLE IF NOT EXISTS `tp_company` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '店铺id',
  `token` char(16) NOT NULL DEFAULT '' COMMENT '店铺商家唯一标识符',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '店铺名',
  `shortname` varchar(50) NOT NULL DEFAULT '' COMMENT '店铺简称',
  `mp` varchar(13) NOT NULL DEFAULT '' COMMENT '店铺座机',
  `tel` varchar(15) NOT NULL DEFAULT '' COMMENT '店铺手机号',
  `address` varchar(150) NOT NULL DEFAULT '' COMMENT '店铺地址',
  `city` varchar(15) NOT NULL DEFAULT '' COMMENT '店铺所在城市',
  `latitude` decimal(10,7) NOT NULL COMMENT '店铺坐标X',
  `longitude` decimal(10,7) NOT NULL COMMENT '店铺坐标Y',
  `intro` text NOT NULL COMMENT '店铺简介',
  `taxis` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排列顺序',
  `isbranch` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否是分支机构:0为总公司，1为分支机构',
  `logourl` varchar(180) NOT NULL DEFAULT '' COMMENT '店铺简介LOGO',
  `active` varchar(150) DEFAULT NULL COMMENT '店铺活动信息',
  PRIMARY KEY (`id`),
  KEY `token` (`token`),
  KEY `isbranch` (`token`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`latitude`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;