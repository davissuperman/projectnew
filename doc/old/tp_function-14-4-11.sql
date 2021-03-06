DROP TABLE IF EXISTS `tp_function`;

CREATE TABLE IF NOT EXISTS `tp_function` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gid` tinyint(3) NOT NULL,
  `usenum` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `funname` varchar(100) NOT NULL,
  `info` varchar(100) NOT NULL,
  `isserve` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `gid` (`gid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=55 ;

--
-- 转存表中的数据 `tp_function`
--

INSERT INTO `tp_function` (`id`, `gid`, `usenum`, `name`, `funname`, `info`, `isserve`, `status`) VALUES
(1, 5, 0, '天气查询', 'tianqi', '天气查询服务:例  城市名+天气', 1, 1),
(2, 5, 0, '糗事', 'qiushi', '糗事　直接发送糗事', 1, 0),
(3, 5, 0, '计算器', 'jishuan', '计算器使用方法　例：计算50-50　，计算100*100', 1, 1),
(4, 5, 0, '朗读', 'langdu', '朗读＋关键词　例：朗读爱微多用户营销系统', 1, 1),
(5, 5, 0, '健康指数查询', 'jiankang', '健康指数查询　健康＋高，＋重　例：健康170,65', 1, 1),
(6, 5, 0, '快递查询', 'kuaidi', '快递＋快递名＋快递号　例：快递顺丰117215889174', 1, 1),
(7, 7, 0, '笑话', 'xiaohua', '笑话　直接发送笑话', 1, 0),
(8, 5, 0, '藏头诗', 'changtoushi', ' 藏头诗+关键词　例：藏头诗我爱你', 1, 1),
(9, 7, 0, '陪聊', 'peiliao', '聊天　直接输入聊天关键词即可', 1, 0),
(10, 5, 0, '聊天', 'liaotian', '聊天　直接输入聊天关键词即可', 1, 0),
(11, 5, 0, '周公解梦', 'mengjian', '周公解梦　梦见+关键词　例如:梦见父母', 1, 1),
(12, 5, 0, '语音翻译', 'yuyinfanyi', '翻译＋关键词 例：翻译你好', 1, 1),
(13, 5, 0, '火车查询', 'huoche', '火车查询　火车＋城市＋目的地　例火车上海南京', 1, 1),
(14, 5, 0, '公交查询', 'gongjiao', '公交＋城市＋公交编号　例：上海公交774', 1, 1),
(15, 5, 0, '身份证查询', 'shenfenzheng', '身份证＋号码　　例：身份证342423198803015568', 1, 1),
(16, 5, 0, '手机归属地查询', 'shouji', '手机归属地查询(吉凶 运势) 手机＋手机号码　例：手机13917778912', 1, 1),
(17, 5, 0, '音乐查询', 'yinle', '音乐＋音乐名  例：音乐爱你一万年', 1, 1),
(18, 5, 0, '附近周边信息查询', 'fujin', '附近周边信息查询(ＬＢＳ）', 1, 1),
(19, 5, 0, '幸运大转盘', 'xingyun', '输入抽奖　即可参加幸运大转盘抽奖活动', 2, 1),
(20, 6, 0, '淘宝店铺', 'taobao', '输入淘宝＋关键词　即可访问淘宝3g手机店铺', 2, 1),
(21, 5, 0, '会员资料管理', 'userinfo', '会员资料管理　输入会员　即可参与', 2, 1),
(22, 5, 0, '翻译', 'fanyi', '翻译＋关键词 例：翻译你好', 1, 1),
(23, 6, 0, '第三方接口', 'api', '第三方接口整合模块，请在管理平台下载接口文件并配置接口信息，', 1, 1),
(24, 5, 0, '姓名算命', 'suanming', '姓名算命 算命＋关键词　例：算命李白', 1, 1),
(25, 5, 0, '百度百科', 'baike', '百度百科　使用方法：百科＋关键词　例：百科北京', 2, 1),
(26, 5, 0, '彩票查询', 'caipiao', '回复内容:彩票+彩种即可查询彩票中奖信息,例：彩票双色球', 1, 1),
(27, 5, 0, '抽奖', 'choujiang', '抽奖,输入抽奖即可参加幸运大转盘', 1, 1),
(28, 5, 0, '刮刮卡', 'gua2', '刮刮卡抽奖活动', 1, 1),
(29, 5, 0, '3G首页', 'shouye', '输入首页,访问微3g 网站', 1, 1),
(30, 6, 0, 'DIY宣传页', 'adma', 'DIY宣传页,用于创建二维码宣传页权限开启', 1, 1),
(31, 5, 0, '会员卡', 'huiyuanka', '尊贵享受vip会员卡,回复会员卡即可领取会员卡', 1, 1),
(33, 6, 0, '官网帐号审核', 'shenhe', '官网帐号审核', 1, 1),
(34, 5, 0, '歌词查询', 'geci', '歌词查询 回复歌词＋歌名即可查询一首歌曲的歌词,例：歌词醉清风', 1, 1),
(49, 6, 0, '微调研', 'Wdiaoyan', '输入微调研', 2, 1),
(48, 5, 0, '分享', 'fenxiang', '婚庆行业分享', 1, 1),
(50, 6, 0, '全景', 'panorama', '360 全景', 1, 1),
(36, 6, 0, '通用预订系统', 'host_kev', '通用预订系统 可用于酒店预订，ktv订房，旅游预订等。', 2, 1),
(44, 6, 0, '微信墙', 'weixin', '输入 微信墙', 2, 1),
(45, 6, 0, '房产楼盘', 'Estate', '房产楼盘', 2, 1),
(38, 6, 0, '自定义表单', 'diyform', '自定义表单(用于报名，预约,留言)等', 1, 1),
(39, 6, 0, '无线网络订餐', 'dx', '无线网络订餐', 1, 1),
(40, 6, 0, '在线商城', 'shop', '在线商城,购买系统', 1, 1),
(41, 6, 0, '在线团购系统', 'etuan', '在线团购系统', 1, 1),
(42, 5, 0, '自定义菜单', 'diymen_set', '自定义菜单,一键生成轻app', 1, 1),
(43, 6, 0, '活动模块', 'huodong', '活动:请输入‘活动’', 2, 1),
(51, 6, 0, '微预约', 'weiyuyue', '微预约', 2, 1),
(52, 5, 0, '微留言', 'weiliuyan', '微留言', 1, 1),
(53, 5, 0, '微报名', 'weibaomin', '微报名', 1, 1),
(54, 6, 0, '第三方接口', 'Rhjiekou', '', 1, 1);

