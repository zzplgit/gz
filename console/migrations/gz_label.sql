CREATE TABLE IF NOT EXISTS `gz_label` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `class_code` varchar(255) NOT NULL DEFAULT '' COMMENT '班级唯一code',
  `school_code` varchar(255) NOT NULL DEFAULT '' COMMENT '院校唯一code',
  `label_name` varchar(255) NOT NULL DEFAULT '' COMMENT '标签名称',
  `label_describe` varchar(255) NOT NULL DEFAULT '' COMMENT '标签描述',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除 0否 1是',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='标签';