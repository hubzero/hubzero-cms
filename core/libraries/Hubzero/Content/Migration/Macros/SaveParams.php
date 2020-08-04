<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Macros;

use Hubzero\Content\Migration\Macro;
use Hubzero\Config\Registry;

/**
 * Migration macro to save extension params
 **/
class SaveParams extends Macro
{
	/**
	 * Saves extension params (only applies to J2.5 and up!)
	 *
	 * @param   string  $element  The element to which the params apply
	 * @param   array   $params   The params being saved
	 * @return  bool
	 **/
	public function __invoke($element, $params)
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$element = strtolower($element);

			$query = $this->db->getQuery()
				->select('extension_id')
				->from('#__extensions');

			// First, make sure it's there
			if (substr($element, 0, 4) == 'plg_')
			{
				$ext = explode('_', $element);
				$element = $ext[2];

				$query->whereEquals('folder', $ext[1]);
			}

			$query->whereEquals('element', $element);

			$this->db->setQuery($query->toString());
			if (!$id = $this->db->loadResult())
			{
				return false;
			}

			// Build params JSON
			if (is_array($params))
			{
				$params = json_encode($params);
			}
			elseif ($params instanceof Registry)
			{
				$params = $params->toString('JSON');
			}
			else
			{
				$this->log(sprintf('Params for extension "%s" not in usable format', $element), 'warning');
				return false;
			}

			$query = $this->db->getQuery()
				->update('#__extensions')
				->set(array(
					'params' => $params
				))
				->whereEquals('extension_id', $id)
				->toString();
			$this->db->setQuery($query);

			if ($this->db->query())
			{
				$this->log(sprintf('Extension params saved for "%s"', $element));
				return true;
			}
		}

		$this->log(sprintf('Required table not found for saving "%s" params', $element), 'warning');

		return false;
	}
}
