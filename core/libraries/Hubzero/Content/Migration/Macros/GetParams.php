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
class GetParams extends Macro
{
	/**
	 * Get element params
	 *
	 * @param   string  $option     com_xyz
	 * @param   bool    $returnRaw  whether or not to return jregistry object or raw param string
	 * @return  object|string
	 **/
	public function __invoke($element, $returnRaw=false)
	{
		$params = null;

		if ($this->db->tableExists('#__extensions'))
		{
			$query = $this->db->getQuery()
				->select('params')
				->from('#__extensions');

			if (substr($element, 0, 4) == 'plg_')
			{
				$ext = explode('_', $element);
				$element = $ext[2];

				$query->whereEquals('folder', $ext[1]);
			}

			$query->whereEquals('element', $element);

			$this->db->setQuery($query->toString());
			$params = $this->db->loadResult();
		}
		else
		{
			$this->log(sprintf('Required table not found for retrieving "%s" params', $element), 'warning');
		}

		if (!$returnRaw)
		{
			$params = new Registry($params);
		}

		return $params;
	}
}
