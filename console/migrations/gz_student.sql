
CREATE TABLE IF NOT EXISTS `gz_student` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `class_code` varchar(255) NOT NULL DEFAULT '' COMMENT '班级唯一code',
  `school_code` varchar(255) NOT NULL DEFAULT '' COMMENT '院校唯一code',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '姓名',
  `sex_code` varchar(255) NOT NULL DEFAULT '' COMMENT '性别code',
  `sex` varchar(255) NOT NULL DEFAULT '' COMMENT '性别',
  `birthday` int(11) NOT NULL DEFAULT '0' COMMENT '生日',
  `f_structure_code` varchar(255) NOT NULL DEFAULT '' COMMENT '家庭结构code',
  `f_structure` varchar(255) NOT NULL DEFAULT '' COMMENT '家庭结构',
  `ext_student_id` varchar(255) NOT NULL DEFAULT '' COMMENT '外部学生id',
  `ext_region_id` varchar(255) NOT NULL DEFAULT '' COMMENT '外部区域id',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '学生头像',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='学生表';
