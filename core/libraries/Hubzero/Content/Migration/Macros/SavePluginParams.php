<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Macros;

/**
 * Migration macro to save plugin params
 **/
class SavePluginParams extends SaveParams
{
	/**
	 * Save plugin params
	 *
	 * @param   string  $folder   Plugin folder
	 * @param   string  $element  Plugin element
	 * @param   array   $params   Plugin params (if already known)
	 * @return  bool
	 **/
	public function __invoke($folder, $element, $params)
	{
		return parent::__invoke('plg_' . $folder . '_' . $element, $params);
	}
}
