<?php
/**
 * @package		Joomla.Installation
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

class JHtmlInstallation
{
	public static function stepbar()
 	{
		$view = JRequest::getWord('view');
		switch ($view) {
			case '':
			case 'language':
				$on = 1;
				break;
			case 'installkey':
				$on = 2;
				break;
			case 'preinstall':
				$on = 3;
				break;
			case 'license':
				$on = 4;
				break;
			case 'database':
				$on = 5;
				break;
			case 'filesystem':
				$on = 6;
				break;
			case 'site':
				$on = 7;
				break;
			case 'complete':
				$on = 8;
				break;
			case 'remove':
				$on = 9;
				break;
			default:
				$on = 1;
		}

 		$html = '<h2>'.JText::_('INSTL_STEPS_TITLE').'</h2>' .
			'<div class="step'.($on == 1 ? ' active' : '').'" id="language">'.JText::_('INSTL_STEP_1_LABEL').'</div>' .
			'<div class="step'.($on == 2 ? ' active' : '').'" id="installkey">'.JText::_('INSTL_STEP_2_LABEL').'</div>' .
			'<div class="step'.($on == 3 ? ' active' : '').'" id="preinstall">'.JText::_('INSTL_STEP_3_LABEL').'</div>' .
			'<div class="step'.($on == 4 ? ' active' : '').'" id="license">'.JText::_('INSTL_STEP_4_LABEL').'</div>' .
			'<div class="step'.($on == 5 ? ' active' : '').'" id="database">'.JText::_('INSTL_STEP_5_LABEL').'</div>' .
			'<div class="step'.($on == 6 ? ' active' : '').'" id="filesystem">'.JText::_('INSTL_STEP_6_LABEL').'</div>' .
			'<div class="step'.($on == 7 ? ' active' : '').'" id="site">'.JText::_('INSTL_STEP_7_LABEL').'</div>' .
			'<div class="step'.($on == 8 ? ' active' : '').'" id="complete">'.JText::_('INSTL_STEP_8_LABEL').'</div>';
			return $html;
	}
}
