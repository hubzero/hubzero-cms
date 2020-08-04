<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Macros;

use Hubzero\Content\Migration\Macro;

/**
 * Migration macro to add a module entry
 **/
class AddModuleEntry extends Macro
{
	/**
	 * Add, as needed, the module entry to the appropriate table, depending on the CMS version
	 *
	 * @param   string  $element  Plugin element
	 * @param   int     $enabled  Whether or not the plugin should be enabled
	 * @param   array   $params   Plugin params (if already known)
	 * @param   int     $client   Client (site=0, admin=1)
	 * @return  bool
	 **/
	public function __invoke($element, $enabled=1, $params='', $client=0)
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$name = $element;

			// First, make sure it isn't already there
			$query = $this->db->getQuery()
				->select('extension_id')
				->from('#__extensions')
				->whereEquals('name', $name)
				->toString();
			$this->db->setQuery($query);
			if ($this->db->loadResult())
			{
				$this->log(sprintf('Extension entry already exists for module "%s"', $element));
				return true;
			}

			$ordering = 0;

			if (!empty($params) && is_array($params))
			{
				$params = json_encode($params);
			}

			$query = $this->db->getQuery()
				->insert('#__extensions')
				->values(array(
					'name'           => $name,
					'type'           => 'module',
					'element'        => $element,
					'folder'         => '',
					'client_id'      => $client,
					'enabled'        => $enabled,
					'access'         => 1,
					'protected'      => 0,
					'manifest_cache' => '',
					'params'         => $params,
					'custom_data'    => '',
					'system_data'    => '',
					'checked_out'    => 0,
					'ordering'       => $ordering,
					'state'          => 0
				))
				->toString();
			$this->db->setQuery($query);
			$this->db->query();

			$this->log(sprintf('Added extension entry for module "%s"', $element));

			return true;
		}

		$this->log(sprintf('Required table not found for adding module "%s"', $element), 'warning');

		return false;
	}
}
