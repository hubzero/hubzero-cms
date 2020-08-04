<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Macros;

use Hubzero\Content\Migration\Macro;

/**
 * Migration macro to delete a module entry
 **/
class DeleteModuleEntry extends Macro
{
	/**
	 * Remove module entries from the appropriate table, depending on the CMS version
	 *
	 * @param   string  $name    Plugin name
	 * @param   int     $client  Client (site=0, admin=1)
	 * @return  bool
	 **/
	public function __invoke($element, $client=null)
	{
		if ($this->db->tableExists('#__extensions'))
		{
			// Delete module entry
			$query = $this->db->getQuery()
				->delete('#__extensions')
				->whereEquals('element', $element);
			if (isset($client))
			{
				$query->whereEquals('client_id', $client);
			}

			$this->db->setQuery($query->toString());
			$this->db->query();

			$this->log(sprintf('Removed extension entry for module "%s"', $element));
		}

		// See if entries are present in #__modules table as well
		$query = $this->db->getQuery()
			->select('id')
			->from('#__modules')
			->whereEquals('module', $element);
		if (isset($client))
		{
			$query->whereEquals('client_id', $client);
		}

		$this->db->setQuery($query->toString());
		$ids = $this->db->loadColumn();

		if ($ids && count($ids) > 0)
		{
			// Delete modules and module menu entries
			$query = $this->db->getQuery()
				->delete('#__modules')
				->whereIn('id', $ids)
				->toString();
			$this->db->setQuery($query);
			$this->db->query();

			$query = $this->db->getQuery()
				->delete('#__modules_menu')
				->whereIn('moduleid', $ids)
				->toString();
			$this->db->setQuery($query);
			$this->db->query();

			$this->log(sprintf('Removed module/menu entries for module "%s"', $element));
		}

		return true;
	}
}
