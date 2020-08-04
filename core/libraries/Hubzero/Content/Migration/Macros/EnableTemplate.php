<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Macros;

use Hubzero\Content\Migration\Macro;

/**
 * Migration macro to enable a component
 **/
class EnableComponent extends Macro
{
	/**
	 * Enable component
	 *
	 * @param   string  $element  Element
	 * @return  bool
	 **/
	public function __invoke($element)
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$enabled = 1;

			$query = $this->db->getQuery()
				->update('#__extensions')
				->set(array(
					'enabled' => $enabled
				))
				->whereEquals('element', $element)
				->toString();
			$this->db->setQuery($query);
			if ($this->db->query())
			{
				$this->log(sprintf('Set component "%s" status to "%s"', $element, $enabled));
				return true;
			}
		}

		return false;
	}
}
