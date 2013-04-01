<?php

class ComCourses20130401 extends Migration
{
	protected function up()
	{
		$query = "INSERT INTO `jos_plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
			SELECT 'Courses - Course FAQ','faq','courses',0,9,1,0,0,0,'0000-00-00 00:00:00',''
			FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `jos_plugins` WHERE `name` = 'Courses - Course FAQ');
		
			INSERT INTO `jos_plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
			SELECT 'Courses - Course Related','related','courses',0,10,1,0,0,0,'0000-00-00 00:00:00',''
			FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `jos_plugins` WHERE `name` = 'Courses - Course Related');
		
			INSERT INTO `jos_plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)
			SELECT 'Courses - Store','store','courses',0,12,1,0,0,0,'0000-00-00 00:00:00',''
			FROM DUAL WHERE NOT EXISTS (SELECT `name` FROM `jos_plugins` WHERE `name` = 'Courses - Store');";

		$this->get('db')->exec($query);
	}

	protected function down()
	{
		$query = "DELETE FROM `jos_plugins` WHERE `element` = 'faq' AND `folder`='courses';
			DELETE FROM `jos_plugins` WHERE `element` = 'related' AND `folder`='courses';
			DELETE FROM `jos_plugins` WHERE `element` = 'store' AND `folder`='courses';";

		$this->get('db')->exec($query);
	}
}