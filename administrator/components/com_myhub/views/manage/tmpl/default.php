<?php
// No direct access
defined('_JEXEC') or die( 'Restricted access' );

if ($this->task == 'customize') {
	JToolBarHelper::title( JText::_( 'MyHUB' ).': <small><small>[ '. JText::_('Customize').' ]</small></small>', 'user.png' );
	JToolBarHelper::save('save');
	JToolBarHelper::cancel();
} else {
	JToolBarHelper::title( JText::_( 'MyHUB' ), 'user.png' );
	JToolBarHelper::custom( 'customize', 'move.png', 'move_f2.png', 'Customize', false );
	JToolBarHelper::preferences('com_myhub', '550');
}


$html  = '<div class="main section">'."\n";
if ($this->task != 'customize') {
	$html .= '<form action="index.php?option='.$this->option.'" method="post" name="adminForm" id="cpnlc">'."\n";
	$html .= "\t".'<input type="hidden" name="task" value="" />'."\n";
	$html .= '</form>'."\n";
}
$html .= '<table id="droppables">'."\n";
$html .= "\t".'<tbody>'."\n";
$html .= "\t\t".'<tr>'."\n";

// Initialize customization abilities
if ($this->task == 'customize') {
	// Get the control panel
	$html .= "\t\t\t".'<td id="modules-dock" style="vertical-align: top;">'."\n";
	$html .= '<script type="text/javascript">'."\n";
	$html .= 'function submitbutton(pressbutton) '."\n";
	$html .= '{'."\n";
	$html .= '	var form = document.getElementById("adminForm");'."\n";
	$html .= '	if (pressbutton == "cancel") {'."\n";
	$html .= '		submitform( pressbutton );'."\n";
	$html .= '		return;'."\n";
	$html .= '	}'."\n";
	$html .= '	// do field validation'."\n";
	$html .= '	submitform( pressbutton );'."\n";
	$html .= '}'."\n";
	$html .= '</script>'."\n";
	$html .= '<form action="index.php?option='.$this->option.'" method="post" name="adminForm" id="cpnlc">'."\n";
	$html .= "\t".'<input type="hidden" name="task" value="save" />'."\n";
	$html .= "\t".'<input type="hidden" name="uid" id="uid" value="'. $this->juser->get('id') .'" />'."\n";
	$html .= "\t".'<input type="hidden" name="serials" id="serials" value="'. $this->usermods[0].';'.$this->usermods[1].';'.$this->usermods[2] .'" />'."\n";
	$html .= "\t".'<h3>'.JText::_('MODULES').'</h3>'."\n";
	$html .= "\t".'<p>Click on a module name from the list to add it to your page.</p>'."\n";
	$html .= "\t".'<div id="available">'."\n";
	
	$view = new JView( array('name'=>'manage', 'layout'=>'list') );
	$view->modules = $this->availmods;
	$html .= $view->loadTemplate();
	
	$html .= "\t".'</div>'."\n";
	$html .= "\t".'<div class="clear"></div>'."\n";
	$html .= '</form>'."\n";
	$html .= "\t\t\t".'</td>'."\n";
}
// Loop through each column and output modules assigned to each one
for ( $c = 0; $c < $this->cols; $c++ ) 
{
	$html .= "\t\t\t".'<td class="sortable" id="sortcol_'.$c.'">'."\n";
	$html .= MyhubController::outputModules($this->mymods[$c], $this->juser->get('id'), $this->task);
	$html .= "\t\t\t".'</td>'."\n";
}
$html .= "\t\t".'</tr>'."\n";
$html .= "\t".'</tbody>'."\n";
$html .= '</table>'."\n";
$html .= '<input type="hidden" name="uid" id="uid" value="'.$this->juser->get('id').'" />'."\n";
$html .= '</div><!-- / .main section -->'."\n";

echo $html;