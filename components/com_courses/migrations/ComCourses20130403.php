<?php

class ComCourses20130403 extends Migration
{
	protected function up()
	{
		$query = "ALTER TABLE `jos_courses_pages` ADD `course_id` INT(11)  NOT NULL  DEFAULT '0'  AFTER `id`;";

		$this->get('db')->exec($query);
	}

	protected function down()
	{
		$query = "ALTER TABLE `jos_courses_pages` DROP `course_id`;";

		$this->get('db')->exec($query);
	}
}