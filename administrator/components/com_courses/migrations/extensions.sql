LOCK TABLES `jos_plugins` WRITE;

INSERT INTO `jos_plugins` (`id`, `name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
VALUES
	(null,'Members - Courses','courses','members',0,16,1,0,0,0,'0000-00-00 00:00:00',''),
	(null,'Courses - Syllabus','syllabus','courses',0,1,0,0,0,0,'0000-00-00 00:00:00',''),
	(null,'Courses - Disucssions','forum','courses',0,2,1,0,0,0,'0000-00-00 00:00:00',''),
	(null,'Courses - My Progress','progress','courses',0,3,1,0,0,0,'0000-00-00 00:00:00',''),
	(null,'Courses - Announcements','announcements','courses',0,4,1,0,0,0,'0000-00-00 00:00:00',''),
	(null,'Courses - Dashboard','dashboard','courses',0,5,1,0,0,0,'0000-00-00 00:00:00',''),
	(null,'Courses - Course Overview','overview','courses',0,6,1,0,0,0,'0000-00-00 00:00:00',''),
	(null,'Courses - Course Reviews','reviews','courses',0,7,1,0,0,0,'0000-00-00 00:00:00',''),
	(null,'Courses - Course Offerings','offerings','courses',0,8,1,0,0,0,'0000-00-00 00:00:00',''),
	(null,'Courses - Course Related','related','courses',0,9,1,0,0,0,'0000-00-00 00:00:00','');

UNLOCK TABLES;