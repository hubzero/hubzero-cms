<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Macros;

use Hubzero\Content\Migration\Macro;

/**
 * Migration macro to add a plugin entry
 **/
class AddPluginEntry extends Macro
{
	/**
	 * Add, as needed, the plugin entry to the appropriate table, depending on the CMS version
	 *
	 * @param   string  $folder   Plugin folder
	 * @param   string  $element  Plugin element
	 * @param   int     $enabled  Whether or not the plugin should be enabled
	 * @param   array   $params   Plugin params (if already known)
	 * @return  bool
	 **/
	public function __invoke($folder, $element, $enabled=1, $params='')
	{
		if (!$this->db->tableExists('#__extensions'))
		{
			$this->log(sprintf('Required table not found for adding plugin "plg_%s_%s"', $folder, $element), 'warning');
			return false;
		}

		$folder  = strtolower($folder);
		$element = strtolower($element);
		$name    = 'plg_' . $folder . '_' . $element;

		// First, make sure it isn't already there
		$query = $this->db->getQuery()
			->select('extension_id')
			->from('#__extensions')
			->whereEquals('folder', $folder)
			->whereEquals('element', $element)
			->toString();
		$this->db->setQuery($query);
		if ($this->db->loadResult())
		{
			$this->log(sprintf('Extension entry already exists for plugin "%s"', $name));
			return true;
		}

		// Get ordering
		$query = $this->db->getQuery()
			->select('ordering')
			->from('#__extensions')
			->whereEquals('folder', $folder)
			->order('ordering', 'desc')
			->limit(1)
			->toString();
		$this->db->setQuery($query);
		$ordering = (is_numeric($this->db->loadResult())) ? $this->db->loadResult()+1 : 1;

		if (!empty($params) && is_array($params))
		{
			$params = json_encode($params);
		}

		$query = $this->db->getQuery()
			->insert('#__extensions')
			->values(array(
				'name'           => $name,
				'type'           => 'plugin',
				'element'        => $element,
				'folder'         => $folder,
				'client_id'      => 0,
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
		if (!$this->db->query())
		{
			$this->log(sprintf('Failed to add extension entry for plugin "%s"', $name));
			return false;
		}

		$this->log(sprintf('Added extension entry for plugin "%s"', $name));

		return true;
	}
}
