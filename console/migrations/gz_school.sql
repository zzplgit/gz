
CREATE TABLE IF NOT EXISTS `gz_school` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `school_code` varchar(255) NOT NULL DEFAULT '' COMMENT '院校唯一code',
  `school_name` varchar(255) NOT NULL DEFAULT '' COMMENT '院校名称',
  `school_tel` varchar(255) NOT NULL DEFAULT '' COMMENT '院校电话',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='院校';
