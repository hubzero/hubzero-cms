<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * HUBzero plugin class for system overview
 */
class plgHubzeroSystemplate extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return information about this hub
	 *
	 * @param   string  $values
	 * @return  array
	 */
	public function onSystemOverview($values = 'all')
	{
		if ($values != 'all')
		{
			return;
		}

		$response = new stdClass;
		$response->name  = 'template';
		$response->label = 'Template';
		$response->data  = array();

		$tmpl = 'system';

		// Get the active site template
		$db = App::get('db');
		$query = $db->getQuery()
			->select('s.id')
			->select('s.home')
			->select('s.template')
			->select('s.params')
			->select('e.protected')
			->from('#__template_styles', 's')
			->whereEquals('s.client_id', '0')
			->whereEquals('e.enabled', '1')
			->joinRaw('#__extensions as e', 'e.element=s.template AND e.type=' . $db->quote('template') . ' AND e.client_id=s.client_id', 'left');

		$path = PATH_APP;

		$db->setQuery($query->toString());
		$templates = $db->loadObjectList('id');
		foreach ($templates as $template)
		{
			if ($template->home == 1)
			{
				if ($template->protected)
				{
					$path = PATH_CORE;
				}
				$tmpl = $template->template;
			}
		}

		$response->data['site'] = $this->_obj('Name', $tmpl);

		$overrides = array();
		$path .= '/templates/' . $tmpl . '/html';

		if (is_dir($path))
		{
			$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
			foreach ($objects as $name => $file)
			{
				if ($file->isDir())
				{
					continue;
				}

				$overrides[] = str_replace(PATH_CORE . '/templates/' . $tmpl . '/html', '', $name);
			}
		}

		$response->data['overrides'] = $this->_obj('Overrides', $overrides);

		return $response;
	}

	/**
	 * Assign label and data to an object
	 *
	 * @param   string $label
	 * @param   mixed  $value
	 * @return  object
	 */
	private function _obj($label, $value)
	{
		$obj = new stdClass;
		$obj->label = $label;
		$obj->value = $value;

		return $obj;
	}
}
