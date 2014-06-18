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

namespace Plugins\Content\Formathtml\Macros\Group;

require_once JPATH_ROOT.'/plugins/content/formathtml/macros/group.php';

use Plugins\Content\Formathtml\Macros\GroupMacro;

/**
 * Group events Macro
 */
class Resources extends GroupMacro
{
	/**
	 * Allow macro in partial parsing?
	 *
	 * @var string
	 */
	public $allowPartial = true;

	/**
	 * Returns description of macro, use, and accepted arguments
	 *
	 * @return     array
	 */
	public function description()
	{
		$txt = array();
		$txt['html']  = '<p>Displays group resources.</p>';
		$txt['html'] .= '<p>Examples:</p>
							<ul>
								<li><code>[[Group.Resources()]]</code></li>
								<li><code>[[Group.Resources(3)]]</code> - Displays the 3 latest group resources</li>
								<li><code>[[Group.Resources(type=teachingmaterials, 5)]]</code> - Displays the 5 latest group teachingmaterials resources.</li>
							</ul>';
		return $txt['html'];
	}

	/**
	 * Generate macro output
	 *
	 * @return     string
	 */
	public function render()
	{
		// check if we can render
		if (!parent::canRender())
		{
			return \JText::_('[This macro is designed for Groups only]');
		}

		// get args
		$args = $this->getArgs();

		// get details
		$type  = $this->_getType($args, 'all');
		$limit = $this->_getLimit($args, 5);
		$class = $this->_getClass($args);

		//get resources
		$groupResources = $this->_getResources($type, $limit);

		$html = '<div class="resources ' . $class . '">';

		foreach ($groupResources as $resource)
		{
			$area = strtolower(preg_replace("/[^a-zA-Z0-9]/", '', $resource->area));
			$resourceLink     = \JRoute::_('index.php?option=com_resources&id=' . $resource->id);
			$resourceTypeLink = \JRoute::_('index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=resources&area=' . $area);

			$html .= '<a href="' . $resourceLink . '"><strong>' . $resource->title . '</strong></a>';
			$html .= '<p class="category"> in: <a href="' . $resourceTypeLink . '">' . $resource->area . '</a></p>';
			$html .= '<p>' . \Hubzero\Utility\String::truncate($resource->itext) . '</p>';
		}

		$html .= '</div>';

		return $html;
	}

	/**
	 * [_getResources description]
	 * @param  string  $type  [description]
	 * @param  integer $limit [description]
	 * @return [type]         [description]
	 */
	private function _getResources($type = 'all', $limit = 5)
	{
		// database object
		$database = \JFactory::getDBO();

		// Instantiate some needed objects
		$rr = new \ResourcesResource($database);

		// Build query
		$filters = array();
		$filters['now'] = date('Y-m-d H:i:s', time() + 0 * 60 * 60);
		$filters['sortby'] = 'date';
		$filters['group'] = $this->group->get('cn');
		$filters['access'] = 'all';
		$filters['authorized'] = '';
		$filters['select'] = 'records';
		$filters['limit'] = $limit;
		$filters['limitstart'] = 0;

		// Get categories
		$rt = new \ResourcesType($database);
		$categories = $rt->getMajorTypes();

		// Normalize the category names
		// e.g., "Oneline Presentations" -> "onlinepresentations"
		$cats = array();
		for ($i = 0; $i < count($categories); $i++)
		{
			$normalized = preg_replace("/[^a-zA-Z0-9]/", '', $categories[$i]->type);
			$normalized = strtolower($normalized);

			$cats[$normalized] = array();
			$cats[$normalized]['id'] = $categories[$i]->id;
		}

		// do we have a type?
		if (in_array($type, array_keys($cats)))
		{
			$filters['type'] = $cats[$type]['id'];
		}

		// Get results
		$database->setQuery($rr->buildPluginQuery($filters));
		$rows = $database->loadObjectList();

		return $rows;
	}

	/**
	 * Get item limit
	 *
	 * @param  array  $args  Macro Arguments
	 * @return mixed
	 */
	private function _getLimit( &$args, $default = 5 )
	{
		foreach ($args as $k => $arg)
		{
			if (is_numeric($arg) && $arg > 0 && $arg < 50)
			{
				$limit = $arg;
				unset($args[$k]);
				return $limit;
			}
		}

		// if we didnt find one return default
		return $default;
	}

	/**
	 * Get class
	 *
	 * @param  array  $args  Macro Arguments
	 * @return mixed
	 */
	private function _getClass( &$args )
	{
		foreach ($args as $k => $arg)
		{
			if (preg_match('/class=([\w-]*)/', $arg, $matches))
			{
				$class = (isset($matches[1])) ? $matches[1] : '';
				unset($args[$k]);
				return $class;
			}
		}
	}

	/**
	 * Get type
	 *
	 * @param  array  $args  Macro Arguments
	 * @return mixed
	 */
	private function _getType( &$args )
	{
		foreach ($args as $k => $arg)
		{
			if (preg_match('/type=([\w]*)/', $arg, $matches))
			{
				$type = (isset($matches[1])) ? $matches[1] : '';
				unset($args[$k]);
				return $type;
			}
		}
	}
}