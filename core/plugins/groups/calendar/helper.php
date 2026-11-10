<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Helper Class
 */
namespace Plugins\Groups\Calendar;

class Helper
{
    /**
     * Link string patterns that ook like URLs or email addresses
     *
     * @param      string $text Text to autolink
     * @return     string
     */
    public static function autoLinkText($text)
    {
        //replace email links
        $text = preg_replace('/([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})/', '<a href="mailto:$1">$1</a>', $text);

        //replace url links
        $text = preg_replace('#\b((?<!href=")(https?://www[.]|(?:https?|ftps?)://|(?<!://)www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))#', '<a class="ext-link" rel="external" href="$1">$1</a>', $text);

        //return auto-linked text
        return $text;
    }

    /**
     * Gets an array of timezone abbreviation and name
     * based on supplied offset (to UTC) value
     *
     * @param      string $timezone Timezone offset
     * @return     array
     */
    public static function getTimezoneNameAndAbbreviation($timezone)
    {
        $abbreviations = [
            '-12'   => ['abbreviation' => 'IDLW',  'name' => 'International Date Line West'],
            '-11'   => ['abbreviation' => 'MART',  'name' => 'Midway Island, Samoa Time'],
            '-10'   => ['abbreviation' => 'HAST',  'name' => 'Hawaii-Aleutian Standard Time'],
            '-9.5'  => ['abbreviation' => 'MART',  'name' => 'Taiohae, Marquesas Islands Time'],
            '-9'    => ['abbreviation' => 'AKST',  'name' => 'Alaska Standard Time'],
            '-8'    => ['abbreviation' => 'PST',   'name' => 'Pacific Standard Time'],
            '-7'    => ['abbreviation' => 'MST',   'name' => 'Mountain Standard Time'],
            '-6'    => ['abbreviation' => 'CST',   'name' => 'Central Standard Time'],
            '-5'    => ['abbreviation' => 'EST',   'name' => 'Eastern Standard Time'],
            '-4.5'  => ['abbreviation' => 'VET',   'name' => 'Venezuelan Standard Time'],
            '-4'    => ['abbreviation' => 'AST',   'name' => 'Atlantic Standard Time'],
            '-3.5'  => ['abbreviation' => 'NST',   'name' => 'Newfoundland Standard Time'],
            '-3'    => ['abbreviation' => 'WGT',   'name' => 'West Greenland Time'],
            '-2'    => ['abbreviation' => 'MAT',   'name' => 'Mid Atlantic Time'],
            '-1'    => ['abbreviation' => 'EGT',   'name' => 'East Greenland Time'],
            '0'     => ['abbreviation' => 'GMT',   'name' => 'Greenwich Mean Time'],
            '1'     => ['abbreviation' => 'CET',   'name' => 'Central European Time'],
            '2'     => ['abbreviation' => 'EET',   'name' => 'Eastern European Time'],
            '3'     => ['abbreviation' => 'EAT',   'name' => 'East Africa Time'],
            '3.5'   => ['abbreviation' => 'IRST',  'name' => 'Iran Standard Time'],
            '4'     => ['abbreviation' => 'GET',   'name' => 'Georgia Standard Time'],
            '4.5'   => ['abbreviation' => 'AFT',   'name' => 'Afghanistan Time'],
            '5'     => ['abbreviation' => 'PKT',   'name' => 'Pakistan Standard Time'],
            '5.5'   => ['abbreviation' => 'IST',   'name' => 'India Standard Time'],
            '5.75'  => ['abbreviation' => 'NPT',   'name' => 'Nepal Time'],
            '6'     => ['abbreviation' => 'BST',   'name' => 'Bangladesh Standard Time'],
            '6.5'   => ['abbreviation' => 'MMT',   'name' => 'Myanmar Time'],
            '7'     => ['abbreviation' => 'ICT',   'name' => 'Indochina Time'],
            '8'     => ['abbreviation' => 'HKT',   'name' => 'Hong Kong Time'],
            '8.75'  => ['abbreviation' => 'ULAT',  'name' => 'Ulaanbaatar Time'],
            '9'     => ['abbreviation' => 'JST',   'name' => 'Japan Standard Time'],
            '9.5'   => ['abbreviation' => 'ACST',  'name' => 'Australian Central Standard Time'],
            '10'    => ['abbreviation' => 'AEST',  'name' => 'Australian Eastern Standard Time'],
            '10.5'  => ['abbreviation' => 'LHST',  'name' => 'Lord Howe Standard Time'],
            '11'    => ['abbreviation' => 'NCT',   'name' => 'New Caledonia Time'],
            '11.5'  => ['abbreviation' => 'NFT',   'name' => 'Norfolk Time'],
            '12'    => ['abbreviation' => 'NZST',  'name' => 'New Zealand Standard Time'],
            '12.75' => ['abbreviation' => 'CHAST', 'name' => 'Chatham Island Standard Time'],
            '13'    => ['abbreviation' => 'TOT',   'name' => 'Tonga Time'],
            '14'    => ['abbreviation' => 'LINT',  'name' => 'Line Islands Time'],
        ];

        if (isset($abbreviations[$timezone])) {
            return $abbreviations[$timezone];
        }

        // IANA zone (post-migration): derive the current abbreviation and name.
        if (!$timezone) {
            return null;
        }
        try {
            $now = new \DateTime('now', new \DateTimeZone($timezone));
            return ['abbreviation' => $now->format('T'), 'name' => $timezone];
        } catch (\Exception $e) {
            return null;
        }
    }
}
