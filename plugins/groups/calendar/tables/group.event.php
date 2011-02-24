<?php
defined('_JEXEC') or die( 'Restricted access' );

Class GroupEvent extends JTable
{
	var $id = null;
	var $gidNumber = null;
	var $actorid = null;
	var $title = null;
	var $details = null;
	var $type = null;
	var $start = null;
	var $end = null;
	var $active = null;
	var $created = null;
	
	function __construct( &$db ) 
	{
		parent::__construct('#__xgroups_events', 'id', $db );
	}
	
	//-----
}

?>