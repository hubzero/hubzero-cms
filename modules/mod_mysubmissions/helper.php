<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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
 * Short description for 'modMySubmissions'
 * 
 * Long description (if any) ...
 */
class modMySubmissions
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

	//----------------------------------------------------------
	// Checks
	//----------------------------------------------------------

	/**
	 * Short description for 'step_type_check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $id Parameter description (if any) ...
	 * @return     void
	 */
	public function step_type_check( $id )
	{
		// do nothing
	}

	/**
	 * Short description for 'step_compose_check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $id Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function step_compose_check( $id )
	{
		return $id;
	}

	/**
	 * Short description for 'step_attach_check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $id Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
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

	/**
	 * Short description for 'step_authors_check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $id Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
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

	/**
	 * Short description for 'step_tags_check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $id Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
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

	/**
	 * Short description for 'step_review_check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $id Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function step_review_check( $id )
	{
		return 0;
	}

	/**
	 * Short description for 'display'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function display()
	{
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}

		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'tables'.DS.'resource.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'tables'.DS.'type.php' );

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
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'tables'.DS.'assoc.php');
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'tables'.DS.'contributor.php');
			include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'helpers'.DS.'tags.php' );
		}
	}
}

