<?php
/**
 * @package    framework
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Macros;

use Hubzero\Content\Migration\Macro;

/**
 * Migration macro to add a template entry
 **/
class AddTemplateEntry extends Macro
{
	/**
	 * Add, as needed, templates to the CMS
	 *
	 * @param   string  $element    Template element
	 * @param   string  $name       Template name
	 * @param   int     $client     Admin or site client
	 * @param   int     $enabled    Whether or not the template should be enabled
	 * @param   int     $home       Whether or not this should become the enabled template
	 * @param   array   $styles     Template styles
	 * @param   int     $protected  Whether or not the template is a core one or not
	 * @return  bool
	 **/
	public function __invoke($element, $name=null, $client=1, $enabled=1, $home=0, $styles=null, $protected=0)
	{
		if ($this->db->tableExists('#__extensions'))
		{
			if (!isset($name))
			{
				if (substr($element, 0, 4) == 'tpl_')
				{
					$name    = substr($element, 4);
					$element = $name;
				}
				else
				{
					$name = $element;
				}

				$name = ucwords($name);
			}

			// First, see if it already exists
			$query = $this->db->getQuery()
				->select('extension_id')
				->from('#__extensions')
				->whereEquals('type', 'template')
				->whereEquals('client_id', $client)
					->whereEquals('element', $element, 1)
					->orWhereLike('element', $name, 1)
					->resetDepth()
				->toString();
			$this->db->setQuery($query);

			if (!$this->db->loadResult())
			{
				$query = $this->db->getQuery()
					->insert('#__extensions')
					->values(array(
						'name'           => $name,
						'type'           => 'template',
						'element'        => $element,
						'folder'         => '',
						'client_id'      => $client,
						'enabled'        => $enabled,
						'access'         => 1,
						'protected'      => 0,
						'manifest_cache' => '',
						'params'         => '{}',
						'custom_data'    => '',
						'system_data'    => '',
						'checked_out'    => 0,
						'ordering'       => 0,
						'state'          => 0
					))
					->toString();
				$this->db->setQuery($query);
				$this->db->query();

				$this->log(sprintf('Added extension entry for template "%s"', $element));

				if ($this->db->tableExists('#__template_styles'))
				{
					// If we're setting this template to be default, disable others first
					if ($home)
					{
						$query = $this->db->getQuery()
							->update('#__template_styles')
							->set(array(
								'home' => 0
							))
							->whereEquals('client_id', $client)
							->toString();
						$this->db->setQuery($query);
						$this->db->query();

						$this->log(sprintf('Disabling "home" for all other templates (client "%s")', $client));
					}

					$query = $this->db->getQuery()
						->insert('#__template_styles')
						->values(array(
							'template'  => $element,
							'client_id' => $client,
							'home'      => $home,
							'title'     => $name,
							'params'    => ((isset($styles)) ? $this->db->quote(json_encode($styles)) : '{}')
						))
						->toString();
					$this->db->setQuery($query);
					$this->db->query();

					$this->log(sprintf('Added style entry for template "%s"', $element));
				}
			}
			else
			{
				$this->log(sprintf('Extension entry already exists for template "%s"', $element));
			}

			return true;
		}

		$this->log(sprintf('Required table not found for adding template "%s"', $element), 'warning');

		return false;
	}
}
