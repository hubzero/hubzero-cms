<?php

class NewsletterTemplate extends JTable
{   
	var $id 		= NULL;
	var $name		= NULL;
	var $template 	= NULL;
	
	//----
	
	public function __construct( &$db )
	{
		parent::__construct( '#__newsletter_template', 'id', $db );
	}
	
	//-----
	
	public function getTemplate( $id = null )
	{
		$sql = "SELECT * FROM {$this->_tbl}";
		
		if($id)
		{   
			$sql .= " WHERE id=".$id;
			$this->_db->setQuery($sql);
			return $this->_db->loadObject();
		}
		else
		{
			$this->_db->setQuery($sql);
			return $this->_db->loadObjectList();
		}
		
	}
}