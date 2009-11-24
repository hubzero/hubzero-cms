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

class modMySubmissions
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

	//----------------------------------------------------------
	// Checks
	//----------------------------------------------------------
	
	public function step_type_check( $id )
	{
		// do nothing
	}
	
	//-----------
	
	public function step_compose_check( $id )
	{
		return $id;
	}

	//-----------
	
	public function step_attach_check( $id )
	{
		if ($id) {
			$database =& JFactory::getDBO();
			$ra = new ResourcesAssoc( $database );
			$total = $ra->getCount( $id );
		} else {
			$total = 0;
		}
		return $total;
	}

	//-----------
	
	public function step_authors_check( $id )
	{
		if ($id) {
			$database =& JFactory::getDBO();
			$rc = new ResourcesContributor( $database );
			$contributors = $rc->getCount( $id, 'resources' );
		} else {
			$contributors = 0;
		}

		return $contributors;
	}
	
	//-----------
	
	public function step_tags_check( $id )
	{
		$database =& JFactory::getDBO();

		$rt = new ResourcesTags( $database );
		$tags = $rt->getTags( $id );

		if (count($tags) > 0) {
			return 1;
		} else {
			return 0;
		}
	}
	
	//-----------

	public function step_review_check( $id ) 
	{
		return 0;
	}

	//-----------

	public function display()
	{
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}
		
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.resource.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.type.php' );

		$this->steps = array('Type','Compose','Attach','Authors','Tags','Review');
		
		$database =& JFactory::getDBO();
		
		$rr = new ResourcesResource( $database );
		$rt = new ResourcesType( $database );
		
		$query = "SELECT r.*, t.type AS typetitle 
			FROM ".$rr->getTableName()." AS r 
			LEFT JOIN ".$rt->getTableName()." AS t ON r.type=t.id 
			WHERE r.published=2 AND r.standalone=1 AND r.type!=7 AND r.created_by=".$juser->get('id');
	    $database->setQuery( $query );
	    $this->rows = $database->loadObjectList();
	
		if (!empty($this->rows)) {
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.assoc.php');
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.contributor.php');
			include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'resources.tags.php' );
		}
	}
}

//-------------------------------------------------------------

$modmysubmissions = new modMySubmissions( $params );
$modmysubmissions->display();

require( JModuleHelper::getLayoutPath('mod_mysubmissions') );
