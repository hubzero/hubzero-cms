ALTER TABLE `jos_courses_offering_members` DROP PRIMARY KEY;
ALTER TABLE `jos_courses_offering_members` ADD `id` INT(11) UNSIGNED  NOT NULL  AUTO_INCREMENT  PRIMARY KEY  FIRST;
ALTER TABLE `jos_courses_offering_members` CHANGE `offering_id` `offering_id` INT(11)  NOT NULL  DEFAULT '0';
ALTER TABLE `jos_courses_offering_members` CHANGE `user_id` `user_id` INT(11)  NOT NULL  DEFAULT '0';
ALTER TABLE `jos_courses_offering_members` ADD INDEX `idx_offering_id` (`offering_id`);
ALTER TABLE `jos_courses_offering_members` ADD INDEX `idx_user_id` (`user_id`);
ALTER TABLE `jos_courses_offering_members` ADD INDEX `idx_role_id` (`role_id`);
ALTER TABLE `jos_courses_offering_members` ADD INDEX `idx_section_id` (`section_id`);

ALTER TABLE `jos_courses_offerings` CHANGE `start_date` `start_date` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `jos_courses_offerings` CHANGE `end_date` `end_date` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `jos_courses_offerings` ADD INDEX `idx_state` (`state`);
ALTER TABLE `jos_courses_offerings` ADD INDEX `idx_course_id` (`course_id`);
ALTER TABLE `jos_courses_offerings` ADD INDEX `idx_created_by` (`created_by`);

ALTER TABLE `jos_courses_pages` ADD INDEX `idx_offering_id` (`offering_id`);
ALTER TABLE `jos_courses_roles` ADD INDEX `idx_offering_id` (`offering_id`);
ALTER TABLE `jos_courses_units` ADD INDEX `idx_offering_id` (`offering_id`);

ALTER TABLE `jos_courses_page_hits` ADD INDEX `idx_offering_id` (`offering_id`);
ALTER TABLE `jos_courses_page_hits` ADD INDEX `idx_page_id` (`page_id`);
ALTER TABLE `jos_courses_page_hits` ADD INDEX `idx_user_id` (`user_id`);

ALTER TABLE `jos_courses_email_log` CHANGE `eid` `email_id` INT(11)  NOT NULL  DEFAULT '0';
ALTER TABLE `jos_courses_email_log` CHANGE `evid` `version_id` INT(11)  NOT NULL  DEFAULT '0';
ALTER TABLE `jos_courses_email_version` CHANGE `eid` `email_id` INT(11)  NOT NULL  DEFAULT '0';

ALTER TABLE `jos_courses_announcements` ADD INDEX `idx_offering_id` (`offering_id`);
ALTER TABLE `jos_courses_announcements` ADD INDEX `idx_section_id` (`section_id`);
ALTER TABLE `jos_courses_announcements` ADD INDEX `idx_created_by` (`created_by`);
ALTER TABLE `jos_courses_announcements` ADD INDEX `idx_state` (`state`);
ALTER TABLE `jos_courses_announcements` ADD INDEX `idx_priority` (`priority`);

ALTER TABLE `jos_courses_asset_associations` ADD INDEX `idx_asset_id` (`asset_id`);
ALTER TABLE `jos_courses_asset_associations` ADD INDEX `idx_scope_id` (`scope_id`);
ALTER TABLE `jos_courses_asset_associations` ADD INDEX `idx_scope` (`scope`);

ALTER TABLE `jos_courses_asset_groups` ADD INDEX `idx_unit_id` (`unit_id`);
ALTER TABLE `jos_courses_asset_groups` ADD INDEX `idx_created_by` (`created_by`);

ALTER TABLE `jos_courses_assets` ADD INDEX `idx_course_id` (`course_id`);
ALTER TABLE `jos_courses_assets` ADD INDEX `idx_created_by` (`created_by`);
