<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration to fix file extensions on screenshots
 **/

class Migration20181015161919ComTools extends Base
{
	/**
	 * Up;
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__screenshots'))
		{
			$query = "SELECT * FROM `#__screenshots` WHERE LOWER(`filename`) NOT LIKE '%.png'
					AND LOWER(`filename`) NOT LIKE '%.gif'
					AND LOWER(`filename`) NOT LIKE '%.jpg'
					AND LOWER(`filename`) NOT LIKE '%.bmp'
					AND LOWER(`filename`) NOT LIKE '%.swf'
					AND LOWER(`filename`) NOT LIKE '%.mov';";

			$this->db->setQuery($query);
			$rows = $this->db->loadObjectList();

			foreach ($rows as $row)
			{
				if (!trim($row->filename))
				{
					continue;
				}

				if (substr($row->filename, -4) != '.')
				{
					$ext = substr($row->filename, -3);

					if (in_array($ext, array('png', 'PNG', 'gif', 'GIF', 'jpg', 'JPG', 'bmp', 'BMP', )))
					{
						$filename = substr($row->filename, 0, -3) . '.' . $ext;

						$query = "UPDATE `#__screenshots` SET `filename`=" . $this->db->quote($filename) . " WHERE `id`=" . $this->db->quote($row->id);
						$this->db->setQuery($query);
						$this->db->query();
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
		// None
	}
}
