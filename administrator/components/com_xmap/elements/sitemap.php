<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class JElementSitemap extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'Sitemap';

	function fetchElement($name, $value, &$node, $control_name)
	{
		global $mainframe;

		$db		=& JFactory::getDBO();
		$fieldName	= $control_name.'['.$name.']';
		
		$sql = "SELECT id, name from #__xmap_sitemap order by name";
		$db->setQuery($sql);
		$rows = $db->loadObjectList();

		$html = JHTML::_('select.genericlist',$rows,$fieldName,'','id','name',$value);

		return $html;
	}

}
