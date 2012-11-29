<?php

class NewsletterSecondaryStory extends JTable
{   
	var $id 		= NULL;
	var $campaign  	= NULL;
	var $title 		= NULL;
	var $story	 	= NULL;
	
	//----
	
	public function __construct( &$db )
	{
		parent::__construct( '#__newsletter_secondary_story', 'id', $db );
	}

	//-----
	
	public function getStories( $campaign )
	{
		$sql = "SELECT * FROM {$this->_tbl} WHERE campaign=".$campaign;
		$this->_db->setQuery($sql);
		return $this->_db->loadObjectList();
	} 
}