/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 80018
Source Host           : localhost:3306
Source Database       : bbs

Target Server Type    : MYSQL
Target Server Version : 80018
File Encoding         : 65001

Date: 2021-01-11 11:53:44
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for collection
-- ----------------------------
DROP TABLE IF EXISTS `collection`;
CREATE TABLE `collection` (
  `cid` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '收藏id',
  `cuid` bigint(20) unsigned NOT NULL COMMENT '收藏者id',
  `ctid` bigint(20) unsigned NOT NULL COMMENT '收藏主题id',
  `ctime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `cdeletetime` datetime DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`cid`),
  KEY `fk_coll_user` (`cuid`),
  KEY `ctid` (`ctid`),
  CONSTRAINT `collection_ibfk_1` FOREIGN KEY (`ctid`) REFERENCES `topic` (`tid`),
  CONSTRAINT `fk_coll_user` FOREIGN KEY (`cuid`) REFERENCES `user` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of collection
-- ----------------------------
INSERT INTO `collection` VALUES ('12', '13', '55', '2021-01-09 18:30:02', null);
INSERT INTO `collection` VALUES ('15', '5', '32', '2021-01-10 22:00:27', null);
INSERT INTO `collection` VALUES ('16', '5', '56', '2021-01-10 22:02:51', null);
INSERT INTO `collection` VALUES ('17', '5', '6', '2021-01-10 22:08:46', null);

-- ----------------------------
-- Table structure for msg
-- ----------------------------
DROP TABLE IF EXISTS `msg`;
CREATE TABLE `msg` (
  `msgid` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '留言id',
  `msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '消息内容',
  `userid` bigint(20) unsigned NOT NULL COMMENT '非真实发送者id',
  `friendid` bigint(20) unsigned NOT NULL COMMENT '非真实接收者id',
  `sendid` bigint(20) unsigned NOT NULL COMMENT '发送者id',
  `acceptid` bigint(20) unsigned NOT NULL COMMENT '接收者id',
  `msgtype` tinyint(4) unsigned NOT NULL COMMENT '消息类型',
  `sendtime` datetime NOT NULL COMMENT '发送时间',
  `status` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '消息状态',
  PRIMARY KEY (`msgid`),
  KEY `fk_msg_user1` (`acceptid`),
  KEY `fk_msg_user2` (`sendid`),
  KEY `fk_msg_msgtype` (`msgtype`),
  KEY `fk_msg_msgstatus` (`status`),
  CONSTRAINT `fk_msg_msgstatus` FOREIGN KEY (`status`) REFERENCES `msgstatus` (`statusid`),
  CONSTRAINT `fk_msg_msgtype` FOREIGN KEY (`msgtype`) REFERENCES `msgtype` (`msgtypeid`),
  CONSTRAINT `fk_msg_user1` FOREIGN KEY (`acceptid`) REFERENCES `user` (`uid`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_msg_user2` FOREIGN KEY (`sendid`) REFERENCES `user` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of msg
-- ----------------------------

-- ----------------------------
-- Table structure for msgstatus
-- ----------------------------
DROP TABLE IF EXISTS `msgstatus`;
CREATE TABLE `msgstatus` (
  `statusid` tinyint(4) unsigned NOT NULL,
  `msgstatus` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '消息状态',
  PRIMARY KEY (`statusid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of msgstatus
-- ----------------------------
INSERT INTO `msgstatus` VALUES ('1', '未读');
INSERT INTO `msgstatus` VALUES ('2', '已读');
INSERT INTO `msgstatus` VALUES ('3', '删除');

-- ----------------------------
-- Table structure for msgtype
-- ----------------------------
DROP TABLE IF EXISTS `msgtype`;
CREATE TABLE `msgtype` (
  `msgtypeid` tinyint(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '消息类型id',
  `type` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '消息类型',
  PRIMARY KEY (`msgtypeid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of msgtype
-- ----------------------------
INSERT INTO `msgtype` VALUES ('1', '系统消息');
INSERT INTO `msgtype` VALUES ('2', '普通消息');

-- ----------------------------
-- Table structure for reply
-- ----------------------------
DROP TABLE IF EXISTS `reply`;
CREATE TABLE `reply` (
  `rid` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '回复id',
  `rtid` bigint(20) unsigned NOT NULL COMMENT '主贴编号',
  `ruid` bigint(20) unsigned NOT NULL COMMENT '回复者id',
  `rtarget` bigint(20) NOT NULL COMMENT '被回复者id',
  `rcontents` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '回复内容',
  `rlikecount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点赞数',
  `rrid` bigint(20) NOT NULL DEFAULT '0' COMMENT '父回复帖编号',
  `rtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '回复时间',
  `rdeletetime` datetime DEFAULT NULL COMMENT '删除时间',
  `rread` bit(1) NOT NULL DEFAULT b'0' COMMENT '被回复者是否已读',
  PRIMARY KEY (`rid`),
  KEY `fk_reply_topic` (`rtid`),
  KEY `fk_reply_user` (`ruid`),
  CONSTRAINT `fk_reply_topic` FOREIGN KEY (`rtid`) REFERENCES `topic` (`tid`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `fk_reply_user` FOREIGN KEY (`ruid`) REFERENCES `user` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of reply
-- ----------------------------
INSERT INTO `reply` VALUES ('1', '2', '5', '10', '哈哈哈', '2', '0', '2020-06-17 21:24:06', null, '\0');
INSERT INTO `reply` VALUES ('2', '32', '10', '7', '<p><strong>我也秃了我也秃了我也秃了</strong></p>', '0', '0', '2020-06-19 02:31:21', null, '\0');
INSERT INTO `reply` VALUES ('3', '32', '10', '7', '<p><strong>我也秃了我也秃了我也秃了</strong></p>', '0', '0', '2020-06-19 02:31:57', null, '\0');
INSERT INTO `reply` VALUES ('4', '32', '10', '7', '<p><strong>我也秃了我也秃了我也秃了</strong></p>', '1', '0', '2020-06-19 02:32:24', null, '\0');
INSERT INTO `reply` VALUES ('5', '32', '5', '7', '测试回复评论', '1', '4', '2020-06-19 08:57:03', null, '\0');
INSERT INTO `reply` VALUES ('6', '32', '7', '7', '<p><strong style=\"color: rgb(230, 0, 0);\" class=\"ql-size-large\"><em>有人吗有人吗</em></strong></p>', '1', '0', '2020-06-19 10:28:08', null, '\0');
INSERT INTO `reply` VALUES ('7', '32', '5', '7', '我来了！', '1', '0', '2020-06-19 10:45:55', null, '\0');
INSERT INTO `reply` VALUES ('8', '32', '7', '7', '<p><img src=\"http://192.168.184.1:666/static/t_images/20200619\\c0d821fdfca0690c01ac2b18a4cd930c.png\"></p>', '0', '0', '2020-06-19 13:16:23', null, '\0');
INSERT INTO `reply` VALUES ('9', '32', '7', '7', '<h1 class=\"ql-align-center\">刷新</h1>', '0', '0', '2020-06-19 13:20:35', null, '\0');
INSERT INTO `reply` VALUES ('10', '32', '7', '7', '<p>什么东西</p>', '0', '0', '2020-06-19 13:21:52', null, '\0');
INSERT INTO `reply` VALUES ('11', '2', '7', '5', '<blockquote>测试223255</blockquote>', '1', '0', '2020-06-19 13:33:42', null, '\0');
INSERT INTO `reply` VALUES ('12', '2', '7', '5', '测试评论234', '1', '0', '2020-06-19 14:43:09', null, '\0');
INSERT INTO `reply` VALUES ('13', '2', '7', '7', '？？', '1', '0', '2020-06-19 14:51:00', null, '\0');
INSERT INTO `reply` VALUES ('14', '2', '7', '5', '你是谁', '2', '0', '2020-06-19 14:52:22', null, '\0');
INSERT INTO `reply` VALUES ('15', '2', '10', '7', '回复哈哈哈123', '2', '14', '2020-06-19 15:00:24', null, '\0');
INSERT INTO `reply` VALUES ('16', '2', '8', '10', '我是你爸爸', '2', '15', '2020-06-19 15:15:38', null, '\0');
INSERT INTO `reply` VALUES ('17', '31', '8', '7', '<h1><strong>我吐了 你呢</strong></h1>', '1', '0', '2020-06-19 15:20:09', null, '\0');
INSERT INTO `reply` VALUES ('18', '31', '7', '8', '我也吐了 太难了', '1', '17', '2020-06-19 15:21:49', null, '\0');
INSERT INTO `reply` VALUES ('19', '50', '11', '11', '<p class=\"ql-align-center\"><strong class=\"ql-size-huge\" style=\"color: rgb(0, 102, 204);\">我爱学习</strong></p>', '1', '0', '2020-06-19 15:52:09', null, '\0');
INSERT INTO `reply` VALUES ('20', '50', '11', '11', '？？？？？？', '1', '19', '2020-06-19 15:52:36', null, '\0');
INSERT INTO `reply` VALUES ('21', '50', '11', '11', '菜鸡', '0', '20', '2020-06-19 15:52:47', null, '\0');
INSERT INTO `reply` VALUES ('22', '2', '11', '7', '我是你爸爸', '2', '14', '2020-06-19 15:53:25', null, '\0');
INSERT INTO `reply` VALUES ('23', '53', '11', '10', '<p>哈哈哈哈哈</p>', '0', '0', '2020-06-19 18:57:41', null, '\0');
INSERT INTO `reply` VALUES ('24', '55', '12', '12', '<p>评论测试111</p><p><img src=\"http://192.168.184.1:666/static/t_images/20200620\\91b09d786b5288bf9cec36f9d7d916ef.jpg\"></p>', '1', '0', '2020-06-20 08:52:24', null, '\0');
INSERT INTO `reply` VALUES ('25', '55', '12', '12', '？？？？？？？你是谁', '3', '24', '2020-06-20 08:52:40', null, '\0');
INSERT INTO `reply` VALUES ('26', '55', '10', '12', '<p><img src=\"http://192.168.184.1:666/static/t_images/20200620\\2909feb80a77b411bd5c7bf93b6edaf6.png\"></p>', '0', '0', '2020-06-20 09:38:53', null, '\0');
INSERT INTO `reply` VALUES ('27', '55', '13', '12', '<p>啊哈</p>', '0', '0', '2020-06-20 18:55:53', null, '\0');
INSERT INTO `reply` VALUES ('28', '32', '10', '7', '我来试试', '1', '9', '2020-06-20 20:31:41', '2021-01-07 10:27:53', '\0');
INSERT INTO `reply` VALUES ('29', '56', '15', '15', '<h1>评论测试123</h1><p><span style=\"color: rgb(230, 0, 0);\">如图</span></p><p><span style=\"color: rgb(230, 0, 0);\"><span class=\"ql-cursor\">﻿</span></span><img src=\"http://192.168.184.1:666/static/t_images/20200624\\9d399cb4e81a23f6398b070443d6850d.jpg\"></p>', '1', '0', '2020-06-24 11:06:17', null, '\0');
INSERT INTO `reply` VALUES ('30', '56', '15', '15', '评论回复测试1234', '0', '29', '2020-06-24 11:06:45', null, '\0');
INSERT INTO `reply` VALUES ('31', '57', '17', '17', '<h2><span style=\"color: rgb(230, 0, 0);\">我爱学习</span></h2><p><img src=\"http://192.168.184.1:666/static/t_images/20200624\\b31a7bd2d7c8fa1685c72ed501529274.jpg\"></p>', '1', '0', '2020-06-24 11:38:52', null, '\0');
INSERT INTO `reply` VALUES ('32', '57', '17', '17', '我也爱学习123--测试回复', '0', '31', '2020-06-24 11:39:16', null, '\0');
INSERT INTO `reply` VALUES ('33', '58', '18', '18', '<p><strong>技术分享--测试回复</strong></p><p><strong><span class=\"ql-cursor\">﻿</span></strong><img src=\"http://192.168.184.1:666/static/t_images/20200624\\fa96e4c2b0edba8875bb5a9a64930b66.jpg\"></p>', '1', '0', '2020-06-24 11:46:37', null, '\0');
INSERT INTO `reply` VALUES ('34', '58', '18', '18', '测试评论111', '2', '33', '2020-06-24 11:46:53', null, '\0');
INSERT INTO `reply` VALUES ('35', '58', '18', '18', '测试评论1234', '1', '34', '2020-06-24 11:47:09', null, '\0');
INSERT INTO `reply` VALUES ('36', '32', '5', '10', '测试杀杀杀杀杀杀杀杀杀', '0', '28', '2020-12-28 09:09:40', null, '\0');
INSERT INTO `reply` VALUES ('37', '32', '5', '7', '<h2>哈<strong><em>哈哈</em></strong>哈哈哈哈</h2><p><img src=\"http://192.168.184.1:666/static/t_images/20201228\\79e16d2af138c72a14f7056f4ef149f5.jpeg\"></p>', '0', '0', '2020-12-28 09:14:04', null, '\0');
INSERT INTO `reply` VALUES ('38', '32', '10', '7', '刷新也不行', '0', '9', '2021-01-07 18:47:11', null, '\0');
INSERT INTO `reply` VALUES ('39', '58', '10', '18', '<p>sss</p>', '0', '0', '2021-01-07 22:24:41', null, '\0');
INSERT INTO `reply` VALUES ('40', '56', '5', '15', '<p>你是谁</p>', '0', '0', '2021-01-08 16:46:13', null, '\0');

-- ----------------------------
-- Table structure for report
-- ----------------------------
DROP TABLE IF EXISTS `report`;
CREATE TABLE `report` (
  `reid` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '举报id',
  `rid` bigint(20) unsigned NOT NULL COMMENT '回复id',
  `uid` bigint(20) unsigned NOT NULL COMMENT '举报者id',
  `type` tinyint(4) NOT NULL COMMENT '举报类型',
  `reason` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '举报理由',
  PRIMARY KEY (`reid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of report
-- ----------------------------

-- ----------------------------
-- Table structure for reporttype
-- ----------------------------
DROP TABLE IF EXISTS `reporttype`;
CREATE TABLE `reporttype` (
  `typeid` int(5) NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL COMMENT '举报类型',
  PRIMARY KEY (`typeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of reporttype
-- ----------------------------
INSERT INTO `reporttype` VALUES ('10001', '低俗色情');
INSERT INTO `reporttype` VALUES ('10002', '垃圾广告');
INSERT INTO `reporttype` VALUES ('10003', '内容低俗无意义');
INSERT INTO `reporttype` VALUES ('10004', '辱骂攻击');
INSERT INTO `reporttype` VALUES ('10005', '其他违法信息');
INSERT INTO `reporttype` VALUES ('10006', '涉及未成年不良信息');
INSERT INTO `reporttype` VALUES ('10007', '抄袭我的内容');
INSERT INTO `reporttype` VALUES ('10008', '暴露我的隐私');
INSERT INTO `reporttype` VALUES ('10009', '内容里有关我的不实描述');

-- ----------------------------
-- Table structure for role
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `roleid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rolename` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '' COMMENT '角色名称',
  PRIMARY KEY (`roleid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of role
-- ----------------------------
INSERT INTO `role` VALUES ('1', '管理员');
INSERT INTO `role` VALUES ('2', '注册会员');

-- ----------------------------
-- Table structure for section
-- ----------------------------
DROP TABLE IF EXISTS `section`;
CREATE TABLE `section` (
  `sid` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '板块编号',
  `sname` varchar(10) COLLATE utf8mb4_general_ci NOT NULL COMMENT '板块名称',
  `smasterid` bigint(20) unsigned NOT NULL COMMENT '版主id',
  `simg` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '图片',
  `sstatement` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '板块说明',
  `sclickcount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '板块点击数',
  `stopiccount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '版块主题帖数量',
  `stime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `sdeletetime` datetime DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`sid`),
  KEY `fk_section_user` (`smasterid`),
  CONSTRAINT `fk_section_user` FOREIGN KEY (`smasterid`) REFERENCES `user` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of section
-- ----------------------------
INSERT INTO `section` VALUES ('1', '日常分享', '10', '/static/img/section/logo.png', '日常分享', '121', '5', '2020-06-17 14:27:40', null);
INSERT INTO `section` VALUES ('2', '学习交流', '10', '/static/img/section/logo.png', '学习交流', '104', '5', '2020-06-17 14:27:40', null);
INSERT INTO `section` VALUES ('3', '技术分享', '10', '/static/img/section/logo.png', '技术分享', '68', '3', '2020-06-17 15:48:34', null);

-- ----------------------------
-- Table structure for topic
-- ----------------------------
DROP TABLE IF EXISTS `topic`;
CREATE TABLE `topic` (
  `tid` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '帖子id',
  `tsid` int(11) unsigned NOT NULL COMMENT '板块id',
  `tuid` bigint(20) unsigned NOT NULL COMMENT '题主id',
  `treplycount` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '回复数量',
  `ttopic` varchar(100) COLLATE utf8mb4_general_ci NOT NULL COMMENT '帖子标题',
  `good` bit(1) NOT NULL DEFAULT b'0' COMMENT '是否精华',
  `top` bit(1) NOT NULL DEFAULT b'0' COMMENT '是否置顶',
  `forcetop` bit(1) DEFAULT b'0' COMMENT '首页强制置顶',
  `tcontents` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT '内容',
  `ttime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '发帖时间',
  `tlikecount` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点赞数',
  `tlastclick` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '最后回复时间',
  `tmodifytime` datetime DEFAULT NULL COMMENT '修改时间',
  `tdeletetime` datetime DEFAULT NULL COMMENT '删除时间',
  `collections` int(11) NOT NULL DEFAULT '0' COMMENT '收藏量',
  `check` bit(1) NOT NULL DEFAULT b'0' COMMENT '审核标志',
  PRIMARY KEY (`tid`),
  KEY `fk_topic_user` (`tuid`),
  KEY `fk_topic_section` (`tsid`),
  CONSTRAINT `fk_topic_user` FOREIGN KEY (`tuid`) REFERENCES `user` (`uid`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of topic
-- ----------------------------
INSERT INTO `topic` VALUES ('1', '1', '10', '0', '测试1', '\0', '', '\0', '测试内容测试内容测试内容测试内容', '2020-06-17 16:34:28', '5', '2020-06-17 08:33:23', null, null, '0', '');
INSERT INTO `topic` VALUES ('2', '2', '5', '0', '测试2', '', '\0', '\0', '测试内容测试内容测试内容测试内容', '2020-06-17 16:36:01', '8', '2020-06-17 16:34:28', null, null, '0', '');
INSERT INTO `topic` VALUES ('3', '2', '6', '0', '测试22', '\0', '\0', '\0', '测试内容测试内容测试内容测试内容', '2020-06-17 16:36:01', '7', '2020-06-17 16:34:28', null, null, '0', '');
INSERT INTO `topic` VALUES ('4', '3', '7', '0', '测试3', '\0', '\0', '\0', '测试内容测试内容测试内容测试内容', '2020-06-17 16:36:01', '4', '2020-06-17 16:34:28', null, null, '0', '');
INSERT INTO `topic` VALUES ('6', '1', '10', '0', 'c', '\0', '', '', 'csf', '2020-06-17 16:38:25', '3', '2020-06-17 16:34:28', null, null, '1', '');
INSERT INTO `topic` VALUES ('7', '3', '10', '0', 'cashifa哈哈哈', '\0', '\0', '\0', '擦撒法是飞洒发飞洒水水水水水', '2020-06-17 22:06:11', '8', '2020-06-17 22:34:28', null, null, '0', '');
INSERT INTO `topic` VALUES ('8', '1', '10', '0', '置顶测试1', '', '', '', '我是置顶', '2020-06-17 22:39:14', '10', '2020-06-17 22:39:14', null, null, '0', '');
INSERT INTO `topic` VALUES ('9', '1', '10', '0', '我来试试！！', '\0', '\0', '\0', '<h1 class=\"ql-align-center\"><strong class=\"ql-font-serif\" style=\"color: rgb(230, 0, 0); background-color: rgb(255, 255, 0);\"><em><s><u>我来试试</u></s></em></strong></h1><ol><li class=\"ql-align-center\">哈哈</li><li class=\"ql-align-center\">哈哈</li><li class=\"ql-align-center\">hhhh</li></ol><p><a href=\"http://www.baidu.com\" rel=\"noopener noreferrer\" target=\"_blank\">百度百度百度</a></p><p><br></p><pre class=\"ql-syntax\" spellcheck=\"false\">&lt;script&gt;alert(\"js\");&lt;/script&gt;\n</pre><p><br></p><p>&lt;script&gt;alert(\"js\");&lt;/script&gt;</p>', '2020-06-18 17:43:47', '16', '2020-06-18 17:43:47', null, null, '0', '');
INSERT INTO `topic` VALUES ('10', '1', '10', '0', '我来试试！！', '\0', '\0', '\0', '<h1 class=\"ql-align-center\"><strong class=\"ql-font-serif\" style=\"color: rgb(230, 0, 0); background-color: rgb(255, 255, 0);\"><em><s><u>我来试试</u></s></em></strong></h1><ol><li class=\"ql-align-center\">哈哈</li><li class=\"ql-align-center\">哈哈</li><li class=\"ql-align-center\">hhhh</li></ol><p><a href=\"http://www.baidu.com\" rel=\"noopener noreferrer\" target=\"_blank\">百度百度百度</a></p><p><br></p><pre class=\"ql-syntax\" spellcheck=\"false\">&lt;script&gt;alert(\"js\");&lt;/script&gt;\n</pre><p><br></p><p>&lt;script&gt;alert(\"js\");&lt;/script&gt;</p>', '2020-06-18 17:44:13', '56', '2020-06-18 17:44:13', null, null, '0', '');
INSERT INTO `topic` VALUES ('11', '3', '7', '0', '技术分享帖', '\0', '\0', '\0', '<h1 class=\"ql-align-center\">技术分享帖技术分享帖技术分享帖</h1>', '2020-06-18 19:03:06', '5', '2020-06-18 19:03:06', null, null, '0', '');
INSERT INTO `topic` VALUES ('12', '1', '7', '0', '令人窒息', '\0', '\0', '\0', '<p><img src=\"http://192.168.184.1:666/static/t_images/20200618\\dd8e43efa44e71f1eeecb64f047b5d11.jpg\"></p>', '2020-06-18 19:03:54', '12', '2020-06-18 19:03:54', null, null, '0', '');
INSERT INTO `topic` VALUES ('25', '1', '7', '0', '?????', '\0', '\0', '\0', '<p>????</p>', '2020-06-18 22:28:58', '5', '2020-06-18 22:28:58', null, null, '0', '');
INSERT INTO `topic` VALUES ('26', '1', '7', '0', '?????', '\0', '\0', '\0', '<p>????</p>', '2020-06-18 22:28:59', '7', '2020-06-18 22:28:59', null, null, '0', '');
INSERT INTO `topic` VALUES ('27', '1', '7', '0', '?????', '\0', '\0', '\0', '<p>????</p>', '2020-06-18 22:28:59', '5', '2020-06-18 22:28:59', null, null, '0', '');
INSERT INTO `topic` VALUES ('28', '1', '7', '0', '?????', '\0', '\0', '\0', '<p>????</p>', '2020-06-18 22:28:59', '12', '2020-06-18 22:28:59', null, null, '0', '');
INSERT INTO `topic` VALUES ('29', '3', '7', '0', '为什么又不行啊啊啊啊', '\0', '\0', '\0', '<p>为什么又不行啊啊啊啊为什么又不行啊啊啊啊为什么又不行啊啊啊啊</p>', '2020-06-18 22:29:13', '31', '2020-06-18 22:29:13', null, null, '0', '');
INSERT INTO `topic` VALUES ('30', '3', '7', '0', '为什么又不行啊啊啊啊', '\0', '\0', '\0', '<p>为什么又不行啊啊啊啊为什么又不行啊啊啊啊为什么又不行啊啊啊啊</p>', '2020-06-18 22:30:22', '66', '2020-06-18 22:30:22', null, null, '0', '');
INSERT INTO `topic` VALUES ('31', '3', '7', '0', '为什么又不行啊啊啊啊 我吐了我吐了我吐了', '\0', '\0', '\0', '<h1 class=\"ql-align-center\">我吐了我吐了我吐了我吐了</h1><h1 class=\"ql-align-center\">我吐了我吐了我吐了我吐了</h1><p class=\"ql-align-center\"><br></p><h1 class=\"ql-align-center\">我吐了我吐了我吐了我吐了</h1><p class=\"ql-align-center\"><br></p><h1 class=\"ql-align-center\">我吐了我吐了我吐了我吐了</h1><p class=\"ql-align-center\"><br></p><h1 class=\"ql-align-center\">我吐了我吐了我吐了我吐了</h1><p class=\"ql-align-center\"><br></p><h1 class=\"ql-align-center\">我吐了我吐了我吐了我吐了</h1><p class=\"ql-align-center\"><br></p><h1 class=\"ql-align-center\">我吐了我吐了我吐了我吐了</h1><p class=\"ql-align-center\"><br></p><h1 class=\"ql-align-center\">我吐了我吐了我吐了我吐了</h1><p class=\"ql-align-center\"><br></p><p class=\"ql-align-center\"><br></p>', '2020-06-18 22:32:12', '12', '2020-06-18 22:32:12', null, null, '0', '');
INSERT INTO `topic` VALUES ('32', '3', '7', '0', '我吐了我秃了', '', '', '', '<h1 class=\"ql-align-center\"><a href=\"https://www.cnblogs.com/mengfangui/p/9066908.html\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: rgb(3, 120, 187);\">VUE 路由变化页面数据不刷新问题</a></h1><h3 class=\"ql-align-justify\">出现这种情况是因为依赖路由的params参数获取写在created生命周期里面,因为相同路由二次甚至多次加载的关系 没有达到监听，退出页面再进入另一个文章页面并不会运行created组件生命周期，导致文章数据还是第一次进入的数据。</h3><p class=\"ql-indent-1 ql-align-center\"><span style=\"color: rgb(230, 0, 0);\">解决方法：watch监听路由是否变化</span></p><pre class=\"ql-syntax\" spellcheck=\"false\">watch: {\n&nbsp; // 方法1\n&nbsp; \'$route\' (to, from) { //监听路由是否变化\n&nbsp; &nbsp; if(this.$route.params.articleId){// 判断条件1&nbsp; 判断传递值的变化\n&nbsp; &nbsp; &nbsp; //获取文章数据\n&nbsp; &nbsp; }\n&nbsp; }&nbsp;\n&nbsp; &nbsp;//方法2\n&nbsp; \'$route\'(to, from) {\n&nbsp; &nbsp; if (to.path == \"/page\") {&nbsp; /// 判断条件2&nbsp; 监听路由名 监听你从什么路由跳转过来的\n&nbsp; &nbsp; &nbsp; &nbsp;this.message = this.$route.query.msg&nbsp; &nbsp; &nbsp;\n&nbsp; &nbsp; }\n&nbsp; }&nbsp;&nbsp;\n}\n</pre><p class=\"ql-align-center\"><br></p><p class=\"ql-align-center\"><img src=\"http://192.168.184.1:666/static/t_images/20200618\\9a35884533867ae37521b50f3deb4914.jpg\"></p>', '2020-06-18 22:39:24', '58', '2020-06-18 22:39:24', null, null, '1', '');
INSERT INTO `topic` VALUES ('44', '2', '11', '0', '什么东西啊', '\0', '\0', '\0', '<h1 class=\"ql-align-center\">你是谁</h1><p class=\"ql-align-center\"><img src=\"http://192.168.184.1:666/static/t_images/20200619\\98f42c18c88c0964b8ba366753ba5b38.png\"></p><iframe class=\"ql-video\" frameborder=\"0\" allowfullscreen=\"true\" src=\"http://www.baidu.com\"></iframe><p class=\"ql-align-center\"><br></p>', '2020-06-19 15:45:49', '0', '2020-06-19 15:45:49', null, null, '0', '');
INSERT INTO `topic` VALUES ('45', '2', '11', '0', '什么东西啊', '\0', '\0', '\0', '<h1 class=\"ql-align-center\">你是谁</h1><p class=\"ql-align-center\"><img src=\"http://192.168.184.1:666/static/t_images/20200619\\98f42c18c88c0964b8ba366753ba5b38.png\"></p><iframe class=\"ql-video\" frameborder=\"0\" allowfullscreen=\"true\" src=\"http://www.baidu.com\"></iframe><p class=\"ql-align-center\"><br></p>', '2020-06-19 15:45:50', '0', '2020-06-19 15:45:50', null, null, '0', '');
INSERT INTO `topic` VALUES ('46', '2', '11', '0', '什么东西啊', '\0', '\0', '\0', '<h1 class=\"ql-align-center\">你是谁</h1><p class=\"ql-align-center\"><img src=\"http://192.168.184.1:666/static/t_images/20200619\\98f42c18c88c0964b8ba366753ba5b38.png\"></p><p class=\"ql-align-center\"><br></p>', '2020-06-19 15:45:56', '0', '2020-06-19 15:45:56', null, null, '0', '');
INSERT INTO `topic` VALUES ('47', '2', '11', '0', '什么东西啊', '\0', '\0', '\0', '<p>为什么啊</p>', '2020-06-19 15:46:48', '0', '2020-06-19 15:46:48', null, null, '0', '');
INSERT INTO `topic` VALUES ('48', '2', '11', '0', '什么东西啊', '\0', '\0', '\0', '<p>为什么啊</p>', '2020-06-19 15:47:43', '0', '2020-06-19 15:47:43', null, null, '0', '');
INSERT INTO `topic` VALUES ('49', '3', '11', '0', '什么鬼', '\0', '\0', '\0', '<p><img src=\"http://192.168.184.1:666/static/t_images/20200619\\77e570b96744edfe8a5c66dec2206e6a.png\"><span class=\"ql-cursor\">﻿</span></p><iframe class=\"ql-video\" frameborder=\"0\" allowfullscreen=\"true\" src=\"http://www.baidu.com\"></iframe><p><br></p>', '2020-06-19 15:48:10', '0', '2020-06-19 15:48:10', null, null, '0', '');
INSERT INTO `topic` VALUES ('50', '2', '11', '0', '学习学习', '\0', '\0', '\0', '<h1 class=\"ql-align-center\"><strong>主题</strong></h1><p class=\"ql-align-center\"><br></p><p class=\"ql-align-center\">如图</p><p class=\"ql-align-center\"><img src=\"http://192.168.184.1:666/static/t_images/20200619\\7b818483f13d75685cb1551853c4aee1.png\"></p><p class=\"ql-align-center\"><strong class=\"ql-size-large\" style=\"background-color: rgb(230, 0, 0); color: rgb(255, 255, 0);\"><em>什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼什么鬼</em></strong></p>', '2020-06-19 15:51:28', '1', '2020-06-19 15:51:28', null, null, '0', '');
INSERT INTO `topic` VALUES ('51', '3', '10', '0', '管理员没时间做', '', '', '', '<p class=\"ql-align-center\"><span class=\"ql-size-huge\" style=\"color: rgb(230, 0, 0);\">用户修改信息没时间做用户修改信息没时间做</span></p>', '2020-06-19 15:56:31', '0', '2020-06-19 15:56:31', null, null, '0', '');
INSERT INTO `topic` VALUES ('52', '1', '10', '0', '我不干了！！！！', '\0', '\0', '\0', '<h1><strong style=\"background-color: rgb(230, 0, 0);\"><em>我不干了！！！！</em></strong></h1>', '2020-06-19 15:57:34', '0', '2020-06-19 15:57:34', null, null, '0', '');
INSERT INTO `topic` VALUES ('53', '1', '10', '0', '用户个人信息不能修改', '\0', '\0', '\0', '<p>因为没时间</p>', '2020-06-19 15:58:59', '0', '2020-06-19 15:58:59', null, null, '0', '');
INSERT INTO `topic` VALUES ('54', '1', '10', '0', '后台有界面没功能', '\0', '', '', '<p>因为没时间</p>', '2020-06-19 16:00:03', '2', '2020-06-19 16:00:03', null, null, '0', '');
INSERT INTO `topic` VALUES ('55', '2', '12', '0', '主题', '\0', '\0', '\0', '<h1 class=\"ql-align-center\"><span style=\"color: rgb(230, 0, 0);\">主题主题主题主题</span></h1><p class=\"ql-align-center\"><img src=\"http://192.168.184.1:666/static/t_images/20200620\\b7f56fcd36a27c23bc0eec42abeb557e.jpg\"></p><p class=\"ql-align-center\"><a href=\"http://www.baidu.com\" rel=\"noopener noreferrer\" target=\"_blank\">百度</a></p>', '2020-06-20 08:51:48', '2', '2020-06-20 08:51:48', null, null, '0', '');
INSERT INTO `topic` VALUES ('56', '1', '15', '0', '日常分享123', '\0', '\0', '\0', '<p class=\"ql-align-center\"><span class=\"ql-font-serif ql-size-huge\">日常分享</span></p><p><span class=\"ql-font-serif ql-size-huge\">我的分享图：</span><img src=\"http://192.168.184.1:666/static/t_images/20200624\\d21f2389b02c26bc92b0986dc333d551.jpg\"></p><pre class=\"ql-syntax\" spellcheck=\"false\">&lt;script&gt;alert(\"xss\");&lt;/script&gt;\n</pre>', '2020-06-24 11:05:19', '0', '2020-06-24 11:05:19', null, null, '1', '');
INSERT INTO `topic` VALUES ('57', '2', '17', '0', '学习交流12345', '\0', '\0', '\0', '<h1 class=\"ql-align-center\"><span class=\"ql-size-huge\">学习</span></h1><p class=\"ql-align-center\"><span style=\"color: rgb(230, 0, 0);\">图片</span></p><p class=\"ql-align-center\"><span style=\"color: rgb(230, 0, 0);\"><span class=\"ql-cursor\">﻿</span></span><img src=\"http://192.168.184.1:666/static/t_images/20200624\\3a24d17e8bb06d39fabc1535314c17d7.jpg\"></p>', '2020-06-24 11:38:11', '0', '2020-06-24 11:38:11', null, null, '0', '');
INSERT INTO `topic` VALUES ('58', '3', '18', '0', '技术分享帖', '', '', '', '<h1 class=\"ql-align-center\"><span style=\"color: rgb(230, 0, 0);\">技术分享帖</span></h1><p class=\"ql-align-center\"><br></p><p class=\"ql-align-center\"><img src=\"http://192.168.184.1:666/static/t_images/20200624\\466282f607a3ae610a48dccacdda7228.jpg\"></p><pre class=\"ql-syntax ql-align-center\" spellcheck=\"false\">11代码块\n</pre>', '2020-06-24 11:45:33', '0', '2020-06-24 11:45:33', null, '2021-01-07 14:31:01', '0', '');
INSERT INTO `topic` VALUES ('59', '2', '5', '0', '帖子标12323', '', '', '', '<iframe class=\"ql-video\" frameborder=\"0\" allowfullscreen=\"true\" src=\"http://www.baidu.com\"></iframe><p><br></p>', '2020-12-28 09:22:56', '2', '2020-12-28 09:22:56', null, null, '0', '');
INSERT INTO `topic` VALUES ('60', '1', '5', '0', 'check111', '\0', '\0', '\0', '<p>check111check111check111</p>', '2021-01-06 10:30:51', '0', '2021-01-06 10:30:51', null, null, '0', '\0');

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `uid` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `uname` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户名',
  `upassword` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '用户密码',
  `uface` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '用户头像',
  `uemail` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT '用户电邮',
  `ubirthday` date DEFAULT NULL COMMENT '用户生日',
  `usex` bit(1) NOT NULL DEFAULT b'0' COMMENT '用户性别',
  `ustatement` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '用户个人说明',
  `ulastlogin` datetime DEFAULT NULL COMMENT '最后登录时间',
  `uregdate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '注册时间',
  `ustate` bit(1) NOT NULL DEFAULT b'0' COMMENT '账号锁定状态',
  `upoint` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '用户积分',
  `roleid` int(11) unsigned NOT NULL COMMENT '角色id',
  PRIMARY KEY (`uid`),
  KEY `fk_user_role` (`roleid`),
  CONSTRAINT `fk_user_role` FOREIGN KEY (`roleid`) REFERENCES `role` (`roleid`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('5', 'test2', '14e1b600b1fd579f47433b88e8d85291', '20201228\\f15978349fd09b123814d947c33735c8.jpg', null, null, '\0', null, '2021-01-11 09:49:29', '2020-06-17 11:28:44', '\0', '0', '2');
INSERT INTO `user` VALUES ('6', 'admin2', '14e1b600b1fd579f47433b88e8d85291', null, null, null, '\0', null, '2020-06-17 15:00:18', '2020-06-17 11:29:01', '\0', '0', '1');
INSERT INTO `user` VALUES ('7', '哈哈哈123', '14e1b600b1fd579f47433b88e8d85291', '20200620\\26590666667bec4e33b130f2a72427bf.jpg', null, null, '\0', null, '2021-01-07 17:31:34', '2020-06-17 11:31:54', '\0', '0', '2');
INSERT INTO `user` VALUES ('8', '哈哈哈12345', '14e1b600b1fd579f47433b88e8d85291', null, null, null, '\0', null, '2020-06-19 15:15:24', '2020-06-17 11:32:04', '\0', '0', '2');
INSERT INTO `user` VALUES ('9', '哈哈哈123456', '14e1b600b1fd579f47433b88e8d85291', null, null, null, '\0', null, null, '2020-06-17 11:32:06', '\0', '0', '2');
INSERT INTO `user` VALUES ('10', 'admin', '14e1b600b1fd579f47433b88e8d85291', '20200620\\16d8c23f8e004e37e4f6b14f37c90cbc.jpg', null, null, '\0', null, '2021-01-10 22:14:10', '2020-06-17 11:32:33', '\0', '0', '1');
INSERT INTO `user` VALUES ('11', 'test3', '14e1b600b1fd579f47433b88e8d85291', null, null, null, '\0', null, '2020-06-19 18:56:54', '2020-06-19 15:35:08', '\0', '0', '2');
INSERT INTO `user` VALUES ('12', 'test6', 'f8de1968939dd4ac5992ce962993ac2b', '20200620\\9848e794d22af258aef8f5eaa1205c74.jpg', null, null, '\0', null, '2020-06-20 08:48:37', '2020-06-20 08:48:25', '\0', '0', '2');
INSERT INTO `user` VALUES ('13', 'tc130', 'b27cc31ed4ec98f9d8b7f5808f29fe5e', '20200620\\d8bc730e87733c79f8390d428a0b32ae.jpg', null, null, '\0', null, '2020-06-20 18:51:32', '2020-06-20 18:51:19', '\0', '0', '2');
INSERT INTO `user` VALUES ('14', 'test5', '14e1b600b1fd579f47433b88e8d85291', null, null, null, '\0', null, '2020-06-24 10:54:34', '2020-06-24 10:54:23', '\0', '0', '2');
INSERT INTO `user` VALUES ('15', 'test7', '224cf2b695a5e8ecaecfb9015161fa4b', '20200624\\c17fdf7169200b169aa2a1080c772c71.jpg', null, null, '\0', null, '2020-06-24 11:02:34', '2020-06-24 11:02:24', '\0', '0', '2');
INSERT INTO `user` VALUES ('16', 'test8', '14e1b600b1fd579f47433b88e8d85291', '20200624\\0659800145d11bffa1792d7ac594400c.jpg', null, null, '\0', null, '2020-06-24 11:14:18', '2020-06-24 11:14:03', '\0', '0', '2');
INSERT INTO `user` VALUES ('17', 'test10', '224cf2b695a5e8ecaecfb9015161fa4b', '20200624\\69710fe618fe89c6eb9838823b91e8b0.jpg', null, null, '\0', null, '2020-06-24 11:35:55', '2020-06-24 11:35:43', '\0', '0', '2');
INSERT INTO `user` VALUES ('18', 'test11', '224cf2b695a5e8ecaecfb9015161fa4b', '20200624\\aafe3436960a2b535c7bdf33d374be1d.jpg', null, null, '\0', null, '2020-06-24 11:43:17', '2020-06-24 11:43:03', '\0', '0', '2');
INSERT INTO `user` VALUES ('19', '冲冲冲', '14e1b600b1fd579f47433b88e8d85291', null, null, null, '\0', null, '2021-01-08 20:16:42', '2021-01-08 20:16:30', '\0', '0', '2');

-- ----------------------------
-- Function structure for fnStripTags
-- ----------------------------
DROP FUNCTION IF EXISTS `fnStripTags`;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fnStripTags`( Dirty varchar(4000) ) RETURNS varchar(4000) CHARSET utf8mb4 COLLATE utf8mb4_general_ci
    DETERMINISTIC
BEGIN

    DECLARE iStart, iEnd, iLength int;

    WHILE Locate( '<', Dirty ) > 0 And Locate( '>', Dirty, Locate( '<', Dirty )) > 0 DO

    BEGIN

        SET iStart = Locate( '<', Dirty ), iEnd = Locate( '>', Dirty, Locate('<', Dirty ));

        SET iLength = ( iEnd - iStart) + 1;

        IF iLength > 0 THEN

            BEGIN

                SET Dirty = Insert( Dirty, iStart, iLength, '');

            END;

        END IF;

    END;

    END WHILE;

    RETURN Dirty;

END
;;
DELIMITER ;
