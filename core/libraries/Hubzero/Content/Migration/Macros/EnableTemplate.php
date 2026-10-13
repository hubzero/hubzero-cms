<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Macros;

use Hubzero\Content\Migration\Macro;

/**
 * Migration macro to enable a template
 *
 * (This file used to declare EnableComponent, so the macro could never be
 * resolved and only updated rows by element regardless of type.)
 **/
class EnableTemplate extends Macro
{
	/**
	 * Enable template
	 *
	 * @param   string  $element  Element
	 * @param   int     $client   Client id (0 site, 1 admin); null for both
	 * @return  bool
	 **/
	public function __invoke($element, $client=null)
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$enabled = 1;

			$query = $this->db->getQuery()
				->update('#__extensions')
				->set(array(
					'enabled' => $enabled
				))
				->whereEquals('type', 'template')
				->whereEquals('element', $element);

			if ($client !== null)
			{
				$query->whereEquals('client_id', (int) $client);
			}

			$this->db->setQuery($query->toString());
			if ($this->db->query())
			{
				$this->log(sprintf('Set template "%s" status to "%s"', $element, $enabled));
				return true;
			}
		}

		return false;
	}
}
