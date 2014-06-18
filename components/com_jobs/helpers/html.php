<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

if (!defined('n'))
{
	/**
	 * Shortcut constant for tabs
	 */
	define('t', "\t");

	/**
	 * Shortcut constant for new lines
	 */
	define('n', "\n");

	/**
	 * Shortcut constant for line returns
	 */
	define('r', "\r");

	/**
	 * Shortcut constant for break tags
	 */
	define('br', '<br />');

	/**
	 * Shortcut constant for space character
	 */
	define('sp', '&#160;');

	/**
	 * Shortcut constant for ampersand
	 */
	define('a', '&amp;');
}

/**
 * Jobs helper class for misc. HTML
 */
class JobsHtml
{
	/**
	 * Remove paragraph tags and break tags
	 *
	 * @param      string $pee Text to unparagraph
	 * @return     string
	 */
	public function txt_unpee($pee)
	{
		$pee = str_replace("\t", '', $pee);
		$pee = str_replace('</p><p>', '', $pee);
		$pee = str_replace('<p>', '', $pee);
		$pee = str_replace('</p>', "\n", $pee);
		$pee = str_replace('<br />', '', $pee);
		$pee = trim($pee);
		return $pee;
	}

	/**
	 * Return a confirmation screen
	 *
	 * @param      string $returnurl URL to return to if they press 'no'
	 * @param      string $actionurl URL to go to if they press 'yes'
	 * @param      string $action    Action the confirmation is for
	 * @return     string HTML
	 */
	public function confirmscreen($returnurl, $actionurl, $action='cancelsubscription')
	{
		$html  = '<div class="confirmwrap">' . "\n";
		$html .= "\t" . '<div class="confirmscreen">' . "\n";
		$html .= "\t" . '<p class="warning">' . JText::_('CONFIRM_ARE_YOU_SURE') . ' ';
		if ($action == 'cancelsubscription')
		{
			$html .= strtolower(JText::_('SUBSCRIPTION_CANCEL_THIS'));
		}
		else if ($action == 'withdrawapp')
		{
			$html .=  JText::_('APPLICATION_WITHDRAW');
		}
		else
		{
			$html .= JText::_('ACTION_PERFORM_THIS');
		}
		$yes  = strtoupper(JText::_('YES'));
		$yes .= $action == 'cancelsubscription' ? ', ' . JText::_('ACTION_CANCEL_IT') : '';
		$yes .= $action == 'withdrawapp'        ? ', ' . JText::_('ACTION_WITHDRAW')  : '';

		$no  = strtoupper(JText::_('NO'));
		$no .= $action == 'cancelsubscription' ? ', ' . JText::_('ACTION_DO_NOT_CANCEL')   : '';
		$no .= $action == 'withdrawapp'        ? ', ' . JText::_('ACTION_DO_NOT_WITHDRAW') : '';

		$html .= '?</p>' . "\n";
		$html .= "\t" . '<p><span class="yes"><a href="' . $actionurl . '">' . $yes . '</a></span> <span class="no"><a href="' . $returnurl . '">' . $no . '</a></span></p>';
		$html .= "\t" . '</div>' . "\n";
		$html .= '</div>' . "\n";

		return $html;
	}

	/**
	 * Generate a select form
	 *
	 * @param      string $name  Field name
	 * @param      array  $array Data to populate select with
	 * @param      mixed  $value Value to select
	 * @param      string $class Class to add
	 * @return     string HTML
	 */
	public function formSelect($name, $array, $value, $class='')
	{
		$out  = '<select name="' . $name . '" id="' . $name . '"';
		$out .= ($class) ? ' class="' . $class . '">' . "\n" : '>' . "\n";
		foreach ($array as $avalue => $alabel)
		{
			$selected = ($avalue == $value || $alabel == $value)
					  ? ' selected="selected"'
					  : '';
			$out .= ' <option value="' . $avalue . '"' . $selected . '>' . $alabel . '</option>' . "\n";
		}
		$out .= '</select>' . "\n";
		return $out;
	}

	/**
	 * Returns a wiki syntax reference table
	 *
	 * @return     string HTML
	 */
	public function wikiHelp()
	{
		$out  = '';

		return $out;
	}
}

