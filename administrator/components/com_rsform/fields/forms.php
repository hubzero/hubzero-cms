<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

class JFormFieldForms extends JFormField
{
	/**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	protected $type = 'Forms';

	protected function getInput()
	{
		JHTML::_('behavior.tooltip');

		// Construct the various argument calls that are supported.
		$attribs  = ' ';
		$attribs .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$attribs .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : 'class="inputbox"';
		
		$db = JFactory::getDBO();
		$db->setQuery("SELECT * FROM #__rsform_forms ORDER BY FormId DESC");
		$results = $db->loadObjectList();
		
		$options = array();
		foreach ($results as $result)
			$options[] = JHTML::_('select.option', $result->FormId, $result->FormTitle);
		
		$formTitle = '';
		if (empty($this->value))
		{
			$this->value = JRequest::getInt('formId');
			$db->setQuery("SELECT FormTitle FROM #__rsform_forms WHERE FormId='".$this->value."'");
			$formTitle = $db->loadResult();
		}

		// Render the HTML SELECT list.
		$return = JHTML::_('select.genericlist', $options, $this->name, $attribs, 'value', 'text', $this->value, $this->id);
		
		if ($formTitle)
			$return .= '<script type="text/javascript">document.adminForm.name.value = "'.addslashes($formTitle).'";</script>';
		
		return $return;
	}
}
?>