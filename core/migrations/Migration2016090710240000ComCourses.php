<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script to drop misnamed field that can get left behind during upgrades
 **/
class Migration2016090710240000ComCourses extends Base
{	
	public function up()
	{
		if ($this->db->tableHasField('#__courses_form_respondents', 'attempts'))
                {
                        $query = "ALTER TABLE `#__courses_form_respondents` DROP `attempts`;";
                        $this->db->setQuery($query);
                        $this->db->query();
                }
	}

	public function down()
	{
	}
}
