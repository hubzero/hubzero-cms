<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Macros;

use Hubzero\Content\Migration\Macro;

/**
 * Migration macro to disable a template
 **/
class DisableTemplate extends Macro
{
	/**
	 * Disable template
	 *
	 * A template whose style is the current default ("home") for its client
	 * is left alone, since disabling it would leave that client without a
	 * template.
	 *
	 * @param   string  $element  Element
	 * @param   int     $client   Client id (0 site, 1 admin); null for both
	 * @return  bool
	 **/
	public function __invoke($element, $client=null)
	{
		if (!$this->db->tableExists('#__extensions'))
		{
			return false;
		}

		if ($this->db->tableExists('#__template_styles'))
		{
			$query = $this->db->getQuery()
				->select('id')
				->from('#__template_styles')
				->whereEquals('template', $element)
				->whereEquals('home', 1);

			if ($client !== null)
			{
				$query->whereEquals('client_id', (int) $client);
			}

			$this->db->setQuery($query->toString());
			if ($this->db->loadResult())
			{
				$this->log(sprintf('Template "%s" is the default template; not disabling it', $element));
				return false;
			}
		}

		$enabled = 0;

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

		return false;
	}
}
