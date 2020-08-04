<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Macros;

use Hubzero\Content\Migration\Macro;

/**
 * Migration macro to delete a template entry
 **/
class DeleteTemplateEntry extends Macro
{
	/**
	 * Remove template entires from the appropriate tables
	 *
	 * @param   string  $name    Template element name
	 * @param   int     $client  Client id
	 * @return  bool
	 **/
	public function __invoke($element, $client=1)
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$query = $this->db->getQuery()
				->delete('#__extensions')
				->whereEquals('type', 'template')
				->whereEquals('element', $element)
				->whereEquals('client_id', $client)
				->toString();
			$this->db->setQuery($query);
			$this->db->query();

			$this->log(sprintf('Removed extension entry for template "%s"', $element));

			if ($this->db->tableExists('#__template_styles'))
			{
				$query = $this->db->getQuery()
					->delete('#__template_styles')
					->whereEquals('template', $element)
					->whereEquals('client_id', $client)
					->toString();
				$this->db->setQuery($query);
				$this->db->query();

				$this->log(sprintf('Removed style entry for template "%s"', $element));

				// Now make sure we have an enabled template (don't really care which one it is)
				$query = $this->db->getQuery()
					->select('id')
					->from('#__template_styles')
					->whereEquals('home', 1)
					->whereEquals('client_id', $client)
					->toString();
				$this->db->setQuery($query);
				if (!$this->db->loadResult())
				{
					$query = $this->db->getQuery()
						->select('id')
						->from('#__template_styles')
						->whereEquals('client_id', $client)
						->order('id', 'desc')
						->limit(1)
						->toString();
					$this->db->setQuery($query);
					if ($id = $this->db->loadResult())
					{
						$query = $this->db->getQuery()
							->update('#__template_styles')
							->set(array(
								'home' => 1
							))
							->whereEquals('id', $id)
							->toString();
						$this->db->setQuery($query);
						$this->db->query();

						$this->log(sprintf('Setting "home" for template style "%s"', $id));
					}
				}
			}

			return true;
		}

		return false;
	}
}
