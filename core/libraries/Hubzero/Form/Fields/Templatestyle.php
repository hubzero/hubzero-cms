<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Fields;

use Hubzero\Html\Builder\Select as Dropdown;
use Hubzero\Base\ClientManager;
use App;

/**
 * Supports a select grouped list of template styles
 */
class Templatestyle extends Groupedlist
{
	/**
	 * The form field type.
	 *
	 * @var  string
	 */
	public $type = 'Templatestyle';

	/**
	 * Method to get the list of template style options
	 * grouped by template.
	 * Use the client attribute to specify a specific client.
	 * Use the template attribute to specify a specific template
	 *
	 * @return  array  The field option objects as a nested array in groups.
	 */
	protected function getGroups()
	{
		// Initialize variables.
		$groups = array();
		$lang = App::get('language');

		// Get the client and client_id.
		$clientName = $this->element['client'] ? (string) $this->element['client'] : 'site';
		$client = ClientManager::client($clientName, true);

		// Get the template.
		$template = (string) $this->element['template'];

		// Get the database object and a new query object.
		$db = App::get('db');

		// Build the query.
		$query = $db->getQuery()
			->select('s.id')
			->select('s.title')
			->select('e.name', 'name')
			->select('s.template')
			->from('#__template_styles', 's')
			->whereEquals('s.client_id', (int) $client->id)
			->order('template', 'asc')
			->order('title', 'asc');
		if ($template)
		{
			$query->whereEquals('s.template', $template);
		}
		$query
			->join('#__extensions as e', 'e.element', 's.template', 'left')
			->whereEquals('e.enabled', '1')
			->whereEquals('e.type', 'template');

		// Set the query and load the styles.
		$db->setQuery($query->toString());
		$styles = $db->loadObjectList();

		// Build the grouped list array.
		if ($styles)
		{
			foreach ($styles as $style)
			{
				$template = $style->template;
					$lang->load('tpl_' . $template . '.sys', PATH_APP . '/templates/' . $template, null, false, true)
				||	$lang->load('tpl_' . $template . '.sys', PATH_CORE . '/templates/' . $template, null, false, true);
				$name = $lang->txt($style->name);

				// Initialize the group if necessary.
				if (!isset($groups[$name]))
				{
					$groups[$name] = array();
				}

				$groups[$name][] = Dropdown::option($style->id, $style->title);
			}
		}

		// Merge any additional groups in the XML definition.
		$groups = array_merge(parent::getGroups(), $groups);

		return $groups;
	}
}
