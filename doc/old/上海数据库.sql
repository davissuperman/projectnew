-- -----------------------------
--  �û���  �Ѿ���ע���û���ϵͳ�Ի�ʱ��ȡ/�չ�ע���û�����עʱ�õ�
-- -------------------------------

CREATE TABLE tp_customer_service_fans(
	openid char(28) NOT NULL DEFAULT '' COMMENT '΢��openid',	
	token char(16) NOT NULL DEFAULT ''  COMMENT '���ں�Ψһ��ʶ',	
	nickname varchar(20) NOT NULL DEFAULT ''  COMMENT '�ǳ�',	 
	`sex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT ' 2 Ů 1 ��  ֵΪ0ʱ��δ֪',
	province varchar(20) NOT NULL DEFAULT ''  COMMENT 'ʡ',	
	city varchar(20) NOT NULL DEFAULT ''  COMMENT '����',
	headimgurl varchar(150) NOT NULL DEFAULT ''  COMMENT 'ͷ��',
	tel varchar(15) NOT NULL DEFAULT ''  COMMENT '�绰',
	remark varchar(20) NOT NULL DEFAULT ''  COMMENT '��ע',
	gid int(10) unsigned NOT NULL DEFAULT '0' COMMENT'��id',
	subscribe_time int(10) unsigned NOT NULL DEFAULT '0' COMMENT'��עʱ��',
	PRIMARY KEY (openid),	
	KEY token (token),
	KEY gid(gid)	
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- ��Ʒ��
-- ----------------------------
DROP TABLE IF EXISTS `tp_product`;
CREATE TABLE `tp_product` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '����ID',
  `catid` mediumint(4) NOT NULL DEFAULT '0' COMMENT '�������ID',
  `storeid` mediumint(4) NOT NULL DEFAULT '0' COMMENT '�̻�ID',
  `name` varchar(150) NOT NULL DEFAULT '' COMMENT '��Ʒ����',
  `price` float NOT NULL DEFAULT '0' COMMENT '��۸�',
  `oprice` float NOT NULL DEFAULT '0' COMMENT '��Ʒԭ��',
  `discount` float DEFAULT '10' COMMENT '��Ʒ�ۿ�',
  `intro` text NOT NULL COMMENT '��Ʒ��ϸ��Ϣ',
  `token` varchar(50) NOT NULL COMMENT '�û�token',
  `keyword` varchar(100) NOT NULL DEFAULT '' COMMENT '�ظ��ؼ���',
  `etalon` text NOT NULL COMMENT '������',
  `index_logo` varchar(255) NOT NULL COMMENT '��ҳLOGO',
  `logourl` text NOT NULL COMMENT '��ƷչʾͼƬ',
  `time` int(10) NOT NULL DEFAULT '0' COMMENT 'ʱ��',
  `s_goods` smallint(2) NOT NULL COMMENT '��Ʒ����',
  `repertory` int(6) NOT NULL COMMENT '��Ʒ���',
  `recommend` varchar(255) NOT NULL COMMENT '�Ƽ�',
  `freight` varchar(20) NOT NULL COMMENT '�˷ѷ�ʽ',
  `color` varchar(255) NOT NULL COMMENT '��Ʒ��ɫ',
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
-- ��Ʒ������
-- ----------------------------
DROP TABLE IF EXISTS `tp_product_shop`;
CREATE TABLE `tp_product_shop` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '��Ʒ��ΨһID',
  `bid` int(11) DEFAULT NULL COMMENT '��Ʒ�ı��',
  `dt` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT '��Ʒ�ļ�Ǯ',
  `ds` int(2) DEFAULT NULL COMMENT '��Ʒ״̬',
  `wecha_id` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '�û�Ψһ��ʾ',
  `oprice` int(11) DEFAULT NULL COMMENT '�ܼ۸�',
  `payid` int(1) DEFAULT '0' COMMENT '�Ƿ񸶿�',
  `number` varchar(200) CHARACTER SET utf8 DEFAULT NULL COMMENT '�������',
  `pc` int(5) DEFAULT NULL COMMENT '���ʽ',
  `sc` int(3) DEFAULT '1' COMMENT '����״̬',
  `ex` int(11) DEFAULT NULL COMMENT '��Ʒ���',
  `pt` int(2) DEFAULT '1' COMMENT '��Ʒ״̬',
  `address` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '�ջ���ַ',
  `moble` varchar(12) CHARACTER SET utf8 DEFAULT NULL COMMENT '�û��ֻ���',
  `user` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '�û�',
  `token` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '�̻���ʾ',
  `il` text CHARACTER SET utf8 COMMENT '��Ʒ��Ϣ',
  `time` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT 'ʱ��',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
--
-- LBS �û�λ����Ϣ�� `tp_company_location`
--

CREATE TABLE IF NOT EXISTS `tp_company_location` (
  `openid` char(29) NOT NULL DEFAULT '' COMMENT '�û�id',
  `token` char(16) NOT NULL DEFAULT '' COMMENT '���ں�Ψһ��ʶ',
  `latitude` decimal(10,7) NOT NULL COMMENT 'γ��',
  `longitude` decimal(10,7) NOT NULL COMMENT '����',
  `lasttime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '������ʱ��',
  PRIMARY KEY (`openid`),
  KEY `lasttime` (`lasttime`),
  KEY `token` (`token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- LBS �̼�ͳ�� `tp_company_nav`
--

CREATE TABLE IF NOT EXISTS `tp_company_nav` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) unsigned NOT NULL COMMENT '�������ͣ�1���̵���,2�����',
  `openid` char(28) NOT NULL DEFAULT '' COMMENT '�û�wecha_id',
  `token` char(16) NOT NULL DEFAULT '' COMMENT '�̼�token',
  `shop_id` int(11) unsigned NOT NULL COMMENT '����id',
  `time` int(10) unsigned NOT NULL COMMENT '����ʱ��(ʱ���)',
  `date` date NOT NULL COMMENT '��������(Y-m-d)',
  `hour` tinyint(2) unsigned NOT NULL COMMENT '����ʱ��(h)',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `token` (`token`),
  KEY `shop_id` (`shop_id`),
  KEY `hour` (`hour`),
  KEY `date` (`date`),
  KEY `time` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
--
-- LBS �̼���Ϣ�� `tp_company`
--
CREATE TABLE IF NOT EXISTS `tp_company` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '����id',
  `token` char(16) NOT NULL DEFAULT '' COMMENT '�����̼�Ψһ��ʶ��',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '������',
  `shortname` varchar(50) NOT NULL DEFAULT '' COMMENT '���̼��',
  `mp` varchar(13) NOT NULL DEFAULT '' COMMENT '��������',
  `tel` varchar(15) NOT NULL DEFAULT '' COMMENT '�����ֻ���',
  `address` varchar(150) NOT NULL DEFAULT '' COMMENT '���̵�ַ',
  `city` varchar(15) NOT NULL DEFAULT '' COMMENT '�������ڳ���',
  `latitude` decimal(10,7) NOT NULL COMMENT '��������X',
  `longitude` decimal(10,7) NOT NULL COMMENT '��������Y',
  `intro` text NOT NULL COMMENT '���̼��',
  `taxis` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '����˳��',
  `isbranch` tinyint(1) NOT NULL DEFAULT '0' COMMENT '�Ƿ��Ƿ�֧����:0Ϊ�ܹ�˾��1Ϊ��֧����',
  `logourl` varchar(180) NOT NULL DEFAULT '' COMMENT '���̼��LOGO',
  `active` varchar(150) DEFAULT NULL COMMENT '���̻��Ϣ',
  PRIMARY KEY (`id`),
  KEY `token` (`token`),
  KEY `isbranch` (`token`),
  KEY `latitude` (`latitude`),
  KEY `longitude` (`latitude`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;