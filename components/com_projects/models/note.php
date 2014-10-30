<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'models' . DS . 'book.php');
include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'helpers' . DS . 'editor.php');
include_once(JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'helpers' . DS . 'parser.php');

/**
 * Project Note model
 */
class ProjectModelNote extends WikiModelBook
{
	/**
	 * Project group cn
	 *
	 * @var string
	 */
	private $_group_cn = '';

	/**
	 * Project id
	 *
	 * @var string
	 */
	private $_project_id = '';

	/**
	 * Constructor
	 *
	 * @param   string $scope
	 * @return  void
	 */
	public function __construct($scope = '__site__', $group_cn = '', $project_id = 0)
	{
		$this->_db = JFactory::getDBO();
		$this->_scope = $scope;
		$this->_tbl = new WikiTablePage($this->_db);
		$this->_group_cn = $group_cn;
		$this->_project_id = $project_id;

		parent::__construct($scope);
	}

	/**
	 * Set and get a specific page
	 *
	 * @param   mixed  $id Integer or string of tag to look up
	 * @return  object WikiModelPage
	 */
	public function page($id=null, $scope = '')
	{
		$scope = $scope ? $scope : $this->_scope;
		$this->_cache['page'] = WikiModelPage::getInstance($id, $scope);

		return $this->_cache['page'];
	}

	/**
	 * Get public stamp for note
	 *
	 * @return    object
	 */
	public function getPublicStamp( $id = 0, $register = false, $listed = NULL )
	{
		if (!is_file(JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
			.'com_projects' . DS . 'tables' . DS . 'project.public.stamp.php') )
		{
			return false;
		}

		$page = $this->page($id);

		if (!$page)
		{
			return false;
		}

		require_once( JPATH_ROOT . DS . 'administrator' . DS . 'components'.DS
			.'com_projects' . DS . 'tables' . DS . 'project.public.stamp.php');

		$objSt = new ProjectPubStamp( $this->_db );

		// Build reference for latest revision of page
		$reference = array(
			'pageid'   => $page->get('id'),
			'pagename' => $page->get('pagename'),
			'revision' => NULL
		);

		// Check valid stamp
		$objSt->checkStamp($this->_project_id, json_encode($reference), 'notes');
		$list = ($listed !== NULL && $listed != $objSt->listed) ? true : false;

		if ($list == true)
		{
			return 	$objSt->registerStamp($this->_project_id, json_encode($reference), 'notes', $listed);
		}

		// Register new stamp?
		if ((!$objSt->id && $register == true))
		{
			$objSt->registerStamp($this->_project_id, json_encode($reference), 'notes', $listed);
			$objSt->checkStamp($this->_project_id, json_encode($reference), 'notes');
		}

		return $objSt;
	}

