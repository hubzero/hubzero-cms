<?php

class ComCourses20130401 extends Migration
{
	protected function up()
	{
		$query = "ALTER TABLE `jos_courses_offering_sections` ADD `enrollment` TINYINT(2)  NOT NULL  DEFAULT '0'  AFTER `created_by`;";

		$this->get('db')->exec($query);
	}

	protected function down()
	{
		$query = "ALTER TABLE `jos_courses_offering_sections` DROP `enrollment`;";

		$this->get('db')->exec($query);
	}
}