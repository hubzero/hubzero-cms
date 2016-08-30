<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing scope of custom migrations
 **/
class Migration20151006204314Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (is_dir(PATH_APP . DS . 'migrations') && $this->db->tableExists('#__migrations'))
		{
			// Get any migrations that have been moved to app
			$exclude = array(".", "..", "index.html");
			$files   = array_diff(scandir(PATH_APP . DS . 'migrations'), $exclude);

			if ($files && count($files) > 0)
			{
				foreach ($files as $file)
				{
					$query = "UPDATE `#__migrations` SET `scope` = 'app/migrations' WHERE `file` = " . $this->db->quote($file);
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
	}
}