	/**
	 * Get default project note
	 *
	 * @param      string $group cn of project group
	 * @param      string $masterscope
	 * @param      string $prefix
	 * @return     void
	 */
	public function getFirstNote( $prefix = '' )
	{
		$query = "SELECT p.pagename FROM #__wiki_page AS p
				  WHERE p.group_cn='" . $this->_group_cn . "' AND p.state!=2
				  AND p.scope='" . $this->_scope . "'";
		$query.= $prefix ? "AND p.pagename LIKE '" . $prefix . "%'" : "";
		$query.= " ORDER BY p.times_rated, p.id ASC LIMIT 1";

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get a list of parent project notes
	 *
	 * @param      string $scope
	 * @return     void
	 */
	public function getParentNotes( $scope = '' )
	{
		$scope = $scope ? $scope : $this->_scope;
		$parts = explode ( '/', $scope );
		$remaining = array_slice($parts, 3);
		if ($remaining)
		{
			$query = "SELECT DISTINCT p.pagename, p.title, p.scope ";
			$query.= "FROM #__wiki_page AS p ";
			$query.= "WHERE p.group_cn='" . $this->_group_cn . "' AND p.state!=2 ";
			$k = 1;
			$where = '';
			foreach ($remaining as $r)
			{
				$where .= "p.pagename='" . trim($r) . "'";
				$where .= $k == count($remaining) ? '' : ' OR ';
				$k++;
			}
			$query.= "AND (".$where.")" ;
			$this->_db->setQuery($query);
			return $this->_db->loadObjectList();
		}
		else
		{
			return array();
		}
	}

	/**
	 * Get a list of project notes
	 *
	 * @param      int $limit
	 * @param      string $orderby
	 * @return     object list
	 */
	public function getNotes( $limit = 0, $orderby = 'p.scope, p.times_rated ASC, p.id' )
	{
		$query = "SELECT DISTINCT p.id, p.pagename, p.title, p.scope, p.times_rated
		          FROM #__wiki_page AS p
				  WHERE p.group_cn='" . $this->_group_cn . "'
				  AND p.scope LIKE '" . $this->_scope . "%'
				  AND p.pagename NOT LIKE 'Template:%'
				  AND p.state!=2
				  ORDER BY $orderby ";
		$query.= intval($limit) ? " LIMIT $limit" : '';

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get count of project notes
	 *
	 * @return     void
	 */
	public function getNoteCount()
	{
		$query = "SELECT COUNT(*) FROM #__wiki_page AS p
				  WHERE p.group_cn='" . $this->_group_cn . "' AND p.state!=2
				  AND p.pagename NOT LIKE 'Template:%'";
		$query.= $this->_scope ? " AND p.scope LIKE '" . $this->_scope . "%'" : "";

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get project note
	 *
	 * @param      string $id
	 * @return     void
	 */
	public function getSelectedNote( $id = '' )
	{
		$query = "SELECT DISTINCT p.id, p.pagename, p.title, p.scope, p.times_rated,
	 		  	  (SELECT v.version FROM #__wiki_version as v WHERE v.pageid=p.id
				  ORDER by v.version DESC LIMIT 1) as version,
				  (SELECT vv.id FROM #__wiki_version as vv WHERE vv.pageid=p.id
				  ORDER by vv.id DESC LIMIT 1) as instance
			      FROM #__wiki_page AS p
				  WHERE p.group_cn='" . $this->_group_sn . "'
				  AND p.scope LIKE '" . $this->_scope . "%'
				  AND p.state!=2
				  AND p.pagename NOT LIKE 'Template:%'";
		$query.=  is_numeric($id) ? " AND p.id='$id' LIMIT 1" : " AND p.pagename='$id' LIMIT 1";

		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();

		return $result ? $result[0] : NULL;
	}

	/**
	 * Get last note order
	 *
	 * @param      string $scope
	 * @return     void
	 */
	public function getLastNoteOrder( $scope = '' )
	{
		$scope = $scope ? $scope : $this->_scope;
		$query = "SELECT p.times_rated FROM #__wiki_page AS p
				  WHERE p.group_cn='" . $this->_group_cn . "'
				  AND p.scope='" . $scope . "'
				  ORDER BY p.times_rated DESC LIMIT 1";

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Fix scope paths after page rename
	 *
	 * @param      string $scope
	 * @param      string $oldpagename
	 * @param      string $newpagename
	 * @return     void
	 */
	public function fixScopePaths( $scope, $oldpagename, $newpagename )
	{
		$query = "UPDATE #__wiki_page AS p SET p.scope=replace(p.scope, '/" . $oldpagename . "', '/" . $newpagename . "')
				  WHERE p.group_cn='" . $this->_group_cn . "'";

		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			return false;
		}
		return true;
	}

	/**
	 * Save note order
	 *
	 * @param      string $scope
	 * @param      int $order
	 * @return     void
	 */
	public function saveNoteOrder( $scope, $order = 0 )
	{
		$query = "UPDATE #__wiki_page AS p SET p.times_rated='" . $order . "'
				  WHERE p.group_cn='" . $this->_group_cn . "'
				  AND p.scope='" . $scope . "'
				  AND p.times_rated='0'";

		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			return false;
		}
		return true;
	}

	/**
	 * Get path to wiki page images and files
	 *
	 * @param      int 	$page
	 *
	 * @return     string
	 */
	public function getWikiPath( $id = 0)
	{
		// Ensure we have an ID to work with
		$listdir = JRequest::getInt('lid', 0);
		$id = $id ? $id : $listdir;

		if (!$id)
		{
			return false;
		}

		// Load wiki configs
		$wiki_config = JComponentHelper::getParams( 'com_wiki' );

		$path =  DS . trim($wiki_config->get('filepath', '/site/wiki'), DS) . DS . $id;

		if (!is_dir(JPATH_ROOT . $path))
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create(JPATH_ROOT . $path))
			{
				return false;
			}
		}

		return $path;
	}
}

