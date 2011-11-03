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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
//----------------------------------------------------------
// Extended database class
//----------------------------------------------------------

/**
 * Short description for 'SelectedQuotes'
 * 
 * Long description (if any) ...
 */
class SelectedQuotes extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id             = NULL;  // @var int(11) Primary key

	/**
	 * Description for 'userid'
	 * 
	 * @var unknown
	 */
	var $userid         = NULL;  // @var int(11)

	/**
	 * Description for 'fullname'
	 * 
	 * @var unknown
	 */
	var $fullname       = NULL;  // @var string

	/**
	 * Description for 'org'
	 * 
	 * @var unknown
	 */
	var $org            = NULL;  // @var string

	/**
	 * Description for 'miniquote'
	 * 
	 * @var unknown
	 */
	var $miniquote      = NULL;  // @var text

	/**
	 * Description for 'short_quote'
	 * 
	 * @var unknown
	 */
	var $short_quote    = NULL;  // @var text

	/**
	 * Description for 'quote'
	 * 
	 * @var unknown
	 */
	var $quote          = NULL;  // @var text

	/**
	 * Description for 'picture'
	 * 
	 * @var string
	 */
	var $picture        = NULL;  // @var string

	/**
	 * Description for 'date'
	 * 
	 * @var unknown
	 */
	var $date           = NULL;  // @var datetime	

	/**
	 * Description for 'flash_rotation'
	 * 
	 * @var unknown
	 */
	var $flash_rotation = NULL;  // @var int(1)

	/**
	 * Description for 'notable_quotes'
	 * 
	 * @var unknown
	 */
	var $notable_quotes = NULL;  // @var int(1)

	/**
	 * Description for 'notes'
	 * 
	 * @var unknown
	 */
	var $notes          = NULL;	 // @var string

	//-----------

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__selected_quotes', 'id', $db );
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
		if (trim( $this->quote ) == '') {
			$this->setError( JText::_('Quote must contain text.') );
			return false;
		}

		return true;
	}

	/**
	 * Short description for 'buildQuery'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function buildQuery( $filters )
	{
		/*$query = "FROM $this->_tbl ";
		if ((isset($filters['search']) && $filters['search'] != '') 
		 || (isset($filters['id']) && $filters['id'] != 0)
		 || (isset($filters['notable_quotes']) && $filters['notable_quotes'] != '')) {
			$query .= "WHERE";
		}
		if (isset($filters['search']) && $filters['search'] != '' ) {
			$words = explode(' ', $filters['search']);
			$sqlsearch = "";
			foreach ($words as $word) 
			{
				$sqlsearch .= " (LOWER(fullname) LIKE '%$word%') OR";
			}
			$query .= substr($sqlsearch, 0, -3);
		}
		if (isset($filters['notable_quotes']) && $filters['notable_quotes']) {
			$query .= " notable_quotes=".$filters['notable_quotes'];
		}
		if (isset($filters['id']) && $filters['id'] != 0 ) {
			$query .= " AND id=".$filters['id'];
		}
		if ($filters['sortby'] == '') {
			$filters['sortby'] = 'date';
		}
		$query .= "\n ORDER BY ".$filters['sortby']." DESC";
		if (isset($filters['limit']) && $filters['limit'] > 0) {
			$query .= " LIMIT ".$filters['limit'];
		}
		return $query;*/
		$query = "FROM $this->_tbl ";
		$query .= "WHERE 1=1 ";
		if (isset($filters['search']) && $filters['search'] != '' ) {
			$words = explode(' ', $filters['search']);
			$sqlsearch = "";
			foreach ($words as $word)
			{
				$sqlsearch .= " (LOWER(fullname) LIKE '%$word%') OR";
			}
			$query .= ' AND '.substr($sqlsearch, 0, -3);
		}
		if (isset($filters['notable_quotes']) && $filters['notable_quotes']==1) {
			$query .= " AND notable_quotes=".$filters['notable_quotes'];
		}
		if (isset($filters['flash_rotation']) && $filters['flash_rotation']==1) {
			$query .= " AND flash_rotation=".$filters['flash_rotation'];
		}
		if (isset($filters['miniquote']) && $filters['miniquote']==1) {
			$query .= " AND miniquote!=''";
		}
		if (isset($filters['id']) && $filters['id'] != 0 ) {
			$query .= " AND id=".$filters['id'];
		}
		if (empty($filters['sortby'])) {
			$filters['sortby'] = 'date';
		}
		$query .= "\n ORDER BY ".$filters['sortby']." DESC";
		if (isset($filters['limit']) && $filters['limit'] != 'all' && $filters['limit'] != '') {
			$query .= " LIMIT ".$filters['limit'];
		}
		return $query;
	}

	/**
	 * Short description for 'getCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getCount( $filters=array() )
	{
		$query  = "SELECT COUNT(*) ".$this->buildQuery( $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getResults'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getResults( $filters=array() )
	{
		$query  = "SELECT * ".$this->buildQuery( $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'deletePicture'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $config Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function deletePicture( $config=null )
	{
		// Load the component config
		if (!$config) {
			$config =& JComponentHelper::getParams( 'com_feedback' );
		}

		// Incoming member ID
		if (!$this->id) {
			$this->setError( JText::_('FEEDBACK_NO_ID') );
			return false;
		}

		// Incoming file
		if (!$this->picture) {
			return true;
		}

		// Build the file path
		ximport('Hubzero_View_Helper_Html');
		$dir  = Hubzero_View_Helper_Html::niceidformat( $this->id );
		$path = JPATH_ROOT;
		if (substr($config->get('uploadpath'), 0, 1) != DS) {
			$path .= DS;
		}
		if (substr($config->get('uploadpath'), -1, 1) == DS) {
			$path = substr($config->get('uploadpath'), 0, (strlen($config->get('uploadpath')) - 1));
		}
		$path .= $config->get('uploadpath').DS.$dir;

		if (!file_exists($path.DS.$this->picture) or !$this->picture) {
			return true;
		} else {
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path.DS.$this->picture)) {
				$this->setError( JText::_('UNABLE_TO_DELETE_FILE') );
				return false;
			}
		}

		return true;
	}
}

