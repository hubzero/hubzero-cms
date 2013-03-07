DROP TABLE IF EXISTS `jos_courses_managers`;
ALTER TABLE `jos_courses_offering_members` MODIFY COLUMN `user_id` INT(11) NOT NULL DEFAULT '0' AFTER `id`;
ALTER TABLE `jos_courses_offering_members` ADD `course_id` INT(11)  NOT NULL  DEFAULT '0'  AFTER `user_id`;
ALTER TABLE `jos_courses_offering_members` MODIFY COLUMN `section_id` INT(11) NOT NULL DEFAULT '0' AFTER `offering_id`;
RENAME TABLE `jos_courses_offering_members` TO `jos_courses_members`;
