CREATE TABLE `jos_courses_offering_section_dates` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int(11) NOT NULL DEFAULT '0',
  `scope` varchar(150) NOT NULL DEFAULT '',
  `scope_id` int(11) NOT NULL DEFAULT '0',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
