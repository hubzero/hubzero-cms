<?php

class ComCourses20130322 extends Hubzero_Migration
{
	protected function up()
	{
		$query = "ALTER TABLE `jos_courses_form_respondent_progress` ADD `submitted` DATETIME  NULL  AFTER `answer_id`;";

		$this->get('db')->exec($query);
	}

	protected function down()
	{
		$query = "ALTER TABLE `jos_courses_form_respondent_progress` DROP `submitted`;";

		$this->get('db')->exec($query);
	}
}