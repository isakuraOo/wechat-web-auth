/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50635
 Source Host           : localhost
 Source Database       : wx_auth

 Target Server Type    : MySQL
 Target Server Version : 50635
 File Encoding         : utf-8

 Date: 04/20/2017 01:58:44 AM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `allowed_list`
-- ----------------------------
DROP TABLE IF EXISTS `allowed_list`;
CREATE TABLE `allowed_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键，自增 ID',
  `system` varchar(12) NOT NULL COMMENT '系统标识字符串',
  `token` varchar(128) NOT NULL COMMENT '签名 Token，白名单系统签名以及授权中心签名验证时需要用到',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='授权中心白名单数据表';

SET FOREIGN_KEY_CHECKS = 1;
