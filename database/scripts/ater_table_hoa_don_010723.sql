ALTER TABLE `hoa_don`
	ADD COLUMN ` is_combo` INT NULL DEFAULT '0' AFTER `ngay`,
	ADD COLUMN `so_luong_combo` INT NULL DEFAULT '0' AFTER ` is_combo`,
	ADD COLUMN `so_luong_con_lai` INT NULL DEFAULT '0' AFTER `so_luong_combo`;
SELECT `DEFAULT_COLLATION_NAME` FROM `information_schema`.`SCHEMATA` WHERE `SCHEMA_NAME`='haidv';

CREATE TABLE `chi_tiet_combo` (
	`id` INT NOT NULL AUTO_INCREMENT,
	`ma_chi_tiet_hd` INT NOT NULL DEFAULT '0',
	`created_at` TIMESTAMP NOT NULL,
	`updated_at` TIMESTAMP NOT NULL,
	`deleted_at` TIMESTAMP NOT NULL,
	PRIMARY KEY (`id`)
)
