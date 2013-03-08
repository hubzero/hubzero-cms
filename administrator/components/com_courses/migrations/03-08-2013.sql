ALTER TABLE `jos_courses_offering_section_codes` CHANGE `redeemed` `redeemed` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00';
ALTER TABLE `jos_courses_offering_section_codes` ADD `redeemed_by` INT(11)  NOT NULL  DEFAULT '0'  AFTER `redeemed`;
