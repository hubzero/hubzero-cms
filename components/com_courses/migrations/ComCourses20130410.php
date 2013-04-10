<?php

class ComCourses20130410 extends Hubzero_Migration
{
	protected function up()
	{
		$query = "ALTER TABLE `jos_courses_member_notes` ADD INDEX `idx_scoped` (`scope`, `scope_id`);
			ALTER TABLE `jos_courses_member_notes` ADD INDEX `idx_createdby` (`created_by`);";

		$this->get('db')->exec($query);
	}

	protected function down()
	{
		$query = "DROP INDEX `idx_scoped` ON `jos_courses_member_notes`;
				DROP INDEX `idx_createdby` ON `jos_courses_member_notes`;";

		$this->get('db')->exec($query);
	}
}