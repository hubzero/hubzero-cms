<?php

class ComCourses20130412 extends Hubzero_Migration
{
	protected function up()
	{
		$query = "ALTER TABLE `jos_courses_offering_sections` ADD `grade_policy_id` INT(11)  NOT NULL  DEFAULT '1'  AFTER `enrollment`;";

		$this->get('db')->exec($query);
	}

	protected function down()
	{
		$query = "ALTER TABLE `jos_courses_offering_sections` DROP `grade_policy_id`;";

		$this->get('db')->exec($query);
	}
}