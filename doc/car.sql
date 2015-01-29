USE `pinv`;

/*Table structure for table `tp_car_brand` */

DROP TABLE IF EXISTS `tp_car_brand`;

CREATE TABLE `tp_car_brand` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `token` char(16) NOT NULL,
  `brand` varchar(50) NOT NULL COMMENT '品牌名称',
  `url` varchar(50) NOT NULL DEFAULT '' COMMENT '官网',
  `logo` varchar(200) NOT NULL DEFAULT '',
  `sort` int(11) unsigned DEFAULT NULL,
  `info` varchar(200) NOT NULL DEFAULT '' COMMENT '品牌简介',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

/*Data for the table `tp_car_brand` */

insert  into `tp_car_brand`(`id`,`token`,`brand`,`url`,`logo`,`sort`,`info`) values (3,'xosooe1384219311','福特','http://www.ford.com.cn','http://www.pinv.com/PUBLIC/imagess/xosooe1384219311/534756e305292.jpg',3,'福特（Ford）是世界著名的汽车品牌，为美国福特汽车公司旗下的众多品牌之一，公司及品牌名“福特”来源..'),(4,'xosooe1384219311','宝马','http://www.bmw.com.cn/cn/zh/index.html','http://www.pinv.com/PUBLIC/imagess/xosooe1384219311/534756c79ed4d.jpg',1,''),(5,'','SSsssdf','SSSSSS','./tpl/User/default/common/car/car_logo.png',1,''),(6,'','SSsssdf','SSS','./tpl/User/default/common/car/car_logo.png',1,''),(7,'','AAA','AAAA','./tpl/User/default/common/car/car_logo.png',1,''),(16,'xosooe1384219311','aaaasss','ssss','./tpl/User/pinv/common/img/car/car_logo.png',1,''),(14,'xosooe1384219311','ssssssssdf','aaaaaa','./tpl/User/pinv/common/img/car/car_logo.png',1,'');

/*Table structure for table `tp_car_model` */

DROP TABLE IF EXISTS `tp_car_model`;

CREATE TABLE `tp_car_model` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `token` char(16) NOT NULL,
  `model` varchar(50) NOT NULL COMMENT '车型名称',
  `brand_series` varchar(50) NOT NULL COMMENT '品牌/车系',
  `model_year` int(11) unsigned NOT NULL COMMENT '年款',
  `sort` int(11) unsigned NOT NULL COMMENT '显示顺序',
  `panorama_id` int(11) unsigned NOT NULL COMMENT '全景相册名称',
  `guide_price` decimal(10,3) NOT NULL COMMENT '官方指导价',
  `dealer_price` decimal(10,3) NOT NULL COMMENT '经销商报价',
  `emission` double NOT NULL COMMENT '排量',
  `stalls` tinyint(1) unsigned NOT NULL COMMENT '挡位个数',
  `box` tinyint(1) unsigned NOT NULL COMMENT '变速箱',
  `pic_url` varchar(200) NOT NULL COMMENT '图片',
  `s_id` int(11) unsigned NOT NULL COMMENT '车系id',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

/*Data for the table `tp_car_model` */

insert  into `tp_car_model`(`id`,`token`,`model`,`brand_series`,`model_year`,`sort`,`panorama_id`,`guide_price`,`dealer_price`,`emission`,`stalls`,`box`,`pic_url`,`s_id`) values (1,'xosooe1384219311','福特福克斯 2014自动AT','福特福克斯',12,1,0,'8.990','10.100',1,6,4,'http://www.pinv.com/PUBLIC/imagess/xosooe1384219311/53463f7fddc11.jpg',1),(5,'xosooe1384219311','福特福克斯 2014自动AT','福特福克斯',22,1,0,'0.000','0.000',1,1,1,'./tpl/User/pinv/common/img/car/car_jx.jpg',1),(6,'xosooe1384219311','福特福克斯 2014自动AT1111222','福特福克斯',22,1,0,'0.000','0.000',1,1,1,'./tpl/User/pinv/common/img/car/car_jx.jpg',1),(7,'xosooe1384219311','ssssssssddsdsdf','福特福克斯',22,1,0,'0.000','0.000',1,1,1,'./tpl/User/pinv/common/img/car/car_jx.jpg',1);

/*Table structure for table `tp_car_news` */

DROP TABLE IF EXISTS `tp_car_news`;

