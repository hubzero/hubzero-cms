<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Macros;

use Hubzero\Content\Migration\Macro;

/**
 * Migration macro to disable a module
 **/
class DisableModule extends Macro
{
	/**
	 * Enable module
	 *
	 * @param   string  $element  Element
	 * @return  bool
	 **/
	public function __invoke($element)
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$enabled = 0;

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
				if ($this->db->tableExists('#__modules'))
				{
					$query = $this->db->getQuery()
						->update('#__modules')
						->set(array(
							'published' => $enabled
						))
						->whereEquals('module', $element)
						->toString();
					$this->db->setQuery($query);
					$this->db->query();
				}

				$this->log(sprintf('Set module "%s" status to "%s"', $element, $enabled));
				return true;
			}
		}

		return false;
	}
}
