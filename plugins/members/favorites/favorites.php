<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Members Plugin class for favorites resources
 */
class plgMembersFavorites extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage('', JPATH_ROOT);
	}

	/**
	 * Event call to determine if this plugin should return data
	 * 
	 * @param      object  $user   JUser
	 * @param      object  $member MembersProfile
	 * @return     array   Plugin name
	 */
	public function &onMembersAreas($user, $member)
	{
		$areas['favorites'] = JText::_('PLG_MEMBERS_FAVORITES');
		return $areas;
	}

	/**
	 * Event call to return data for a specific member
	 * 
	 * @param      object  $user   JUser
	 * @param      object  $member MembersProfile
	 * @param      string  $option Component name
	 * @param      string  $areas  Plugins to return data
	 * @return     array   Return array of html
	 */
	public function onMembers($user, $member, $option, $areas)
	{
		$returnhtml = true;

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas)) 
		{
			if (!array_intersect($areas, $this->onMembersAreas($user, $member)) 
			 && !array_intersect($areas, array_keys($this->onMembersAreas($user, $member)))) 
			{
				$returnhtml = false;
			}
		}

		$database =& JFactory::getDBO();
		$dispatcher =& JDispatcher::getInstance();

		// Incoming paging vars
		$limit = JRequest::getInt('limit', 25);
		$limitstart = JRequest::getInt('limitstart', 0);

		// Trigger the functions that return the areas we'll be using
		$areas = array();
		$searchareas = $dispatcher->trigger('onMembersFavoritesAreas', array());
		foreach ($searchareas as $area) 
		{
			$areas = array_merge($areas, $area);
		}

		// Get the active category
		$area = JRequest::getVar('area', '');
		if ($area) 
		{
			$activeareas = array($area);
		} 
		else 
		{
			$limit = 5;
			$activeareas = $areas;
		}

		// If we're just returning metadata, we set the limitstart to -1 to use as a flag
		// This allows us to reduce the overall number of queries
		if (!$returnhtml) 
		{
			$limitstart = -1;
		}

		// Get the search result totals
		$totals = $dispatcher->trigger('onMembersFavorites', array(
				$member,
				$option,
				0,
				$limitstart,
				$activeareas
			)
		);

		// Get the total results found (sum of all categories)
		$i = 0;
		$total = 0;
		$cats = array();
		foreach ($areas as $c=>$t) 
		{
			$cats[$i]['category'] = $c;

			// Do sub-categories exist?
			if (is_array($t) && !empty($t)) 
			{
				// They do - do some processing
				$cats[$i]['title'] = ucfirst($c);
				$cats[$i]['total'] = 0;
				$cats[$i]['_sub']  = array();
				$z = 0;
				// Loop through each sub-category
				foreach ($t as $s=>$st) 
				{
					// Ensure a matching array of totals exist
					if (is_array($totals[$i]) && !empty($totals[$i]) && isset($totals[$i][$z])) 
					{
						// Add to the parent category's total
						$cats[$i]['total'] = $cats[$i]['total'] + $totals[$i][$z];
						// Get some info for each sub-category
						$cats[$i]['_sub'][$z]['category'] = $s;
						$cats[$i]['_sub'][$z]['title']    = $st;
						$cats[$i]['_sub'][$z]['total']    = $totals[$i][$z];
					}
					$z++;
				}
			} 
			else 
			{
				// No sub-categories - this should be easy
				$cats[$i]['title'] = $t;
				$cats[$i]['total'] = (!is_array($totals[$i])) ? $totals[$i] : 0;
			}

			// Add to the overall total
			$total = $total + intval($cats[$i]['total']);
			$i++;
		}

		$arr = array(
			'html' => '',
			'metadata' => ''
		);

		// Build the HTML
		if ($returnhtml) 
		{
			$limit = ($limit == 0) ? 'all' : $limit;

			// Get the search results
			$results = $dispatcher->trigger('onMembersFavorites', array(
					$member,
					$option,
					$limit,
					$limitstart,
					$activeareas
				)
			);

			// Do we have an active area?
			if (count($activeareas) == 1 && !is_array(current($activeareas))) 
			{
				$active = current($activeareas);
				//$active = $activeareas[0];
			} 
			else 
			{
				$active = '';
			}

			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('members', 'favorites');

			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'members',
					'element' => 'favorites',
					'name'    => 'display'
				)
			);
			//$view->authorized = $authorized;
			$view->totals  = $totals;
			$view->results = $results;
			$view->cats    = $cats;
			$view->active  = $active;
			$view->option  = $option;
			$view->start   = $limitstart;
			$view->limit   = $limit;
			$view->total   = $total;
			$view->member  = $member;
			if ($this->getError()) 
			{
				foreach ($this->getErrors() as $error)
				{
					$view->setError($error);
				}
			}

			$arr['html'] = $view->loadTemplate();
		}

		//return meta data
		$arr['metadata'] = array();
		$total = 0;

		foreach ($cats as $cat) 
		{
			$total += $cat['total'];
		}

		$prefix = ($user->get('id') == $member->get("uidNumber")) ? 'I have' : $member->get("name") . ' has';
		$title = $prefix . " {$total} favorites.";

		$arr['metadata']['count'] = $total;

		/*
		// Loop through each category
		foreach ($cats as $cat) 
		{
			$total += $cat['total'];
		}

		if($total > 0) {
			$title = $prefix . " {$total} favorites.";
			$text = "<span title=\"{$title}\">{$total}</span>";
			$arr['metadata'] = "<span class=\"meta\">{$text}</span>";	
		}
		*/

		return $arr;
	}
}
