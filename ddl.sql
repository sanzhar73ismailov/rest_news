CREATE TABLE `data` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `ann_date` DATE NOT NULL,
  `ann_text` TEXT COLLATE utf8_general_ci NOT NULL,
  `deleted` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
)ENGINE=InnoDB
CHARACTER SET 'utf8' COLLATE 'utf8_general_ci';