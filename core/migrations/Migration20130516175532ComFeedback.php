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
 * Migration script for feedback image update based on asset relocation
 **/
class Migration20130516175532ComFeedback extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__components'))
		{
			$query = "UPDATE `#__components` SET `params` = REPLACE(`params`,'/components/com_feedback/images/contributor.gif','/components/com_feedback/assets/img/contributor.gif') WHERE `option` = 'com_feedback';";
		}
		else
		{
			$query = "UPDATE `#__extensions` SET `params` = REPLACE(`params`,'/components/com_feedback/images/contributor.gif','/components/com_feedback/assets/img/contributor.gif') WHERE `element` = 'com_feedback';";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__components'))
		{
			$query = "UPDATE `#__components` SET `params` = REPLACE(`params`,'/components/com_feedback/assets/img/contributor.gif','/components/com_feedback/images/contributor.gif') WHERE `option` = 'com_feedback';";
		}
		else
		{
			$query = "UPDATE `#__extensions` SET `params` = REPLACE(`params`,'/components/com_feedback/assets/img/contributor.gif','/components/com_feedback/images/contributor.gif') WHERE `element` = 'com_feedback';";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
