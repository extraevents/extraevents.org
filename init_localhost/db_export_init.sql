/*
 Navicat MySQL Data Transfer

 Source Server         : mysql.hosting.nic.ru
 Source Server Type    : MySQL
 Source Server Version : 50641
 Source Host           : mysql.hosting.nic.ru
 Source Database       : suphair_export

 Target Server Type    : MySQL
 Target Server Version : 50641
 File Encoding         : utf-8

 Date: 05/21/2023 06:46:49 AM
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `ee_competitions`
-- ----------------------------
DROP TABLE IF EXISTS `ee_competitions`;
CREATE TABLE `ee_competitions` (
  `id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `city` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `country` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `extra_events` text COLLATE utf8mb4_unicode_ci,
  `extra_events_organizers` text COLLATE utf8mb4_unicode_ci,
  `extra_events_contact` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `ee_events`
-- ----------------------------
DROP TABLE IF EXISTS `ee_events`;
CREATE TABLE `ee_events` (
  `id` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(54) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `person_count` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `ee_ranks_average`
-- ----------------------------
DROP TABLE IF EXISTS `ee_ranks_average`;
CREATE TABLE `ee_ranks_average` (
  `person` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `event` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `best` int(11) NOT NULL DEFAULT '0',
  `world_rank` int(11) NOT NULL DEFAULT '0',
  `continent_rank` int(11) NOT NULL DEFAULT '0',
  `country_rank` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `ee_ranks_single`
-- ----------------------------
DROP TABLE IF EXISTS `ee_ranks_single`;
CREATE TABLE `ee_ranks_single` (
  `person` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `event` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `best` int(11) NOT NULL DEFAULT '0',
  `world_rank` int(11) NOT NULL DEFAULT '0',
  `continent_rank` int(11) NOT NULL DEFAULT '0',
  `country_rank` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- ----------------------------
--  Table structure for `ee_results`
-- ----------------------------
DROP TABLE IF EXISTS `ee_results`;
CREATE TABLE `ee_results` (
  `competition` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `event` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `round_type` varchar(1) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pos` smallint(6) NOT NULL DEFAULT '0',
  `best` int(11) NOT NULL DEFAULT '0',
  `average` int(11) NOT NULL DEFAULT '0',
  `person1` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `person2` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `person3` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `person4` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `format` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `value1` int(11) NOT NULL DEFAULT '0',
  `value2` int(11) NOT NULL DEFAULT '0',
  `value3` int(11) NOT NULL DEFAULT '0',
  `value4` int(11) NOT NULL DEFAULT '0',
  `value5` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

SET FOREIGN_KEY_CHECKS = 1;
