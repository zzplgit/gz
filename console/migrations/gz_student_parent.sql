
CREATE TABLE IF NOT EXISTS `gz_student_parent` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `student_id` int(10) NOT NULL DEFAULT '0' COMMENT 'id for gz_student',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '姓名',
  `phone` varchar(255) NOT NULL DEFAULT '' COMMENT '手机号',
  `send_sms` tinyint(1) NOT NULL DEFAULT '0' COMMENT '发送短信 0否 1是',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除 0否 1是',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='学生家长表';
