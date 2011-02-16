<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');
JHTML::_('behavior.tooltip');

class JElementForms extends JElement
{
	/**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	var $_name = 'Forms';

	function fetchElement($name, $value, &$node, $control_name)
	{
		// Base name of the HTML control.
		$ctrl  = $control_name .'['. $name .']';

		// Construct the various argument calls that are supported.
		$attribs = ' ';
		if ($v = $node->attributes( 'size' ))
			$attribs .= 'size="'.$v.'"';
		if ($v = $node->attributes('class'))
			$attribs .= 'class="'.$v.'"';
		else
			$attribs .= 'class="inputbox"';
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__rsform_forms ORDER BY FormId DESC");
		$results = $db->loadObjectList();
		
		$options = array();
		foreach ($results as $result)
			$options[] = JHTML::_('select.option', $result->FormId, $result->FormTitle);
		
		$formTitle = '';
		if (empty($value))
		{
			$value = JRequest::getInt('formId');
			$db->setQuery("SELECT FormTitle FROM #__rsform_forms WHERE FormId='".$value."'");
			$formTitle = $db->loadResult();
		}

		// Render the HTML SELECT list.
		$return = JHTML::_('select.genericlist', $options, $ctrl, $attribs, 'value', 'text', $value, $control_name.$name );
		
		if ($formTitle)
			$return .= '<script type="text/javascript">document.adminForm.name.value = "'.addslashes($formTitle).'";</script>';
		
		return $return;
	}
}
?>