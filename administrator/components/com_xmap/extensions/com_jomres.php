<?php
/**
* @author Hazel Wilson - http://www.highlandvision.com
* @email hazel@highlandvision.com
* @package Xmap
* @license GNU/GPL
* @description Xmap plugin for Jomres component
*/

defined( '_VALID_MOS' ) or defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

/** Adds support for Jomres categories to Xmap */
class xmap_com_jomres 
	{

	/** Get the content tree for this kind of content */
	function getTree( &$xmap, &$parent, &$params ) 
		{
		if (defined('JPATH_SITE')) 
			$mosConfig_absolute_path = JPATH_SITE;
		else
			global $mosConfig_absolute_path;

		//Include the jomres stuff
		require_once('components/com_jomres/integration.php');

		$link_query = parse_url( $parent->link );
		parse_str( html_entity_decode($link_query['query']), $link_vars);
		$task 						= 	xmap_com_jomres::getParam($link_vars,'task',"");
		
		if ($task != "")
			return $tree;

		$priority 					= 	xmap_com_jomres::getParam($params,'priority',$parent->priority);
		$changefreq 				= 	xmap_com_jomres::getParam($params,'changefreq',$parent->changefreq);
		if ($priority  == '-1')
			$priority 				= 	$parent->priority;
		if ($changefreq  == '-1')
			$changefreq 			= 	$parent->changefreq;

		$params['priority'] 		= 	$priority;
		$params['changefreq'] 		= 	$changefreq;

		xmap_com_jomres::getCategoryTree($xmap, $parent, $params, $prop);

		return true;
		}

	/** Jomres support */
	function getCategoryTree( &$xmap, &$parent,&$params, $prop=0 ) 
		{
		if (defined('JPATH_SITE')) 
			{
			$database 					= 	&JFactory::getDBO();
			$mosConfig_absolute_path 	= 	JPATH_SITE;
			} 
		else 
			global $database,$mosConfig_absolute_path;
		

		$query = "SELECT propertys_uid,property_name,UNIX_TIMESTAMP(`timestamp`) FROM #__jomres_propertys WHERE published=1 ORDER BY propertys_uid";
		$rows = doSelectSql($query);

		$xmap->changeLevel(1);
		
		foreach($rows as $row) 
		{
			$node 				= 	new stdclass;

			$node->id 			= 	$parent->id;
			$node->uid 			= 	$parent->uid.'c'.$row->propertys_uid;
			$node->browserNav 	= 	$parent->browserNav;
			$node->name 		= 	stripslashes($row->property_name);
			$node->modified 	= 	intval($row->timestamp);
			$node->modified 	= 	$row->timestamp; 
			$node->priority 	= 	$params['priority'];
			$node->changefreq 	= 	$params['changefreq'];
			//$node->link 		= 	jomresURL('index.php?option=com_jomres&amp;task=viewproperty&amp;property_uid='.$row->propertys_uid);
			$node->link 		= 	'index.php?option=com_jomres&amp;task=viewproperty&amp;property_uid='.$row->propertys_uid;
			$node->pid 			= 	$row->propertys_uid;	
			$node->expandible 	= 	false;   //Doesn't support children								

		    //if ($xmap->printNode($node) !== FALSE) 
				//xmap_com_jomres::getCategoryTree( $xmap, $parent, $params, $row->propertys_uid);
    			$xmap->printNode($node);
	    	}
		
		$xmap->changeLevel(-1);

		return true;
	}


	function &getParam($arr, $name, $def) 
	{
		$var 		= 	JArrayHelper::getValue( $arr, $name, $def, '' );
		return $var;
	}
}