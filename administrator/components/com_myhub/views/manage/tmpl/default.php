<?php
/**
 * @package     hubzero-cms
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 */

// Check to ensure this file is included in Joomla!
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

