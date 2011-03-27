<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
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
defined('_JEXEC') or die( 'Restricted access' );

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_members_points' );

//-----------

class plgMembersPoints extends JPlugin
{
	public function plgMembersPoints(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'members', 'points' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onMembersAreas( $authorized )
	{
		if (!$authorized) {
			$areas = array();
		} else {
			$areas = array(
				'points' => JText::_('PLG_MEMBERS_POINTS')
			);
		}

		return $areas;
	}

	//-----------

	public function onMembers( $member, $option, $authorized, $areas )
	{
		$returnhtml = true;
		$returnmeta = true;

		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (!array_intersect( $areas, $this->onMembersAreas( $authorized ) ) 
			&& !array_intersect( $areas, array_keys( $this->onMembersAreas( $authorized ) ) )) {
				$returnhtml = false;
			}
		}
		
		if (!$authorized) {
			$returnhtml = false;
			$returnmeta = false;
		}
		
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);
		
		$database =& JFactory::getDBO();
		$tables = $database->getTableList();
		$table = $database->_table_prefix.'users_points';
		
		if (!in_array($table,$tables)) {
			ximport('Hubzero_View_Helper_Html');
			$arr['html'] = Hubzero_View_Helper_Html::error( JText::_('PLG_MEMBERS_POINTS_ERROR_MISSING_TABLE') );
			return $arr;
		}

		ximport('Hubzero_Bank');

		$BTL = new Hubzero_Bank_Teller( $database, $member->get('uidNumber') );

		// Build the final HTML
		if ($returnhtml) {
			ximport('Hubzero_Document');
			Hubzero_Document::addPluginStylesheet('members', 'points');
			
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'=>'members',
					'element'=>'points',
					'name'=>'history'
				)
			);

			$view->sum = $BTL->summary();

			$view->credit = $BTL->credit_summary();
			$funds = $view->sum - $view->credit;

			$view->funds = ($funds > 0) ? $funds : 0;
			$view->hist = $BTL->history(0);
			if ($this->getError()) {
				$view->setError( $this->getError() );
			}
			
			$arr['html'] = $view->loadTemplate();
		}
		
		// Build the HTML meant for the "about" tab's metadata overview
		if ($returnmeta) {
			$arr['metadata'] = '<p class="points"><a href="'.JRoute::_('index.php?option='.$option.'&id='.$member->get('uidNumber').'&active=points').'">'.JText::sprintf('PLG_MEMBERS_POINTS_NUMBER_POINTS',$BTL->summary()).'</a></p>'."\n";
		}

		return $arr;
	}
}
