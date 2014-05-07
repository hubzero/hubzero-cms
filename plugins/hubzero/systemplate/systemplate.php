<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * HUBzero plugin class for system overview
 */
class plgHubzeroSystemplate extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return information about this hub
	 * 
	 * @return  array
	 */
	public function onSystemOverview()
	{
		$response = new stdClass;
		$response->name  = 'template';
		$response->label = 'Template';
		$response->data  = array();

		$tmpl = 'system';

		// Get the active site template
		$db = JFactory::getDbo();

		// Site
		$query = $db->getQuery(true);
		$query->select('id, home, template, s.params');
		$query->from('#__template_styles as s');
		$query->where('s.client_id = 0');
		$query->where('e.enabled = 1');
		$query->leftJoin('#__extensions as e ON e.element=s.template AND e.type='.$db->quote('template').' AND e.client_id=s.client_id');

		$db->setQuery($query);
		$templates = $db->loadObjectList('id');
		foreach ($templates as $template) 
		{
			if ($template->home == 1)
			{
				$tmpl = $template->template;
			}
		}

		$response->data['site'] = $this->_obj('Name', $tmpl);

		$overrides = array();
		$path = JPATH_ROOT . '/templates/' . $tmpl . '/html';

		if (is_dir($path))
		{
			$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
			foreach ($objects as $name => $file)
			{
				if ($file->isDir())
				{
					continue;
				}

				$overrides[] = str_replace(JPATH_ROOT . '/templates/' . $tmpl . '/html', '', $name);
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
