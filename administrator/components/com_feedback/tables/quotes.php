<?php
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class FeedbackQuotes extends JTable 
{
	var $id         = NULL;  // @var int(11) Primary key
	var $userid	    = NULL;  // @var int(11)
	var $fullname   = NULL;  // @var string
	var $org	    = NULL;  // @var string
	var $quote      = NULL;  // @var text
	var $picture    = NULL;  // @var string
	var $date	    = NULL;  // @var datetime	
	var $publish_ok = NULL;  // @var int(1)
	var $contact_ok = NULL;  // @var int(1)
	var $notes 		= NULL;	 // @var string

	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( '#__feedback', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->quote ) == '') {
			$this->setError( JText::_('Quote must contain text.') );
			return false;
		}

		return true;
	}
	
	//-----------
	
	public function buildQuery( $filters ) 
	{
		$query = "FROM $this->_tbl ";
		if ((isset($filters['search']) && $filters['search'] != '') 
		 || (isset($filters['id']) && $filters['id'] != 0)) {
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
		if (isset($filters['id']) && $filters['id'] != 0 ) {
			$query .= " AND id=".$filters['id'];
		}
		$query .= "\n ORDER BY ".$filters['sortby']." DESC";
		if (isset($filters['limit'])) {
			$query .= " LIMIT ".$filters['limit'];
		}
		return $query;
	}
	
	//-----------
	
	public function getCount( $filters=array() ) 
	{
		$query = "SELECT COUNT(*) ".$this->buildQuery( $filters );
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getResults( $filters=array() ) 
	{
		$query  = "SELECT * ".$this->buildQuery( $filters );
		
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
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
		ximport('fileuploadutils');
		$dir  = FileUploadUtils::niceidformat( $this->id );
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
