<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Macros;

use Hubzero\Content\Migration\Macro;

/**
 * Migration macro to delete a plugin entry
 **/
class DeletePluginEntry extends Macro
{
	/**
	 * Remove plugin entries from the appropriate table, depending on the CMS version
	 *
	 * @param   string  $folder   Plugin folder
	 * @param   string  $element  Plugin element
	 * @return  bool
	 **/
	public function __invoke($folder, $element=null)
	{
		$table = '#__extensions';
		if ($this->db->tableExists('#__plugins'))
		{
			$table = '#__plugins';
		}

		if ($this->db->tableExists($table))
		{
			$enabled = 0;

			$query = $this->db->getQuery()
				->delete($table)
				->whereEquals('folder', $folder);
			if ($element)
			{
				$query->whereEquals('element', $element);
			}

			$query = $query
				->toString();

			$this->db->setQuery($query);

			if ($this->db->query())
			{
				$this->log(sprintf('Removed plugin entry "plg_%s_%s"', $folder, $element));
				return true;
			}
		}

		return true;
	}
}
