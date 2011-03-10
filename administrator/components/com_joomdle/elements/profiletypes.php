<?php
/**
* @Copyright Ready Bytes Software Labs Pvt. Ltd. (C) 2010- author-Team Joomlaxi
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

class JElementProfiletypes extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'Profiletypes';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$reqnone = false;
		$reqall = false;
		if(isset($node->_attributes->addnone))
			$reqnone = true;
			
		if(isset($node->_attributes->addall))
			$reqall = true;
			
		$ptypeHtml = $this->getProfiletypeFieldHTML($name,$value,$control_name,$reqnone,$reqall);

		return $ptypeHtml;
	}
	
	
	function getProfiletypeFieldHTML($name,$value,$control_name='params',$reqnone=false,$reqall=false)
	{	
		require_once JPATH_ROOT.DS.'components'.DS.'com_xipt'.DS.'libraries'.DS.'profiletypes.php';
		$required			='1';
		$html				= '';
		$class				= ($required == 1) ? ' required' : '';
		$options			= XiPTLibraryProfiletypes::getProfiletypeArray();
		
		$html	.= '<select id="'.$control_name.'['.$name.']" name="'.$control_name.'['.$name.']" title="' . "Select Account Type" . '::' . "Please Select your account type" . '">';
		
		if($reqall) {
			$selected	= ( JString::trim(0) == $value ) ? ' selected="true"' : '';
			$html	.= '<option value="' . 0 . '"' . $selected . '>' . JText::_("ALL") . '</option>';
		}
		
		if($reqnone) {
			$selected	= ( JString::trim(-1) == $value ) ? ' selected="true"' : '';
			$html	.= '<option value="' . -1 . '"' . $selected . '>' . JText::_("NONE") . '</option>';
		}
		
		for( $i = 0; $i < count( $options ); $i++ )
		{
		    $option		= $options[ $i ]->name;
			$id			= $options[ $i ]->id;
		    
		    $selected	= ( JString::trim($id) == $value ) ? ' selected="true"' : '';
			$html	.= '<option value="' . $id . '"' . $selected . '>' . $option . '</option>';
		}
			
		$html	.= '</select>';	
		$html   .= '<span id="errprofiletypemsg" style="display: none;">&nbsp;</span>';
		
		return $html;
	}
}
