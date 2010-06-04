<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

$html  = '';

if ($this->container) {
	$html .= '<div class="draggable" id="mod_'.$this->module->id.'">'."\n";
}

if ($this->extras) {
	//$html .= '<div class="draggable" id="lid'.$module->id.'">'."\n";
	$html .= "\t".'<div class="cwrap">'."\n";

	// Add the 'close' button
	if ($this->act == 'customize') {
		$html .= '<a class="close" href="'.JRoute::_('index.php?option='.$this->option).'#" onclick="HUB.Myhub.removeModule(this);return false;" title="'.JText::_('REMOVE_MODULE').'">[ X ]</a>';
	}
	// Add the module title
	$html .= "\t\t".'<h3 class="handle">'.$this->module->title.'</h3>'."\n";
	$html .= "\t\t".'<div class="body">'."\n";
	
	if ($this->rendered != '') {
		$html .= "\t\t\t".'<p class="modcontrols">';
		// Add the 'edit' button
		if ($this->act == 'customize') {
			$html .= '<a class="edimodl" id="e_'.$this->module->id.'" href="'.JRoute::_('index.php?option='.$this->option).'#" title="'.JText::_('EDIT_TITLE').'" onclick="return HUB.Myhub.editModule(this, \'f_'.$this->module->id.'\');">'.JText::_('EDIT').'</a>';
		} else {
			$html .= '<a class="edimodl" id="e_'.$this->module->id.'" href="'.JRoute::_('index.php?option='.$this->option).'?act=customize" title="'.JText::_('EDIT_TITLE').'">'.JText::_('EDIT').'</a>';
		}
		$html .= '</p>'."\n";
		$html .= "\t\t\t".'<form class="fparams" id="f_'.$this->module->id.'" onsubmit="return HUB.Myhub.saveModule(this,'.$this->module->id.');">'."\n";
		$html .= $this->rendered;
		$html .= "\t\t\t\t".'<input type="submit" name="submit" value="'.JText::_('BUTTON_SAVE').'" />'."\n";
		$html .= "\t\t\t".'</form>'."\n";
	}
}

// Is it a custom module (i.e., HTML)?
if ($this->module->module == 'mod_custom') { 
	$html .= $this->module->content;
} else {
	$this->rparams['style'] = 'none';
	//$module = JModuleHelper::getModule( $module->module );
	$this->module->user = false;
	$html .= '<p>Module content here.</p>'; //MyhubHtml::renderModule($module, $rparams);
}

if ($this->extras) {
	$html .= "\t\t".'</div><!-- / .body -->'."\n";
	$html .= "\t".'</div><!-- / .cwrap -->'."\n";
}

if ($this->container) {
	$html .= '</div><!-- / .draggable #mod_'.$this->module->id.' -->'."\n";
}

echo $html;