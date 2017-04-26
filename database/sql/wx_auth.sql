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

 Date: 04/26/2017 18:19:30 PM
*/

SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `access_token`
-- ----------------------------
DROP TABLE IF EXISTS `access_token`;
CREATE TABLE `access_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键，自增 ID',
  `token` varchar(255) NOT NULL COMMENT '访问令牌',
  `openid` varchar(28) NOT NULL COMMENT 'openid',
  `create_time` int(11) NOT NULL COMMENT '生效时间',
  `invalid_time` int(11) NOT NULL COMMENT '失效时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='访问令牌数据表';

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

-- ----------------------------
--  Table structure for `wx_user_info`
-- ----------------------------
DROP TABLE IF EXISTS `wx_user_info`;
CREATE TABLE `wx_user_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键，自增 ID',
  `openid` varchar(28) NOT NULL COMMENT 'openid',
  `nickname` varchar(50) NOT NULL COMMENT '昵称',
  `sex` tinyint(4) NOT NULL COMMENT '性别 0 未知 1 男 2 女',
  `province` varchar(30) NOT NULL COMMENT '省份',
  `city` varchar(30) NOT NULL COMMENT '城市',
  `country` varchar(30) NOT NULL COMMENT '国家',
  `headimgurl` varchar(255) NOT NULL COMMENT '头像链接',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信授权用户数据表';

SET FOREIGN_KEY_CHECKS = 1;
