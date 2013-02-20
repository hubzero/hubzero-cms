ALTER TABLE `jos_courses_asset_groups` ADD `state` TINYINT(2)  NOT NULL  DEFAULT '0'  AFTER `created_by`;
ALTER TABLE `jos_courses_units` DROP `start_date`;
ALTER TABLE `jos_courses_units` DROP `end_date`;
ALTER TABLE `jos_courses_units` ADD `state` TINYINT(2)  NOT NULL  DEFAULT '0'  AFTER `created_by`;
ALTER TABLE `jos_courses_offerings` DROP `start_date`;
ALTER TABLE `jos_courses_offerings` DROP `end_date`;
