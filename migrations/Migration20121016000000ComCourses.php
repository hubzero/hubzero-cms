<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding courses component entry
 **/
class Migration20121016000000ComCourses extends Base
{
	public function up()
	{
		$this->addComponentEntry('courses', 'com_courses', 0);
	}
}