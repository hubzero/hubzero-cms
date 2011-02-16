<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.helper');

$document =& JFactory::getDocument();
$document->addScript(JURI::base().'components/com_rsform/assets/js/'.(JPluginHelper::isEnabled('system','mtupgrade') ? 'rainbow12' : 'rainbow').'.js');
$document->addScript(JURI::base().'components/com_rsform/assets/js/colors.js');
$document->addStyleSheet(JURI::base().'components/com_rsform/assets/css/colors.css');

class JElementColors extends JElement
{
	/**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	var $_name = 'Colors';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$size = ( $node->attributes('size') ? 'size="'.$node->attributes('size').'"' : '' );
		$class = ( $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="rsform_change_color"' );
        /*
         * Required to avoid a cycle of encoding &
         * html_entity_decode was used in place of htmlspecialchars_decode because
         * htmlspecialchars_decode is not compatible with PHP 4
         */
        $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);
		
		$html  = '';
		$html .= ' <img id="'.$control_name.$name.'i" class="rsform_color" src="'.JURI::base().'components/com_rsform/assets/images/rainbow/color.gif" alt="Color" />';
		$html .= ' <input type="text" name="'.$control_name.'['.$name.']" id="'.$control_name.$name.'" value="'.$value.'" '.$class.' '.$size.' style="background-color: '.$value.';" />';
		
		$script  = '<script type="text/javascript">'."\n"
				  ."window.addEvent('domready', function() {\n"
				  ."new MooRainbow('".$control_name.$name."i', {\n"
				  ."id: '".$control_name.$name."',\n"
				  ."imgPath: '".JURI::base()."components/com_rsform/assets/images/rainbow/',\n"
				  ."startColor: rsform_hex_to_rgb($('".$control_name.$name."').value),\n"
				  ."wheel: true,\n"
				  ."onChange: function(color) {\n"
				  ."$('".$control_name.$name."').setStyle('background-color', color.hex);\n"
				  ."$('".$control_name.$name."').value = color.hex;\n"
				  ."},\n"
				  ."onComplete: function(color) {\n"
				  ."$('".$control_name.$name."').setStyle('background-color', color.hex);\n"
				  ."$('".$control_name.$name."').value = color.hex;\n"
				  ."}\n"
				  ."});\n"
				  ."});\n"
				  ."</script>";
		
		$doc =& JFactory::getDocument();
		$doc->addCustomTag($script);
		
		return $html;
	}
}
?>