
CREATE TABLE IF NOT EXISTS `gz_sms_verify` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `phone` varchar(255) NOT NULL DEFAULT '' COMMENT '电话号码',
  `captcha` varchar(255) NOT NULL DEFAULT '' COMMENT '验证码',
  `scene` varchar(255) NOT NULL DEFAULT '' COMMENT '发送类型 FORGET_THE_PASSWORD:忘记密码',
  `send_at` int(11) NOT NULL DEFAULT '0' COMMENT '发送时间',
  `send_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 0:未发送 1:已发送 2:发送失败',
  `send_type_msg` varchar(255) NOT NULL DEFAULT '' COMMENT '发送状态说明',
  `ali_request_id` varchar(255) NOT NULL DEFAULT '' COMMENT '使用阿里云发送成功后返回的RequestId',
  `ali_biz_id` varchar(255) NOT NULL DEFAULT '' COMMENT '使用阿里云发送成功后返回的BizId',
  `ip` varchar(255) NOT NULL DEFAULT '',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='手机验证码';
