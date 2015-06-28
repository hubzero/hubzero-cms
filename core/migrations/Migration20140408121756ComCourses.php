<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding indices
 **/
class Migration20140408121756ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasField('#__courses_members', 'token'))
		{
			$query = "ALTER TABLE `#__courses_members` ADD `token` VARCHAR(23)  NOT NULL  DEFAULT '';";
			$this->db->setQuery($query);
			$this->db->query();

			$path = PATH_APP . DS . 'site' . DS . 'courses' . DS . 'certificates';

			if (is_dir($path))
			{
				require_once(PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');

				// Loop through all files and separate them into arrays of images, folders, and other
				$dirIterator = new DirectoryIterator($path);
				foreach ($dirIterator as $file)
				{
					if ($file->isDot())
					{
						continue;
					}

					if ($file->isDir())
					{
						continue;
					}

					if ($file->isFile())
					{
						$name = $file->getFilename();
						if (('cvs' == strtolower($name))
						 || ('.svn' == strtolower($name)))
						{
							continue;
						}

						$bits = explode('_', $name);
						if (count($bits) < 4)
						{
							continue;
						}

						$course = $bits[1];
						$offering = $bits[2];
						$user = strstr($bits[3], '.', true);

						$member = \Components\Courses\Models\Member::getInstance($user, $course, $offering, null);
						$member->token();
					}
				}
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__courses_members', 'token'))
		{
			$query = "ALTER TABLE `#__courses_members` DROP COLUMN `token`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}