CREATE TABLE `tp_car_news` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `token` char(16) NOT NULL,
  `news_id` int(11) unsigned NOT NULL COMMENT '新闻id',
  `pre_id` int(11) unsigned NOT NULL COMMENT '最新优惠',
  `usedcar_id` int(11) unsigned NOT NULL COMMENT '尊享二手车',
  `album_id` int(11) unsigned NOT NULL COMMENT '品牌相册',
  `company_id` int(11) unsigned NOT NULL COMMENT '店铺LBS',
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `tp_car_news` */

insert  into `tp_car_news`(`id`,`token`,`news_id`,`pre_id`,`usedcar_id`,`album_id`,`company_id`) values (2,'xosooe1384219311',356,362,356,13,164);

/*Table structure for table `tp_car_owner_care` */

DROP TABLE IF EXISTS `tp_car_owner_care`;

CREATE TABLE `tp_car_owner_care` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `token` char(16) NOT NULL,
  `keyword` varchar(50) NOT NULL COMMENT '触发关键词',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `head_url` varchar(200) NOT NULL DEFAULT '' COMMENT '图文消息封面',
  `info` text NOT NULL COMMENT '介绍/内容',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `tp_car_owner_care` */

insert  into `tp_car_owner_care`(`id`,`token`,`keyword`,`title`,`head_url`,`info`) values (3,'xosooe1384219311','车主关怀','这是新添加的车主关怀sss','/tpl/User/pinv/common/img/car/carowner.png','这是新添加的车主关怀');

/*Table structure for table `tp_car_owners` */

DROP TABLE IF EXISTS `tp_car_owners`;

CREATE TABLE `tp_car_owners` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `token` char(16) NOT NULL,
  `wecha_id` char(28) NOT NULL,
  `series_id` int(11) NOT NULL COMMENT '车系id',
  `brand_id` int(11) NOT NULL COMMENT '品牌id',
  `car_no` varchar(20) NOT NULL,
  `car_userName` varchar(50) NOT NULL,
  `car_startTime` varchar(50) NOT NULL,
  `car_insurance_lastDate` varchar(50) DEFAULT NULL,
  `car_insurance_lastCost` decimal(10,2) DEFAULT NULL,
  `car_care_mileage` int(11) unsigned DEFAULT NULL,
  `user_tel` char(11) NOT NULL,
  `car_care_lastDate` varchar(50) DEFAULT NULL,
  `car_care_lastCost` decimal(10,2) DEFAULT NULL,
  `kfinfo` varchar(200) DEFAULT '',
  `insurance_Date` varchar(50) DEFAULT NULL,
  `insurance_Cost` decimal(10,2) DEFAULT NULL,
  `care_mileage` int(11) unsigned DEFAULT NULL,
  `car_care_Date` varchar(50) DEFAULT NULL,
  `car_care_Cost` decimal(10,2) DEFAULT NULL,
  `car_buyTime` varchar(50) NOT NULL DEFAULT '',
  `car_care_inspection` varchar(50) DEFAULT '',
  `care_next_mileage` int(11) unsigned DEFAULT '0',
  `next_care_inspection` varchar(50) DEFAULT '',
  `next_insurance_Date` varchar(50) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='3G car';

/*Data for the table `tp_car_owners` */

insert  into `tp_car_owners`(`id`,`token`,`wecha_id`,`series_id`,`brand_id`,`car_no`,`car_userName`,`car_startTime`,`car_insurance_lastDate`,`car_insurance_lastCost`,`car_care_mileage`,`user_tel`,`car_care_lastDate`,`car_care_lastCost`,`kfinfo`,`insurance_Date`,`insurance_Cost`,`care_mileage`,`car_care_Date`,`car_care_Cost`,`car_buyTime`,`car_care_inspection`,`care_next_mileage`,`next_care_inspection`,`next_insurance_Date`) values (9,'xosooe1384219311','miaomiao',4,2,'京A12344','王强','1398873600',NULL,NULL,NULL,'15101000709',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,'1391184000','',0,'',''),(10,'xosooe1384219311','haiyan',1,3,'京A12345','吴吴','1388505600',NULL,NULL,NULL,'15190996789',NULL,NULL,'',NULL,NULL,NULL,NULL,NULL,'1383235200','',0,'','');

/*Table structure for table `tp_car_reservation` */

DROP TABLE IF EXISTS `tp_car_reservation`;

