<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Html\Parameter\Element;

use Hubzero\Html\Parameter\Element;
use Hubzero\Html\Builder\Select;
use Hubzero\Facades\App;

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
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
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
        if (!strlen($value)) {
            $value = App::get('config')->get('offset');
        }

        $lang = App::get('language');

        // LOCALE SETTINGS
        $timezones = array(
            Select::option(-12, $lang->txt('UTC__12_00__INTERNATIONAL_DATE_LINE_WEST')),
            Select::option(-11, $lang->txt('UTC__11_00__MIDWAY_ISLAND__SAMOA')),
            Select::option(-10, $lang->txt('UTC__10_00__HAWAII')),
            Select::option(-9.5, $lang->txt('UTC__09_30__TAIOHAE__MARQUESAS_ISLANDS')),
            Select::option(-9, $lang->txt('UTC__09_00__ALASKA')),
            Select::option(-8, $lang->txt('UTC__08_00__PACIFIC_TIME__US__AMP__CANADA_')),
            Select::option(-7, $lang->txt('UTC__07_00__MOUNTAIN_TIME__US__AMP__CANADA_')),
            Select::option(-6, $lang->txt('UTC__06_00__CENTRAL_TIME__US__AMP__CANADA___MEXICO_CITY')),
            Select::option(-5, $lang->txt('UTC__05_00__EASTERN_TIME__US__AMP__CANADA___BOGOTA__LIMA')),
            Select::option(-4, $lang->txt('UTC__04_00__ATLANTIC_TIME__CANADA___CARACAS__LA_PAZ')),
            Select::option(-4.5, $lang->txt('UTC__04_30__VENEZUELA')),
            Select::option(-3.5, $lang->txt('UTC__03_30__ST__JOHN_S__NEWFOUNDLAND__LABRADOR')),
            Select::option(-3, $lang->txt('UTC__03_00__BRAZIL__BUENOS_AIRES__GEORGETOWN')),
            Select::option(-2, $lang->txt('UTC__02_00__MID_ATLANTIC')),
            Select::option(-1, $lang->txt('UTC__01_00__AZORES__CAPE_VERDE_ISLANDS')),
            Select::option(0, $lang->txt('UTC_00_00__WESTERN_EUROPE_TIME__LONDON__LISBON__CASABLANCA')),
            Select::option(1, $lang->txt('UTC__01_00__AMSTERDAM__BERLIN__BRUSSELS__COPENHAGEN__MADRID__PARIS')),
            Select::option(2, $lang->txt('UTC__02_00__ISTANBUL__JERUSALEM__KALININGRAD__SOUTH_AFRICA')),
            Select::option(3, $lang->txt('UTC__03_00__BAGHDAD__RIYADH__MOSCOW__ST__PETERSBURG')),
            Select::option(3.5, $lang->txt('UTC__03_30__TEHRAN')),
            Select::option(4, $lang->txt('UTC__04_00__ABU_DHABI__MUSCAT__BAKU__TBILISI')),
            Select::option(4.5, $lang->txt('UTC__04_30__KABUL')),
            Select::option(5, $lang->txt('UTC__05_00__EKATERINBURG__ISLAMABAD__KARACHI__TASHKENT')),
            Select::option(5.5, $lang->txt('UTC__05_30__BOMBAY__CALCUTTA__MADRAS__NEW_DELHI__COLOMBO')),
            Select::option(
                5.75,
                $lang->txt('UTC__05_45__KATHMANDU')
            ),
                Select::option(6, $lang->txt('UTC__06_00__ALMATY__DHAKA')),
            Select::option(6.5, $lang->txt('UTC__06_30__YAGOON')),
            Select::option(7, $lang->txt('UTC__07_00__BANGKOK__HANOI__JAKARTA__PHNOM_PENH')),
            Select::option(8, $lang->txt('UTC__08_00__BEIJING__PERTH__SINGAPORE__HONG_KONG')),
            Select::option(8.75, $lang->txt('UTC__08_00__WESTERN_AUSTRALIA')),
            Select::option(9, $lang->txt('UTC__09_00__TOKYO__SEOUL__OSAKA__SAPPORO__YAKUTSK')),
            Select::option(9.5, $lang->txt('UTC__09_30__ADELAIDE__DARWIN__YAKUTSK')),
            Select::option(10, $lang->txt('UTC__10_00__EASTERN_AUSTRALIA__GUAM__VLADIVOSTOK')),
            Select::option(10.5, $lang->txt('UTC__10_30__LORD_HOWE_ISLAND__AUSTRALIA_')),
            Select::option(11, $lang->txt('UTC__11_00__MAGADAN__SOLOMON_ISLANDS__NEW_CALEDONIA')),
            Select::option(11.5, $lang->txt('UTC__11_30__NORFOLK_ISLAND')),
            Select::option(12, $lang->txt('UTC__12_00__AUCKLAND__WELLINGTON__FIJI__KAMCHATKA')),
            Select::option(
                12.75,
                $lang->txt('UTC__12_45__CHATHAM_ISLAND')
            ), Select::option(13, $lang->txt('UTC__13_00__TONGA')),
            Select::option(14, $lang->txt('UTC__14_00__KIRIBATI'))
        );

        return Select::genericlist(
            $timezones,
            $control_name . '[' . $name . ']',
            array(
                'id' => $control_name . $name,
                'list.attr' => 'class="inputbox"',
                'list.select' => $value
            )
        );
    }
}
