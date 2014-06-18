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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Usage plugin class for partners
 */
class plgUsagePartners extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the name of the area this plugin retrieves records for
	 *
	 * @return     array
	 */
	public function onUsageAreas()
	{
		return array(
			'partners' => JText::_('PLG_USAGE_PARTNERS')
		);
	}

	/**
	 * Event call for displaying usage data
	 *
	 * @param      string $option        Component name
	 * @param      string $task          Component task
	 * @param      object $db            JDatabase
	 * @param      array  $months        Month names (Jan -> Dec)
	 * @param      array  $monthsReverse Month names in reverse (Dec -> Jan)
	 * @param      string $enddate       Time period
	 * @return     string HTML
	 */
	public function onUsageDisplay($option, $task, $db, $months, $monthsReverse, $enddate)
	{
		// Check if our task is the area we want to return results for
		if ($task)
		{
			if (!in_array($task, $this->onUsageAreas())
			 && !in_array($task, array_keys($this->onUsageAreas())))
			{
				return '';
			}
		}

		// Set some vars
		$thisyear = date("Y");

		$o = UsageHelper::options($db, $enddate, $thisyear, $monthsReverse, 'check_for_regiondata');

		// Build HTML
		$html  = '<form method="post" action="'. JRoute::_('index.php?option=' . $option . '&task=' . $task) .'">' . "\n";
		$html .= "\t" . '<fieldset class="filters">' . "\n";
		$html .= "\t\t" . '<label>' . "\n";
		$html .= "\t\t\t".JText::_('PLG_USAGE_SHOW_DATA_FOR').': ' . "\n";
		$html .= "\t\t\t" . '<select name="selectedPeriod" id="selectedPeriod">' . "\n";
		$html .= $o;
		$html .= "\t\t\t" . '</select>' . "\n";
		$html .= "\t\t" . '</label> <input type="submit" value="'.JText::_('PLG_USAGE_VIEW').'" />' . "\n";
		$html .= "\t" . '</fieldset>' . "\n";
		$html .= '</form>' . "\n";
		$html .= UsageHelper::toplist($db, 24, 1, $enddate);
		$html .= UsageHelper::toplist($db, 22, 2, $enddate);
		$html .= UsageHelper::toplist($db, 26, 3, $enddate);
		$html .= UsageHelper::toplist($db, 25, 4, $enddate);
		$html .= UsageHelper::toplist($db, 27, 5, $enddate);
		$html .= UsageHelper::toplist($db, 23, 6, $enddate);
		$html .= UsageHelper::toplist($db, 21, 7, $enddate);
		$html .= UsageHelper::toplist($db, 20, 8, $enddate);

		// Return HTML
		return $html;
	}
}
