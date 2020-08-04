<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Macros;

use Hubzero\Content\Migration\Macro;

/**
 * Migration macro to rename a plugin entry
 **/
class RenamePluginEntry extends Macro
{
	/**
	 * Rename a plugin entry in the appropriate table, depending on the CMS version
	 *
	 * @param   string  $folder   Plugin folder
	 * @param   string  $element  Plugin element
	 * @param   string  $name     The new plugin name
	 * @return  bool
	 **/
	public function __invoke($folder, $element, $name)
	{
		$table = '#__extensions';
		$pk    = 'extension_id';
		if ($this->db->tableExists('#__plugins'))
		{
			$table = '#__plugins';
			$pk    = 'id';
		}

		$folder  = strtolower($folder);
		$element = strtolower($element);

		$query = $this->db->getQuery()
			->select($pk)
			->from($table)
			->whereEquals('folder', $folder)
			->whereEquals('element', $element)
			->toString();

		// First, make sure the plugin exists
		$this->db->setQuery($query);

		if ($id = $this->db->loadResult())
		{
			$query = $this->db->getQuery()
				->update($table)
				->set(array(
					'name' => $name
				))
				->whereEquals($pk, $id)
				->toString();

			$this->db->setQuery($query);

			if ($this->db->query())
			{
				$this->log(sprintf('Renamed plugin plg_%s_%s to "%s"', $folder, $element, $name));
				return true;
			}
		}

		return false;
	}
}
