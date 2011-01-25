<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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
defined('_JEXEC') or die( 'Restricted access' );

class modMyResources
{
	private $attributes = array();

	//-----------

	public function __construct( $params ) 
	{
		$this->params = $params;
	}

	//-----------

	public function __set($property, $value)
	{
		$this->attributes[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->attributes[$property])) {
			return $this->attributes[$property];
		}
	}
	
	//-----------
	
	public function display()
	{
		$no_html = JRequest::getInt( 'no_html', 0 );
		if (!$no_html) {
			// Push the module CSS to the template
			ximport('Hubzero_Document');
			Hubzero_Document::addModuleStyleSheet('mod_myresources');

			// Add the JavaScript that does the AJAX magic to the template
			$document =& JFactory::getDocument();
			$document->addScript('/modules/mod_myresources/mod_myresources.js');
		}
		
		$database =& JFactory::getDBO();
		$juser =& JFactory::getUser();

		$this->limit = intval( $this->params->get( 'limit' ) );
		$this->limit = $this->limit ? $this->limit : 5;

		$this->sort = $this->params->get( 'sort' );
		$this->sort = ($this->sort) ? $this->sort : 'publish_up';
		
		// Get "published" contributions
		$query  = "SELECT DISTINCT R.id, R.title, R.type, R.logical_type AS logicaltype, 
							AA.subtable, R.created, R.created_by, R.modified, R.published, R.publish_up, R.standalone, 
							R.rating, R.times_rated, R.alias, R.ranking, rt.type AS typetitle, R.params ";
		if ($this->sort == 'usage') {
			$query .= ", (SELECT rs.users FROM #__resource_stats AS rs WHERE rs.resid=R.id AND rs.period=14 ORDER BY rs.datetime DESC LIMIT 1) AS users ";
		}
		$query .= "FROM #__author_assoc AS AA, #__resource_types AS rt, #__resources AS R ";
		//$query .= "LEFT JOIN #__resource_types AS t ON R.logical_type=t.id ";
		$query .= "WHERE AA.authorid = ". $juser->get('id') ." ";
		$query .= "AND R.id = AA.subid ";
		$query .= "AND AA.subtable = 'resources' ";
		$query .= "AND R.standalone=1 AND R.type=rt.id AND R.published=1 ";
		$query .= "ORDER BY ";

		switch ($this->sort) 
		{
			case 'usage':
				$query .= "users DESC";
			break;
			case 'title':
				$query .= "title ASC, publish_up DESC";
			break;
			case 'publish_up':
			default:
				$query .= "publish_up DESC, title ASC";
			break;
		}
		if ($this->limit > 0 && $this->limit != 'all') {
			$query .= " LIMIT ".$this->limit;
		}

		$database->setQuery($query);
		
		$this->contributions = $database->loadObjectList();
	}
}