CREATE TABLE `tp_car_reservation` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `token` char(16) NOT NULL,
  `keyword` varchar(50) NOT NULL,
  `title` varchar(50) NOT NULL,
  `picurl` varchar(200) NOT NULL,
  `addtype` varchar(20) NOT NULL DEFAULT 'house_book',
  `address` varchar(50) NOT NULL,
  `place` varchar(50) DEFAULT NULL,
  `longitude` varchar(13) NOT NULL,
  `latitude` varchar(13) NOT NULL,
  `tel` varchar(13) NOT NULL,
  `headpic` varchar(200) NOT NULL,
  `info` varchar(200) NOT NULL,
  `typename` varchar(50) DEFAULT NULL,
  `typename2` varchar(50) DEFAULT NULL,
  `typename3` varchar(50) DEFAULT NULL,
  `type` tinyint(4) DEFAULT '1',
  `date` varchar(50) DEFAULT NULL,
  `allnums` varchar(50) DEFAULT NULL,
  `name_show` tinyint(4) DEFAULT '1',
  `time_show` tinyint(4) DEFAULT '1',
  `txt1` varchar(50) DEFAULT NULL,
  `value1` varchar(50) DEFAULT NULL,
  `txt2` varchar(50) DEFAULT NULL,
  `value2` varchar(50) DEFAULT NULL,
  `txt3` varchar(50) DEFAULT NULL,
  `value3` varchar(50) DEFAULT NULL,
  `txt4` varchar(50) DEFAULT NULL,
  `value4` varchar(50) DEFAULT NULL,
  `txt5` varchar(50) DEFAULT NULL,
  `value5` varchar(50) DEFAULT NULL,
  `select1` varchar(50) DEFAULT NULL,
  `svalue1` varchar(100) DEFAULT NULL,
  `select2` varchar(50) DEFAULT NULL,
  `svalue2` varchar(100) DEFAULT NULL,
  `select3` varchar(50) DEFAULT NULL,
  `svalue3` varchar(100) DEFAULT NULL,
  `select4` varchar(50) DEFAULT NULL,
  `svalue4` varchar(100) DEFAULT NULL,
  `select5` varchar(50) DEFAULT NULL,
  `svalue5` varchar(100) DEFAULT NULL,
  `datename` varchar(100) DEFAULT NULL,
  `take` int(11) unsigned NOT NULL DEFAULT '1',
  `email` varchar(30) DEFAULT NULL,
  `open_email` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `sms` varchar(13) DEFAULT NULL,
  `open_sms` tinyint(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

/*Data for the table `tp_car_reservation` */

insert  into `tp_car_reservation`(`id`,`token`,`keyword`,`title`,`picurl`,`addtype`,`address`,`place`,`longitude`,`latitude`,`tel`,`headpic`,`info`,`typename`,`typename2`,`typename3`,`type`,`date`,`allnums`,`name_show`,`time_show`,`txt1`,`value1`,`txt2`,`value2`,`txt3`,`value3`,`txt4`,`value4`,`txt5`,`value5`,`select1`,`svalue1`,`select2`,`svalue2`,`select3`,`svalue3`,`select4`,`svalue4`,`select5`,`svalue5`,`datename`,`take`,`email`,`open_email`,`sms`,`open_sms`) values (9,'xosooe1384219311','预约保养','ssBSD-','./tpl/User/pinv/common/img/car/car_jx.jpg','maintain','',NULL,'','','86-10-8415090','./tpl/User/pinv/common/img/car/car_jx.jpg','',NULL,NULL,NULL,1,NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,NULL,0),(8,'xosooe1384219311','预约试驾','预约试驾','./tpl/User/pinv/common/img/car/car_jx.jpg','drive','北京市朝阳区京顺路111号凯德Mall1层',NULL,'113.78','36.78','86-10-8415090','./tpl/User/pinv/common/img/car/car_jx.jpg','adfafasdfadsf',NULL,NULL,NULL,1,NULL,NULL,1,1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,0,NULL,0);

/*Table structure for table `tp_car_reservebook` */

DROP TABLE IF EXISTS `tp_car_reservebook`;

CREATE TABLE `tp_car_reservebook` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `rid` int(11) unsigned NOT NULL,
  `token` char(16) NOT NULL,
  `wecha_id` char(16) NOT NULL,
  `truename` varchar(50) NOT NULL,
  `tel` varchar(13) NOT NULL,
  `addtype` varchar(50) NOT NULL COMMENT '预约类型',
  `series_id` int(11) NOT NULL DEFAULT '0',
  `brand_id` int(11) NOT NULL DEFAULT '0',
  `dateline` varchar(50) NOT NULL,
  `timepart` varchar(50) NOT NULL,
  `info` varchar(100) NOT NULL DEFAULT '',
  `booktime` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `token` (`token`),
  KEY `wecha_id` (`wecha_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

/*Data for the table `tp_car_reservebook` */

insert  into `tp_car_reservebook`(`id`,`rid`,`token`,`wecha_id`,`truename`,`tel`,`addtype`,`series_id`,`brand_id`,`dateline`,`timepart`,`info`,`booktime`) values (4,7,'xosooe1384219311','haiyan','吴吴','15190996789','maintain',4,2,'1398873600','8:00-9:00','森',1396830935);

/*Table structure for table `tp_car_saler` */

DROP TABLE IF EXISTS `tp_car_saler`;

CREATE TABLE `tp_car_saler` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `token` char(16) NOT NULL,
  `name` varchar(50) NOT NULL COMMENT '姓名',
  `picture` varchar(200) NOT NULL COMMENT '头像',
  `mobile` char(13) NOT NULL COMMENT '电话',
  `sort` tinyint(4) unsigned NOT NULL COMMENT '显示顺序',
  `salestype` tinyint(1) unsigned NOT NULL COMMENT '类型',
  `info` varchar(200) NOT NULL COMMENT '介绍',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

/*Data for the table `tp_car_saler` */

insert  into `tp_car_saler`(`id`,`token`,`name`,`picture`,`mobile`,`sort`,`salestype`,`info`) values (1,'xosooe1384219311','赵延珂','tpl/User/pinv/common/img/car/car_sell.png','13370181903',1,2,'赵延珂'),(2,'xosooe1384219311','景辉','tpl/User/pinv/common/img/car/car_sell.png','13370181906',1,1,'景辉'),(8,'xosooe1384219311','森ss','./tpl/User/pinv/common/img/car/car_sell.png','13370181903',1,1,'');

/*Table structure for table `tp_car_series` */

DROP TABLE IF EXISTS `tp_car_series`;

CREATE TABLE `tp_car_series` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(11) unsigned NOT NULL COMMENT '品牌id',
  `brand` varchar(50) NOT NULL COMMENT '品牌名称',
  `token` char(16) NOT NULL,
  `name` varchar(50) NOT NULL COMMENT '车系名称',
  `shortname` varchar(50) NOT NULL COMMENT '车系简称',
  `picture` varchar(200) NOT NULL COMMENT '图片',
  `sort` int(11) unsigned NOT NULL COMMENT '显示顺序',
  `info` varchar(200) NOT NULL COMMENT '车系亮点',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

/*Data for the table `tp_car_series` */

insert  into `tp_car_series`(`id`,`brand_id`,`brand`,`token`,`name`,`shortname`,`picture`,`sort`,`info`) values (1,3,'福特','xosooe1384219311','福特福克斯','福克斯','http://www.pinv.com/PUBLIC/imagess/xosooe1384219311/53368a03a424a.jpg',2,'Focus的意思就是焦点，它是欧洲福特的看家车型之一。与那些已经进化了四、五代的车型相比，福克斯显然是个小字辈，它诞生与1998年，到今天才发展到第三代。啊打发啊打发啊啊'),(4,3,'福特','xosooe1384219311','福特福克斯sss','福特福克斯','http://www.pinv.com/PUBLIC/imagess/xosooe1384219311/5338bee8bcbc7.jpg',1,'&lt;span style=&quot;color: rgb(51, 51, 51); font-family: NSimSun; line-height: 20px; background-color: rgb(249, 249, 249);&quot;&gt;涡轮发动机动力强劲；操控性能优秀；内部空间充足。&lt;/span&gt;&lt;br /&gt;'),(8,3,'福特','xosooe1384219311','福特福克','SSSS','./tpl/User/pinv/common/img/car/car_jx.jpg',1,''),(9,2,'大众','xosooe1384219311','sss','ssSSSS','./tpl/User/pinv/common/img/car/car_jx.jpg',1,''),(7,2,'大众','xosooe1384219311','POLO','POLO','./tpl/User/pinv/common/img/car/car_jx.jpg',1,''),(10,2,'大众','xosooe1384219311','sss森','ssssSSSS','./tpl/User/pinv/common/img/car/car_jx.jpg',1,''),(11,3,'福特','xosooe1384219311','ssss','sssSSSS','./tpl/User/pinv/common/img/car/car_jx.jpg',1,'');

/*Table structure for table `tp_car_set` */

DROP TABLE IF EXISTS `tp_car_set`;

CREATE TABLE `tp_car_set` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `token` char(16) NOT NULL,
  `keyword` varchar(50) NOT NULL COMMENT '触发关键词',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '图文标题',
  `head_url` varchar(200) NOT NULL DEFAULT '' COMMENT '回复封面图片',
  `url` varchar(200) NOT NULL COMMENT '图文外链',
  `title1` varchar(50) NOT NULL DEFAULT '' COMMENT '经销车型',
  `title2` varchar(50) NOT NULL DEFAULT '' COMMENT '销售顾问',
  `title3` varchar(50) NOT NULL DEFAULT '' COMMENT '在线预约',
  `title4` varchar(50) NOT NULL DEFAULT '' COMMENT '车主关怀',
  `title5` varchar(50) NOT NULL DEFAULT '' COMMENT '实用工具',
  `title6` varchar(50) NOT NULL DEFAULT '' COMMENT '车型欣赏',
  `head_url_1` varchar(200) NOT NULL DEFAULT '' COMMENT '经销车型图片',
  `head_url_2` varchar(200) NOT NULL DEFAULT '' COMMENT '销售顾问图片',
  `head_url_3` varchar(200) NOT NULL DEFAULT '' COMMENT '在线预约题图片',
  `head_url_4` varchar(200) NOT NULL DEFAULT '' COMMENT '车主关怀图片',
  `head_url_5` varchar(200) NOT NULL DEFAULT '' COMMENT '实用工具题图片',
  `head_url_6` varchar(200) NOT NULL DEFAULT '' COMMENT '车型欣赏图片',
  `url1` varchar(200) NOT NULL DEFAULT '' COMMENT '经销车型图文外链',
  `url2` varchar(200) NOT NULL DEFAULT '' COMMENT '销售顾问图文外链',
  `url3` varchar(200) NOT NULL DEFAULT '' COMMENT '在线预约图文外链',
  `url4` varchar(200) NOT NULL DEFAULT '' COMMENT '车主关怀图文外链',
  `url5` varchar(200) NOT NULL DEFAULT '' COMMENT '实用工具图文外链',
  `url6` varchar(200) NOT NULL DEFAULT '' COMMENT '车型欣赏图文外链',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `tp_car_set` */

insert  into `tp_car_set`(`id`,`token`,`keyword`,`title`,`head_url`,`url`,`title1`,`title2`,`title3`,`title4`,`title5`,`title6`,`head_url_1`,`head_url_2`,`head_url_3`,`head_url_4`,`head_url_5`,`head_url_6`,`url1`,`url2`,`url3`,`url4`,`url5`,`url6`) values (3,'xosooe1384219311','','微汽车','http://www.pinv.com/PUBLIC/imagess/xosooe1384219311/534150895d52a.png','http://www.wzhi.cc/index.php?g=Wap&amp;m=Car&amp;a=index&amp;token=xosooe1384219311','经销车型','销售顾问','在线预约','车主关怀','实用工具','车型欣赏','http://www.wzhi.cc/tpl/User/pinv/common/img/car/car_jx.jpg','http://www.wzhi.cc/tpl/User/pinv/common/img/car/car_yuyue.jpg','http://www.wzhi.cc/tpl/User/pinv/common/img/car/car_yuyue.jpg','http://www.wzhi.cc/tpl/User/pinv/common/img/car/carowner.png','http://www.wzhi.cc/tpl/User/pinv/common/img/car/tool-box-preferences.png','http://www.wzhi.cc/tpl/User/pinv/common/img/car/lanbo14.jpg','http://www.wzhi.cc/index.php?g=Wap&amp;m=Car&amp;a=brands&amp;token=xosooe1384219311','http://www.wzhi.cc/index.php?g=Wap&amp;m=Car&amp;a=salers&amp;token=xosooe1384219311','http://www.wzhi.cc/index.php?g=Wap&amp;m=Car&amp;a=CarReserveBook&amp;token=xosooe1384219311&amp;addtype=drive','http://www.wzhi.cc/index.php?g=Wap&amp;m=Car&amp;a=owner&amp;token=xosooe1384219311','http://www.wzhi.cc/index.php?g=Wap&amp;m=Car&amp;a=tool&amp;token=xosooe1384219311','http://baidu.com');
