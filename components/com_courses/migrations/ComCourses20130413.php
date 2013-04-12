<?php

class ComCourses20130413 extends Hubzero_Migration
{
	protected function up()
	{
		// Add a unique index on grade book and asset_id field to forms table
		$query = "ALTER TABLE `jos_courses_grade_book` ADD UNIQUE INDEX `alternate_key` (`user_id`, `scope`, `scope_id`);
					ALTER TABLE `jos_courses_forms` ADD `asset_id` INT(11)  NULL  DEFAULT NULL  AFTER `created`;
					ALTER TABLE `jos_courses_forms` ADD `asset_id` INT(11)  NULL  DEFAULT NULL  AFTER `created`;";
		$this->get('db')->exec($query);

		// Get the form id from the asset content fields
		$query = "SELECT `id`, `content` FROM `jos_courses_assets` WHERE `type`='exam';";
		$result = $this->get('db')->query($query);
		$rows   = $result->fetchAll(PDO::FETCH_ASSOC);

		// Now insert those into the new forms asset_id field
		foreach ($rows as $row)
		{
			$stmt = $this->get('db')->prepare("UPDATE `jos_courses_forms` SET `asset_id` = ? WHERE `id` = ? AND `asset_id` IS NULL");
			$stmt->execute(array($row['id'], json_decode($row['content'])->form_id));
		}

		// Delete the content field for asset type of exam
		$query = "UPDATE `jos_courses_assets` SET `content` = '' WHERE `type` = 'exam';";
		$this->get('db')->exec($query);
	}
}