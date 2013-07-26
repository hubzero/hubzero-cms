<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Members Plugin class for points
 */
class plgMembersPoints extends JPlugin
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

		$this->loadLanguage();
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
		//default areas returned to nothing
		$areas = array();

		//if this is the logged in user show them
		if ($user->get('id') == $member->get('uidNumber'))
		{
			$areas['points'] = JText::_('PLG_MEMBERS_POINTS');
		}

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
		$returnmeta = true;

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas)) 
		{
			if (!array_intersect($areas, $this->onMembersAreas($user, $member))
			 && !array_intersect($areas, array_keys($this->onMembersAreas($user, $member)))) 
			{
				$returnhtml = false;
			}
		}

		//if (!$authorized) {
		//	$returnhtml = false;
		//	$returnmeta = false;
		//}

		$arr = array(
			'html' => '',
			'metadata' => ''
		);

		$database =& JFactory::getDBO();
		$tables = $database->getTableList();
		$table = $database->getPrefix() . 'users_points';

		if (!in_array($table,$tables)) 
		{
			ximport('Hubzero_View_Helper_Html');
			$arr['html'] = Hubzero_View_Helper_Html::error(JText::_('PLG_MEMBERS_POINTS_ERROR_MISSING_TABLE'));
			return $arr;
		}

		ximport('Hubzero_Bank');

		$BTL = new Hubzero_Bank_Teller($database, $member->get('uidNumber'));

		// Build the final HTML
		if ($returnhtml) 
		{
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('members', 'points');

			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'members',
					'element' => 'points',
					'name'    => 'history'
				)
			);

			$view->sum = $BTL->summary();

			$view->credit = $BTL->credit_summary();
			$funds = $view->sum - $view->credit;

			$view->funds = ($funds > 0) ? $funds : 0;
			$view->hist = $BTL->history(0);
			if ($this->getError()) 
			{
				$view->setError($this->getError());
			}

			$arr['html'] = $view->loadTemplate();
		}

		// Build the HTML meant for the "about" tab's metadata overview
		if ($returnmeta) 
		{
			$arr['metadata'] = array();

			$points = $BTL->summary();

			$prefix = ($user->get('id') == $member->get('uidNumber')) ? 'I have' : $member->get('name') . ' has';
			$title = $prefix . ' ' . $points . ' points.';

			$arr['metadata']['count'] = $points;
		}

		return $arr;
	}
}
