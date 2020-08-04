<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Macros;

use Hubzero\Content\Migration\Macro;

/**
 * Migration macro to disable a plugin
 **/
class DisablePlugin extends Macro
{
	/**
	 * Disable plugin
	 *
	 * @param   string  $folder   Plugin folder
	 * @param   string  $element  Plugin element
	 * @return  bool
	 **/
	public function __invoke($folder, $element)
	{
		$table = '#__extensions';
		$field = 'enabled';
		if ($this->db->tableExists('#__plugins'))
		{
			$table = '#__plugins';
			$field = 'published';
		}

		if ($this->db->tableExists($table))
		{
			$enabled = 0;

			$query = $this->db->getQuery()
				->update($table)
				->set(array(
					$field => $enabled
				))
				->whereEquals('folder', $folder)
				->whereEquals('element', $element)
				->toString();

			$this->db->setQuery($query);

			if ($this->db->query())
			{
				$this->log(sprintf('Set plugin "plg_%s_%s" status to "%s"', $folder, $element, $enabled));
				return true;
			}
		}

		return false;
	}
}
