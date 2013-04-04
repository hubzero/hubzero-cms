<?php

class ComCourses20130404 extends Migration
{
	protected function up()
	{
		$query = "ALTER TABLE `jos_courses_announcements` 
			ADD `publish_up` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00',
			ADD `publish_down` DATETIME  NOT NULL  DEFAULT '0000-00-00 00:00:00',
			ADD `sticky` TINYINT(2)  NOT NULL  DEFAULT '0';";

		$this->get('db')->exec($query);
	}

	protected function down()
	{
		$query = "ALTER TABLE `jos_courses_pages` DROP `publish_up`, DROP `publish_down`, DROP `sticky`;";

		$this->get('db')->exec($query);
	}
}