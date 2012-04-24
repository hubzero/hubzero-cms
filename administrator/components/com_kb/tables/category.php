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
defined('_JEXEC') or die('Restricted access');

/**
 * Short description for 'KbCategory'
 * 
 * Long description (if any) ...
 */
class KbCategory extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id           = NULL;  // @var int(11) Primary key

	/**
	 * Description for 'title'
	 * 
	 * @var unknown
	 */
	var $title        = NULL;  // @var varchar(250)

	/**
	 * Description for 'description'
	 * 
	 * @var unknown
	 */
	var $description  = NULL;  // @var text

	/**
	 * Description for 'section'
	 * 
	 * @var unknown
	 */
	var $section      = NULL;  // @var int(1)

	/**
	 * Description for 'state'
	 * 
	 * @var unknown
	 */
	var $state        = NULL;  // @var int(3)

	/**
	 * Description for 'access'
	 * 
	 * @var unknown
	 */
	var $access       = NULL;  // @var int(3)

	/**
	 * Description for 'alias'
	 * 
	 * @var unknown
	 */
	var $alias        = NULL;  // @var varchar(200)

	//-----------

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__faq_categories', 'id', $db);
	}

	/**
	 * Short description for 'check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function check()
	{
		$this->id = intval($this->id);
		
		if (trim($this->title) == '') 
		{
			$this->setError(JText::_('COM_KB_ERROR_EMPTY_TITLE'));
			return false;
		}
		
		if (!$this->alias)
		{
			$this->alias = str_replace(' ', '-', strtolower($this->title));
		}
		$this->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', $this->alias);
		
		return true;
	}

	/**
	 * Short description for 'loadAlias'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $oid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function loadAlias($oid=NULL)
	{
		if (empty($oid)) {
			return false;
		}
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE alias='$oid'");
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind($result);
		} else {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Short description for 'getCategories'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $noauth Parameter description (if any) ...
	 * @param      integer $empty_cat Parameter description (if any) ...
	 * @param      mixed $catid Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getCategories($noauth, $empty_cat=0, $catid=0)
	{
        $juser =& JFactory::getUser();

		if ($empty_cat) {
			$empty = '';
		} else {
			$empty = "\n HAVING COUNT(b.id) > 0";
		}

		if ($catid) {
			$sect = "b.category";
		} else {
			$sect = "b.section";
		}

		$query = "SELECT a.*, COUNT(b.id) AS numitems"
				. " FROM $this->_tbl AS a"
				. " LEFT JOIN #__faq AS b ON ".$sect." = a.id AND b.state=1 AND b.access=0"
				. " WHERE a.state=1 AND a.section=".$catid
				. ($noauth ? " AND a.access <= '". $juser->get('aid') ."'" : '')
				. " GROUP BY a.id"
				. $empty
				. " ORDER BY a.title";
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'deleteSef'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $option Parameter description (if any) ...
	 * @param      string $id Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function deleteSef($option, $id=NULL)
	{
		if ($id == NULL) {
			$id = $this->id;
		}
		$this->_db->setQuery("DELETE FROM #__redirection WHERE newurl='index.php?option=".$option."&task=category&id=".$id."'");
		if ($this->_db->query()) {
			return true;
		} else {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Short description for 'getAllSections'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     object Return description (if any) ...
	 */
	public function getAllSections()
	{
		$this->_db->setQuery("SELECT m.id, m.title, m.alias FROM $this->_tbl AS m WHERE m.section=0 ORDER BY m.title");
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getAllCategories'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     object Return description (if any) ...
	 */
	public function getAllCategories()
	{
		$this->_db->setQuery("SELECT m.id, m.title, m.alias FROM $this->_tbl AS m WHERE m.section!=0 ORDER BY m.title");
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getCategoriesCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getCategoriesCount($filters=array())
	{
		$query  = "SELECT count(*) FROM $this->_tbl WHERE section=";
		$query .= (isset($filters['id'])) ? $filters['id'] : "0";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getCategoriesAll'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getCategoriesAll($filters=array())
	{
		if (isset($filters['id']) && $filters['id']) {
			$sect = $filters['id'];
			$sfield = "category";
		} else {
			$sect = 0;
			$sfield = "section";
		}

		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query = "SELECT m.id, m.title, m.section, m.state, m.access, m.alias, g.name AS groupname, 
					(SELECT count(*) FROM #__faq AS fa WHERE fa.".$sfield."=m.id) AS total, 
					(SELECT count(*) FROM $this->_tbl AS fc WHERE fc.section=m.id) AS cats"
				. " FROM #__faq_categories AS m"
				. " LEFT JOIN #__groups AS g ON g.id = m.access";
		}
		else 
		{
			$query = "SELECT m.id, m.title, m.section, m.state, m.access, m.alias, g.title AS groupname, 
					(SELECT count(*) FROM #__faq AS fa WHERE fa.".$sfield."=m.id) AS total, 
					(SELECT count(*) FROM $this->_tbl AS fc WHERE fc.section=m.id) AS cats"
				. " FROM #__faq_categories AS m"
				. " LEFT JOIN #__viewlevels AS g ON g.id = (m.access + 1)";
		}
		$query .= " WHERE m.section=".$sect
				. " ORDER BY ".$filters['filterby'];
		if (isset($filters['limit']) && $filters['limit'])
		{
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}
		
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

