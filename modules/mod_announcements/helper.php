<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Module class for displaying announcements
 */
class modAnnouncementsHelper
{
	/**
	 * Container for properties
	 * 
	 * @var array
	 */
	private $attributes = array();

	/**
	 * Constructor
	 * 
	 * @param      object $params JParameter
	 * @param      object $module Database row
	 * @return     void
	 */
	public function __construct($params, $module)
	{
		$this->params = $params;
		$this->module = $module;
	}

	/**
	 * Set a property
	 * 
	 * @param      string $property Name of property to set
	 * @param      mixed  $value    Value to set property to
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}

	/**
	 * Get a property
	 * 
	 * @param      string $property Name of property to retrieve
	 * @return     mixed
	 */
	public function __get($property)
	{
		if (isset($this->attributes[$property])) 
		{
			return $this->attributes[$property];
		}
	}

	/**
	 * Get a list of content pages
	 * 
	 * @return     void
	 */
	private function _getList()
	{
		$db =& JFactory::getDBO();
	
		$catid   = (int) $this->params->get('catid', 0);
		$orderby = 'a.publish_up DESC';
		$limit   = (int) $this->params->get('numitems', 0);
		$limitby = $limit ? ' LIMIT 0,' . $limit : '';

		$date =& JFactory::getDate();
		$now = $date->toMySQL();

		$nullDate = $db->getNullDate();

		// query to determine article count
		$query = 'SELECT a.*, s.alias as secname, cc.alias as catname, ' .
			' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
			' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
			' FROM #__content AS a' .
			' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
			' INNER JOIN #__sections AS s ON s.id = a.sectionid' .
			' WHERE a.state = 1 ' .
			' AND (a.publish_up = ' . $db->Quote($nullDate) . ' OR a.publish_up <= ' . $db->Quote($now) . ' ) ' .
			' AND (a.publish_down = ' . $db->Quote($nullDate) . ' OR a.publish_down >= ' . $db->Quote($now) . ' )' .
			' AND cc.id = '. (int) $catid .
			' AND cc.section = s.id' .
			' AND cc.published = 1' .
			' AND s.published = 1' .
			' ORDER BY ' . $orderby . ' ' . $limitby;
		
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$query = 'SELECT a.*, cc.alias as catname, cc.path as catpath, ' .
				' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
				' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
				' FROM #__content AS a' .
				' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
				' WHERE a.state = 1 ' .
				' AND (a.publish_up = ' . $db->Quote($nullDate) . ' OR a.publish_up <= ' . $db->Quote($now) . ' ) ' .
				' AND (a.publish_down = ' . $db->Quote($nullDate) . ' OR a.publish_down >= ' . $db->Quote($now) . ' )' .
				' AND cc.id = '. (int) $catid .
				' AND cc.published = 1' .
				' ORDER BY ' . $orderby . ' ' . $limitby;
		}
		
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}

	/**
	 * Display module content
	 * 
	 * @return     void
	 */
	public function display() 
	{
		//check if cache diretory is writable as cache files will be created for the announcements
		if ($this->params->get('cache', 1) && !is_writable(JPATH_BASE . DS . 'cache'))
		{
			echo '<p class="warning">' . JText::_('Please make cache directory writable.') . '</p>';
			return;
		}

		//check if category has been set
		if (!intval($this->params->get('catid', 0)))
		{
			echo '<p class="warning">' . JText::_('No category specified.') . '</p>';
			return;
		}

		// Push some CSS to the template
		ximport('Hubzero_Document');
		Hubzero_Document::addModuleStylesheet($this->module->module);

		$this->content = $this->_getList();	
		$this->cid = (int) $this->params->get('catid', 0);	
		$this->container = $this->params->get('container', 'block-announcements');

		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}
