<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Html\Parameter\Element;

use Hubzero\Html\Parameter\Element;
use Hubzero\Html\Builder\Select;

/**
 * Renders a timezones element
 */
class Timezones extends Element
{
	/**
	 * Element name
	 *
	 * @var  string
	 */
	protected $_name = 'Timezones';

	/**
	 * Fetch a calendar element
	 *
	 * @param   string  $name          Element name
	 * @param   string  $value         Element value
	 * @param   object  &$node         XMLElement node object containing the settings for the element
	 * @param   string  $control_name  Control name
	 * @return  string
	 */
	public function fetchElement($name, $value, &$node, $control_name)
	{
		if (!strlen($value))
		{
			$value = \App::get('config')->get('offset');
		}

		$lang = \App::get('language');

		// LOCALE SETTINGS
		$timezones = array(
			Builder\Select::option(-12, $lang->txt('UTC__12_00__INTERNATIONAL_DATE_LINE_WEST')),
			Builder\Select::option(-11, $lang->txt('UTC__11_00__MIDWAY_ISLAND__SAMOA')),
			Builder\Select::option(-10, $lang->txt('UTC__10_00__HAWAII')),
			Builder\Select::option(-9.5, $lang->txt('UTC__09_30__TAIOHAE__MARQUESAS_ISLANDS')),
			Builder\Select::option(-9, $lang->txt('UTC__09_00__ALASKA')),
			Builder\Select::option(-8, $lang->txt('UTC__08_00__PACIFIC_TIME__US__AMP__CANADA_')),
			Builder\Select::option(-7, $lang->txt('UTC__07_00__MOUNTAIN_TIME__US__AMP__CANADA_')),
			Builder\Select::option(-6, $lang->txt('UTC__06_00__CENTRAL_TIME__US__AMP__CANADA___MEXICO_CITY')),
			Builder\Select::option(-5, $lang->txt('UTC__05_00__EASTERN_TIME__US__AMP__CANADA___BOGOTA__LIMA')),
			Builder\Select::option(-4, $lang->txt('UTC__04_00__ATLANTIC_TIME__CANADA___CARACAS__LA_PAZ')),
			Builder\Select::option(-4.5, $lang->txt('UTC__04_30__VENEZUELA')),
			Builder\Select::option(-3.5, $lang->txt('UTC__03_30__ST__JOHN_S__NEWFOUNDLAND__LABRADOR')),
			Builder\Select::option(-3, $lang->txt('UTC__03_00__BRAZIL__BUENOS_AIRES__GEORGETOWN')),
			Builder\Select::option(-2, $lang->txt('UTC__02_00__MID_ATLANTIC')),
			Builder\Select::option(-1, $lang->txt('UTC__01_00__AZORES__CAPE_VERDE_ISLANDS')),
			Builder\Select::option(0, $lang->txt('UTC_00_00__WESTERN_EUROPE_TIME__LONDON__LISBON__CASABLANCA')),
			Builder\Select::option(1, $lang->txt('UTC__01_00__AMSTERDAM__BERLIN__BRUSSELS__COPENHAGEN__MADRID__PARIS')),
			Builder\Select::option(2, $lang->txt('UTC__02_00__ISTANBUL__JERUSALEM__KALININGRAD__SOUTH_AFRICA')),
			Builder\Select::option(3, $lang->txt('UTC__03_00__BAGHDAD__RIYADH__MOSCOW__ST__PETERSBURG')),
			Builder\Select::option(3.5, $lang->txt('UTC__03_30__TEHRAN')),
			Builder\Select::option(4, $lang->txt('UTC__04_00__ABU_DHABI__MUSCAT__BAKU__TBILISI')),
			Builder\Select::option(4.5, $lang->txt('UTC__04_30__KABUL')),
			Builder\Select::option(5, $lang->txt('UTC__05_00__EKATERINBURG__ISLAMABAD__KARACHI__TASHKENT')),
			Builder\Select::option(5.5, $lang->txt('UTC__05_30__BOMBAY__CALCUTTA__MADRAS__NEW_DELHI__COLOMBO')),
			Builder\Select::option(5.75, $lang->txt('UTC__05_45__KATHMANDU')), Builder\Select::option(6, $lang->txt('UTC__06_00__ALMATY__DHAKA')),
			Builder\Select::option(6.5, $lang->txt('UTC__06_30__YAGOON')),
			Builder\Select::option(7, $lang->txt('UTC__07_00__BANGKOK__HANOI__JAKARTA__PHNOM_PENH')),
			Builder\Select::option(8, $lang->txt('UTC__08_00__BEIJING__PERTH__SINGAPORE__HONG_KONG')),
			Builder\Select::option(8.75, $lang->txt('UTC__08_00__WESTERN_AUSTRALIA')),
			Builder\Select::option(9, $lang->txt('UTC__09_00__TOKYO__SEOUL__OSAKA__SAPPORO__YAKUTSK')),
			Builder\Select::option(9.5, $lang->txt('UTC__09_30__ADELAIDE__DARWIN__YAKUTSK')),
			Builder\Select::option(10, $lang->txt('UTC__10_00__EASTERN_AUSTRALIA__GUAM__VLADIVOSTOK')),
			Builder\Select::option(10.5, $lang->txt('UTC__10_30__LORD_HOWE_ISLAND__AUSTRALIA_')),
			Builder\Select::option(11, $lang->txt('UTC__11_00__MAGADAN__SOLOMON_ISLANDS__NEW_CALEDONIA')),
			Builder\Select::option(11.5, $lang->txt('UTC__11_30__NORFOLK_ISLAND')),
			Builder\Select::option(12, $lang->txt('UTC__12_00__AUCKLAND__WELLINGTON__FIJI__KAMCHATKA')),
			Builder\Select::option(12.75, $lang->txt('UTC__12_45__CHATHAM_ISLAND')), Builder\Select::option(13, $lang->txt('UTC__13_00__TONGA')),
			Builder\Select::option(14, $lang->txt('UTC__14_00__KIRIBATI'))
		);

		return Builder\Select::genericlist(
			$timezones,
			$control_name . '[' . $name . ']',
			array('id' => $control_name . $name, 'list.attr' => 'class="inputbox"', 'list.select' => $value)
		);
	}
}
