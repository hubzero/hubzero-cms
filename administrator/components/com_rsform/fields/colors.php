<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2010 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');
jimport('joomla.plugin.helper');

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_rsform'.DS.'helpers'.DS.'rsform.php');

$document =& JFactory::getDocument();
$document->addScript(JURI::base().'components/com_rsform/assets/js/'.(JPluginHelper::isEnabled('system','mtupgrade') || RSFormProHelper::isJ16() ? 'rainbow12' : 'rainbow').'.js');
$document->addScript(JURI::base().'components/com_rsform/assets/js/colors.js');
$document->addStyleSheet(JURI::base().'components/com_rsform/assets/css/colors.css');

class JFormFieldColors extends JFormField
{
	/**
	* Element name
	*
	* @access       protected
	* @var          string
	*/
	protected $type = 'Colors';

	protected function getInput()
	{
		// Initialize some field attributes.
		$size		= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$class		= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : 'class="rsform_change_color"';

		/*
		return '<input type="text" name="'.$this->name.'" id="'.$this->id.'"' .
				' value="'.htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8').'"' .
				$class.$size.$disabled.$readonly.$onchange.$maxLength.'/>';*/
				
		//$size = ( $node->attributes('size') ? 'size="'.$node->attributes('size').'"' : '' );
		//$class = ( $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="rsform_change_color"' );
        /*
         * Required to avoid a cycle of encoding &
         * html_entity_decode was used in place of htmlspecialchars_decode because
         * htmlspecialchars_decode is not compatible with PHP 4
         */
        $value = htmlspecialchars(html_entity_decode($this->value, ENT_QUOTES), ENT_QUOTES);
		
		$html  = '';
		$html .= ' <img id="'.$this->name.'i" class="rsform_color" src="'.JURI::base().'components/com_rsform/assets/images/rainbow/color.gif" alt="Color" />';
		$html .= ' <input type="text" name="'.$this->name.'" id="'.$this->id.'" value="'.$value.'" '.$class.' '.$size.' style="background-color: '.$value.';" />';
		
		$script  = '<script type="text/javascript">'."\n"
				  ."window.addEvent('domready', function() {\n"
				  ."new MooRainbow('".$this->name."i', {\n"
				  ."id: '".$this->id."',\n"
				  ."imgPath: '".JURI::base()."components/com_rsform/assets/images/rainbow/',\n"
				  ."startColor: rsform_hex_to_rgb($('".$this->id."').value),\n"
				  ."wheel: true,\n"
				  ."onChange: function(color) {\n"
				  ."document.id('".$this->id."').setStyle('background-color', color.hex);\n"
				  ."document.id('".$this->id."').value = color.hex;\n"
				  ."},\n"
				  ."onComplete: function(color) {\n"
				  ."document.id('".$this->id."').setStyle('background-color', color.hex);\n"
				  ."document.id('".$this->id."').value = color.hex;\n"
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