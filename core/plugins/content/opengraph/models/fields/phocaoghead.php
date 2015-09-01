<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('JPATH_BASE') or die;
jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldPhocaOGHead extends JFormField
{
	protected $type = 'PhocaOGHead';
	
	protected function getInput() {
		return '';
	}
	
	protected function getLabel() {
	
		// Temporary solution
		
		echo '<div class="clr"></div>';
		$image		= '';
		$style		= 'background: #CCE6FF; color: #0069CC;padding:5px;margin:5px 0;';
		
		if ($this->element['default']) {
		
			if ($image != '') {
				return '<div style="'.$style.'">'
				.'<table border="0"><tr>'
				.'<td valign="middle" align="center">'. $image.'</td>'
				.'<td valign="middle" align="center">'
				.'<strong>'. JText::_($this->element['default']) . '</strong></td>'
				.'</tr></table>'
				.'</div>';
			} else {
				return '<div style="'.$style.'">'
				.'<strong>'. JText::_($this->element['default']) . '</strong>'
				.'</div>';
			}
		} else {
			return parent::getLabel();
		}
		echo '<div class="clr"></div>';
	}
}
?>