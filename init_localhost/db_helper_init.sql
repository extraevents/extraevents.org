/*
 Navicat MySQL Data Transfer

 Source Server         : mysql.hosting.nic.ru
 Source Server Type    : MySQL
 Source Server Version : 50641
 Source Host           : mysql.hosting.nic.ru
 Source Database       : suphair_helper

 Target Server Type    : MySQL
 Target Server Version : 50641
 File Encoding         : utf-8

 Date: 05/21/2023 06:55:46 AM
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `backup`
-- ----------------------------
DROP TABLE IF EXISTS `backup`;
CREATE TABLE `backup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(255) DEFAULT NULL,
  `db` varchar(255) DEFAULT NULL,
  `dir` varchar(255) DEFAULT NULL,
  `format` varchar(255) DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2037 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `cash`
-- ----------------------------
DROP TABLE IF EXISTS `cash`;
CREATE TABLE `cash` (
  `process` varchar(255) NOT NULL,
  `cash` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`process`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `competition_status`
-- ----------------------------
DROP TABLE IF EXISTS `competition_status`;
CREATE TABLE `competition_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person` varchar(10) DEFAULT NULL,
  `competition` varchar(50) DEFAULT NULL,
  `status_old` varchar(50) DEFAULT NULL,
  `status_new` varchar(50) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `send_notification` tinyint(4) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=160 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `cron`
-- ----------------------------
DROP TABLE IF EXISTS `cron`;
CREATE TABLE `cron` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_exec` varchar(255) DEFAULT NULL,
  `task_begin` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `task_end` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `details` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=699380 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `db_count`
-- ----------------------------
DROP TABLE IF EXISTS `db_count`;
CREATE TABLE `db_count` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `db_count` varchar(255) DEFAULT NULL,
  `request` varchar(255) DEFAULT NULL,
  `is_post` bit(1) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=654672 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `discort`
-- ----------------------------
DROP TABLE IF EXISTS `discort`;
CREATE TABLE `discort` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `webhookurl` varchar(256) DEFAULT NULL,
  `text` text,
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  `result` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=153 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `fcapi`
-- ----------------------------
DROP TABLE IF EXISTS `fcapi`;
CREATE TABLE `fcapi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(255) NOT NULL,
  `status` varchar(11) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `file_clear`
-- ----------------------------
DROP TABLE IF EXISTS `file_clear`;
CREATE TABLE `file_clear` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(16) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `filemtime` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8986 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `file_size`
-- ----------------------------
DROP TABLE IF EXISTS `file_size`;
CREATE TABLE `file_size` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dir` varchar(32) DEFAULT NULL,
  `subdir` varchar(32) DEFAULT NULL,
  `file_mb` int(11) DEFAULT NULL,
  `files` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=735 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `form`
-- ----------------------------
DROP TABLE IF EXISTS `form`;
CREATE TABLE `form` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `request` text,
  `get` text,
  `post` text,
  `session` text,
  `server` text,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2982 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `form_process`
-- ----------------------------
DROP TABLE IF EXISTS `form_process`;
CREATE TABLE `form_process` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `request` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `details` text,
  `message` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1546 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `import_team`
-- ----------------------------
DROP TABLE IF EXISTS `import_team`;
CREATE TABLE `import_team` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person` varchar(10) DEFAULT NULL,
  `member` varchar(10) DEFAULT NULL,
  `message` text,
  `details` text,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `register`
-- ----------------------------
DROP TABLE IF EXISTS `register`;
CREATE TABLE `register` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person` varchar(10) DEFAULT NULL,
  `competition_id` text,
  `event_id` text,
  `action` text,
  `result_id` int(11) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `details` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=370 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `scoretaker`
-- ----------------------------
DROP TABLE IF EXISTS `scoretaker`;
CREATE TABLE `scoretaker` (
  `competition_id` varchar(50) DEFAULT NULL,
  `event_id` varchar(50) DEFAULT NULL,
  `round_number` int(1) DEFAULT NULL,
  `person` varchar(10) DEFAULT NULL,
  `card_id` int(11) DEFAULT NULL,
  `details` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `smtp`
-- ----------------------------
DROP TABLE IF EXISTS `smtp`;
CREATE TABLE `smtp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `body` text,
  `result` text,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `smtp` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=897 DEFAULT CHARSET=utf8;

-- ----------------------------
--  Table structure for `telegram`
-- ----------------------------
DROP TABLE IF EXISTS `telegram`;
CREATE TABLE `telegram` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chat_id` int(11) DEFAULT NULL,
  `reciever` varchar(255) DEFAULT NULL,
  `text` text,
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  `result` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=537 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `update_wcaid`
-- ----------------------------
DROP TABLE IF EXISTS `update_wcaid`;
CREATE TABLE `update_wcaid` (
  `ee_id` varchar(10) DEFAULT NULL,
  `wca_id` varchar(10) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `wcaapi`
-- ----------------------------
DROP TABLE IF EXISTS `wcaapi`;
CREATE TABLE `wcaapi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(255) NOT NULL,
  `status` varchar(11) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1926 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `wcaoauth`
-- ----------------------------
DROP TABLE IF EXISTS `wcaoauth`;
CREATE TABLE `wcaoauth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `session` varchar(52) DEFAULT NULL,
  `wca_id` varchar(10) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `auth_begin` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `auth_end` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session` (`session`)
) ENGINE=InnoDB AUTO_INCREMENT=1174 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

SET FOREIGN_KEY_CHECKS = 1;
