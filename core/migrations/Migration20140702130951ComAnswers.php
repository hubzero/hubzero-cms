<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for setting state=3 on reported question comments
 **/
class Migration20140702130951ComAnswers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableHasField('#__answers_questions', 'state'))
		{
			$query = "SELECT referenceid FROM `#__abuse_reports` WHERE state=0 AND category IN ('question')";
			$this->db->setQuery($query);
			if ($ids = $this->db->loadColumn())
			{
				$ids = array_map('intval', $ids);

				$query = "UPDATE `#__answers_questions` SET state=3 WHERE id IN (" . implode(',', $ids) . ")";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableHasField('#__answers_responses', 'state'))
		{
			$query = "SELECT referenceid FROM `#__abuse_reports` WHERE state=0 AND category IN ('answer')";
			$this->db->setQuery($query);
			if ($ids = $this->db->loadColumn())
			{
				$ids = array_map('intval', $ids);

				$query = "UPDATE `#__answers_responses` SET state=3 WHERE id IN (" . implode(',', $ids) . ")";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableHasField('#__item_comments', 'state'))
		{
			$query = "SELECT referenceid FROM `#__abuse_reports` WHERE state=0 AND category IN ('itemcomment', 'answercomment')";
			$this->db->setQuery($query);
			if ($ids = $this->db->loadColumn())
			{
				$ids = array_map('intval', $ids);

				$query = "UPDATE `#__item_comments` SET state=3 WHERE id IN (" . implode(',', $ids) . ")";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__answers_questions', 'state'))
		{
			$query = "UPDATE `#__answers_questions` SET state=1 WHERE state=3";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableHasField('#__answers_responses', 'state'))
		{
			$query = "UPDATE `#__answers_responses` SET state=1 WHERE state=3";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableHasField('#__item_comments', 'state'))
		{
			$query = "UPDATE `#__item_comments` SET state=1 WHERE state=3";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
