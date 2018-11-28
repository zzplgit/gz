
CREATE TABLE IF NOT EXISTS `gz_cycle` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `year_id` varchar(255) NOT NULL DEFAULT '' COMMENT 'id for year',
  `school_code` varchar(255) NOT NULL DEFAULT '' COMMENT '院校唯一code',
  `start_at` int(11) NOT NULL DEFAULT '0' COMMENT '开始时间',
  `end_at` int(11) NOT NULL DEFAULT '0' COMMENT '结束时间',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='评估期间';
