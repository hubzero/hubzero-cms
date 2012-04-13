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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'modSlidingPanes'
 * 
 * Long description (if any) ...
 */
class modSlidingPanes
{

	/**
	 * Description for 'attributes'
	 * 
	 * @var array
	 */
	private $attributes = array();

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $params Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( $params )
	{
		$this->params = $params;
	}

	/**
	 * Short description for '__set'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}

	/**
	 * Short description for '__get'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function __get($property)
	{
		if (isset($this->attributes[$property])) {
			return $this->attributes[$property];
		}
	}

	/**
	 * Short description for '_getList'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	private function _getList()
	{
		//global $mainframe;

		$db =& JFactory::getDBO();
		//$user =& JFactory::getUser();
		//$aid = $user->get('aid', 0);

		$catid 	 = (int) $this->params->get('catid', 0);
		$random  = $this->params->get('random', 0);
		$orderby = $random ? 'RAND()' : 'a.ordering';
		$limit   = (int) $this->params->get('limitslides', 0);
		$limitby = $limit ? ' LIMIT 0,'.$limit : '';

		//$contentConfig =& JComponentHelper::getParams( 'com_content' );
		//$noauth	= !$contentConfig->get('shownoauth');

		$date =& JFactory::getDate();
		$now = $date->toMySQL();

		$nullDate = $db->getNullDate();

		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			// query to determine article count
			$query = 'SELECT a.*,' .
				' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
				' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
				' FROM #__content AS a' .
				' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
				' INNER JOIN #__sections AS s ON s.id = a.sectionid' .
				' WHERE a.state = 1 ' .
				//($noauth ? ' AND a.access <= ' .(int) $aid. ' AND cc.access <= ' .(int) $aid. ' AND s.access <= ' .(int) $aid : '').
				' AND (a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' ) ' .
				' AND (a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )' .
				' AND cc.id = '. (int) $catid .
				' AND cc.section = s.id' .
				' AND cc.published = 1' .
				' AND s.published = 1' .
				' ORDER BY '.$orderby.' '.$limitby;
		}
		else 
		{
			// query to determine article count
			$query = 'SELECT a.* FROM #__content AS a' .
				' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
				' WHERE a.state = 1 ' .
				' AND (a.publish_up = '.$db->Quote($nullDate).' OR a.publish_up <= '.$db->Quote($now).' ) ' .
				' AND (a.publish_down = '.$db->Quote($nullDate).' OR a.publish_down >= '.$db->Quote($now).' )' .
				' AND cc.id = '. (int) $catid .
				' AND cc.published = 1' .
				' ORDER BY '.$orderby.' '.$limitby;
		}
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}

	/**
	 * Short description for 'display'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function display()
	{
		$jdocument =& JFactory::getDocument();

		$type = $this->params->get('animation', 'slide');
		
		// Check if we have multiple instances of the module running
		// If so, we only want to push the CSS and JS to the template once
		if (!$this->multiple_instances) {
			// Push some CSS to the template
			ximport('Hubzero_Document');
			Hubzero_Document::addModuleStylesheet('mod_sliding_panes', $type.'.css');
			Hubzero_Document::addModuleScript('mod_sliding_panes');

			// Push some javascript to the template
			/*if (is_file(JPATH_ROOT.'/modules/mod_sliding_panes/mod_sliding_panes.js')) {
				$jdocument->addScript('/modules/mod_sliding_panes/mod_sliding_panes.js');
			}*/
		}

		$id = rand();

		$this->content = $this->_getList();

		$this->container = $this->params->get('container', 'pane-sliders');

		if (JPluginHelper::isEnabled('system', 'jquery'))
		{
			$js = "$(document).ready(function(){ $('#".$this->container." .panes-content').jSlidingPanes(); });";
		}
		else 
		{
			$js = "window.addEvent('domready', function(){
				if ($('".$this->container."')) {
					myTabs".$id." = new ModSlidingPanes('".$this->container."', ".$this->params->get('rotate', 1).");

					// this sets it up to work even if it's width isn't a set amount of pixels
					window.addEvent('resize', myTabs".$id.".recalcWidths.bind(myTabs".$id."));
				}
			});";
		}

		$jdocument->addScriptDeclaration($js);
	}
}

