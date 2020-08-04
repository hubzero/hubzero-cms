<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Macros;

use Hubzero\Content\Migration\Macro;

/**
 * Migration macro to install a module
 **/
class InstallModule extends Macro
{
	/**
	 * Instead of just adding to the extensions table, install module in modules table
	 *
	 * @param   string  $module    Module name
	 * @param   string  $position  Module position
	 * @param   bool    $always    If true - always install, false - only install if another module of that type isn't present
	 * @param   array   $params    Params (if already known)
	 * @param   int     $client    Client (site=0, admin=1)
	 * @param   mixed   $menus     (int, array) menus to install to (0=all)
	 * @return  void
	 **/
	public function __invoke($module, $position, $always=true, $params='', $client=0, $menus=0)
	{
		$title    = $this->db->quote(ucfirst($module));
		$position = $this->db->quote($position);
		$module   = $this->db->quote('mod_' . strtolower($module));
		$client   = $this->db->quote((int)$client);
		$access   = ($this->db->tableExists('#__extensions')) ? 1 : 0;

		// Build params string
		$params = json_encode($params);

		if (!$always)
		{
			$query = $this->db->getQuery()
				->select('id')
				->from('#__modules')
				->whereEquals('module', $module)
				->toString();
			$this->db->setQuery($query);

			if ($this->db->loadResult())
			{
				return true;
			}
		}

		$query = $this->db->getQuery()
			->select('ordering')
			->from('#__modules')
			->whereEquals('position', $position)
			->order('ordering', 'desc')
			->limit(1)
			->toString();
		$this->db->setQuery($query);
		$ordering = (int)(($this->db->loadResult()) ? $this->db->loadResult() + 1 : 0);

		$query = $this->db->getQuery()
			->insert('#__modules')
			->values(array(
				'title'     => $title,
				'content'   => '',
				'ordering'  => $ordering,
				'position'  => $position,
				'published' => 1,
				'module'    => $module,
				'access'    => $access,
				'showtitle' => 0,
				'params'    => $params,
				'client_id' => $client
			))
			->toString();

		$this->db->setQuery($query);
		$this->db->query();
		$id = $this->db->quote($this->db->insertid());

		$menus = (array)$menus;
		foreach ($menus as $menu)
		{
			$menu  = $this->db->quote($menu);

			$query = $this->db->getQuery()
				->insert('#__modules_menu')
				->values(array(
					'moduleid' => $id,
					'menuid'   => $menu
				))
				->toString();
			$this->db->setQuery($query);
			$this->db->query();

			$this->log(sprintf('Added module_menu entry for module "%s" to menu "%s"', $module, $menu));
		}

		return true;
	}
}
