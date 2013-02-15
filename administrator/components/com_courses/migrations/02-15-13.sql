# change asset description to content (that's really how we're using it anyways)

ALTER TABLE `jos_courses_assets` CHANGE `description` `content` MEDIUMTEXT;
