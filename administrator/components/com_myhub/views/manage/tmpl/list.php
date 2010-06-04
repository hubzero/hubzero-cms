<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

if ($this->modules) {
	$html  = '<ul>'."\n";
	foreach ($this->modules as $module)
	{
		 $html .= "\t".'<li><label for="_add_m_'.$module->id.'">'.$module->title.'</label> <input type="button" value="'.JText::_('BUTTON_ADD').'" id="_add_m_'.$module->id.'" onclick="HUB.Myhub.addModule(\''.$module->id.'\');return false;" /></li>'."\n";
	}
	$html .= '</ul>'."\n";
} else {
	$html  = JText::_('NO_MODULES');
}

echo $html;