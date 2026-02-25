<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for usage setup
**/
class Migration20131112130740ComUsage extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // Get stats DB object
        $config     = $this->getParams('com_usage');
        $siteConfig = \App::get('config');

        $options['driver']   = $config->get('statsDBDriver');
        $options['host']     = $config->get('statsDBHost');
        $options['user']     = $config->get('statsDBUsername');
        $options['password'] = $config->get('statsDBPassword');
        $options['database'] = $config->get('statsDBDatabase');

        if (empty($options['driver'])) {
            $options['driver']   = $siteConfig->get('dbtype');
        }
        if (empty($options['host'])) {
            $options['host']     = $siteConfig->get('host');
        }
        if (empty($options['user'])) {
            $options['user']     = $siteConfig->get('user');
        }
        if (empty($options['password'])) {
            $options['password'] = $siteConfig->get('password');
        }
        if (empty($options['database'])) {
            $options['database'] = $siteConfig->get('db') . '_metrics';
        }

        $originalDriver    = $options['driver'];
        $options['driver'] = 'pdo';

        try {
            $statsDb = \Hubzero\Database\Driver::getInstance($options);
        } catch (\Exception $e) {
            // Fail silently
            return true;
        }

        $options['driver'] = $originalDriver;

        if ($schema->tableExists('#__extensions')) {
            $result = $this->db->getQuery(true)
                ->select('params')
                ->from('#__extensions')
                ->where('element', '=', 'com_usage')
                ->value('params');

            $params = (array) json_decode($result);
        } else {
            $result = $this->db->getQuery(true)
                ->select('params')
                ->from('#__plugins')
                ->where('element', '=', 'com_usage')
                ->value('params');

            $params = array();

            if (!empty($result)) {
                $ar = explode("\n", $result);

                foreach ($ar as $a) {
                    $a = trim($a);
                    if (empty($a)) {
                        continue;
                    }

                    $ar2 = explode("=", $a, 2);
                    $params[$ar2[0]] = (isset($ar2[1])) ? $ar2[1] : '';
                }
            }
        }

        $params['statsDBDriver']   = $options['driver'];
        $params['statsDBHost']     = $options['host'];
        $params['statsDBUsername'] = $options['user'];
        $params['statsDBPassword'] = $options['password'];
        $params['statsDBDatabase'] = $options['database'];

        if ($schema->tableExists('#__extensions')) {
            $params = json_encode($params);

            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set(['params' => $params])
                ->where('element', '=', 'com_usage')
                ->execute();
        } else {
            $p = '';
            foreach ($params as $k => $v) {
                $p .= "{$k}={$v}\n";
            }

            $params = $p;

            $this->db->getQuery(true)
                ->update('#__plugins')
                ->set(['params' => $params])
                ->where('element', '=', 'com_usage')
                ->execute();
        }

        if (!$statsDb->schema()->tableExists('countries')) {
            try {
                $statsDb->schema()->createTable('countries')
                    ->string('code', 4)->default('')
                    ->string('name', 128)->default('')
                    ->primaryKey('code')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $rows = array (
                  0 =>
                  array (
                    'code' => 'CN',
                    'name' => 'CHINA',
                  ),
                  1 =>
                  array (
                    'code' => 'AU',
                    'name' => 'AUSTRALIA',
                  ),
                  2 =>
                  array (
                    'code' => 'JP',
                    'name' => 'JAPAN',
                  ),
                  3 =>
                  array (
                    'code' => 'TH',
                    'name' => 'THAILAND',
                  ),
                  4 =>
                  array (
                    'code' => 'IN',
                    'name' => 'INDIA',
                  ),
                  5 =>
                  array (
                    'code' => 'MY',
                    'name' => 'MALAYSIA',
                  ),
                  6 =>
                  array (
                    'code' => 'KR',
                    'name' => 'KOREA, REPUBLIC OF',
                  ),
                  7 =>
                  array (
                    'code' => 'HK',
                    'name' => 'HONG KONG',
                  ),
                  8 =>
                  array (
                    'code' => 'TW',
                    'name' => 'TAIWAN',
                  ),
                  9 =>
                  array (
                    'code' => 'PH',
                    'name' => 'PHILIPPINES',
                  ),
                  10 =>
                  array (
                    'code' => 'VN',
                    'name' => 'VIET NAM',
                  ),
                  11 =>
                  array (
                    'code' => 'FR',
                    'name' => 'FRANCE',
                  ),
                  12 =>
                  array (
                    'code' => 'UK',
                    'name' => 'UNITED KINGDOM',
                  ),
                  13 =>
                  array (
                    'code' => 'DE',
                    'name' => 'GERMANY',
                  ),
                  14 =>
                  array (
                    'code' => 'SE',
                    'name' => 'SWEDEN',
                  ),
                  15 =>
                  array (
                    'code' => 'IT',
                    'name' => 'ITALY',
                  ),
                  16 =>
                  array (
                    'code' => 'ES',
                    'name' => 'SPAIN',
                  ),
                  17 =>
                  array (
                    'code' => 'AT',
                    'name' => 'AUSTRIA',
                  ),
                  18 =>
                  array (
                    'code' => 'NL',
                    'name' => 'NETHERLANDS',
                  ),
                  19 =>
                  array (
                    'code' => 'AE',
                    'name' => 'UNITED ARAB EMIRATES',
                  ),
                  20 =>
                  array (
                    'code' => 'IL',
                    'name' => 'ISRAEL',
                  ),
                  21 =>
                  array (
                    'code' => 'UA',
                    'name' => 'UKRAINE',
                  ),
                  22 =>
                  array (
                    'code' => 'CZ',
                    'name' => 'CZECH REPUBLIC',
                  ),
                  23 =>
                  array (
                    'code' => 'RU',
                    'name' => 'RUSSIAN FEDERATION',
                  ),
                  24 =>
                  array (
                    'code' => 'KZ',
                    'name' => 'KAZAKHSTAN',
                  ),
                  25 =>
                  array (
                    'code' => 'PT',
                    'name' => 'PORTUGAL',
                  ),
                  26 =>
                  array (
                    'code' => 'GR',
                    'name' => 'GREECE',
                  ),
                  27 =>
                  array (
                    'code' => 'SA',
                    'name' => 'SAUDI ARABIA',
                  ),
                  28 =>
                  array (
                    'code' => 'DK',
                    'name' => 'DENMARK',
                  ),
                  29 =>
                  array (
                    'code' => 'IR',
                    'name' => 'IRAN, ISLAMIC REPUBLIC OF',
                  ),
                  30 =>
                  array (
                    'code' => 'NO',
                    'name' => 'NORWAY',
                  ),
                  31 =>
                  array (
                    'code' => 'US',
                    'name' => 'UNITED STATES',
                  ),
                  32 =>
                  array (
                    'code' => 'CA',
                    'name' => 'CANADA',
                  ),
                  33 =>
                  array (
                    'code' => 'MX',
                    'name' => 'MEXICO',
                  ),
                  34 =>
                  array (
                    'code' => 'BM',
                    'name' => 'BERMUDA',
                  ),
                  35 =>
                  array (
                    'code' => 'VI',
                    'name' => 'VIRGIN ISLANDS, U.S.',
                  ),
                  36 =>
                  array (
                    'code' => 'PR',
                    'name' => 'PUERTO RICO',
                  ),
                  37 =>
                  array (
                    'code' => 'NZ',
                    'name' => 'NEW ZEALAND',
                  ),
                  38 =>
                  array (
                    'code' => 'SG',
                    'name' => 'SINGAPORE',
                  ),
                  39 =>
                  array (
                    'code' => 'ID',
                    'name' => 'INDONESIA',
                  ),
                  40 =>
                  array (
                    'code' => 'NP',
                    'name' => 'NEPAL',
                  ),
                  41 =>
                  array (
                    'code' => 'PG',
                    'name' => 'PAPUA NEW GUINEA',
                  ),
                  42 =>
                  array (
                    'code' => 'PK',
                    'name' => 'PAKISTAN',
                  ),
                  43 =>
                  array (
                    'code' => 'CH',
                    'name' => 'SWITZERLAND',
                  ),
                  44 =>
                  array (
                    'code' => 'IE',
                    'name' => 'IRELAND',
                  ),
                  45 =>
                  array (
                    'code' => 'BS',
                    'name' => 'BAHAMAS',
                  ),
                  46 =>
                  array (
                    'code' => 'VC',
                    'name' => 'SAINT VINCENT AND THE GRENADINES',
                  ),
                  47 =>
                  array (
                    'code' => 'AR',
                    'name' => 'ARGENTINA',
                  ),
                  48 =>
                  array (
                    'code' => 'UY',
                    'name' => 'URUGUAY',
                  ),
                  49 =>
                  array (
                    'code' => 'DM',
                    'name' => 'DOMINICA',
                  ),
                  50 =>
                  array (
                    'code' => 'BD',
                    'name' => 'BANGLADESH',
                  ),
                  51 =>
                  array (
                    'code' => 'TK',
                    'name' => 'TOKELAU',
                  ),
                  52 =>
                  array (
                    'code' => 'KH',
                    'name' => 'CAMBODIA',
                  ),
                  53 =>
                  array (
                    'code' => 'MO',
                    'name' => 'MACAO',
                  ),
                  54 =>
                  array (
                    'code' => 'MV',
                    'name' => 'MALDIVES',
                  ),
                  55 =>
                  array (
                    'code' => 'AF',
                    'name' => 'AFGHANISTAN',
                  ),
                  56 =>
                  array (
                    'code' => 'NC',
                    'name' => 'NEW CALEDONIA',
                  ),
                  57 =>
                  array (
                    'code' => 'FJ',
                    'name' => 'FIJI',
                  ),
                  58 =>
                  array (
                    'code' => 'MN',
                    'name' => 'MONGOLIA',
                  ),
                  59 =>
                  array (
                    'code' => 'WF',
                    'name' => 'WALLIS AND FUTUNA',
                  ),
                  60 =>
                  array (
                    'code' => 'PL',
                    'name' => 'POLAND',
                  ),
                  61 =>
                  array (
                    'code' => 'RO',
                    'name' => 'ROMANIA',
                  ),
                  62 =>
                  array (
                    'code' => 'TR',
                    'name' => 'TURKEY',
                  ),
                  63 =>
                  array (
                    'code' => 'SK',
                    'name' => 'SLOVAKIA',
                  ),
                  64 =>
                  array (
                    'code' => 'MK',
                    'name' => 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF',
                  ),
                  65 =>
                  array (
                    'code' => 'FI',
                    'name' => 'FINLAND',
                  ),
                  66 =>
                  array (
                    'code' => 'AM',
                    'name' => 'ARMENIA',
                  ),
                  67 =>
                  array (
                    'code' => 'SI',
                    'name' => 'SLOVENIA',
                  ),
                  68 =>
                  array (
                    'code' => 'SY',
                    'name' => 'SYRIAN ARAB REPUBLIC',
                  ),
                  69 =>
                  array (
                    'code' => 'LI',
                    'name' => 'LIECHTENSTEIN',
                  ),
                  70 =>
                  array (
                    'code' => 'QA',
                    'name' => 'QATAR',
                  ),
                  71 =>
                  array (
                    'code' => 'BE',
                    'name' => 'BELGIUM',
                  ),
                  72 =>
                  array (
                    'code' => 'NG',
                    'name' => 'NIGERIA',
                  ),
                  73 =>
                  array (
                    'code' => 'BG',
                    'name' => 'BULGARIA',
                  ),
                  74 =>
                  array (
                    'code' => 'IS',
                    'name' => 'ICELAND',
                  ),
                  75 =>
                  array (
                    'code' => 'AL',
                    'name' => 'ALBANIA',
                  ),
                  76 =>
                  array (
                    'code' => 'CY',
                    'name' => 'CYPRUS',
                  ),
                  77 =>
                  array (
                    'code' => 'LU',
                    'name' => 'LUXEMBOURG',
                  ),
                  78 =>
                  array (
                    'code' => 'HU',
                    'name' => 'HUNGARY',
                  ),
                  79 =>
                  array (
                    'code' => 'EE',
                    'name' => 'ESTONIA',
                  ),
                  80 =>
                  array (
                    'code' => 'BY',
                    'name' => 'BELARUS',
                  ),
                  81 =>
                  array (
                    'code' => 'LV',
                    'name' => 'LATVIA',
                  ),
                  82 =>
                  array (
                    'code' => 'IQ',
                    'name' => 'IRAQ',
                  ),
                  83 =>
                  array (
                    'code' => 'KG',
                    'name' => 'KYRGYZSTAN',
                  ),
                  84 =>
                  array (
                    'code' => 'MD',
                    'name' => 'MOLDOVA, REPUBLIC OF',
                  ),
                  85 =>
                  array (
                    'code' => 'YE',
                    'name' => 'YEMEN',
                  ),
                  86 =>
                  array (
                    'code' => 'LT',
                    'name' => 'LITHUANIA',
                  ),
                  87 =>
                  array (
                    'code' => 'HR',
                    'name' => 'CROATIA',
                  ),
                  88 =>
                  array (
                    'code' => 'BA',
                    'name' => 'BOSNIA AND HERZEGOVINA',
                  ),
                  89 =>
                  array (
                    'code' => 'UZ',
                    'name' => 'UZBEKISTAN',
                  ),
                  90 =>
                  array (
                    'code' => 'GE',
                    'name' => 'GEORGIA',
                  ),
                  91 =>
                  array (
                    'code' => 'AZ',
                    'name' => 'AZERBAIJAN',
                  ),
                  92 =>
                  array (
                    'code' => 'JE',
                    'name' => 'JERSEY',
                  ),
                  93 =>
                  array (
                    'code' => 'SM',
                    'name' => 'SAN MARINO',
                  ),
                  94 =>
                  array (
                    'code' => 'BR',
                    'name' => 'BRAZIL',
                  ),
                  95 =>
                  array (
                    'code' => 'SJ',
                    'name' => 'SVALBARD AND JAN MAYEN',
                  ),
                  96 =>
                  array (
                    'code' => 'ZA',
                    'name' => 'SOUTH AFRICA',
                  ),
                  97 =>
                  array (
                    'code' => 'VE',
                    'name' => 'VENEZUELA, BOLIVARIAN REPUBLIC OF',
                  ),
                  98 =>
                  array (
                    'code' => 'CO',
                    'name' => 'COLOMBIA',
                  ),
                  99 =>
                  array (
                    'code' => 'EG',
                    'name' => 'EGYPT',
                  ),
                  100 =>
                  array (
                    'code' => 'CL',
                    'name' => 'CHILE',
                  ),
                  101 =>
                  array (
                    'code' => 'DZ',
                    'name' => 'ALGERIA',
                  ),
                  102 =>
                  array (
                    'code' => 'PE',
                    'name' => 'PERU',
                  ),
                  103 =>
                  array (
                    'code' => 'KW',
                    'name' => 'KUWAIT',
                  ),
                  104 =>
                  array (
                    'code' => 'MA',
                    'name' => 'MOROCCO',
                  ),
                  105 =>
                  array (
                    'code' => 'AO',
                    'name' => 'ANGOLA',
                  ),
                  106 =>
                  array (
                    'code' => 'LY',
                    'name' => 'LIBYAN ARAB JAMAHIRIYA',
                  ),
                  107 =>
                  array (
                    'code' => 'SD',
                    'name' => 'SUDAN',
                  ),
                  108 =>
                  array (
                    'code' => 'EC',
                    'name' => 'ECUADOR',
                  ),
                  109 =>
                  array (
                    'code' => 'OM',
                    'name' => 'OMAN',
                  ),
                  110 =>
                  array (
                    'code' => 'DO',
                    'name' => 'DOMINICAN REPUBLIC',
                  ),
                  111 =>
                  array (
                    'code' => 'LK',
                    'name' => 'SRI LANKA',
                  ),
                  112 =>
                  array (
                    'code' => 'TN',
                    'name' => 'TUNISIA',
                  ),
                  113 =>
                  array (
                    'code' => 'GT',
                    'name' => 'GUATEMALA',
                  ),
                  114 =>
                  array (
                    'code' => 'LB',
                    'name' => 'LEBANON',
                  ),
                  115 =>
                  array (
                    'code' => 'RS',
                    'name' => 'SERBIA',
                  ),
                  116 =>
                  array (
                    'code' => 'MM',
                    'name' => 'MYANMAR',
                  ),
                  117 =>
                  array (
                    'code' => 'CR',
                    'name' => 'COSTA RICA',
                  ),
                  118 =>
                  array (
                    'code' => 'KE',
                    'name' => 'KENYA',
                  ),
                  119 =>
                  array (
                    'code' => 'ET',
                    'name' => 'ETHIOPIA',
                  ),
                  120 =>
                  array (
                    'code' => 'PA',
                    'name' => 'PANAMA',
                  ),
                  121 =>
                  array (
                    'code' => 'JO',
                    'name' => 'JORDAN',
                  ),
                  122 =>
                  array (
                    'code' => 'TZ',
                    'name' => 'TANZANIA, UNITED REPUBLIC OF',
                  ),
                  123 =>
                  array (
                    'code' => 'CI',
                    'name' => 'COTE DIVOIRE',
                  ),
                  124 =>
                  array (
                    'code' => 'CM',
                    'name' => 'CAMEROON',
                  ),
                  125 =>
                  array (
                    'code' => 'SV',
                    'name' => 'EL SALVADOR',
                  ),
                  126 =>
                  array (
                    'code' => 'BH',
                    'name' => 'BAHRAIN',
                  ),
                  127 =>
                  array (
                    'code' => 'TT',
                    'name' => 'TRINIDAD AND TOBAGO',
                  ),
                  128 =>
                  array (
                    'code' => 'BO',
                    'name' => 'BOLIVIA, PLURINATIONAL STATE OF',
                  ),
                  129 =>
                  array (
                    'code' => 'GH',
                    'name' => 'GHANA',
                  ),
                  130 =>
                  array (
                    'code' => 'PY',
                    'name' => 'PARAGUAY',
                  ),
                  131 =>
                  array (
                    'code' => 'UG',
                    'name' => 'UGANDA',
                  ),
                  132 =>
                  array (
                    'code' => 'ZM',
                    'name' => 'ZAMBIA',
                  ),
                  133 =>
                  array (
                    'code' => 'HN',
                    'name' => 'HONDURAS',
                  ),
                  134 =>
                  array (
                    'code' => 'GQ',
                    'name' => 'EQUATORIAL GUINEA',
                  ),
                  135 =>
                  array (
                    'code' => 'JM',
                    'name' => 'JAMAICA',
                  ),
                  136 =>
                  array (
                    'code' => 'AX',
                    'name' => 'ALAND ISLANDS',
                  ),
                  137 =>
                  array (
                    'code' => 'AD',
                    'name' => 'ANDORRA',
                  ),
                  138 =>
                  array (
                    'code' => 'FO',
                    'name' => 'FAROE ISLANDS',
                  ),
                  139 =>
                  array (
                    'code' => 'GI',
                    'name' => 'GIBRALTAR',
                  ),
                  140 =>
                  array (
                    'code' => 'GL',
                    'name' => 'GREENLAND',
                  ),
                  141 =>
                  array (
                    'code' => 'GG',
                    'name' => 'GUERNSEY',
                  ),
                  142 =>
                  array (
                    'code' => 'VA',
                    'name' => 'HOLY SEE (VATICAN CITY STATE',
                  ),
                  143 =>
                  array (
                    'code' => 'IM',
                    'name' => 'ISLE OF MAN',
                  ),
                  144 =>
                  array (
                    'code' => 'MT',
                    'name' => 'MALTA',
                  ),
                  145 =>
                  array (
                    'code' => 'MC',
                    'name' => 'MONACO',
                  ),
                  146 =>
                  array (
                    'code' => 'ME',
                    'name' => 'MONTENEGRO',
                  ),
                  147 =>
                  array (
                    'code' => 'PS',
                    'name' => 'PALESTINIAN TERRITORY, OCCUPIED',
                  ),
                  148 =>
                  array (
                    'code' => 'TJ',
                    'name' => 'TAJIKISTAN',
                  ),
                  149 =>
                  array (
                    'code' => 'TM',
                    'name' => 'TURKMENISTAN',
                  ),
                  150 =>
                  array (
                    'code' => 'CD',
                    'name' => 'CONGO, THE DEMOCRATIC REPUBLIC OF THE',
                  ),
                  151 =>
                  array (
                    'code' => 'AN',
                    'name' => 'NETHERLANDS ANTILLES',
                  ),
                  152 =>
                  array (
                    'code' => 'BZ',
                    'name' => 'BELIZE',
                  ),
                  153 =>
                  array (
                    'code' => 'SN',
                    'name' => 'SENEGAL',
                  ),
                  154 =>
                  array (
                    'code' => 'MG',
                    'name' => 'MADAGASCAR',
                  ),
                  155 =>
                  array (
                    'code' => 'NA',
                    'name' => 'NAMIBIA',
                  ),
                  156 =>
                  array (
                    'code' => 'MW',
                    'name' => 'MALAWI',
                  ),
                  157 =>
                  array (
                    'code' => 'GA',
                    'name' => 'GABON',
                  ),
                  158 =>
                  array (
                    'code' => 'ML',
                    'name' => 'MALI',
                  ),
                  159 =>
                  array (
                    'code' => 'BJ',
                    'name' => 'BENIN',
                  ),
                  160 =>
                  array (
                    'code' => 'TD',
                    'name' => 'CHAD',
                  ),
                  161 =>
                  array (
                    'code' => 'BW',
                    'name' => 'BOTSWANA',
                  ),
                  162 =>
                  array (
                    'code' => 'CV',
                    'name' => 'CAPE VERDE',
                  ),
                  163 =>
                  array (
                    'code' => 'RW',
                    'name' => 'RWANDA',
                  ),
                  164 =>
                  array (
                    'code' => 'CG',
                    'name' => 'CONGO',
                  ),
                  165 =>
                  array (
                    'code' => 'MZ',
                    'name' => 'MOZAMBIQUE',
                  ),
                  166 =>
                  array (
                    'code' => 'GM',
                    'name' => 'GAMBIA',
                  ),
                  167 =>
                  array (
                    'code' => 'LS',
                    'name' => 'LESOTHO',
                  ),
                  168 =>
                  array (
                    'code' => 'MU',
                    'name' => 'MAURITIUS',
                  ),
                  169 =>
                  array (
                    'code' => 'ZW',
                    'name' => 'ZIMBABWE',
                  ),
                  170 =>
                  array (
                    'code' => 'BF',
                    'name' => 'BURKINA FASO',
                  ),
                  171 =>
                  array (
                    'code' => 'SL',
                    'name' => 'SIERRA LEONE',
                  ),
                  172 =>
                  array (
                    'code' => 'SO',
                    'name' => 'SOMALIA',
                  ),
                  173 =>
                  array (
                    'code' => 'NE',
                    'name' => 'NIGER',
                  ),
                  174 =>
                  array (
                    'code' => 'CF',
                    'name' => 'CENTRAL AFRICAN REPUBLIC',
                  ),
                  175 =>
                  array (
                    'code' => 'SZ',
                    'name' => 'SWAZILAND',
                  ),
                  176 =>
                  array (
                    'code' => 'TG',
                    'name' => 'TOGO',
                  ),
                  177 =>
                  array (
                    'code' => 'BI',
                    'name' => 'BURUNDI',
                  ),
                  178 =>
                  array (
                    'code' => 'SC',
                    'name' => 'SEYCHELLES',
                  ),
                  179 =>
                  array (
                    'code' => 'GN',
                    'name' => 'GUINEA',
                  ),
                  180 =>
                  array (
                    'code' => 'GW',
                    'name' => 'GUINEA-BISSAU',
                  ),
                  181 =>
                  array (
                    'code' => 'LR',
                    'name' => 'LIBERIA',
                  ),
                  182 =>
                  array (
                    'code' => 'MR',
                    'name' => 'MAURITANIA',
                  ),
                  183 =>
                  array (
                    'code' => 'DJ',
                    'name' => 'DJIBOUTI',
                  ),
                  184 =>
                  array (
                    'code' => 'RE',
                    'name' => 'REUNION',
                  ),
                  185 =>
                  array (
                    'code' => 'NI',
                    'name' => 'NICARAGUA',
                  ),
                  186 =>
                  array (
                    'code' => 'CU',
                    'name' => 'CUBA',
                  ),
                  187 =>
                  array (
                    'code' => 'KY',
                    'name' => 'CAYMAN ISLANDS',
                  ),
                  188 =>
                  array (
                    'code' => 'VG',
                    'name' => 'VIRGIN ISLANDS, BRITISH',
                  ),
                  189 =>
                  array (
                    'code' => 'MH',
                    'name' => 'MARSHALL ISLANDS',
                  ),
                  190 =>
                  array (
                    'code' => 'AQ',
                    'name' => 'ANTARCTICA',
                  ),
                  191 =>
                  array (
                    'code' => 'BB',
                    'name' => 'BARBADOS',
                  ),
                  192 =>
                  array (
                    'code' => 'AW',
                    'name' => 'ARUBA',
                  ),
                  193 =>
                  array (
                    'code' => 'AI',
                    'name' => 'ANGUILLA',
                  ),
                  194 =>
                  array (
                    'code' => 'KN',
                    'name' => 'SAINT KITTS AND NEVIS',
                  ),
                  195 =>
                  array (
                    'code' => 'GD',
                    'name' => 'GRENADA',
                  ),
                  196 =>
                  array (
                    'code' => 'LC',
                    'name' => 'SAINT LUCIA',
                  ),
                  197 =>
                  array (
                    'code' => 'MS',
                    'name' => 'MONTSERRAT',
                  ),
                  198 =>
                  array (
                    'code' => 'TC',
                    'name' => 'TURKS AND CAICOS ISLANDS',
                  ),
                  199 =>
                  array (
                    'code' => 'AG',
                    'name' => 'ANTIGUA AND BARBUDA',
                  ),
                  200 =>
                  array (
                    'code' => 'TV',
                    'name' => 'TUVALU',
                  ),
                  201 =>
                  array (
                    'code' => 'PF',
                    'name' => 'FRENCH POLYNESIA',
                  ),
                  202 =>
                  array (
                    'code' => 'SB',
                    'name' => 'SOLOMON ISLANDS',
                  ),
                  203 =>
                  array (
                    'code' => 'VU',
                    'name' => 'VANUATU',
                  ),
                  204 =>
                  array (
                    'code' => 'ER',
                    'name' => 'ERITREA',
                  ),
                  205 =>
                  array (
                    'code' => 'HT',
                    'name' => 'HAITI',
                  ),
                  206 =>
                  array (
                    'code' => 'SH',
                    'name' => 'SAINT HELENA, ASCENSION AND TRISTAN DA CUNHA',
                  ),
                  207 =>
                  array (
                    'code' => 'FM',
                    'name' => 'MICRONESIA, FEDERATED STATES OF',
                  ),
                  208 =>
                  array (
                    'code' => 'EH',
                    'name' => 'WESTERN SAHARA',
                  ),
                  209 =>
                  array (
                    'code' => 'CX',
                    'name' => 'CHRISTMAS ISLAND',
                  ),
                  210 =>
                  array (
                    'code' => 'LA',
                    'name' => 'LAO PEOPLES DEMOCRATIC REPUBLIC',
                  ),
                  211 =>
                  array (
                    'code' => 'IO',
                    'name' => 'BRITISH INDIAN OCEAN TERRITORY',
                  ),
                  212 =>
                  array (
                    'code' => 'GU',
                    'name' => 'GUAM',
                  ),
                  213 =>
                  array (
                    'code' => 'WS',
                    'name' => 'SAMOA',
                  ),
                  214 =>
                  array (
                    'code' => 'SR',
                    'name' => 'SURINAME',
                  ),
                  215 =>
                  array (
                    'code' => 'CK',
                    'name' => 'COOK ISLANDS',
                  ),
                  216 =>
                  array (
                    'code' => 'KI',
                    'name' => 'KIRIBATI',
                  ),
                  217 =>
                  array (
                    'code' => 'NU',
                    'name' => 'NIUE',
                  ),
                  218 =>
                  array (
                    'code' => 'TO',
                    'name' => 'TONGA',
                  ),
                  219 =>
                  array (
                    'code' => 'TF',
                    'name' => 'FRENCH SOUTHERN TERRITORIES',
                  ),
                  220 =>
                  array (
                    'code' => 'MQ',
                    'name' => 'MARTINIQUE',
                  ),
                  221 =>
                  array (
                    'code' => 'YT',
                    'name' => 'MAYOTTE',
                  ),
                  222 =>
                  array (
                    'code' => 'NF',
                    'name' => 'NORFOLK ISLAND',
                  ),
                  223 =>
                  array (
                    'code' => 'AS',
                    'name' => 'AMERICAN SAMOA',
                  ),
                  224 =>
                  array (
                    'code' => 'BN',
                    'name' => 'BRUNEI DARUSSALAM',
                  ),
                  225 =>
                  array (
                    'code' => 'BT',
                    'name' => 'BHUTAN',
                  ),
                  226 =>
                  array (
                    'code' => 'BV',
                    'name' => 'BOUVET ISLAND',
                  ),
                  227 =>
                  array (
                    'code' => 'CC',
                    'name' => 'COCOS (KEELING',
                  ),
                  228 =>
                  array (
                    'code' => 'FK',
                    'name' => 'FALKLAND ISLANDS (MALVINAS',
                  ),
                  229 =>
                  array (
                    'code' => 'GF',
                    'name' => 'FRENCH GUIANA',
                  ),
                  230 =>
                  array (
                    'code' => 'GP',
                    'name' => 'GUADELOUPE',
                  ),
                  231 =>
                  array (
                    'code' => 'GS',
                    'name' => 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS',
                  ),
                  232 =>
                  array (
                    'code' => 'GY',
                    'name' => 'GUYANA',
                  ),
                  233 =>
                  array (
                    'code' => 'HM',
                    'name' => 'HEARD ISLAND AND MCDONALD ISLANDS',
                  ),
                  234 =>
                  array (
                    'code' => 'MP',
                    'name' => 'NORTHERN MARIANA ISLANDS',
                  ),
                  235 =>
                  array (
                    'code' => 'PW',
                    'name' => 'PALAU',
                  ),
                  236 =>
                  array (
                    'code' => 'UM',
                    'name' => 'UNITED STATES MINOR OUTLYING ISLANDS',
                  ),
                  237 =>
                  array (
                    'code' => 'KP',
                    'name' => 'KOREA, DEMOCRATIC PEOPLES REPUBLIC OF',
                  ),
                  238 =>
                  array (
                    'code' => 'NR',
                    'name' => 'NAURU',
                  ),
                  239 =>
                  array (
                    'code' => 'PM',
                    'name' => 'SAINT PIERRE AND MIQUELON',
                  ),
                  240 =>
                  array (
                    'code' => 'MF',
                    'name' => 'SAINT MARTIN',
                  ),
                  241 =>
                  array (
                    'code' => 'KM',
                    'name' => 'COMOROS',
                  ),
                  242 =>
                  array (
                    'code' => 'TL',
                    'name' => 'TIMOR-LESTE',
                  ),
                  243 =>
                  array (
                    'code' => 'ST',
                    'name' => 'SAO TOME AND PRINCIPE',
                  ),
                );
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('sessionlog_metrics')) {
            try {
                $statsDb->schema()->createTable('sessionlog_metrics')
                    ->unsignedBigInteger('id', ['autoIncrement' => true])
                    ->unsignedBigInteger('sessnum')
                    ->string('user', 150)->default('')
                    ->string('ip', 15)->default('')
                    ->datetime('start')->default('0000-00-00 00:00:00')
                    ->string('appname', 150)->default('')
                    ->tinyText('host')->nullable()
                    ->tinyText('domain')->nullable()
                    ->tinyText('orgtype')->nullable()
                    ->char('countryresident', 2)->nullable()
                    ->char('countrycitizen', 2)->nullable()
                    ->char('ipcountry', 2)->nullable()
                    ->primaryKey('sessnum')
                    ->uniqueIndex('id', 'id')
                    ->index('user', 'user')
                    ->index('start', 'start')
                    ->index('appname', 'appname')
                    ->index('countryresident', 'countryresident')
                    ->index('countrycitizen', 'countrycitizen')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('#__xprofiles_metrics')) {
            try {
                $statsDb->schema()->createTable('#__xprofiles_metrics')
                    ->integer('uidNumber')
                    ->string('name', 255)->default('')
                    ->string('username', 150)->default('')
                    ->string('email', 100)->default('')
                    ->datetime('registerDate')->default('0000-00-00 00:00:00')
                    ->string('gidNumber', 11)->default('')
                    ->string('homeDirectory', 255)->default('')
                    ->string('loginShell', 255)->default('')
                    ->string('ftpShell', 255)->default('')
                    ->string('userPassword', 255)->default('')
                    ->string('gid', 255)->default('')
                    ->string('orgtype', 255)->default('')
                    ->string('organization', 255)->default('')
                    ->char('countryresident', 2)->default('')
                    ->char('countryorigin', 2)->default('')
                    ->string('gender', 255)->default('')
                    ->string('url', 255)->default('')
                    ->text('reason')
                    ->integer('mailPreferenceOption')->default(0)
                    ->integer('usageAgreement')->default(0)
                    ->integer('jobsAllowed')->default(0)
                    ->datetime('modifiedDate')->default('0000-00-00 00:00:00')
                    ->integer('emailConfirmed')->default(0)
                    ->string('regIP', 255)->default('')
                    ->string('regHost', 255)->default('')
                    ->string('nativeTribe', 255)->default('')
                    ->string('phone', 255)->default('')
                    ->string('proxyPassword', 255)->default('')
                    ->string('proxyUidNumber', 255)->default('')
                    ->string('givenName', 255)->default('')
                    ->string('middleName', 255)->default('')
                    ->string('surname', 255)->default('')
                    ->string('picture', 255)->default('')
                    ->integer('vip')->default(0)
                    ->tinyInteger('public')->default(0)
                    ->text('params')
                    ->text('note')
                    ->integer('shadowExpire')->nullable()
                    ->primaryKey('uidNumber')
                    ->index('username', 'username')
                    ->index('orgtype', 'orgtype')
                    ->index('countryresident', 'countryresident')
                    ->index('countryorigin', 'countryorigin')
                    ->index('registerDate', 'registerDate')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('summary_andmore_vals')) {
            try {
                $statsDb->schema()->createTable('summary_andmore_vals')
                    ->tinyInteger('rowid')->default(0)
                    ->tinyInteger('colid')->default(0)
                    ->datetime('datetime')->default('0000-00-00 00:00:00')
                    ->tinyInteger('period')->default(1)
                    ->bigInteger('value')->default(0)
                    ->tinyInteger('valfmt')->default(0)
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('summary_misc_vals')) {
            try {
                $statsDb->schema()->createTable('summary_misc_vals')
                    ->tinyInteger('rowid')->default(0)
                    ->tinyInteger('colid')->default(0)
                    ->datetime('datetime')->default('0000-00-00 00:00:00')
                    ->tinyInteger('period')->default(1)
                    ->string('value', 200)->default('')
                    ->tinyInteger('valfmt')->default(0)
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('summary_simusage_vals')) {
            try {
                $statsDb->schema()->createTable('summary_simusage_vals')
                    ->tinyInteger('rowid')->default(0)
                    ->tinyInteger('colid')->default(0)
                    ->datetime('datetime')->default('0000-00-00 00:00:00')
                    ->tinyInteger('period')->default(1)
                    ->bigInteger('value')->default(0)
                    ->tinyInteger('valfmt')->default(0)
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('toolstart')) {
            try {
                $statsDb->schema()->createTable('toolstart')
                    ->bigInteger('id', ['autoIncrement' => true])
                    ->bigInteger('sessionid')->nullable()
                    ->datetime('datetime')->default('0000-00-00 00:00:00')
                    ->tinyText('orgtype')->nullable()
                    ->char('countryresident', 2)->nullable()
                    ->char('countrycitizen', 2)->nullable()
                    ->tinyInteger('success')->default(0)
                    ->char('ipcountry', 2)->nullable()
                    ->string('ip', 15)->default('')
                    ->tinyText('host')->nullable()
                    ->string('user', 150)->nullable()
                    ->tinyText('tool')
                    ->integer('pid')->nullable()
                    ->tinyText('domain')->nullable()
                    ->tinyText('filesystem')->nullable()
                    ->tinyText('execunit')->nullable()
                    ->float('walltime')->default(0)
                    ->float('cputime')->default(0)
                    ->tinyText('error')->nullable()
                    ->primaryKey('id')
                    ->index('datetime', 'datetime')
                    ->index('success', 'success')
                    ->index('sessionid', 'sessionid')
                    ->index('ipcountry', 'ipcountry')
                    ->index('countrycitizen', 'countrycitizen')
                    ->index('countryresident', 'countryresident')
                    ->index('ip', 'ip')
                    ->index('user', 'user')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('userlogin')) {
            try {
                $statsDb->schema()->createTable('userlogin')
                    ->bigInteger('id', ['autoIncrement' => true])
                    ->datetime('datetime')->default('0000-00-00 00:00:00')
                    ->string('user', 255)->default('-')
                    ->bigInteger('uidNumber')->default(0)
                    ->string('ip', 15)->default('')
                    ->string('action', 40)->default('')
                    ->primaryKey('id')
                    ->uniqueIndex('userlogin', ['datetime', 'user', 'uidNumber', 'ip', 'action'])
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('web')) {
            try {
                $statsDb->schema()->createTable('web')
                    ->bigInteger('id', ['autoIncrement' => true])
                    ->bigInteger('elementid')->nullable()
                    ->bigInteger('sessionid')->nullable()
                    ->datetime('datetime')->default('0000-00-00 00:00:00')
                    ->char('ipcountry', 2)->nullable()
                    ->tinyText('content')
                    ->tinyText('referrer')->nullable()
                    ->tinyText('useragent')->nullable()
                    ->string('ip', 15)->default('')
                    ->tinyText('host')->nullable()
                    ->tinyText('domain')->nullable()
                    ->integer('uidNumber')->nullable()
                    ->string('apache_pid', 120)->default('')
                    ->string('joomla_sessionid', 120)->default('')
                    ->string('site_cookie', 120)->default('')
                    ->string('auth_type', 120)->default('')
                    ->string('component_name', 120)->default('')
                    ->string('view_name', 120)->default('')
                    ->string('task_name', 120)->default('')
                    ->string('action_name', 120)->default('')
                    ->string('item_name', 120)->default('')
                    ->tinyInteger('dnload')->nullable()
                    ->primaryKey('id')
                    ->index('datetime', 'datetime')
                    ->index('sessionid', 'sessionid')
                    ->index('elementid', 'elementid')
                    ->index('ipcountry', 'ipcountry')
                    ->index('ip', 'ip')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('webhits')) {
            try {
                $statsDb->schema()->createTable('webhits')
                    ->datetime('datetime')->default('0000-00-00 00:00:00')
                    ->bigInteger('hits')->default(0)
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('websessions')) {
            try {
                $statsDb->schema()->createTable('websessions')
                    ->bigInteger('id')->default(0)
                    ->datetime('datetime')->default('0000-00-00 00:00:00')
                    ->char('ipcountry', 2)->default('')
                    ->string('ip', 15)->default('')
                    ->tinyText('host')->nullable()
                    ->tinyText('domain')->nullable()
                    ->bigInteger('duration')->default(0)
                    ->tinyInteger('jobs')->default(0)
                    ->bigInteger('webevents')->default(0)
                    ->primaryKey('id')
                    ->index('datetime', 'datetime')
                    ->index('ipcountry', 'ipcountry')
                    ->index('ip', 'ip')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('domainclass')) {
            try {
                $statsDb->schema()->createTable('domainclass')
                    ->string('domain', 64)->default('')
                    ->tinyInteger('class')->default(0)
                    ->string('country', 4)->default('')
                    ->string('state', 4)->default('')
                    ->tinyText('name')
                    ->primaryKey('domain')
                    ->index('class', 'class')
                    ->index('domain_class', ['domain', 'class'])
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $rows = array (
                  0 =>
                  array (
                    'domain' => 'cnea.gov.ar',
                    'class' => '3',
                    'country' => 'ar',
                    'state' => '',
                    'name' => 'Comision Nacional de la Energia Atomica',
                  ),
                  1 =>
                  array (
                    'domain' => 'surfer.at',
                    'class' => '4',
                    'country' => 'at',
                    'state' => '',
                    'name' => 'Telekabel Wien GmbH, Vienna',
                  ),
                  2 =>
                  array (
                    'domain' => 'teleweb.at',
                    'class' => '4',
                    'country' => 'at',
                    'state' => '',
                    'name' => 'Telekabel Wien GmbH, Wien',
                  ),
                  3 =>
                  array (
                    'domain' => 'univie.ac.at',
                    'class' => '1',
                    'country' => 'at',
                    'state' => '',
                    'name' => 'Universitaet Wien Zentraler Informatikdienst',
                  ),
                  4 =>
                  array (
                    'domain' => 'tuwien.ac.at',
                    'class' => '1',
                    'country' => 'at',
                    'state' => '',
                    'name' => 'Vienna University of Technology',
                  ),
                  5 =>
                  array (
                    'domain' => 'ecu.edu.au',
                    'class' => '1',
                    'country' => 'au',
                    'state' => '',
                    'name' => 'Edith Cowan University, Perth',
                  ),
                  6 =>
                  array (
                    'domain' => 'tu-graz.ac.at',
                    'class' => '1',
                    'country' => 'au',
                    'state' => '',
                    'name' => 'Graz University of Technology, Graz',
                  ),
                  7 =>
                  array (
                    'domain' => 'mq.edu.au',
                    'class' => '1',
                    'country' => 'au',
                    'state' => '',
                    'name' => 'Macquarie University, Sydney',
                  ),
                  8 =>
                  array (
                    'domain' => 'optusnet.com.au',
                    'class' => '4',
                    'country' => 'au',
                    'state' => '',
                    'name' => 'Optus Administration Pty Ltd',
                  ),
                  9 =>
                  array (
                    'domain' => 'ozemail.com.au',
                    'class' => '4',
                    'country' => 'au',
                    'state' => '',
                    'name' => 'OzEmail Pty Ltd',
                  ),
                  10 =>
                  array (
                    'domain' => 'rmit.edu.au',
                    'class' => '1',
                    'country' => 'au',
                    'state' => '',
                    'name' => 'RMIT University, Melbourne',
                  ),
                  11 =>
                  array (
                    'domain' => 'tpgi.com.au',
                    'class' => '4',
                    'country' => 'au',
                    'state' => '',
                    'name' => 'TPG Internet',
                  ),
                  12 =>
                  array (
                    'domain' => 'telstra.com.au',
                    'class' => '4',
                    'country' => 'au',
                    'state' => '',
                    'name' => 'Telecom Australia (Telstra',
                  ),
                  13 =>
                  array (
                    'domain' => 'newcastle.edu.au',
                    'class' => '1',
                    'country' => 'au',
                    'state' => '',
                    'name' => 'University of Newcastle',
                  ),
                  14 =>
                  array (
                    'domain' => 'usyd.edu.au',
                    'class' => '1',
                    'country' => 'au',
                    'state' => '',
                    'name' => 'University of Sydney',
                  ),
                  15 =>
                  array (
                    'domain' => 'azerin.com',
                    'class' => '0',
                    'country' => 'az',
                    'state' => '',
                    'name' => 'AZERIN AZERBAYCAN-TURKIYE BILGI MUESSESESI, Baku',
                  ),
                  16 =>
                  array (
                    'domain' => 'imec.be',
                    'class' => '0',
                    'country' => 'be',
                    'state' => '',
                    'name' => 'BELNET, Brussels',
                  ),
                  17 =>
                  array (
                    'domain' => 'skynet.be',
                    'class' => '4',
                    'country' => 'be',
                    'state' => '',
                    'name' => 'Belgacom sa/nv, Brussels',
                  ),
                  18 =>
                  array (
                    'domain' => 'coditel.net',
                    'class' => '0',
                    'country' => 'be',
                    'state' => '',
                    'name' => 'CODITEL, Brussels',
                  ),
                  19 =>
                  array (
                    'domain' => 'globaltt.com',
                    'class' => '4',
                    'country' => 'be',
                    'state' => '',
                    'name' => 'Global Telephone & Telecommunication S. A.',
                  ),
                  20 =>
                  array (
                    'domain' => 'kuleuven.ac.be',
                    'class' => '1',
                    'country' => 'be',
                    'state' => '',
                    'name' => 'Katholieke Universiteit Leuven',
                  ),
                  21 =>
                  array (
                    'domain' => 'tele2.be',
                    'class' => '4',
                    'country' => 'be',
                    'state' => '',
                    'name' => 'Tele2 Belgium S.A.',
                  ),
                  22 =>
                  array (
                    'domain' => 'ucl.ac.be',
                    'class' => '1',
                    'country' => 'be',
                    'state' => '',
                    'name' => 'Universite Catholique de Louvain',
                  ),
                  23 =>
                  array (
                    'domain' => 'brasiltelecom.net.br',
                    'class' => '4',
                    'country' => 'br',
                    'state' => '',
                    'name' => 'Brasil Telecom S. A.',
                  ),
                  24 =>
                  array (
                    'domain' => 'virtua.com.br',
                    'class' => '4',
                    'country' => 'br',
                    'state' => '',
                    'name' => 'NET Servios de Communicao S.A.',
                  ),
                  25 =>
                  array (
                    'domain' => 'puc-rio.br',
                    'class' => '1',
                    'country' => 'br',
                    'state' => '',
                    'name' => 'PONTIFICIA UNIVERSIDADE CATOLICA DO RIO DE JANEIRO',
                  ),
                  26 =>
                  array (
                    'domain' => 'ajato.com.br',
                    'class' => '0',
                    'country' => 'br',
                    'state' => '',
                    'name' => 'Rede Ajato Ltda',
                  ),
                  27 =>
                  array (
                    'domain' => 'telesp.net.br',
                    'class' => '4',
                    'country' => 'br',
                    'state' => '',
                    'name' => 'TELECOMUNICACOES DE SAO PAULO S.A.',
                  ),
                  28 =>
                  array (
                    'domain' => 'tdatabrasil.net.br',
                    'class' => '4',
                    'country' => 'br',
                    'state' => '',
                    'name' => 'TELEFONICA EMPRESAS S/A, Sao Paulo',
                  ),
                  29 =>
                  array (
                    'domain' => 'veloxzone.com.br',
                    'class' => '4',
                    'country' => 'br',
                    'state' => '',
                    'name' => 'Telemar Norte Leste S.A., Rio de Janeiro',
                  ),
                  30 =>
                  array (
                    'domain' => 'unicamp.br',
                    'class' => '1',
                    'country' => 'br',
                    'state' => '',
                    'name' => 'UNIVERSIDADE ESTADUAL DE CAMPINAS',
                  ),
                  31 =>
                  array (
                    'domain' => 'ufc.br',
                    'class' => '1',
                    'country' => 'br',
                    'state' => '',
                    'name' => 'Universidade Federal do Ceara',
                  ),
                  32 =>
                  array (
                    'domain' => 'tche.br',
                    'class' => '1',
                    'country' => 'br',
                    'state' => '',
                    'name' => 'Universidade Federal do Rio Grande do Sul',
                  ),
                  33 =>
                  array (
                    'domain' => 'usp.br',
                    'class' => '1',
                    'country' => 'br',
                    'state' => '',
                    'name' => 'University de Sao Paulo',
                  ),
                  34 =>
                  array (
                    'domain' => 'shawcable.net',
                    'class' => '4',
                    'country' => 'ca',
                    'state' => 'ab',
                    'name' => 'Shaw Cablesystems G.P.',
                  ),
                  35 =>
                  array (
                    'domain' => 'telus.net',
                    'class' => '4',
                    'country' => 'ca',
                    'state' => 'ab',
                    'name' => 'Telus Communications Inc.',
                  ),
                  36 =>
                  array (
                    'domain' => 'ualberta.ca',
                    'class' => '1',
                    'country' => 'ca',
                    'state' => 'ab',
                    'name' => 'University of Alberta',
                  ),
                  37 =>
                  array (
                    'domain' => 'ubc.ca',
                    'class' => '1',
                    'country' => 'ca',
                    'state' => 'bc',
                    'name' => 'University of British Columbia',
                  ),
                  38 =>
                  array (
                    'domain' => 'dal.ca',
                    'class' => '1',
                    'country' => 'ca',
                    'state' => 'ns',
                    'name' => 'Dalhousie University, Halifax',
                  ),
                  39 =>
                  array (
                    'domain' => 'carleton.ca',
                    'class' => '1',
                    'country' => 'ca',
                    'state' => 'on',
                    'name' => 'Carleton University, Ottawa',
                  ),
                  40 =>
                  array (
                    'domain' => 'iasl.com',
                    'class' => '4',
                    'country' => 'ca',
                    'state' => 'on',
                    'name' => 'Chunghwa Telecom Co., Ltd.',
                  ),
                  41 =>
                  array (
                    'domain' => 'iplink.net',
                    'class' => '4',
                    'country' => 'ca',
                    'state' => 'on',
                    'name' => 'Interlink',
                  ),
                  42 =>
                  array (
                    'domain' => 'mcmaster.ca',
                    'class' => '1',
                    'country' => 'ca',
                    'state' => 'on',
                    'name' => 'McMaster University, Hamilton',
                  ),
                  43 =>
                  array (
                    'domain' => 'nrc.ca',
                    'class' => '3',
                    'country' => 'ca',
                    'state' => 'on',
                    'name' => 'National Research Council Canada, Ottawa',
                  ),
                  44 =>
                  array (
                    'domain' => 'rogers.com',
                    'class' => '4',
                    'country' => 'ca',
                    'state' => 'on',
                    'name' => 'Rogers Communications Inc.',
                  ),
                  45 =>
                  array (
                    'domain' => 'utoronto.ca',
                    'class' => '1',
                    'country' => 'ca',
                    'state' => 'on',
                    'name' => 'University of Toronto',
                  ),
                  46 =>
                  array (
                    'domain' => 'uwindsor.ca',
                    'class' => '1',
                    'country' => 'ca',
                    'state' => 'on',
                    'name' => 'University of Windsor',
                  ),
                  47 =>
                  array (
                    'domain' => 'sympatico.ca',
                    'class' => '4',
                    'country' => 'ca',
                    'state' => 'qc',
                    'name' => 'Bell Canada',
                  ),
                  48 =>
                  array (
                    'domain' => 'concordia.ca',
                    'class' => '1',
                    'country' => 'ca',
                    'state' => 'qc',
                    'name' => 'Concordia University, Montreal',
                  ),
                  49 =>
                  array (
                    'domain' => 'polymtl.ca',
                    'class' => '1',
                    'country' => 'ca',
                    'state' => 'qc',
                    'name' => 'Ecole Polytechnique de Montreal',
                  ),
                  50 =>
                  array (
                    'domain' => 'ericsson.ca',
                    'class' => '2',
                    'country' => 'ca',
                    'state' => 'qc',
                    'name' => 'Ericsson Canada Inc.',
                  ),
                  51 =>
                  array (
                    'domain' => 'mcgill.ca',
                    'class' => '1',
                    'country' => 'ca',
                    'state' => 'qc',
                    'name' => 'McGill University, Montreal',
                  ),
                  52 =>
                  array (
                    'domain' => 'ulaval.ca',
                    'class' => '1',
                    'country' => 'ca',
                    'state' => 'qc',
                    'name' => 'Universite Laval, Quebec',
                  ),
                  53 =>
                  array (
                    'domain' => 'usherb.ca',
                    'class' => '1',
                    'country' => 'ca',
                    'state' => 'qc',
                    'name' => 'Universite de Sherbrooke',
                  ),
                  54 =>
                  array (
                    'domain' => 'uqtr.ca',
                    'class' => '1',
                    'country' => 'ca',
                    'state' => 'qc',
                    'name' => 'University du Quebec la Trois-Rivieres',
                  ),
                  55 =>
                  array (
                    'domain' => 'videotron.ca',
                    'class' => '4',
                    'country' => 'ca',
                    'state' => 'qc',
                    'name' => 'Videotron Ltd., Montreal',
                  ),
                  56 =>
                  array (
                    'domain' => 'sasknet.sk.ca',
                    'class' => '4',
                    'country' => 'ca',
                    'state' => 'sk',
                    'name' => 'SaskTel corp., Regina',
                  ),
                  57 =>
                  array (
                    'domain' => 'hispeed.ch',
                    'class' => '4',
                    'country' => 'ch',
                    'state' => '',
                    'name' => 'Cablecom GmbH',
                  ),
                  58 =>
                  array (
                    'domain' => 'csfb.com',
                    'class' => '2',
                    'country' => 'ch',
                    'state' => '',
                    'name' => 'Credit Suisse Group',
                  ),
                  59 =>
                  array (
                    'domain' => 'ethz.ch',
                    'class' => '1',
                    'country' => 'ch',
                    'state' => '',
                    'name' => 'Eidgenossische Technische Hochschule Zurich',
                  ),
                  60 =>
                  array (
                    'domain' => 'psi.ch',
                    'class' => '1',
                    'country' => 'ch',
                    'state' => '',
                    'name' => 'Paul Scherrer Institute',
                  ),
                  61 =>
                  array (
                    'domain' => 'epfl.ch',
                    'class' => '2',
                    'country' => 'ch',
                    'state' => '',
                    'name' => 'Swiss Federal Institute of Technology',
                  ),
                  62 =>
                  array (
                    'domain' => 'unige.ch',
                    'class' => '1',
                    'country' => 'ch',
                    'state' => '',
                    'name' => 'University de Geneve',
                  ),
                  63 =>
                  array (
                    'domain' => 'unibas.ch',
                    'class' => '1',
                    'country' => 'ch',
                    'state' => '',
                    'name' => 'University of Basel',
                  ),
                  64 =>
                  array (
                    'domain' => 'bta.net.cn',
                    'class' => '4',
                    'country' => 'cn',
                    'state' => '',
                    'name' => 'Bajing Telecom',
                  ),
                  65 =>
                  array (
                    'domain' => 'tijmu.edu.cn',
                    'class' => '1',
                    'country' => 'cn',
                    'state' => '',
                    'name' => 'Tianjin Medical University',
                  ),
                  66 =>
                  array (
                    'domain' => 'tsinghua.edu.cn',
                    'class' => '1',
                    'country' => 'cn',
                    'state' => '',
                    'name' => 'Tsinghua University',
                  ),
                  67 =>
                  array (
                    'domain' => 'ysu.edu.cn',
                    'class' => '1',
                    'country' => 'cn',
                    'state' => '',
                    'name' => 'Yanshan University',
                  ),
                  68 =>
                  array (
                    'domain' => 'jiitindia.org',
                    'class' => '0',
                    'country' => 'cn',
                    'state' => '',
                    'name' => 'no organization name',
                  ),
                  69 =>
                  array (
                    'domain' => 'sh.cn',
                    'class' => '4',
                    'country' => 'cn',
                    'state' => '',
                    'name' => 'unknwon china ISP',
                  ),
                  70 =>
                  array (
                    'domain' => 'uniweb.net.co',
                    'class' => '4',
                    'country' => 'co',
                    'state' => '',
                    'name' => 'UNIWEB',
                  ),
                  71 =>
                  array (
                    'domain' => 'unal.edu.co',
                    'class' => '1',
                    'country' => 'co',
                    'state' => '',
                    'name' => 'Universidad Nacional de Colombia, Bogata',
                  ),
                  72 =>
                  array (
                    'domain' => 'uniandes.edu.co',
                    'class' => '1',
                    'country' => 'co',
                    'state' => '',
                    'name' => 'Universidad de los Andes, Bogata',
                  ),
                  73 =>
                  array (
                    'domain' => 'ucy.ac.cy',
                    'class' => '1',
                    'country' => 'cy',
                    'state' => '',
                    'name' => 'University of Cyprus',
                  ),
                  74 =>
                  array (
                    'domain' => 'fh-giessen.de',
                    'class' => '1',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'FH Giessen-Friedberg University of Applied Sciences',
                  ),
                  75 =>
                  array (
                    'domain' => 'kfa-juelich.de',
                    'class' => '2',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Forschungszentrum Julich in der Helmholtz',
                  ),
                  76 =>
                  array (
                    'domain' => 'fhg.de',
                    'class' => '2',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Fraunhofer-Gesellschaft',
                  ),
                  77 =>
                  array (
                    'domain' => 'gatel.net',
                    'class' => '4',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Global Access Telecommunications, Inc.',
                  ),
                  78 =>
                  array (
                    'domain' => 'hu-berlin.de',
                    'class' => '1',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Humboldt-Universitat zu Berlin',
                  ),
                  79 =>
                  array (
                    'domain' => 'teleport-iabg.de',
                    'class' => '4',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'IABG-Infocom-Teleport',
                  ),
                  80 =>
                  array (
                    'domain' => 'lrz-muenchen.de',
                    'class' => '2',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Leibniz-Rechenzentrum Munchen',
                  ),
                  81 =>
                  array (
                    'domain' => 'arcor-ip.net',
                    'class' => '0',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Mannesmann Arcor AG & Co, Eschborn',
                  ),
                  82 =>
                  array (
                    'domain' => 'mpg.de',
                    'class' => '1',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Max Plank Institute',
                  ),
                  83 =>
                  array (
                    'domain' => 'mpi-stuttgart.mpg.de',
                    'class' => '1',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Max Plank Institute Stuttgart',
                  ),
                  84 =>
                  array (
                    'domain' => 'mpis.mpg.de',
                    'class' => '1',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Max Plank Institute Stuttgart',
                  ),
                  85 =>
                  array (
                    'domain' => 'mpipks-dresden.mpg.de',
                    'class' => '1',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Max Plank Institute fur Physik Komplexer Systeme',
                  ),
                  86 =>
                  array (
                    'domain' => 'aktivanet.de',
                    'class' => '4',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Preiswerter und Leistungsfaehig',
                  ),
                  87 =>
                  array (
                    'domain' => 'rwth-aachen.de',
                    'class' => '1',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'RWTH-Aachen University',
                  ),
                  88 =>
                  array (
                    'domain' => 'sbs.de',
                    'class' => '2',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Siemens',
                  ),
                  89 =>
                  array (
                    'domain' => 't-dialin.net',
                    'class' => '4',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'T-Online International AG',
                  ),
                  90 =>
                  array (
                    'domain' => 'tu-bs.de',
                    'class' => '1',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Technische Universitat Braunschweig',
                  ),
                  91 =>
                  array (
                    'domain' => 'tu-chemnitz.de',
                    'class' => '1',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Technische Universitat Chemnitz',
                  ),
                  92 =>
                  array (
                    'domain' => 'tu-darmstadt.de',
                    'class' => '1',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Technische Universitat Darmstadt',
                  ),
                  93 =>
                  array (
                    'domain' => 'tu-ilmenau.de',
                    'class' => '1',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Technische Universitat Ilmenau',
                  ),
                  94 =>
                  array (
                    'domain' => 'tu-muenchen.de',
                    'class' => '1',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Technische Universitat Muenchen',
                  ),
                  95 =>
                  array (
                    'domain' => 'tiscali.de',
                    'class' => '4',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Tiscali SpA',
                  ),
                  96 =>
                  array (
                    'domain' => 'uni-duisburg.de',
                    'class' => '1',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Universitat Duisburg-Essen',
                  ),
                  97 =>
                  array (
                    'domain' => 'uni-essen.de',
                    'class' => '1',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Universitat Duisburg-Essen',
                  ),
                  98 =>
                  array (
                    'domain' => 'unibw-hamburg.de',
                    'class' => '1',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Universitat Duisburg-Essen',
                  ),
                  99 =>
                  array (
                    'domain' => 'uni-erlangen.de',
                    'class' => '1',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Universitat Erlangen-Nur',
                  ),
                  100 =>
                  array (
                    'domain' => 'uni-hamburg.de',
                    'class' => '1',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Universitat Hamburg',
                  ),
                  101 =>
                  array (
                    'domain' => 'uni-hannover.de',
                    'class' => '1',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Universitat Hannover-Startseite',
                  ),
                  102 =>
                  array (
                    'domain' => 'uni-jena.de',
                    'class' => '1',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Universitat Jena',
                  ),
                  103 =>
                  array (
                    'domain' => 'uni-konstanz.de',
                    'class' => '1',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Universitat Konstanz',
                  ),
                  104 =>
                  array (
                    'domain' => 'uni-leipzig.de',
                    'class' => '1',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Universitat Leipzig',
                  ),
                  105 =>
                  array (
                    'domain' => 'uni-paderborn.de',
                    'class' => '1',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Universitat Paderborn',
                  ),
                  106 =>
                  array (
                    'domain' => 'uni-wuerzburg.de',
                    'class' => '1',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'Universitat Wurzburg',
                  ),
                  107 =>
                  array (
                    'domain' => 'mcbone.net',
                    'class' => '4',
                    'country' => 'de',
                    'state' => '',
                    'name' => 'freenet Cityline GmbH',
                  ),
                  108 =>
                  array (
                    'domain' => 't-ipconnect.de',
                    'class' => '4',
                    'country' => 'de',
                    'state' => '',
                    'name' => 't-ipconnect.de',
                  ),
                  109 =>
                  array (
                    'domain' => 'dtu.dk',
                    'class' => '1',
                    'country' => 'dk',
                    'state' => '',
                    'name' => 'Danmarks Tekniske Universitet, Kongens Lnngby',
                  ),
                  110 =>
                  array (
                    'domain' => 'tele.dk',
                    'class' => '4',
                    'country' => 'dk',
                    'state' => '',
                    'name' => 'Tele Danmark',
                  ),
                  111 =>
                  array (
                    'domain' => 'starman.ee',
                    'class' => '4',
                    'country' => 'ee',
                    'state' => '',
                    'name' => 'Starman Kaabeltelevisiooni AS',
                  ),
                  112 =>
                  array (
                    'domain' => 'ttu.ee',
                    'class' => '1',
                    'country' => 'ee',
                    'state' => '',
                    'name' => 'Tallinn University of Technology',
                  ),
                  113 =>
                  array (
                    'domain' => 'link.net',
                    'class' => '4',
                    'country' => 'eg',
                    'state' => '',
                    'name' => 'Link Egypt',
                  ),
                  114 =>
                  array (
                    'domain' => 'tedata.net',
                    'class' => '0',
                    'country' => 'eg',
                    'state' => '',
                    'name' => 'TE Data',
                  ),
                  115 =>
                  array (
                    'domain' => 'auna.net',
                    'class' => '0',
                    'country' => 'es',
                    'state' => '',
                    'name' => 'Auna Telecomunicaciones, S.A., Barcelona',
                  ),
                  116 =>
                  array (
                    'domain' => 'csic.es',
                    'class' => '2',
                    'country' => 'es',
                    'state' => '',
                    'name' => 'Consejo Superior de Investigaciones Cientificas, Madrid',
                  ),
                  117 =>
                  array (
                    'domain' => 'intervip.com.br',
                    'class' => '0',
                    'country' => 'es',
                    'state' => '',
                    'name' => 'INTERVIP INFORMATICA LTDA',
                  ),
                  118 =>
                  array (
                    'domain' => 'jazztel.es',
                    'class' => '4',
                    'country' => 'es',
                    'state' => '',
                    'name' => 'Jazztel, Madrid',
                  ),
                  119 =>
                  array (
                    'domain' => 'rima-tde.net',
                    'class' => '4',
                    'country' => 'es',
                    'state' => '',
                    'name' => 'TELEFONICA, S.A., Madrid',
                  ),
                  120 =>
                  array (
                    'domain' => 'tecnun.es',
                    'class' => '1',
                    'country' => 'es',
                    'state' => '',
                    'name' => 'Technologico de la Universidad de Navarra',
                  ),
                  121 =>
                  array (
                    'domain' => 'upm.es',
                    'class' => '1',
                    'country' => 'es',
                    'state' => '',
                    'name' => 'Universidad Politecnica de Madrid',
                  ),
                  122 =>
                  array (
                    'domain' => 'usal.es',
                    'class' => '1',
                    'country' => 'es',
                    'state' => '',
                    'name' => 'Universidad Politecnica de Salamanca',
                  ),
                  123 =>
                  array (
                    'domain' => 'urv.es',
                    'class' => '1',
                    'country' => 'es',
                    'state' => '',
                    'name' => 'Universidad Politecnica de Tarragona',
                  ),
                  124 =>
                  array (
                    'domain' => 'upv.es',
                    'class' => '1',
                    'country' => 'es',
                    'state' => '',
                    'name' => 'Universidad Politecnica de Valencia',
                  ),
                  125 =>
                  array (
                    'domain' => 'ugr.es',
                    'class' => '1',
                    'country' => 'es',
                    'state' => '',
                    'name' => 'Universidad de Granada',
                  ),
                  126 =>
                  array (
                    'domain' => 'uab.es',
                    'class' => '1',
                    'country' => 'es',
                    'state' => '',
                    'name' => 'Universitat Autonoma de Barcelona',
                  ),
                  127 =>
                  array (
                    'domain' => 'uam.es',
                    'class' => '1',
                    'country' => 'es',
                    'state' => '',
                    'name' => 'Universitat Autonoma de Madrid',
                  ),
                  128 =>
                  array (
                    'domain' => 'jyu.fi',
                    'class' => '1',
                    'country' => 'fi',
                    'state' => '',
                    'name' => 'University of Jyvaskylan',
                  ),
                  129 =>
                  array (
                    'domain' => 'suomi.net',
                    'class' => '4',
                    'country' => 'fi',
                    'state' => '',
                    'name' => 'oulu telephone company',
                  ),
                  130 =>
                  array (
                    'domain' => 'noos.net',
                    'class' => '0',
                    'country' => 'fr',
                    'state' => '',
                    'name' => 'Auxipar, Paris',
                  ),
                  131 =>
                  array (
                    'domain' => 'ceram.fr',
                    'class' => '1',
                    'country' => 'fr',
                    'state' => '',
                    'name' => 'CERAM Sophia Anitpolis European School of Business',
                  ),
                  132 =>
                  array (
                    'domain' => 'univ-lyon1.fr',
                    'class' => '1',
                    'country' => 'fr',
                    'state' => '',
                    'name' => 'Centre Informatique Scientifique et Medical de l\\\'Universite Claude Bernard Lyon 1',
                  ),
                  133 =>
                  array (
                    'domain' => 'cea.fr',
                    'class' => '3',
                    'country' => 'fr',
                    'state' => '',
                    'name' => 'Commissariat a L\\\'Energie Atomique',
                  ),
                  134 =>
                  array (
                    'domain' => 'ec-lyon.fr',
                    'class' => '1',
                    'country' => 'fr',
                    'state' => '',
                    'name' => 'Ecole Centrale de Lyon',
                  ),
                  135 =>
                  array (
                    'domain' => 'enserg.fr',
                    'class' => '2',
                    'country' => 'fr',
                    'state' => '',
                    'name' => 'Ecole Nationale Superieure d\\\'Electricite et de Radioelectricite de Grenoble',
                  ),
                  136 =>
                  array (
                    'domain' => 'ensta.fr',
                    'class' => '2',
                    'country' => 'fr',
                    'state' => '',
                    'name' => 'Ecole Nationale Superieure des Techniques Avancees',
                  ),
                  137 =>
                  array (
                    'domain' => 'exabot.com',
                    'class' => '5',
                    'country' => 'fr',
                    'state' => '',
                    'name' => 'Exalead S.A., Paris',
                  ),
                  138 =>
                  array (
                    'domain' => 'dir.com',
                    'class' => '0',
                    'country' => 'fr',
                    'state' => '',
                    'name' => 'FERMIC SA',
                  ),
                  139 =>
                  array (
                    'domain' => 'insa-lyon.fr',
                    'class' => '1',
                    'country' => 'fr',
                    'state' => '',
                    'name' => 'Institut National des Sciences Appliquees de Lyon',
                  ),
                  140 =>
                  array (
                    'domain' => 'insa-toulouse.fr',
                    'class' => '1',
                    'country' => 'fr',
                    'state' => '',
                    'name' => 'Institut National des Sciences Appliquuees de Toulouse',
                  ),
                  141 =>
                  array (
                    'domain' => 'inist.fr',
                    'class' => '3',
                    'country' => 'fr',
                    'state' => '',
                    'name' => 'Institut de l\\\'Information Scientifique et Technique',
                  ),
                  142 =>
                  array (
                    'domain' => 'kaptech.net',
                    'class' => '0',
                    'country' => 'fr',
                    'state' => '',
                    'name' => 'KAPTECH',
                  ),
                  143 =>
                  array (
                    'domain' => 'laas.fr',
                    'class' => '2',
                    'country' => 'fr',
                    'state' => '',
                    'name' => 'Laboratoire d\\\'Automatique et d\\\'Analyse des Systemes du CNRS',
                  ),
                  144 =>
                  array (
                    'domain' => 'noos.fr',
                    'class' => '4',
                    'country' => 'fr',
                    'state' => '',
                    'name' => 'NOOS (Lyonnaise Communications',
                  ),
                  145 =>
                  array (
                    'domain' => 'nerim.net',
                    'class' => '4',
                    'country' => 'fr',
                    'state' => '',
                    'name' => 'Nerim Networks, Paris',
                  ),
                  146 =>
                  array (
                    'domain' => 'proxad.net',
                    'class' => '4',
                    'country' => 'fr',
                    'state' => '',
                    'name' => 'ONLINE, Paris',
                  ),
                  147 =>
                  array (
                    'domain' => 'univ-mrs.fr',
                    'class' => '1',
                    'country' => 'fr',
                    'state' => '',
                    'name' => 'Reseau Informatique',
                  ),
                  148 =>
                  array (
                    'domain' => 'st.com',
                    'class' => '2',
                    'country' => 'fr',
                    'state' => '',
                    'name' => 'STMicroelectronics',
                  ),
                  149 =>
                  array (
                    'domain' => 'club-internet.fr',
                    'class' => '4',
                    'country' => 'fr',
                    'state' => '',
                    'name' => 'T-Online France, Paris',
                  ),
                  150 =>
                  array (
                    'domain' => 'tele2.fr',
                    'class' => '4',
                    'country' => 'fr',
                    'state' => '',
                    'name' => 'Tele2 France S.A.',
                  ),
                  151 =>
                  array (
                    'domain' => 'u-psud.fr',
                    'class' => '1',
                    'country' => 'fr',
                    'state' => '',
                    'name' => 'Universite Paris Sud, Orsay',
                  ),
                  152 =>
                  array (
                    'domain' => 'ups-tlse.fr',
                    'class' => '1',
                    'country' => 'fr',
                    'state' => '',
                    'name' => 'Universite Paul Sabatier de Toulous',
                  ),
                  153 =>
                  array (
                    'domain' => 'univ-nantes.fr',
                    'class' => '1',
                    'country' => 'fr',
                    'state' => '',
                    'name' => 'Universite de Nantes, Nantes',
                  ),
                  154 =>
                  array (
                    'domain' => 'uvsq.fr',
                    'class' => '1',
                    'country' => 'fr',
                    'state' => '',
                    'name' => 'Universite de Versailles-Saint-Quentin-en-Yvelines',
                  ),
                  155 =>
                  array (
                    'domain' => 'wanadoo.fr',
                    'class' => '4',
                    'country' => 'fr',
                    'state' => '',
                    'name' => 'Wanadoo France',
                  ),
                  156 =>
                  array (
                    'domain' => 'baesystems.com',
                    'class' => '2',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'BAE Systems plc',
                  ),
                  157 =>
                  array (
                    'domain' => 'btopenworld.com',
                    'class' => '4',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'British Telecommunications plc',
                  ),
                  158 =>
                  array (
                    'domain' => 'btcentralplus.com',
                    'class' => '4',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'British Telecommunications plc, London',
                  ),
                  159 =>
                  array (
                    'domain' => 'brunel.ac.uk',
                    'class' => '1',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'Brunel University',
                  ),
                  160 =>
                  array (
                    'domain' => 'clara.net',
                    'class' => '0',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'ClaraNET Ltd.',
                  ),
                  161 =>
                  array (
                    'domain' => 'dl.ac.uk',
                    'class' => '1',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'Daresbury Laboratory',
                  ),
                  162 =>
                  array (
                    'domain' => 'dmu.ac.uk',
                    'class' => '1',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'De Montfort University, Leicester',
                  ),
                  163 =>
                  array (
                    'domain' => 'ed.ac.uk',
                    'class' => '1',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'Edinburgh University',
                  ),
                  164 =>
                  array (
                    'domain' => 'lancs.ac.uk',
                    'class' => '1',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'Lancaster University',
                  ),
                  165 =>
                  array (
                    'domain' => 'leeds.ac.uk',
                    'class' => '1',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'Leeds University',
                  ),
                  166 =>
                  array (
                    'domain' => 'npl.co.uk',
                    'class' => '0',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'NPL Management Ltd',
                  ),
                  167 =>
                  array (
                    'domain' => 'oracle.co.uk',
                    'class' => '2',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'Oracle Ltd',
                  ),
                  168 =>
                  array (
                    'domain' => 'plus.com',
                    'class' => '0',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'PlusNet Technologies Ltd',
                  ),
                  169 =>
                  array (
                    'domain' => 'qinetiq.com',
                    'class' => '0',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'QinetiQ',
                  ),
                  170 =>
                  array (
                    'domain' => 'qmul.ac.uk',
                    'class' => '1',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'Queen Mary and Westfield College, University of London',
                  ),
                  171 =>
                  array (
                    'domain' => 'qub.ac.uk',
                    'class' => '1',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'Queens University Belfast',
                  ),
                  172 =>
                  array (
                    'domain' => 'rhul.ac.uk',
                    'class' => '1',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'Royal Holloway- University of London',
                  ),
                  173 =>
                  array (
                    'domain' => 'soton.ac.uk',
                    'class' => '1',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'Southampton University',
                  ),
                  174 =>
                  array (
                    'domain' => 'surrey.ac.uk',
                    'class' => '1',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'Surrey University',
                  ),
                  175 =>
                  array (
                    'domain' => 'blueyonder.co.uk',
                    'class' => '4',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'Telewest Communications Networks Limited',
                  ),
                  176 =>
                  array (
                    'domain' => 'ucl.ac.uk',
                    'class' => '1',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'University College London',
                  ),
                  177 =>
                  array (
                    'domain' => 'cam.ac.uk',
                    'class' => '1',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'University of Cambridge',
                  ),
                  178 =>
                  array (
                    'domain' => 'gla.ac.uk',
                    'class' => '1',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'University of Glasgow',
                  ),
                  179 =>
                  array (
                    'domain' => 'luton.ac.uk',
                    'class' => '1',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'University of Luton',
                  ),
                  180 =>
                  array (
                    'domain' => 'rdg.ac.uk',
                    'class' => '1',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'University of Reading',
                  ),
                  181 =>
                  array (
                    'domain' => 'swan.ac.uk',
                    'class' => '1',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'University of Wales Swansea',
                  ),
                  182 =>
                  array (
                    'domain' => 'zen.co.uk',
                    'class' => '4',
                    'country' => 'gb',
                    'state' => '',
                    'name' => 'Zen Internet Ltd',
                  ),
                  183 =>
                  array (
                    'domain' => 'gtu.edu.ge',
                    'class' => '1',
                    'country' => 'ge',
                    'state' => '',
                    'name' => 'Georgian Technical University',
                  ),
                  184 =>
                  array (
                    'domain' => 'demokritos.gr',
                    'class' => '2',
                    'country' => 'gr',
                    'state' => '',
                    'name' => 'National Centre of Scientific Research \\"DEMOKRITOS\\"',
                  ),
                  185 =>
                  array (
                    'domain' => 'uoa.gr',
                    'class' => '1',
                    'country' => 'gr',
                    'state' => '',
                    'name' => 'National & Kapodistrian University of Athens',
                  ),
                  186 =>
                  array (
                    'domain' => 'otenet.gr',
                    'class' => '4',
                    'country' => 'gr',
                    'state' => '',
                    'name' => 'OTEnet',
                  ),
                  187 =>
                  array (
                    'domain' => 'tellas.gr',
                    'class' => '4',
                    'country' => 'gr',
                    'state' => '',
                    'name' => 'Tellas',
                  ),
                  188 =>
                  array (
                    'domain' => 'cuhk.edu.hk',
                    'class' => '1',
                    'country' => 'hk',
                    'state' => '',
                    'name' => 'Chinese University of Hong Kong Shatin',
                  ),
                  189 =>
                  array (
                    'domain' => 'cityu.edu.hk',
                    'class' => '1',
                    'country' => 'hk',
                    'state' => '',
                    'name' => 'City University of Hong Kong',
                  ),
                  190 =>
                  array (
                    'domain' => 'polyu.edu.hk',
                    'class' => '1',
                    'country' => 'hk',
                    'state' => '',
                    'name' => 'Hong Kong Polytechnic University',
                  ),
                  191 =>
                  array (
                    'domain' => 'ust.hk',
                    'class' => '1',
                    'country' => 'hk',
                    'state' => '',
                    'name' => 'Hong Kong University of Science and Technology',
                  ),
                  192 =>
                  array (
                    'domain' => 'netvigator.com',
                    'class' => '0',
                    'country' => 'hk',
                    'state' => '',
                    'name' => 'PCCW-HKT Datacom Services Limited',
                  ),
                  193 =>
                  array (
                    'domain' => 'hku.hk',
                    'class' => '1',
                    'country' => 'hk',
                    'state' => '',
                    'name' => 'University of Hong Kong',
                  ),
                  194 =>
                  array (
                    'domain' => 'ifs.hr',
                    'class' => '1',
                    'country' => 'hr',
                    'state' => '',
                    'name' => 'Institute of Physics, Zagreb',
                  ),
                  195 =>
                  array (
                    'domain' => 'ui.edu',
                    'class' => '1',
                    'country' => 'id',
                    'state' => '',
                    'name' => 'University of Indonesia, Jawa Borat',
                  ),
                  196 =>
                  array (
                    'domain' => 'biu.ac.il',
                    'class' => '1',
                    'country' => 'il',
                    'state' => '',
                    'name' => 'Bar-Ilan University, Ramat-Gan',
                  ),
                  197 =>
                  array (
                    'domain' => 'barak.net.il',
                    'class' => '4',
                    'country' => 'il',
                    'state' => '',
                    'name' => 'Barak I.T.C., Rosh Ha\\\'ayin',
                  ),
                  198 =>
                  array (
                    'domain' => 'bezeqint.net',
                    'class' => '0',
                    'country' => 'il',
                    'state' => '',
                    'name' => 'Bezeq International',
                  ),
                  199 =>
                  array (
                    'domain' => 'knet.co.il',
                    'class' => '0',
                    'country' => 'il',
                    'state' => '',
                    'name' => 'Mishkei Hatakam, Tel-Aviv',
                  ),
                  200 =>
                  array (
                    'domain' => 'netvision.net.il',
                    'class' => '4',
                    'country' => 'il',
                    'state' => '',
                    'name' => 'NetVision LTD., Haifa',
                  ),
                  201 =>
                  array (
                    'domain' => 'tau.ac.il',
                    'class' => '1',
                    'country' => 'il',
                    'state' => '',
                    'name' => 'Tel Aviv University, Tel Aviv',
                  ),
                  202 =>
                  array (
                    'domain' => 'weizmann.ac.il',
                    'class' => '1',
                    'country' => 'il',
                    'state' => '',
                    'name' => 'Weizmann Institute of Science, Rehovot',
                  ),
                  203 =>
                  array (
                    'domain' => 'amity.edu',
                    'class' => '1',
                    'country' => 'in',
                    'state' => '',
                    'name' => 'Amity Business School, Noida',
                  ),
                  204 =>
                  array (
                    'domain' => 'bol.net.in',
                    'class' => '4',
                    'country' => 'in',
                    'state' => '',
                    'name' => 'Bharat online, New Delhi',
                  ),
                  205 =>
                  array (
                    'domain' => 'touchtelindia.net',
                    'class' => '4',
                    'country' => 'in',
                    'state' => '',
                    'name' => 'Bharti Tele- Ventures limited, New Delhi',
                  ),
                  206 =>
                  array (
                    'domain' => 'eth.net',
                    'class' => '4',
                    'country' => 'in',
                    'state' => '',
                    'name' => 'Dishnet DSL Ltd., Chennai',
                  ),
                  207 =>
                  array (
                    'domain' => 'ernet.in',
                    'class' => '4',
                    'country' => 'in',
                    'state' => '',
                    'name' => 'ERNET',
                  ),
                  208 =>
                  array (
                    'domain' => 'in2cable.com',
                    'class' => '0',
                    'country' => 'in',
                    'state' => '',
                    'name' => 'In2cable (India',
                  ),
                  209 =>
                  array (
                    'domain' => 'iitb.ac.in',
                    'class' => '1',
                    'country' => 'in',
                    'state' => '',
                    'name' => 'Indian Institute of Technology Bombay',
                  ),
                  210 =>
                  array (
                    'domain' => 'iitm.ac.in',
                    'class' => '1',
                    'country' => 'in',
                    'state' => '',
                    'name' => 'Indian Institute of Technology Madras',
                  ),
                  211 =>
                  array (
                    'domain' => 'da-iict.org',
                    'class' => '2',
                    'country' => 'in',
                    'state' => '',
                    'name' => 'Institute Of Information and Communication Technology',
                  ),
                  212 =>
                  array (
                    'domain' => 'jncasr.ac.in',
                    'class' => '1',
                    'country' => 'in',
                    'state' => '',
                    'name' => 'Jawaharial Nehru Centre for Advanced Scientific Research',
                  ),
                  213 =>
                  array (
                    'domain' => 'jnu.ac.in',
                    'class' => '1',
                    'country' => 'in',
                    'state' => '',
                    'name' => 'Jawaharial Nehru University',
                  ),
                  214 =>
                  array (
                    'domain' => 'tunmail.com',
                    'class' => '4',
                    'country' => 'in',
                    'state' => '',
                    'name' => 'Metro Cable Internet Services, New Delhi',
                  ),
                  215 =>
                  array (
                    'domain' => 'bose.res.in',
                    'class' => '2',
                    'country' => 'in',
                    'state' => '',
                    'name' => 'S.N. Bose National Centre for Basic Sciences, Kolkata',
                  ),
                  216 =>
                  array (
                    'domain' => 'sify.net',
                    'class' => '0',
                    'country' => 'in',
                    'state' => '',
                    'name' => 'Satyam Infoway Limited, Chennai',
                  ),
                  217 =>
                  array (
                    'domain' => 'vsnl.net.in',
                    'class' => '4',
                    'country' => 'in',
                    'state' => '',
                    'name' => 'TATA indicom',
                  ),
                  218 =>
                  array (
                    'domain' => 'jdvu.ac.in',
                    'class' => '1',
                    'country' => 'in',
                    'state' => '',
                    'name' => 'jdvu.ac.in',
                  ),
                  219 =>
                  array (
                    'domain' => 'kashanu.ac.ir',
                    'class' => '1',
                    'country' => 'ir',
                    'state' => '',
                    'name' => 'Kashan University',
                  ),
                  220 =>
                  array (
                    'domain' => 'sinet.ir',
                    'class' => '0',
                    'country' => 'ir',
                    'state' => '',
                    'name' => 'Soroush Interactive Network',
                  ),
                  221 =>
                  array (
                    'domain' => 'cnr.it',
                    'class' => '2',
                    'country' => 'it',
                    'state' => '',
                    'name' => 'Consiglio Nazionale delle Ricerche',
                  ),
                  222 =>
                  array (
                    'domain' => 'enea.it',
                    'class' => '2',
                    'country' => 'it',
                    'state' => '',
                    'name' => 'ENEA Italian National Agency for New Technologies, Energy and the Environment',
                  ),
                  223 =>
                  array (
                    'domain' => 'fastres.net',
                    'class' => '0',
                    'country' => 'it',
                    'state' => '',
                    'name' => 'FASTWEB S.P.A., Milano',
                  ),
                  224 =>
                  array (
                    'domain' => 'trieste.it',
                    'class' => '0',
                    'country' => 'it',
                    'state' => '',
                    'name' => 'Geographical Domain Trieste',
                  ),
                  225 =>
                  array (
                    'domain' => 'infn.it',
                    'class' => '3',
                    'country' => 'it',
                    'state' => '',
                    'name' => 'Istituto Nazionale di Fisica Nucleare, Bologna',
                  ),
                  226 =>
                  array (
                    'domain' => 'libero.it',
                    'class' => '1',
                    'country' => 'it',
                    'state' => '',
                    'name' => 'Italia Online S.p.A.',
                  ),
                  227 =>
                  array (
                    'domain' => 'garr.it',
                    'class' => '1',
                    'country' => 'it',
                    'state' => '',
                    'name' => 'Italian Academic and Research Network',
                  ),
                  228 =>
                  array (
                    'domain' => 'eutelia.it',
                    'class' => '0',
                    'country' => 'it',
                    'state' => '',
                    'name' => 'Plugit Spa, Arezzo',
                  ),
                  229 =>
                  array (
                    'domain' => 'polimi.it',
                    'class' => '1',
                    'country' => 'it',
                    'state' => '',
                    'name' => 'Politecnico di Milano',
                  ),
                  230 =>
                  array (
                    'domain' => 'sns.it',
                    'class' => '0',
                    'country' => 'it',
                    'state' => '',
                    'name' => 'Scuola Normale Superiore di Pisa',
                  ),
                  231 =>
                  array (
                    'domain' => 'interbusiness.it',
                    'class' => '4',
                    'country' => 'it',
                    'state' => '',
                    'name' => 'Telecom Italia S.p.A., Roma',
                  ),
                  232 =>
                  array (
                    'domain' => 'tiscali.it',
                    'class' => '4',
                    'country' => 'it',
                    'state' => '',
                    'name' => 'Tiscali SpA',
                  ),
                  233 =>
                  array (
                    'domain' => 'unibo.it',
                    'class' => '1',
                    'country' => 'it',
                    'state' => '',
                    'name' => 'Universita\\\' degli Studi di Bologna',
                  ),
                  234 =>
                  array (
                    'domain' => 'unile.it',
                    'class' => '1',
                    'country' => 'it',
                    'state' => '',
                    'name' => 'Universita\\\' degli Studi di Lecce',
                  ),
                  235 =>
                  array (
                    'domain' => 'unimib.it',
                    'class' => '1',
                    'country' => 'it',
                    'state' => '',
                    'name' => 'Universita\\\' degli Studi di Milano - Bicocca',
                  ),
                  236 =>
                  array (
                    'domain' => 'unimo.it',
                    'class' => '1',
                    'country' => 'it',
                    'state' => '',
                    'name' => 'Universita\\\' degli Studi di Modena',
                  ),
                  237 =>
                  array (
                    'domain' => 'unipr.it',
                    'class' => '1',
                    'country' => 'it',
                    'state' => '',
                    'name' => 'Universita\\\' degli Studi di Parma',
                  ),
                  238 =>
                  array (
                    'domain' => 'unipg.it',
                    'class' => '1',
                    'country' => 'it',
                    'state' => '',
                    'name' => 'Universita\\\' degli Studi di Perugia',
                  ),
                  239 =>
                  array (
                    'domain' => 'unipi.it',
                    'class' => '1',
                    'country' => 'it',
                    'state' => '',
                    'name' => 'Universita\\\' degli Studi di Pisa',
                  ),
                  240 =>
                  array (
                    'domain' => 'uniroma1.it',
                    'class' => '1',
                    'country' => 'it',
                    'state' => '',
                    'name' => 'Universita\\\' degli Studi di Roma La Sapienza',
                  ),
                  241 =>
                  array (
                    'domain' => 'uniroma2.it',
                    'class' => '1',
                    'country' => 'it',
                    'state' => '',
                    'name' => 'Universita\\\' degli Studi di Roma Tor Vergata',
                  ),
                  242 =>
                  array (
                    'domain' => 'unict.it',
                    'class' => '1',
                    'country' => 'it',
                    'state' => '',
                    'name' => 'Universita\\\' di Catania',
                  ),
                  243 =>
                  array (
                    'domain' => 'univ-lille1.fr',
                    'class' => '1',
                    'country' => 'it',
                    'state' => '',
                    'name' => 'Universite des Sciences et Technologies de Lille',
                  ),
                  244 =>
                  array (
                    'domain' => 'accsnet.ne.jp',
                    'class' => '4',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'ACCS NET',
                  ),
                  245 =>
                  array (
                    'domain' => 'asahi-net.or.jp',
                    'class' => '4',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'ASAHI NET',
                  ),
                  246 =>
                  array (
                    'domain' => 'ayu.ne.jp',
                    'class' => '4',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'AYUNET',
                  ),
                  247 =>
                  array (
                    'domain' => 'cybernet.co.jp',
                    'class' => '0',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'CYBERNET SYSTEMS CO., LTD.',
                  ),
                  248 =>
                  array (
                    'domain' => 'dion.ne.jp',
                    'class' => '4',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'DION',
                  ),
                  249 =>
                  array (
                    'domain' => 'yournet.ne.jp',
                    'class' => '4',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'FreeBit.Net Service',
                  ),
                  250 =>
                  array (
                    'domain' => 'fujitsu.co.jp',
                    'class' => '2',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'Fujitsu Limited',
                  ),
                  251 =>
                  array (
                    'domain' => 'goo.ne.jp',
                    'class' => '4',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'GOO',
                  ),
                  252 =>
                  array (
                    'domain' => 'hi-ho.ne.jp',
                    'class' => '4',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'Hi-HO Internet Service',
                  ),
                  253 =>
                  array (
                    'domain' => 'hokudai.ac.jp',
                    'class' => '1',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'Hokkaido University',
                  ),
                  254 =>
                  array (
                    'domain' => 'infoweb.ne.jp',
                    'class' => '4',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'InfoWeb',
                  ),
                  255 =>
                  array (
                    'domain' => 'jst.go.jp',
                    'class' => '3',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'Japan Science and Technology Agency',
                  ),
                  256 =>
                  array (
                    'domain' => 'kcn.ne.jp',
                    'class' => '4',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'KCN-Net Service',
                  ),
                  257 =>
                  array (
                    'domain' => 'kobe-u.ac.jp',
                    'class' => '1',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'Kobe University',
                  ),
                  258 =>
                  array (
                    'domain' => 'kyoto-u.ac.jp',
                    'class' => '1',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'Kyoto University',
                  ),
                  259 =>
                  array (
                    'domain' => 'mei.co.jp',
                    'class' => '2',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'Matsushita Electric Industrial Co., Ltd.',
                  ),
                  260 =>
                  array (
                    'domain' => 'mesh.ad.jp',
                    'class' => '2',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'NEC Corporation',
                  ),
                  261 =>
                  array (
                    'domain' => 'nec.co.jp',
                    'class' => '2',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'NEC Corporation',
                  ),
                  262 =>
                  array (
                    'domain' => 'nims.go.jp',
                    'class' => '3',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'National Institute for Materials Science',
                  ),
                  263 =>
                  array (
                    'domain' => 'ocn.ne.jp',
                    'class' => '4',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'Open Computer Network',
                  ),
                  264 =>
                  array (
                    'domain' => 'odn.ne.jp',
                    'class' => '4',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'Open Data Network',
                  ),
                  265 =>
                  array (
                    'domain' => 'osaka-u.ac.jp',
                    'class' => '1',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'Osaka University',
                  ),
                  266 =>
                  array (
                    'domain' => 'plala.or.jp',
                    'class' => '0',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'PLALA',
                  ),
                  267 =>
                  array (
                    'domain' => 'renesas.com',
                    'class' => '2',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'Renesas Technology Corp., Tokyo',
                  ),
                  268 =>
                  array (
                    'domain' => 'sec.or.jp',
                    'class' => '2',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'Sapporo Electronics and Industries Cultivation',
                  ),
                  269 =>
                  array (
                    'domain' => 'sel.co.jp',
                    'class' => '2',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'Semiconductor Energy Laboratory Co., Ltd.',
                  ),
                  270 =>
                  array (
                    'domain' => 'sharp.co.jp',
                    'class' => '2',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'Sharp Corporation',
                  ),
                  271 =>
                  array (
                    'domain' => 'shu.ac.uk',
                    'class' => '1',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'Sheffield Hallam University',
                  ),
                  272 =>
                  array (
                    'domain' => 'so-net.ne.jp',
                    'class' => '4',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'So-net Service',
                  ),
                  273 =>
                  array (
                    'domain' => 'bbtec.net',
                    'class' => '0',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'SoftbankBB Corp., Tokyo',
                  ),
                  274 =>
                  array (
                    'domain' => 'sony.co.jp',
                    'class' => '2',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'Sony Corporation',
                  ),
                  275 =>
                  array (
                    'domain' => 't-com.ne.jp',
                    'class' => '4',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'T-COM ADSL Service',
                  ),
                  276 =>
                  array (
                    'domain' => 'tohoku.ac.jp',
                    'class' => '1',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'Tohoku University',
                  ),
                  277 =>
                  array (
                    'domain' => 'titech.ac.jp',
                    'class' => '1',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'Tokyo Institute of Technology',
                  ),
                  278 =>
                  array (
                    'domain' => 'pu-toyama.ac.jp',
                    'class' => '1',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'Toyama Prefectural University',
                  ),
                  279 =>
                  array (
                    'domain' => 'u-tokyo.ac.jp',
                    'class' => '1',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'University of Tokyo',
                  ),
                  280 =>
                  array (
                    'domain' => 'tsukuba.ac.jp',
                    'class' => '1',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'University of Tsukuba',
                  ),
                  281 =>
                  array (
                    'domain' => 'eonet.ne.jp',
                    'class' => '4',
                    'country' => 'jp',
                    'state' => '',
                    'name' => 'eonet',
                  ),
                  282 =>
                  array (
                    'domain' => 'ewha.ac.kr',
                    'class' => '1',
                    'country' => 'kr',
                    'state' => '',
                    'name' => 'EWHA University',
                  ),
                  283 =>
                  array (
                    'domain' => 'gist.ac.kr',
                    'class' => '1',
                    'country' => 'kr',
                    'state' => '',
                    'name' => 'Gwangju Institute of Scinece and Technology',
                  ),
                  284 =>
                  array (
                    'domain' => 'hananet.net',
                    'class' => '4',
                    'country' => 'kr',
                    'state' => '',
                    'name' => 'Hanaro Telecom, Inc',
                  ),
                  285 =>
                  array (
                    'domain' => 'inha.ac.kr',
                    'class' => '1',
                    'country' => 'kr',
                    'state' => '',
                    'name' => 'INHA University, Nam-gu Incheon',
                  ),
                  286 =>
                  array (
                    'domain' => 'icu.ac.kr',
                    'class' => '1',
                    'country' => 'kr',
                    'state' => '',
                    'name' => 'Information and Communications University, Daejeon',
                  ),
                  287 =>
                  array (
                    'domain' => 'korea.ac.kr',
                    'class' => '1',
                    'country' => 'kr',
                    'state' => '',
                    'name' => 'Korea University Anam-dong',
                  ),
                  288 =>
                  array (
                    'domain' => 'kaist.ac.kr',
                    'class' => '1',
                    'country' => 'kr',
                    'state' => '',
                    'name' => 'Korean Advanced Institute of Science and Technology',
                  ),
                  289 =>
                  array (
                    'domain' => 'kjist.ac.kr',
                    'class' => '1',
                    'country' => 'kr',
                    'state' => '',
                    'name' => 'Kwangju Institute of Science & Technology',
                  ),
                  290 =>
                  array (
                    'domain' => 'kyungwon.ac.kr',
                    'class' => '1',
                    'country' => 'kr',
                    'state' => '',
                    'name' => 'Kyungwon University',
                  ),
                  291 =>
                  array (
                    'domain' => 'postech.ac.kr',
                    'class' => '1',
                    'country' => 'kr',
                    'state' => '',
                    'name' => 'Pohang Kyungbuk Korea System Management Team',
                  ),
                  292 =>
                  array (
                    'domain' => 'samsung.co.kr',
                    'class' => '2',
                    'country' => 'kr',
                    'state' => '',
                    'name' => 'Samsung Networks Inc, Seoul',
                  ),
                  293 =>
                  array (
                    'domain' => 'snu.ac.kr',
                    'class' => '1',
                    'country' => 'kr',
                    'state' => '',
                    'name' => 'Seoul National University, Seoul',
                  ),
                  294 =>
                  array (
                    'domain' => 'skku.ac.kr',
                    'class' => '1',
                    'country' => 'kr',
                    'state' => '',
                    'name' => 'SungKyunKwan University, Seoul',
                  ),
                  295 =>
                  array (
                    'domain' => 'mii.lt',
                    'class' => '1',
                    'country' => 'lt',
                    'state' => '',
                    'name' => 'Institute of Mathematics and Informatics, Vilnius',
                  ),
                  296 =>
                  array (
                    'domain' => 'rst.lt',
                    'class' => '2',
                    'country' => 'lt',
                    'state' => '',
                    'name' => 'Rytu Skirstomieji Tinklai',
                  ),
                  297 =>
                  array (
                    'domain' => 'vtex.lt',
                    'class' => '0',
                    'country' => 'lt',
                    'state' => '',
                    'name' => 'VTeX Typesetting Services',
                  ),
                  298 =>
                  array (
                    'domain' => 'restena.lu',
                    'class' => '0',
                    'country' => 'lu',
                    'state' => '',
                    'name' => 'Fondation RESTENA',
                  ),
                  299 =>
                  array (
                    'domain' => 'aui.ma',
                    'class' => '1',
                    'country' => 'ma',
                    'state' => '',
                    'name' => 'Al Akhawayn University in Ifrane',
                  ),
                  300 =>
                  array (
                    'domain' => 'unet.com.mk',
                    'class' => '4',
                    'country' => 'mk',
                    'state' => '',
                    'name' => 'Unet Ineternet, Skopje',
                  ),
                  301 =>
                  array (
                    'domain' => 'dial.net.mx',
                    'class' => '4',
                    'country' => 'mx',
                    'state' => '',
                    'name' => 'Avantel',
                  ),
                  302 =>
                  array (
                    'domain' => 'ipicyt.edu.mx',
                    'class' => '1',
                    'country' => 'mx',
                    'state' => '',
                    'name' => 'ipicyt, San Luis Potosi',
                  ),
                  303 =>
                  array (
                    'domain' => 'issuecrawler.net',
                    'class' => '0',
                    'country' => 'nl',
                    'state' => '',
                    'name' => 'Govcom.org Foundation',
                  ),
                  304 =>
                  array (
                    'domain' => 'planet.nl',
                    'class' => '4',
                    'country' => 'nl',
                    'state' => '',
                    'name' => 'Planet Media Group N.V.',
                  ),
                  305 =>
                  array (
                    'domain' => 'quicknet.nl',
                    'class' => '4',
                    'country' => 'nl',
                    'state' => '',
                    'name' => 'QuickNet B. V., Alkmaar',
                  ),
                  306 =>
                  array (
                    'domain' => 'rug.nl',
                    'class' => '1',
                    'country' => 'nl',
                    'state' => '',
                    'name' => 'Rijksuniversiteit Groningen',
                  ),
                  307 =>
                  array (
                    'domain' => 'tudelft.nl',
                    'class' => '1',
                    'country' => 'nl',
                    'state' => '',
                    'name' => 'Technische Universiteit Delft, Delft',
                  ),
                  308 =>
                  array (
                    'domain' => 'tue.nl',
                    'class' => '1',
                    'country' => 'nl',
                    'state' => '',
                    'name' => 'Technische Universiteit Eindhoven',
                  ),
                  309 =>
                  array (
                    'domain' => 'chello.nl',
                    'class' => '4',
                    'country' => 'nl',
                    'state' => '',
                    'name' => 'UPC Broadband N.V',
                  ),
                  310 =>
                  array (
                    'domain' => 'utwente.nl',
                    'class' => '1',
                    'country' => 'nl',
                    'state' => '',
                    'name' => 'Universiteit Twente',
                  ),
                  311 =>
                  array (
                    'domain' => 'uu.nl',
                    'class' => '1',
                    'country' => 'nl',
                    'state' => '',
                    'name' => 'Utrecht University',
                  ),
                  312 =>
                  array (
                    'domain' => 'sulphurcanyon.com',
                    'class' => '4',
                    'country' => 'nm',
                    'state' => '',
                    'name' => 'Sulphur Canyon Internet Services',
                  ),
                  313 =>
                  array (
                    'domain' => 'chipcon.no',
                    'class' => '2',
                    'country' => 'no',
                    'state' => '',
                    'name' => 'Chipcon AS',
                  ),
                  314 =>
                  array (
                    'domain' => 'fastsearch.net',
                    'class' => '5',
                    'country' => 'no',
                    'state' => '',
                    'name' => 'Fast Search and Transfer ASA',
                  ),
                  315 =>
                  array (
                    'domain' => 'picsearch.com',
                    'class' => '5',
                    'country' => 'no',
                    'state' => '',
                    'name' => 'Hammarby Fabriksv',
                  ),
                  316 =>
                  array (
                    'domain' => 'nextgentel.com',
                    'class' => '0',
                    'country' => 'no',
                    'state' => '',
                    'name' => 'NextGenTel AS',
                  ),
                  317 =>
                  array (
                    'domain' => 'unik.no',
                    'class' => '1',
                    'country' => 'no',
                    'state' => '',
                    'name' => 'UNIVERSITETSSTUDIENE P305 KJELLER',
                  ),
                  318 =>
                  array (
                    'domain' => 'uib.no',
                    'class' => '1',
                    'country' => 'no',
                    'state' => '',
                    'name' => 'Universitetet i Bergen',
                  ),
                  319 =>
                  array (
                    'domain' => 'ntc.net.np',
                    'class' => '4',
                    'country' => 'np',
                    'state' => '',
                    'name' => 'Napal Telecom Co. Ltd.',
                  ),
                  320 =>
                  array (
                    'domain' => 'clarkson.edu',
                    'class' => '1',
                    'country' => 'ny',
                    'state' => '',
                    'name' => 'Clarkson University',
                  ),
                  321 =>
                  array (
                    'domain' => 'xtra.co.nz',
                    'class' => '4',
                    'country' => 'nz',
                    'state' => '',
                    'name' => 'Telecom IP Limited',
                  ),
                  322 =>
                  array (
                    'domain' => 'paradise.net.nz',
                    'class' => '4',
                    'country' => 'nz',
                    'state' => '',
                    'name' => 'TelstraClear Limited',
                  ),
                  323 =>
                  array (
                    'domain' => 'cwru.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'oh',
                    'name' => 'Case Western Reserve University',
                  ),
                  324 =>
                  array (
                    'domain' => 'telmex.com.pe',
                    'class' => '4',
                    'country' => 'pe',
                    'state' => '',
                    'name' => 'Telmex',
                  ),
                  325 =>
                  array (
                    'domain' => 'pieas.edu.pk',
                    'class' => '1',
                    'country' => 'pk',
                    'state' => '',
                    'name' => 'Pakistan Institute of engineering & Applied Sciences',
                  ),
                  326 =>
                  array (
                    'domain' => 'agh.edu.pl',
                    'class' => '1',
                    'country' => 'pl',
                    'state' => '',
                    'name' => 'Akademia Gorniczo-Hutnicza, Krakow',
                  ),
                  327 =>
                  array (
                    'domain' => 'aster.pl',
                    'class' => '4',
                    'country' => 'pl',
                    'state' => '',
                    'name' => 'Aster City Cable SP. Z O.O., Warszawa',
                  ),
                  328 =>
                  array (
                    'domain' => 'e-wro.net.pl',
                    'class' => '0',
                    'country' => 'pl',
                    'state' => '',
                    'name' => 'DCG DOMINAS CONSULTING GROUP SP. Z O.O., Wroclaw',
                  ),
                  329 =>
                  array (
                    'domain' => 'lublin.pl',
                    'class' => '0',
                    'country' => 'pl',
                    'state' => '',
                    'name' => 'LUBMAN UMCS SP. Z O.O.',
                  ),
                  330 =>
                  array (
                    'domain' => 'pp.com.pl',
                    'class' => '0',
                    'country' => 'pl',
                    'state' => '',
                    'name' => 'PETER PAN KOMPUTERY',
                  ),
                  331 =>
                  array (
                    'domain' => 'tpnet.pl',
                    'class' => '0',
                    'country' => 'pl',
                    'state' => '',
                    'name' => 'TP S.A., Warszawa',
                  ),
                  332 =>
                  array (
                    'domain' => 'kv.net.pl',
                    'class' => '0',
                    'country' => 'pl',
                    'state' => '',
                    'name' => 'TYTUS.NET, Krakow',
                  ),
                  333 =>
                  array (
                    'domain' => 'poznan.pl',
                    'class' => '1',
                    'country' => 'pl',
                    'state' => '',
                    'name' => 'UNIWERSYTET IM.ADAMA MICKIEWICZA, Poznan',
                  ),
                  334 =>
                  array (
                    'domain' => 'uj.edu.pl',
                    'class' => '1',
                    'country' => 'pl',
                    'state' => '',
                    'name' => 'UNIWERSYTET JAGIELLONSKI, Krakow',
                  ),
                  335 =>
                  array (
                    'domain' => 'wroc.pl',
                    'class' => '0',
                    'country' => 'pl',
                    'state' => '',
                    'name' => 'WROCLAWSKIE CENTRUM SIECIOWO-SUPERKOMPUTEROWE PWR., Wroclav',
                  ),
                  336 =>
                  array (
                    'domain' => 'fuw.edu.pl',
                    'class' => '1',
                    'country' => 'pl',
                    'state' => '',
                    'name' => 'WYDZIAL FIZYKI UNIWERSYTETU WARSZAWSKIEGO, Warszawa',
                  ),
                  337 =>
                  array (
                    'domain' => 'coqui.net',
                    'class' => '4',
                    'country' => 'pr',
                    'state' => '',
                    'name' => 'Puerto Rico Telephone Company',
                  ),
                  338 =>
                  array (
                    'domain' => 'uprr.pr',
                    'class' => '1',
                    'country' => 'pr',
                    'state' => '',
                    'name' => 'University of Puerto Rico, Rio Piedras',
                  ),
                  339 =>
                  array (
                    'domain' => 'edil.pub.ro',
                    'class' => '2',
                    'country' => 'ro',
                    'state' => '',
                    'name' => 'EDIL Microelectronics R&D Centre, Bucharest',
                  ),
                  340 =>
                  array (
                    'domain' => 'metav.ro',
                    'class' => '0',
                    'country' => 'ro',
                    'state' => '',
                    'name' => 'Metav S.A., Bucharest',
                  ),
                  341 =>
                  array (
                    'domain' => 'ras.ru',
                    'class' => '1',
                    'country' => 'ru',
                    'state' => '',
                    'name' => 'Center for Science Telecommunications and Information technologies of RAS',
                  ),
                  342 =>
                  array (
                    'domain' => 'imvs.ru',
                    'class' => '2',
                    'country' => 'ru',
                    'state' => '',
                    'name' => 'Institute of Microprocessor Computer Systems State Company',
                  ),
                  343 =>
                  array (
                    'domain' => 'mrsu.ru',
                    'class' => '1',
                    'country' => 'ru',
                    'state' => '',
                    'name' => 'Mordovian State University N.P.Ogareva',
                  ),
                  344 =>
                  array (
                    'domain' => 'miet.ru',
                    'class' => '1',
                    'country' => 'ru',
                    'state' => '',
                    'name' => 'Moscow Institute of Electronic Technology',
                  ),
                  345 =>
                  array (
                    'domain' => 'sci-nnov.ru',
                    'class' => '0',
                    'country' => 'ru',
                    'state' => '',
                    'name' => 'Sandy Info',
                  ),
                  346 =>
                  array (
                    'domain' => 'nsc.ru',
                    'class' => '1',
                    'country' => 'ru',
                    'state' => '',
                    'name' => 'Siberian Branch of Russian Academy of Science',
                  ),
                  347 =>
                  array (
                    'domain' => 'rssi.ru',
                    'class' => '1',
                    'country' => 'ru',
                    'state' => '',
                    'name' => 'Space Research Institute of Russian Academy of Sciences',
                  ),
                  348 =>
                  array (
                    'domain' => 'icomtex.ru',
                    'class' => '0',
                    'country' => 'ru',
                    'state' => '',
                    'name' => 'icomtex.ru',
                  ),
                  349 =>
                  array (
                    'domain' => 'isu.net.sa',
                    'class' => '4',
                    'country' => 'sa',
                    'state' => '',
                    'name' => 'Internet Services Unit',
                  ),
                  350 =>
                  array (
                    'domain' => 'bredbandsbolaget.se',
                    'class' => '4',
                    'country' => 'se',
                    'state' => '',
                    'name' => 'Bredbandsbolaget',
                  ),
                  351 =>
                  array (
                    'domain' => 'chalmers.se',
                    'class' => '1',
                    'country' => 'se',
                    'state' => '',
                    'name' => 'Chalmers Tekneska Hogskola, Goteborg',
                  ),
                  352 =>
                  array (
                    'domain' => 'liu.se',
                    'class' => '1',
                    'country' => 'se',
                    'state' => '',
                    'name' => 'Linkopings Universitet',
                  ),
                  353 =>
                  array (
                    'domain' => 'lu.se',
                    'class' => '1',
                    'country' => 'se',
                    'state' => '',
                    'name' => 'Lunds university',
                  ),
                  354 =>
                  array (
                    'domain' => 'mh.se',
                    'class' => '1',
                    'country' => 'se',
                    'state' => '',
                    'name' => 'Mid Sweden University',
                  ),
                  355 =>
                  array (
                    'domain' => 'kth.se',
                    'class' => '1',
                    'country' => 'se',
                    'state' => '',
                    'name' => 'Royal Institute of Technology, Stockholm',
                  ),
                  356 =>
                  array (
                    'domain' => 'siceit.se',
                    'class' => '4',
                    'country' => 'se',
                    'state' => '',
                    'name' => 'SizeIT Drift AB',
                  ),
                  357 =>
                  array (
                    'domain' => 'songnetworks.se',
                    'class' => '4',
                    'country' => 'se',
                    'state' => '',
                    'name' => 'TDC Song',
                  ),
                  358 =>
                  array (
                    'domain' => 'telia.com',
                    'class' => '0',
                    'country' => 'se',
                    'state' => '',
                    'name' => 'TeliaSonera AB',
                  ),
                  359 =>
                  array (
                    'domain' => 'skanova.com',
                    'class' => '0',
                    'country' => 'se',
                    'state' => '',
                    'name' => 'TeliaSonera AB, Stockholm',
                  ),
                  360 =>
                  array (
                    'domain' => 'chello.se',
                    'class' => '4',
                    'country' => 'se',
                    'state' => '',
                    'name' => 'UPC Broadband N.V',
                  ),
                  361 =>
                  array (
                    'domain' => 'uu.se',
                    'class' => '1',
                    'country' => 'se',
                    'state' => '',
                    'name' => 'Uppsala universitet',
                  ),
                  362 =>
                  array (
                    'domain' => 'ntu.edu.sg',
                    'class' => '1',
                    'country' => 'sg',
                    'state' => '',
                    'name' => 'Namyang Technological University',
                  ),
                  363 =>
                  array (
                    'domain' => 'nus.edu.sg',
                    'class' => '1',
                    'country' => 'sg',
                    'state' => '',
                    'name' => 'National University of Singapore',
                  ),
                  364 =>
                  array (
                    'domain' => 'pacific.net.sg',
                    'class' => '4',
                    'country' => 'sg',
                    'state' => '',
                    'name' => 'Pacific Internet',
                  ),
                  365 =>
                  array (
                    'domain' => 'singnet.com.sg',
                    'class' => '4',
                    'country' => 'sg',
                    'state' => '',
                    'name' => 'SingTel',
                  ),
                  366 =>
                  array (
                    'domain' => 'mystarhub.com.sg',
                    'class' => '4',
                    'country' => 'sg',
                    'state' => '',
                    'name' => 'Starhub',
                  ),
                  367 =>
                  array (
                    'domain' => 'maxonline.com.sg',
                    'class' => '4',
                    'country' => 'sg',
                    'state' => '',
                    'name' => 'maxonline.com.sg',
                  ),
                  368 =>
                  array (
                    'domain' => 'siol.net',
                    'class' => '0',
                    'country' => 'si',
                    'state' => '',
                    'name' => 'SiOL d.o.o',
                  ),
                  369 =>
                  array (
                    'domain' => 'telecom.sk',
                    'class' => '4',
                    'country' => 'sk',
                    'state' => '',
                    'name' => 'Slovak Telecom a.s.',
                  ),
                  370 =>
                  array (
                    'domain' => 'uniba.sk',
                    'class' => '1',
                    'country' => 'sk',
                    'state' => '',
                    'name' => 'Univerzita Komenskeho v Bratislave',
                  ),
                  371 =>
                  array (
                    'domain' => 'asianet.co.th',
                    'class' => '4',
                    'country' => 'th',
                    'state' => '',
                    'name' => 'Asia Infornet co.,ltd',
                  ),
                  372 =>
                  array (
                    'domain' => 'chula.ac.th',
                    'class' => '1',
                    'country' => 'th',
                    'state' => '',
                    'name' => 'Chulalongkorn University, Bangkok',
                  ),
                  373 =>
                  array (
                    'domain' => 'mahidol.ac.th',
                    'class' => '1',
                    'country' => 'th',
                    'state' => '',
                    'name' => 'Mahidol University, Bangkok',
                  ),
                  374 =>
                  array (
                    'domain' => 'psu.ac.th',
                    'class' => '1',
                    'country' => 'th',
                    'state' => '',
                    'name' => 'Prince of Songkla University, Songkhla',
                  ),
                  375 =>
                  array (
                    'domain' => 'bilkent.edu.tr',
                    'class' => '1',
                    'country' => 'tr',
                    'state' => '',
                    'name' => 'Bilkent University, Ankara',
                  ),
                  376 =>
                  array (
                    'domain' => 'doruk.net.tr',
                    'class' => '4',
                    'country' => 'tr',
                    'state' => '',
                    'name' => 'Doruknet',
                  ),
                  377 =>
                  array (
                    'domain' => 'metu.edu.tr',
                    'class' => '1',
                    'country' => 'tr',
                    'state' => '',
                    'name' => 'Middle East Technical University, Ankara',
                  ),
                  378 =>
                  array (
                    'domain' => 'atlas.net.tr',
                    'class' => '4',
                    'country' => 'tr',
                    'state' => '',
                    'name' => 'Telekom Atlas Online',
                  ),
                  379 =>
                  array (
                    'domain' => 'ttnet.net.tr',
                    'class' => '4',
                    'country' => 'tr',
                    'state' => '',
                    'name' => 'Turk Telekom A.S.',
                  ),
                  380 =>
                  array (
                    'domain' => 'ebix.net.tw',
                    'class' => '4',
                    'country' => 'tw',
                    'state' => '',
                    'name' => 'Asia Pacific Online Service Inc',
                  ),
                  381 =>
                  array (
                    'domain' => 'hinet.net',
                    'class' => '4',
                    'country' => 'tw',
                    'state' => '',
                    'name' => 'Chunghwa Telecom Co., Ltd.',
                  ),
                  382 =>
                  array (
                    'domain' => 'seed.net.tw',
                    'class' => '0',
                    'country' => 'tw',
                    'state' => '',
                    'name' => 'Digital United Inc.',
                  ),
                  383 =>
                  array (
                    'domain' => 'fju.edu.tw',
                    'class' => '1',
                    'country' => 'tw',
                    'state' => '',
                    'name' => 'Fu Jen Catholic University, Taipei',
                  ),
                  384 =>
                  array (
                    'domain' => 'itri.org.tw',
                    'class' => '2',
                    'country' => 'tw',
                    'state' => '',
                    'name' => 'Industrial Technology Research Institute',
                  ),
                  385 =>
                  array (
                    'domain' => 'mtksg.com',
                    'class' => '0',
                    'country' => 'tw',
                    'state' => '',
                    'name' => 'Mediatek Singapore Pte Ltd',
                  ),
                  386 =>
                  array (
                    'domain' => 'hcrc.edu.tw',
                    'class' => '1',
                    'country' => 'tw',
                    'state' => '',
                    'name' => 'Ministry of Education Computer Center',
                  ),
                  387 =>
                  array (
                    'domain' => 'ncnu.edu.tw',
                    'class' => '1',
                    'country' => 'tw',
                    'state' => '',
                    'name' => 'National Chi Nan University',
                  ),
                  388 =>
                  array (
                    'domain' => 'nctu.edu.tw',
                    'class' => '1',
                    'country' => 'tw',
                    'state' => '',
                    'name' => 'National Chiao Tung University',
                  ),
                  389 =>
                  array (
                    'domain' => 'nsc.gov.tw',
                    'class' => '3',
                    'country' => 'tw',
                    'state' => '',
                    'name' => 'National Science Council',
                  ),
                  390 =>
                  array (
                    'domain' => 'ntu.edu.tw',
                    'class' => '1',
                    'country' => 'tw',
                    'state' => '',
                    'name' => 'National Taiwan University, Taipei',
                  ),
                  391 =>
                  array (
                    'domain' => 'ntcu.net',
                    'class' => '1',
                    'country' => 'tw',
                    'state' => '',
                    'name' => 'Northern Taiwan Community University',
                  ),
                  392 =>
                  array (
                    'domain' => 'nthu.edu.tw',
                    'class' => '1',
                    'country' => 'tw',
                    'state' => '',
                    'name' => 'Northern Taiwan Community University',
                  ),
                  393 =>
                  array (
                    'domain' => 'roam.com.tw',
                    'class' => '0',
                    'country' => 'tw',
                    'state' => '',
                    'name' => 'ROAM Multimedia Co. Ltd.',
                  ),
                  394 =>
                  array (
                    'domain' => 'roam.net.tw',
                    'class' => '0',
                    'country' => 'tw',
                    'state' => '',
                    'name' => 'ROAM Multimedia Co. Ltd.',
                  ),
                  395 =>
                  array (
                    'domain' => 'stic.gov.tw',
                    'class' => '3',
                    'country' => 'tw',
                    'state' => '',
                    'name' => 'Science & Technology Policy Research and Information Center',
                  ),
                  396 =>
                  array (
                    'domain' => 'so-net.net.tw',
                    'class' => '4',
                    'country' => 'tw',
                    'state' => '',
                    'name' => 'Sony Network Taiwan Limited.',
                  ),
                  397 =>
                  array (
                    'domain' => 'tfn.net.tw',
                    'class' => '4',
                    'country' => 'tw',
                    'state' => '',
                    'name' => 'Taiwan Fixed Network Co.,Ltd., Taipei',
                  ),
                  398 =>
                  array (
                    'domain' => 'tsmc.com.tw',
                    'class' => '2',
                    'country' => 'tw',
                    'state' => '',
                    'name' => 'Taiwan Semiconductor Manufacturing Company, Ltd.',
                  ),
                  399 =>
                  array (
                    'domain' => 'umc.com',
                    'class' => '2',
                    'country' => 'tw',
                    'state' => '',
                    'name' => 'United Microelectronics Corp.',
                  ),
                  400 =>
                  array (
                    'domain' => 'bestnet.ua',
                    'class' => '0',
                    'country' => 'ua',
                    'state' => '',
                    'name' => 'BestNet, Kharkov',
                  ),
                  401 =>
                  array (
                    'domain' => 'kharkov.ua',
                    'class' => '0',
                    'country' => 'ua',
                    'state' => '',
                    'name' => 'Geographical domain for Kharkov',
                  ),
                  402 =>
                  array (
                    'domain' => 'kiev.ua',
                    'class' => '0',
                    'country' => 'ua',
                    'state' => '',
                    'name' => 'Geographical domain for Kiev',
                  ),
                  403 =>
                  array (
                    'domain' => 'gluk.org',
                    'class' => '0',
                    'country' => 'ua',
                    'state' => '',
                    'name' => 'GlasNet-Ukraine, Ltd.',
                  ),
                  404 =>
                  array (
                    'domain' => 'ntl.com',
                    'class' => '4',
                    'country' => 'uk',
                    'state' => '',
                    'name' => 'NTL Internet Ltd',
                  ),
                  405 =>
                  array (
                    'domain' => 'ntli.net',
                    'class' => '0',
                    'country' => 'uk',
                    'state' => '',
                    'name' => 'NTL Internet Ltd',
                  ),
                  406 =>
                  array (
                    'domain' => 'lanl.gov',
                    'class' => '3',
                    'country' => 'us',
                    'state' => '',
                    'name' => 'Los Alamos National Labs',
                  ),
                  407 =>
                  array (
                    'domain' => 'nasa.gov',
                    'class' => '3',
                    'country' => 'us',
                    'state' => '',
                    'name' => 'NASA',
                  ),
                  408 =>
                  array (
                    'domain' => 'nist.gov',
                    'class' => '3',
                    'country' => 'us',
                    'state' => '',
                    'name' => 'NIST',
                  ),
                  409 =>
                  array (
                    'domain' => 'nih.gov',
                    'class' => '3',
                    'country' => 'us',
                    'state' => '',
                    'name' => 'National Institute of Health',
                  ),
                  410 =>
                  array (
                    'domain' => 'nsf.gov',
                    'class' => '3',
                    'country' => 'us',
                    'state' => '',
                    'name' => 'National Science Foundation',
                  ),
                  411 =>
                  array (
                    'domain' => 'af.mil',
                    'class' => '3',
                    'country' => 'us',
                    'state' => '',
                    'name' => 'US Airforce',
                  ),
                  412 =>
                  array (
                    'domain' => 'army.mil',
                    'class' => '3',
                    'country' => 'us',
                    'state' => '',
                    'name' => 'US Army',
                  ),
                  413 =>
                  array (
                    'domain' => 'usda.gov',
                    'class' => '3',
                    'country' => 'us',
                    'state' => '',
                    'name' => 'US Department of Agriculture',
                  ),
                  414 =>
                  array (
                    'domain' => 'navy.mil',
                    'class' => '3',
                    'country' => 'us',
                    'state' => '',
                    'name' => 'US Navy',
                  ),
                  415 =>
                  array (
                    'domain' => 'nipr.mil',
                    'class' => '3',
                    'country' => 'us',
                    'state' => '',
                    'name' => 'Unknown Military',
                  ),
                  416 =>
                  array (
                    'domain' => 'uaf.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ak',
                    'name' => 'University of Alaska Fairbanks',
                  ),
                  417 =>
                  array (
                    'domain' => 'cfdrc.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'al',
                    'name' => 'CFD Research Corporation',
                  ),
                  418 =>
                  array (
                    'domain' => 'usouthal.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'al',
                    'name' => 'University of South Alabama',
                  ),
                  419 =>
                  array (
                    'domain' => 'alltel.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'ar',
                    'name' => 'ALLTEL Communications',
                  ),
                  420 =>
                  array (
                    'domain' => 'asu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'az',
                    'name' => 'Arizona State University',
                  ),
                  421 =>
                  array (
                    'domain' => 'jetstreamwireless.com',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'az',
                    'name' => 'JetStream Wireless',
                  ),
                  422 =>
                  array (
                    'domain' => 'arizona.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'az',
                    'name' => 'University of Arizona',
                  ),
                  423 =>
                  array (
                    'domain' => 'attens.net',
                    'class' => '5',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'AT&T Enhanced Network Services',
                  ),
                  424 =>
                  array (
                    'domain' => 'accelrys.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Accelrys',
                  ),
                  425 =>
                  array (
                    'domain' => 'atgi.net',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Advanced Telcom Group',
                  ),
                  426 =>
                  array (
                    'domain' => 'agilent.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Agilent Technologies',
                  ),
                  427 =>
                  array (
                    'domain' => 'alexa.com',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Alexa Internet',
                  ),
                  428 =>
                  array (
                    'domain' => 'amat.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Applied Materials',
                  ),
                  429 =>
                  array (
                    'domain' => 'ask.com',
                    'class' => '5',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Ask Jeeves, Inc.',
                  ),
                  430 =>
                  array (
                    'domain' => 'teoma.com',
                    'class' => '5',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Ask Jeeves, Inc.',
                  ),
                  431 =>
                  array (
                    'domain' => 'calpoly.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Cal Poly State University',
                  ),
                  432 =>
                  array (
                    'domain' => 'caltech.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'California Institute of Technology',
                  ),
                  433 =>
                  array (
                    'domain' => 'csun.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'California State University, Northridge',
                  ),
                  434 =>
                  array (
                    'domain' => 'covad.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Covad Communications Company',
                  ),
                  435 =>
                  array (
                    'domain' => 'googlebot.com',
                    'class' => '5',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Google Inc.',
                  ),
                  436 =>
                  array (
                    'domain' => 'intel.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Intel Corporation',
                  ),
                  437 =>
                  array (
                    'domain' => 'jeteye.com',
                    'class' => '5',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'JetEye Technologies Inc.',
                  ),
                  438 =>
                  array (
                    'domain' => 'looksmart.com',
                    'class' => '5',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Looksmart, LTD',
                  ),
                  439 =>
                  array (
                    'domain' => 'mediaserve.net',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'MediaServe LLC',
                  ),
                  440 =>
                  array (
                    'domain' => 'o1.com',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Option One Communications',
                  ),
                  441 =>
                  array (
                    'domain' => 'av.com',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Overture Services Inc.',
                  ),
                  442 =>
                  array (
                    'domain' => 'overture.com',
                    'class' => '5',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Overture Services Inc.',
                  ),
                  443 =>
                  array (
                    'domain' => 'pe.net',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Press-Enterprise Co.',
                  ),
                  444 =>
                  array (
                    'domain' => 'saic.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'SAIC',
                  ),
                  445 =>
                  array (
                    'domain' => 'silvaco.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'SILVACO Data Systems',
                  ),
                  446 =>
                  array (
                    'domain' => 'sjsu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'San Jose State University',
                  ),
                  447 =>
                  array (
                    'domain' => 'scu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Santa Clara University',
                  ),
                  448 =>
                  array (
                    'domain' => 'seagate.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Seagate Technology, LLC.',
                  ),
                  449 =>
                  array (
                    'domain' => 'sensitron.net',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Sensitron',
                  ),
                  450 =>
                  array (
                    'domain' => 'spectrolab.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Spectrolab',
                  ),
                  451 =>
                  array (
                    'domain' => 'stanford.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Stanford University',
                  ),
                  452 =>
                  array (
                    'domain' => 'sun.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Sun Microsystems, Inc.',
                  ),
                  453 =>
                  array (
                    'domain' => 'synopsys.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Synopsys, Inc.',
                  ),
                  454 =>
                  array (
                    'domain' => 'telepacific.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'TelePacific Cummunications',
                  ),
                  455 =>
                  array (
                    'domain' => 'isi.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'USC/Information Sciences Institute',
                  ),
                  456 =>
                  array (
                    'domain' => 'berkeley.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'University of California at Berkeley',
                  ),
                  457 =>
                  array (
                    'domain' => 'ucdavis.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'University of California at Davis',
                  ),
                  458 =>
                  array (
                    'domain' => 'uci.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'University of California, Irvine',
                  ),
                  459 =>
                  array (
                    'domain' => 'ucla.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'University of California, Los Angles',
                  ),
                  460 =>
                  array (
                    'domain' => 'ucr.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'University of California, Riverside',
                  ),
                  461 =>
                  array (
                    'domain' => 'ucsd.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'University of California, San Diego',
                  ),
                  462 =>
                  array (
                    'domain' => 'ucsb.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'University of California, Santa Barbara',
                  ),
                  463 =>
                  array (
                    'domain' => 'wj.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Watkins-Johnson Company',
                  ),
                  464 =>
                  array (
                    'domain' => 'algx.net',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'XO Communications',
                  ),
                  465 =>
                  array (
                    'domain' => 'xo.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'XO Communications, Inc.',
                  ),
                  466 =>
                  array (
                    'domain' => 'xilinx.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Xilinx, Inc.',
                  ),
                  467 =>
                  array (
                    'domain' => 'inktomi.com',
                    'class' => '5',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Yahoo! Inc.',
                  ),
                  468 =>
                  array (
                    'domain' => 'inktomisearch.com',
                    'class' => '5',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Yahoo! Inc.',
                  ),
                  469 =>
                  array (
                    'domain' => 'yahoo.com',
                    'class' => '5',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Yahoo! Inc.',
                  ),
                  470 =>
                  array (
                    'domain' => 'turnitin.com',
                    'class' => '5',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'iParadigms Inc.',
                  ),
                  471 =>
                  array (
                    'domain' => 'lcinet.net',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'linkLINE Communications, Inc.',
                  ),
                  472 =>
                  array (
                    'domain' => 'colostate.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'co',
                    'name' => 'Colorado State University',
                  ),
                  473 =>
                  array (
                    'domain' => 'level3.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'co',
                    'name' => 'Level 3 Communications, Inc',
                  ),
                  474 =>
                  array (
                    'domain' => 'qwest.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'co',
                    'name' => 'Qwest Communications International Inc.',
                  ),
                  475 =>
                  array (
                    'domain' => 'twtelecom.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'co',
                    'name' => 'Time Warner Telecom',
                  ),
                  476 =>
                  array (
                    'domain' => 'colorado.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'co',
                    'name' => 'University of Colorado',
                  ),
                  477 =>
                  array (
                    'domain' => 'virtela.com',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'co',
                    'name' => 'Virtela Communications, Inc.',
                  ),
                  478 =>
                  array (
                    'domain' => 'patmedia.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'ct',
                    'name' => 'Patriot Media & Communications, LLC',
                  ),
                  479 =>
                  array (
                    'domain' => 'uconn.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ct',
                    'name' => 'University of Connecticut',
                  ),
                  480 =>
                  array (
                    'domain' => 'howard.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'dc',
                    'name' => 'Howard University',
                  ),
                  481 =>
                  array (
                    'domain' => 'udel.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'de',
                    'name' => 'University of Delaware',
                  ),
                  482 =>
                  array (
                    'domain' => 'fau.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'fl',
                    'name' => 'Florida Atlantic University',
                  ),
                  483 =>
                  array (
                    'domain' => 'fsu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'fl',
                    'name' => 'Florida State University',
                  ),
                  484 =>
                  array (
                    'domain' => 'ucf.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'fl',
                    'name' => 'University of Central Florida',
                  ),
                  485 =>
                  array (
                    'domain' => 'ufl.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'fl',
                    'name' => 'University of Florida',
                  ),
                  486 =>
                  array (
                    'domain' => 'usf.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'fl',
                    'name' => 'University of South Florida',
                  ),
                  487 =>
                  array (
                    'domain' => 'bellsouth.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'ga',
                    'name' => 'Bellsouth Internet Services',
                  ),
                  488 =>
                  array (
                    'domain' => 'cdc.gov',
                    'class' => '3',
                    'country' => 'us',
                    'state' => 'ga',
                    'name' => 'Centers for Disease Control and Prevention',
                  ),
                  489 =>
                  array (
                    'domain' => 'cau.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ga',
                    'name' => 'Clark Atlanta University',
                  ),
                  490 =>
                  array (
                    'domain' => 'coastalnow.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'ga',
                    'name' => 'Coastal Communications',
                  ),
                  491 =>
                  array (
                    'domain' => 'cox.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'ga',
                    'name' => 'Cox Communications',
                  ),
                  492 =>
                  array (
                    'domain' => 'earthlink.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'ga',
                    'name' => 'Earthlink, Inc.',
                  ),
                  493 =>
                  array (
                    'domain' => 'lightspeed.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'ga',
                    'name' => 'Earthlink, Inc.',
                  ),
                  494 =>
                  array (
                    'domain' => 'mindspring.com',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'ga',
                    'name' => 'Earthlink, Inc.',
                  ),
                  495 =>
                  array (
                    'domain' => 'gatech.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ga',
                    'name' => 'Georgia Institute of Technology',
                  ),
                  496 =>
                  array (
                    'domain' => 'knology.net',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'ga',
                    'name' => 'KNOLOGY Holdings, Inc.',
                  ),
                  497 =>
                  array (
                    'domain' => 'lordaecksargent.com',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'ga',
                    'name' => 'Lord, Aeck & Sargent',
                  ),
                  498 =>
                  array (
                    'domain' => 'noment.net',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'ga',
                    'name' => 'Noment Networks',
                  ),
                  499 =>
                  array (
                    'domain' => 'ameslab.gov',
                    'class' => '3',
                    'country' => 'us',
                    'state' => 'ia',
                    'name' => 'DOE Ames Laboratory (Iowa State',
                  ),
                  500 =>
                  array (
                    'domain' => 'iastate.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ia',
                    'name' => 'Iowa State University',
                  ),
                  501 =>
                  array (
                    'domain' => 'micron.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'id',
                    'name' => 'Micron Technology',
                  ),
                  502 =>
                  array (
                    'domain' => 'tcd.ie',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ie',
                    'name' => 'University of Dublin Trinity College',
                  ),
                  503 =>
                  array (
                    'domain' => 'prserv.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'il',
                    'name' => 'AT&T Global Network Services',
                  ),
                  504 =>
                  array (
                    'domain' => 'bradley.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'il',
                    'name' => 'Bradley University',
                  ),
                  505 =>
                  array (
                    'domain' => 'dmisinetworks.com',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'il',
                    'name' => 'Fusion Broadband',
                  ),
                  506 =>
                  array (
                    'domain' => 'dmisinetworks.net',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'il',
                    'name' => 'Fusion Broadband',
                  ),
                  507 =>
                  array (
                    'domain' => 'iit.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'il',
                    'name' => 'Illinois Institute of Technology',
                  ),
                  508 =>
                  array (
                    'domain' => 'flexabit.net',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'il',
                    'name' => 'McLeodUSA',
                  ),
                  509 =>
                  array (
                    'domain' => 'motorola.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'il',
                    'name' => 'Motorola Inc.',
                  ),
                  510 =>
                  array (
                    'domain' => 'sc03.org',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'il',
                    'name' => 'National Center for Supercomputing Applications',
                  ),
                  511 =>
                  array (
                    'domain' => 'niu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'il',
                    'name' => 'Northern Illinois University',
                  ),
                  512 =>
                  array (
                    'domain' => 'northwestern.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'il',
                    'name' => 'Northwestern University',
                  ),
                  513 =>
                  array (
                    'domain' => 'procommcable.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'il',
                    'name' => 'Procomm Solutions',
                  ),
                  514 =>
                  array (
                    'domain' => 'scnet.net',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'il',
                    'name' => 'Server Central Network',
                  ),
                  515 =>
                  array (
                    'domain' => 'siu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'il',
                    'name' => 'Southern Illinois University',
                  ),
                  516 =>
                  array (
                    'domain' => 'popsite.net',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'il',
                    'name' => 'StarNet, Inc.',
                  ),
                  517 =>
                  array (
                    'domain' => 'travelclick.net',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'il',
                    'name' => 'TravelCLICK Inc.',
                  ),
                  518 =>
                  array (
                    'domain' => 'uchicago.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'il',
                    'name' => 'University of Chicago',
                  ),
                  519 =>
                  array (
                    'domain' => 'uic.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'il',
                    'name' => 'University of Illinois at Chicago',
                  ),
                  520 =>
                  array (
                    'domain' => 'uiuc.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'il',
                    'name' => 'University of Illinois at Urbana Champaign',
                  ),
                  521 =>
                  array (
                    'domain' => 'bsu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'in',
                    'name' => 'Ball State University',
                  ),
                  522 =>
                  array (
                    'domain' => 'bloomingdaletel.com',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'in',
                    'name' => 'Bloomingdale Telephone',
                  ),
                  523 =>
                  array (
                    'domain' => 'ffni.com',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'in',
                    'name' => 'Fairnet, LLC.',
                  ),
                  524 =>
                  array (
                    'domain' => 'in-motion.net',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'in',
                    'name' => 'In-Motion Inc.',
                  ),
                  525 =>
                  array (
                    'domain' => 'indiana.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'in',
                    'name' => 'Indiana University',
                  ),
                  526 =>
                  array (
                    'domain' => 'iupui.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'in',
                    'name' => 'Indiana University-Purdue University at Indianapolis',
                  ),
                  527 =>
                  array (
                    'domain' => 'ssi-pci.net',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'in',
                    'name' => 'NameMyDots.Com, Inc.',
                  ),
                  528 =>
                  array (
                    'domain' => 'purdue.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'in',
                    'name' => 'Purdue University',
                  ),
                  529 =>
                  array (
                    'domain' => 'riverwalk-apts.com',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'in',
                    'name' => 'River Walk Apartments',
                  ),
                  530 =>
                  array (
                    'domain' => 'nd.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'in',
                    'name' => 'University of Notre Dame',
                  ),
                  531 =>
                  array (
                    'domain' => 'wintek.com',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'in',
                    'name' => 'Wintek Corporation',
                  ),
                  532 =>
                  array (
                    'domain' => 'ksu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ks',
                    'name' => 'Kansas State University',
                  ),
                  533 =>
                  array (
                    'domain' => 'dialsprint.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'ks',
                    'name' => 'Sprint Communications Company L.P.',
                  ),
                  534 =>
                  array (
                    'domain' => 'sprintbbd.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'ks',
                    'name' => 'Sprint Communications Company L.P.',
                  ),
                  535 =>
                  array (
                    'domain' => 'spcsdns.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'ks',
                    'name' => 'Sprint PCS',
                  ),
                  536 =>
                  array (
                    'domain' => 'louisville.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ky',
                    'name' => 'University of Louisville',
                  ),
                  537 =>
                  array (
                    'domain' => 'lsu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'la',
                    'name' => 'Louisiana State University',
                  ),
                  538 =>
                  array (
                    'domain' => 'louisiana.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'la',
                    'name' => 'University of Louisiana at Lafayette',
                  ),
                  539 =>
                  array (
                    'domain' => 'bu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ma',
                    'name' => 'Boston University',
                  ),
                  540 =>
                  array (
                    'domain' => 'conversent.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'ma',
                    'name' => 'Conversent Communications',
                  ),
                  541 =>
                  array (
                    'domain' => 'harvard.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ma',
                    'name' => 'Harvard University',
                  ),
                  542 =>
                  array (
                    'domain' => 'hhpublications.com',
                    'class' => '6',
                    'country' => 'us',
                    'state' => 'ma',
                    'name' => 'Horizon House Publications',
                  ),
                  543 =>
                  array (
                    'domain' => 'mitre.org',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ma',
                    'name' => 'MITRE Corporation',
                  ),
                  544 =>
                  array (
                    'domain' => 'mit.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ma',
                    'name' => 'Massachusetts Institute of Technology',
                  ),
                  545 =>
                  array (
                    'domain' => 'neu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ma',
                    'name' => 'Northeastern University',
                  ),
                  546 =>
                  array (
                    'domain' => 'raytheon.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ma',
                    'name' => 'Raytheon Company',
                  ),
                  547 =>
                  array (
                    'domain' => 'townisp.com',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'ma',
                    'name' => 'SHREWSBURY ELECTRIC & COMMUNITY CABLE',
                  ),
                  548 =>
                  array (
                    'domain' => 'umass.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ma',
                    'name' => 'University of Massachusetts',
                  ),
                  549 =>
                  array (
                    'domain' => 'wpi.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ma',
                    'name' => 'Worcester Polytechnic Institute',
                  ),
                  550 =>
                  array (
                    'domain' => 'w3.org',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ma',
                    'name' => 'World Wide Web Consortium',
                  ),
                  551 =>
                  array (
                    'domain' => 'chesapeake.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'md',
                    'name' => 'Chesapeake College',
                  ),
                  552 =>
                  array (
                    'domain' => 'dmv.com',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'md',
                    'name' => 'DelMarVa OnLine',
                  ),
                  553 =>
                  array (
                    'domain' => 'morgan.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'md',
                    'name' => 'Morgan State University',
                  ),
                  554 =>
                  array (
                    'domain' => 'tsi-telsys.com',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'md',
                    'name' => 'TSI Telsys, Inc.',
                  ),
                  555 =>
                  array (
                    'domain' => 'umd.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'md',
                    'name' => 'University of Maryland',
                  ),
                  556 =>
                  array (
                    'domain' => 'delphi.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'mi',
                    'name' => 'Delphi Automotive Systems',
                  ),
                  557 =>
                  array (
                    'domain' => 'dow.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'mi',
                    'name' => 'Dow Chemical Company',
                  ),
                  558 =>
                  array (
                    'domain' => 'gvsu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'mi',
                    'name' => 'Grand Valley State University',
                  ),
                  559 =>
                  array (
                    'domain' => 'msu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'mi',
                    'name' => 'Michigan State University',
                  ),
                  560 =>
                  array (
                    'domain' => 'mtu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'mi',
                    'name' => 'Michigan Technological University',
                  ),
                  561 =>
                  array (
                    'domain' => 'qltd.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'mi',
                    'name' => 'QLTD',
                  ),
                  562 =>
                  array (
                    'domain' => 'ussignalcom.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'mi',
                    'name' => 'RVP Development',
                  ),
                  563 =>
                  array (
                    'domain' => 'umich.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'mi',
                    'name' => 'University of Michigan',
                  ),
                  564 =>
                  array (
                    'domain' => 'mmm.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'mn',
                    'name' => '3M Company',
                  ),
                  565 =>
                  array (
                    'domain' => 'umn.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'mn',
                    'name' => 'University of Minnesota',
                  ),
                  566 =>
                  array (
                    'domain' => 'charter.com',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'mo',
                    'name' => 'Charter Communications',
                  ),
                  567 =>
                  array (
                    'domain' => 'charterpipeline.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'mo',
                    'name' => 'Charter Communications',
                  ),
                  568 =>
                  array (
                    'domain' => 'charter-stl.com',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'mo',
                    'name' => 'Charter Communications, St Louis',
                  ),
                  569 =>
                  array (
                    'domain' => 'umr.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'mo',
                    'name' => 'University of Missouri-Rolla',
                  ),
                  570 =>
                  array (
                    'domain' => 'jsums.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ms',
                    'name' => 'Jackson State University',
                  ),
                  571 =>
                  array (
                    'domain' => 'msstate.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ms',
                    'name' => 'Mississippi State University',
                  ),
                  572 =>
                  array (
                    'domain' => 'montana.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'mt',
                    'name' => 'Montana State University',
                  ),
                  573 =>
                  array (
                    'domain' => 'duke.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'nc',
                    'name' => 'Duke University',
                  ),
                  574 =>
                  array (
                    'domain' => 'goodrich.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'nc',
                    'name' => 'Goodrich Corporation',
                  ),
                  575 =>
                  array (
                    'domain' => 'ncsu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'nc',
                    'name' => 'North Carolina State University',
                  ),
                  576 =>
                  array (
                    'domain' => 'uslec.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'nc',
                    'name' => 'USLEC Corp.',
                  ),
                  577 =>
                  array (
                    'domain' => 'uncc.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'nc',
                    'name' => 'University of North Carolina at Charlotte',
                  ),
                  578 =>
                  array (
                    'domain' => 'alhyde.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'nj',
                    'name' => 'A.L.Hyde Company',
                  ),
                  579 =>
                  array (
                    'domain' => 'att.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'nj',
                    'name' => 'AT&T Corp.',
                  ),
                  580 =>
                  array (
                    'domain' => 'adp.com',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'nj',
                    'name' => 'Automatic Data Processing, Inc.',
                  ),
                  581 =>
                  array (
                    'domain' => 'ge.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'nj',
                    'name' => 'General Electric Company',
                  ),
                  582 =>
                  array (
                    'domain' => 'njit.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'nj',
                    'name' => 'New Jersey Institute of Technology',
                  ),
                  583 =>
                  array (
                    'domain' => 'pppl.gov',
                    'class' => '3',
                    'country' => 'us',
                    'state' => 'nj',
                    'name' => 'Princeton Plasma Physics Laboratory',
                  ),
                  584 =>
                  array (
                    'domain' => 'princeton.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'nj',
                    'name' => 'Princeton University',
                  ),
                  585 =>
                  array (
                    'domain' => 'rcn.com',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'nj',
                    'name' => 'RCN',
                  ),
                  586 =>
                  array (
                    'domain' => 'smartcity.com',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'nj',
                    'name' => 'Smart City Solutions',
                  ),
                  587 =>
                  array (
                    'domain' => 'gigablast.com',
                    'class' => '5',
                    'country' => 'us',
                    'state' => 'nm',
                    'name' => 'Matt Wells',
                  ),
                  588 =>
                  array (
                    'domain' => 'nmsu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'nm',
                    'name' => 'New Mexico State University',
                  ),
                  589 =>
                  array (
                    'domain' => 'unlv.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'nv',
                    'name' => 'University of Nevada at Las Vegas',
                  ),
                  590 =>
                  array (
                    'domain' => 'attbi.com',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'AT&T Corp. (Comcast',
                  ),
                  591 =>
                  array (
                    'domain' => 'bnl.gov',
                    'class' => '3',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'Brookhaven National Laboratory',
                  ),
                  592 =>
                  array (
                    'domain' => 'optonline.net',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'CSC Holdings, Inc',
                  ),
                  593 =>
                  array (
                    'domain' => 'choiceone.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'Choice One OnLine, Inc.',
                  ),
                  594 =>
                  array (
                    'domain' => 'cloud9.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'Cloud 9 Internet',
                  ),
                  595 =>
                  array (
                    'domain' => 'columbia.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'Columbia University',
                  ),
                  596 =>
                  array (
                    'domain' => 'cornell.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'Cornell University',
                  ),
                  597 =>
                  array (
                    'domain' => 'corning.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'Corning Incorporated',
                  ),
                  598 =>
                  array (
                    'domain' => 'deshaw.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'D. E. Shaw & Co., L. P.',
                  ),
                  599 =>
                  array (
                    'domain' => 'kodak.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'Eastman Kodak',
                  ),
                  600 =>
                  array (
                    'domain' => 'edelman.com',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'Edelman Public Relations',
                  ),
                  601 =>
                  array (
                    'domain' => 'ibm.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'IBM Corporation',
                  ),
                  602 =>
                  array (
                    'domain' => 'insightbb.com',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'Insight Communications',
                  ),
                  603 =>
                  array (
                    'domain' => 'mchsi.com',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'Mediacom Communications Corporation',
                  ),
                  604 =>
                  array (
                    'domain' => 'midtel.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'Middleburg Telephone Co.',
                  ),
                  605 =>
                  array (
                    'domain' => 'rpi.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'Rensselaer Polytechnic Institute',
                  ),
                  606 =>
                  array (
                    'domain' => 'rit.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'Rochester Institute of Technology',
                  ),
                  607 =>
                  array (
                    'domain' => 'russreyn.com',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'Russell Reynolds Associates Inc',
                  ),
                  608 =>
                  array (
                    'domain' => 'sunyit.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'SUNY Institute of Technology at Utica/Rome',
                  ),
                  609 =>
                  array (
                    'domain' => 'binghamton.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'SUNY at Binghamton',
                  ),
                  610 =>
                  array (
                    'domain' => 'sunysb.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'SUNY at Stony Brook',
                  ),
                  611 =>
                  array (
                    'domain' => 'buffalo.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'State University of New York at Buffalo',
                  ),
                  612 =>
                  array (
                    'domain' => 'stonybrook.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'State University of New York/Stony Brook',
                  ),
                  613 =>
                  array (
                    'domain' => 'syr.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'Syracuse University',
                  ),
                  614 =>
                  array (
                    'domain' => 'xerox.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'Xerox Corporation',
                  ),
                  615 =>
                  array (
                    'domain' => 'fuse.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'oh',
                    'name' => 'Cincinnati Bell Telephone',
                  ),
                  616 =>
                  array (
                    'domain' => 'convergys.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'oh',
                    'name' => 'Convergys Inc',
                  ),
                  617 =>
                  array (
                    'domain' => 'muohio.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'oh',
                    'name' => 'Miami University',
                  ),
                  618 =>
                  array (
                    'domain' => 'ohio-state.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'oh',
                    'name' => 'Ohio State University',
                  ),
                  619 =>
                  array (
                    'domain' => 'ohiou.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'oh',
                    'name' => 'Ohio University',
                  ),
                  620 =>
                  array (
                    'domain' => 'sierralobo.com',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'oh',
                    'name' => 'Sierra Lobo, Inc.',
                  ),
                  621 =>
                  array (
                    'domain' => 'uc.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'oh',
                    'name' => 'University of Cincinnati',
                  ),
                  622 =>
                  array (
                    'domain' => 'utoledo.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'oh',
                    'name' => 'University of Toledo',
                  ),
                  623 =>
                  array (
                    'domain' => 'okstate.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ok',
                    'name' => 'Oklahoma State University',
                  ),
                  624 =>
                  array (
                    'domain' => 'ou.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ok',
                    'name' => 'University of Oklahoma',
                  ),
                  625 =>
                  array (
                    'domain' => 'mentorg.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'or',
                    'name' => 'Mentor Graphics Corporation',
                  ),
                  626 =>
                  array (
                    'domain' => 'ogi.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'or',
                    'name' => 'OGI School of Science and Engineering',
                  ),
                  627 =>
                  array (
                    'domain' => 'pdx.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'or',
                    'name' => 'Portland State University',
                  ),
                  628 =>
                  array (
                    'domain' => 'adelphia.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'pa',
                    'name' => 'Adelphia Communications Corp.',
                  ),
                  629 =>
                  array (
                    'domain' => 'agere.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'pa',
                    'name' => 'Agere Systems, Inc.',
                  ),
                  630 =>
                  array (
                    'domain' => 'zoominternet.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'pa',
                    'name' => 'Armstrong Cable Services',
                  ),
                  631 =>
                  array (
                    'domain' => 'cmu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'pa',
                    'name' => 'Carnegie-Mellon University',
                  ),
                  632 =>
                  array (
                    'domain' => 'comcast.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'pa',
                    'name' => 'Comcast Corporation',
                  ),
                  633 =>
                  array (
                    'domain' => 'lutron.com',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'pa',
                    'name' => 'Lutron Electronics Co., Inc.',
                  ),
                  634 =>
                  array (
                    'domain' => 'psu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'pa',
                    'name' => 'Pennsylvania State University',
                  ),
                  635 =>
                  array (
                    'domain' => 'temple.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'pa',
                    'name' => 'Temple University',
                  ),
                  636 =>
                  array (
                    'domain' => 'upenn.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'pa',
                    'name' => 'University of Pennsylvania',
                  ),
                  637 =>
                  array (
                    'domain' => 'pitt.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'pa',
                    'name' => 'University of Pittsburgh',
                  ),
                  638 =>
                  array (
                    'domain' => 'hks.com',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'ri',
                    'name' => 'ABAQUS, Inc',
                  ),
                  639 =>
                  array (
                    'domain' => 'brown.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ri',
                    'name' => 'Brown University',
                  ),
                  640 =>
                  array (
                    'domain' => 'lodgenet.net',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'sd',
                    'name' => 'LodgeNet Entertainment Company',
                  ),
                  641 =>
                  array (
                    'domain' => 'cti-pet.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'tn',
                    'name' => 'CTI Molecular Imaging, Inc.',
                  ),
                  642 =>
                  array (
                    'domain' => 'utk.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'tn',
                    'name' => 'University of Tennessee',
                  ),
                  643 =>
                  array (
                    'domain' => 'vanderbilt.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'tn',
                    'name' => 'Vanderbilt University',
                  ),
                  644 =>
                  array (
                    'domain' => 'cox-internet.com',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'Cox Communications',
                  ),
                  645 =>
                  array (
                    'domain' => 'ev1servers.net',
                    'class' => '5',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'Ev1Servers.net',
                  ),
                  646 =>
                  array (
                    'domain' => 'freescale.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'Freescale Semiconductor, Inc.',
                  ),
                  647 =>
                  array (
                    'domain' => 'rice.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'Rice University',
                  ),
                  648 =>
                  array (
                    'domain' => 'ameritech.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'SBC Internet Services, Inc.',
                  ),
                  649 =>
                  array (
                    'domain' => 'pacbell.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'SBC Internet Services, Inc.',
                  ),
                  650 =>
                  array (
                    'domain' => 'snet.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'SBC Internet Services, Inc.',
                  ),
                  651 =>
                  array (
                    'domain' => 'swbell.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'SBC Internet Services, Inc.',
                  ),
                  652 =>
                  array (
                    'domain' => 'swri.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'Southwest Research Institute, San Antonio',
                  ),
                  653 =>
                  array (
                    'domain' => 'tamu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'Texas A&M University',
                  ),
                  654 =>
                  array (
                    'domain' => 'tamuk.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'Texas A&M University - Kingsville',
                  ),
                  655 =>
                  array (
                    'domain' => 'ti.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'Texas Instruments, Inc.',
                  ),
                  656 =>
                  array (
                    'domain' => 'ttu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'Texas Tech University',
                  ),
                  657 =>
                  array (
                    'domain' => 'theplanet.com',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'The Planet Internet Services, Inc.',
                  ),
                  658 =>
                  array (
                    'domain' => 'uh.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'University of Houston',
                  ),
                  659 =>
                  array (
                    'domain' => 'uta.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'University of Texas at Arlington',
                  ),
                  660 =>
                  array (
                    'domain' => 'utexas.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'University of Texas at Austin',
                  ),
                  661 =>
                  array (
                    'domain' => 'utep.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'University of Texas at El Paso',
                  ),
                  662 =>
                  array (
                    'domain' => 'utsa.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'University of Texas at San Antonio',
                  ),
                  663 =>
                  array (
                    'domain' => 'verizon.net',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'Verison',
                  ),
                  664 =>
                  array (
                    'domain' => 'dsl-verizon.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'Verizon',
                  ),
                  665 =>
                  array (
                    'domain' => 'vzavenue.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'Verizon',
                  ),
                  666 =>
                  array (
                    'domain' => 'usu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ut',
                    'name' => 'Utah State University',
                  ),
                  667 =>
                  array (
                    'domain' => 'stsn.com',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'ut',
                    'name' => 'iBAHN (formerly STSN',
                  ),
                  668 =>
                  array (
                    'domain' => 'aol.com',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'va',
                    'name' => 'America Online, Inc.',
                  ),
                  669 =>
                  array (
                    'domain' => 'bnsi.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'va',
                    'name' => 'Broadband Network Services Inc.',
                  ),
                  670 =>
                  array (
                    'domain' => 'cryptic.net',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'va',
                    'name' => 'Cryptic Net Communications',
                  ),
                  671 =>
                  array (
                    'domain' => 'gmai.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'va',
                    'name' => 'GMA Industries, Inc.',
                  ),
                  672 =>
                  array (
                    'domain' => 'jrmtech.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'va',
                    'name' => 'Joseph Russ Moulton, jr.',
                  ),
                  673 =>
                  array (
                    'domain' => 'marketscore.com',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'va',
                    'name' => 'Marketscore Inc',
                  ),
                  674 =>
                  array (
                    'domain' => 'ntc-com.net',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'va',
                    'name' => 'NTC, LLC',
                  ),
                  675 =>
                  array (
                    'domain' => 'newskies.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'va',
                    'name' => 'New Skies Networks, Inc.',
                  ),
                  676 =>
                  array (
                    'domain' => 'rr.com',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'va',
                    'name' => 'Road Runner Hold Co, LLC',
                  ),
                  677 =>
                  array (
                    'domain' => 'src.org',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'va',
                    'name' => 'Semiconductor Research Corporation',
                  ),
                  678 =>
                  array (
                    'domain' => 'sysplan.com',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'va',
                    'name' => 'System Planning Corporation',
                  ),
                  679 =>
                  array (
                    'domain' => 'uu.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'va',
                    'name' => 'UUNET Technologies, Inc.',
                  ),
                  680 =>
                  array (
                    'domain' => 'virginia.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'va',
                    'name' => 'University of Virginia',
                  ),
                  681 =>
                  array (
                    'domain' => 'gte.net',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'va',
                    'name' => 'Verizon',
                  ),
                  682 =>
                  array (
                    'domain' => 'cray.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'wa',
                    'name' => 'Cray Inc.',
                  ),
                  683 =>
                  array (
                    'domain' => 'ncplus.net',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'wa',
                    'name' => 'Komko, Inc',
                  ),
                  684 =>
                  array (
                    'domain' => 'search.msn.com',
                    'class' => '5',
                    'country' => 'us',
                    'state' => 'wa',
                    'name' => 'Microsoft',
                  ),
                  685 =>
                  array (
                    'domain' => 'msn.com',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'wa',
                    'name' => 'Microsoft Corporation',
                  ),
                  686 =>
                  array (
                    'domain' => 'transedge.com',
                    'class' => '0',
                    'country' => 'us',
                    'state' => 'wa',
                    'name' => 'New Edge Networks.',
                  ),
                  687 =>
                  array (
                    'domain' => 'nctv.com',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'wa',
                    'name' => 'Northland Cable Television',
                  ),
                  688 =>
                  array (
                    'domain' => 'pnl.gov',
                    'class' => '3',
                    'country' => 'us',
                    'state' => 'wa',
                    'name' => 'Pacific Northwest National Laboratory',
                  ),
                  689 =>
                  array (
                    'domain' => 'tmodns.net',
                    'class' => '4',
                    'country' => 'us',
                    'state' => 'wa',
                    'name' => 'T-Mobile USA',
                  ),
                  690 =>
                  array (
                    'domain' => 'washington.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'wa',
                    'name' => 'University of Washington',
                  ),
                  691 =>
                  array (
                    'domain' => 'wsu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'wa',
                    'name' => 'Washington State University',
                  ),
                  692 =>
                  array (
                    'domain' => 'wisc.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'wi',
                    'name' => 'University of Wisconsin',
                  ),
                  693 =>
                  array (
                    'domain' => 'cantv.net',
                    'class' => '0',
                    'country' => 've',
                    'state' => '',
                    'name' => 'CANTV Servicios, Caracas',
                  ),
                  694 =>
                  array (
                    'domain' => 'genesisbci.net',
                    'class' => '0',
                    'country' => 've',
                    'state' => '',
                    'name' => 'Genesis Telecom C.A.',
                  ),
                  695 =>
                  array (
                    'domain' => 'bg.ac.yu',
                    'class' => '1',
                    'country' => 'yu',
                    'state' => '',
                    'name' => 'Univerzitet u Beogradu',
                  ),
                  696 =>
                  array (
                    'domain' => 'saix.net',
                    'class' => '0',
                    'country' => 'za',
                    'state' => '',
                    'name' => 'Telkom SA Ltd',
                  ),
                  697 =>
                  array (
                    'domain' => 'sfu.ca',
                    'class' => '1',
                    'country' => 'ca',
                    'state' => 'bc',
                    'name' => 'Simon Fraser University',
                  ),
                  698 =>
                  array (
                    'domain' => 'uwaterloo.ca',
                    'class' => '1',
                    'country' => 'ca',
                    'state' => 'on',
                    'name' => 'University of Waterloo',
                  ),
                  699 =>
                  array (
                    'domain' => 'auburn.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'al',
                    'name' => 'Auburn University',
                  ),
                  700 =>
                  array (
                    'domain' => 'uark.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ar',
                    'name' => 'University of Arkansas, Fayetteville',
                  ),
                  701 =>
                  array (
                    'domain' => 'csupomona.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'California State Polytechnic University, Pomona',
                  ),
                  702 =>
                  array (
                    'domain' => 'glendale.cc.ca.us',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Glendale Community College',
                  ),
                  703 =>
                  array (
                    'domain' => 'untd.com',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'United Online, Inc.',
                  ),
                  704 =>
                  array (
                    'domain' => 'ucsc.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'University of California, Santa Cruz',
                  ),
                  705 =>
                  array (
                    'domain' => 'du.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'co',
                    'name' => 'University of Denver',
                  ),
                  706 =>
                  array (
                    'domain' => 'yale.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ct',
                    'name' => 'Yale University',
                  ),
                  707 =>
                  array (
                    'domain' => 'fiu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'fl',
                    'name' => 'Florida International University',
                  ),
                  708 =>
                  array (
                    'domain' => 'uiowa.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ia',
                    'name' => 'University of Iowa',
                  ),
                  709 =>
                  array (
                    'domain' => 'sxu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'il',
                    'name' => 'Saint Xavier University',
                  ),
                  710 =>
                  array (
                    'domain' => 'uno.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'la',
                    'name' => 'University of New Orleans',
                  ),
                  711 =>
                  array (
                    'domain' => 'xula.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'la',
                    'name' => 'Xavier University of Louisiana',
                  ),
                  712 =>
                  array (
                    'domain' => 'bc.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ma',
                    'name' => 'Boston College',
                  ),
                  713 =>
                  array (
                    'domain' => 'kopin.com',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ma',
                    'name' => 'Kopin Corporation',
                  ),
                  714 =>
                  array (
                    'domain' => 'jhu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'md',
                    'name' => 'Johns Hopkins University',
                  ),
                  715 =>
                  array (
                    'domain' => 'umbc.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'md',
                    'name' => 'University of Maryland Baltimore County',
                  ),
                  716 =>
                  array (
                    'domain' => 'wayne.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'mi',
                    'name' => 'Wayne State University',
                  ),
                  717 =>
                  array (
                    'domain' => 'mnscu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'mn',
                    'name' => 'Minnesota State Colleges and Universities',
                  ),
                  718 =>
                  array (
                    'domain' => 'wfu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'nc',
                    'name' => 'Wake Forest University',
                  ),
                  719 =>
                  array (
                    'domain' => 'unh.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'nh',
                    'name' => 'University of New Hampshire',
                  ),
                  720 =>
                  array (
                    'domain' => 'rowan.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'nj',
                    'name' => 'Rowan College of New Jersey',
                  ),
                  721 =>
                  array (
                    'domain' => 'unm.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'nm',
                    'name' => 'University of New Mexico',
                  ),
                  722 =>
                  array (
                    'domain' => 'nyit.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'New York Institute of Technology',
                  ),
                  723 =>
                  array (
                    'domain' => 'nyu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'New York University',
                  ),
                  724 =>
                  array (
                    'domain' => 'albany.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'State University of New York, Albany',
                  ),
                  725 =>
                  array (
                    'domain' => 'usma.army.mil',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'US Military Academy at West Point',
                  ),
                  726 =>
                  array (
                    'domain' => 'lehigh.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'pa',
                    'name' => 'Lehigh University',
                  ),
                  727 =>
                  array (
                    'domain' => 'sdsmt.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'sd',
                    'name' => 'South Dakota School of Mines & Technology',
                  ),
                  728 =>
                  array (
                    'domain' => 'cbu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'tn',
                    'name' => 'Christian Brothers University',
                  ),
                  729 =>
                  array (
                    'domain' => 'accd.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'Alamo Community College District',
                  ),
                  730 =>
                  array (
                    'domain' => 'smu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'Southern Methodist University',
                  ),
                  731 =>
                  array (
                    'domain' => 'utah.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'ut',
                    'name' => 'University of Utah',
                  ),
                  732 =>
                  array (
                    'domain' => 'odu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'va',
                    'name' => 'Old Dominion University',
                  ),
                  733 =>
                  array (
                    'domain' => 'vcu.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'va',
                    'name' => 'Virginia Commonwealth University',
                  ),
                  734 =>
                  array (
                    'domain' => 'cvtc.edu',
                    'class' => '1',
                    'country' => 'us',
                    'state' => 'wi',
                    'name' => 'Chippewa Valley Technical College',
                  ),
                  735 =>
                  array (
                    'domain' => 'celestica.com',
                    'class' => '2',
                    'country' => 'ca',
                    'state' => 'on',
                    'name' => 'Celestica, Inc',
                  ),
                  736 =>
                  array (
                    'domain' => 'sogetel.net',
                    'class' => '2',
                    'country' => 'ca',
                    'state' => 'qb',
                    'name' => 'Sogetel inc.',
                  ),
                  737 =>
                  array (
                    'domain' => 'cypress.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Cypress Semiconductor',
                  ),
                  738 =>
                  array (
                    'domain' => 'gat.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'General Atomics',
                  ),
                  739 =>
                  array (
                    'domain' => 'hp.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Hewlett-Packard Company',
                  ),
                  740 =>
                  array (
                    'domain' => 'northropgrumman.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Northrop Grumman Corporation',
                  ),
                  741 =>
                  array (
                    'domain' => 'qualcomm.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Qualcomm Inc.',
                  ),
                  742 =>
                  array (
                    'domain' => 'quantum.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Quantum Corporation',
                  ),
                  743 =>
                  array (
                    'domain' => 'rdl.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Research & Development Laboratories',
                  ),
                  744 =>
                  array (
                    'domain' => 'itnes.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'co',
                    'name' => 'ITN Energy Systems',
                  ),
                  745 =>
                  array (
                    'domain' => 'lilly.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'in',
                    'name' => 'Eli Lilly and Company',
                  ),
                  746 =>
                  array (
                    'domain' => 'guidant.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'in',
                    'name' => 'Guidant Corporation',
                  ),
                  747 =>
                  array (
                    'domain' => 'htch.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'mn',
                    'name' => 'Hutchinson Technology Inc.',
                  ),
                  748 =>
                  array (
                    'domain' => 'rea-alp.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'mn',
                    'name' => 'Runestone Electric Association',
                  ),
                  749 =>
                  array (
                    'domain' => 'honeywell.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'nj',
                    'name' => 'Honeywell International Inc',
                  ),
                  750 =>
                  array (
                    'domain' => 'fluentenergy.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'ny',
                    'name' => 'Fluent Energy',
                  ),
                  751 =>
                  array (
                    'domain' => 'spectral-sys.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'oh',
                    'name' => 'Spectral Systems Inc',
                  ),
                  752 =>
                  array (
                    'domain' => 'cmicro.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'or',
                    'name' => 'Cascade Microtech Inc.',
                  ),
                  753 =>
                  array (
                    'domain' => 'mxim.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'or',
                    'name' => 'Maxim Integrated Products',
                  ),
                  754 =>
                  array (
                    'domain' => 'nanomat.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'pa',
                    'name' => 'Nanomat, Inc.',
                  ),
                  755 =>
                  array (
                    'domain' => 'amd.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'Advanced Micro Devices, Inc.',
                  ),
                  756 =>
                  array (
                    'domain' => 'natinst.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'National Instruments Corporation',
                  ),
                  757 =>
                  array (
                    'domain' => 'sematech.org',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'tx',
                    'name' => 'Sematech',
                  ),
                  758 =>
                  array (
                    'domain' => 'fedsources.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'va',
                    'name' => 'Federal Sources Inc.',
                  ),
                  759 =>
                  array (
                    'domain' => 'scitor.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'va',
                    'name' => 'Scitor Corporation',
                  ),
                  760 =>
                  array (
                    'domain' => 'cit.org',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'va',
                    'name' => 'Virginia\\\'s Center for Innovation',
                  ),
                  761 =>
                  array (
                    'domain' => 'boeing.com',
                    'class' => '2',
                    'country' => 'us',
                    'state' => 'wa',
                    'name' => 'Boeing Company',
                  ),
                  762 =>
                  array (
                    'domain' => 'lbl.gov',
                    'class' => '3',
                    'country' => 'us',
                    'state' => 'ca',
                    'name' => 'Lawernce Berkeley National Laboratory',
                  ),
                  763 =>
                  array (
                    'domain' => 'fda.gov',
                    'class' => '3',
                    'country' => 'us',
                    'state' => 'dc',
                    'name' => 'Food and Drug Administration',
                  ),
                  764 =>
                  array (
                    'domain' => 'anl-external.org',
                    'class' => '3',
                    'country' => 'us',
                    'state' => 'il',
                    'name' => 'Argonne National Lab',
                  ),
                  765 =>
                  array (
                    'domain' => 'fnal.gov',
                    'class' => '3',
                    'country' => 'us',
                    'state' => 'il',
                    'name' => 'Fermi National Laboratory',
                  ),
                  766 =>
                  array (
                    'domain' => 'state.nj.us',
                    'class' => '3',
                    'country' => 'us',
                    'state' => 'nj',
                    'name' => 'NJ Office of Information Technology',
                  ),
                  767 =>
                  array (
                    'domain' => 'sandia.gov',
                    'class' => '3',
                    'country' => 'us',
                    'state' => 'nm',
                    'name' => 'Sandia Natational Laboratory',
                  ),
                  768 =>
                  array (
                    'domain' => 'wpafb.af.mil',
                    'class' => '3',
                    'country' => 'us',
                    'state' => 'oh',
                    'name' => 'Wright Patterson AFB',
                  ),
                  769 =>
                  array (
                    'domain' => 'iisc.ernet.in',
                    'class' => '1',
                    'country' => 'in',
                    'state' => '',
                    'name' => 'Indian Institute of Science',
                  ),
                  770 =>
                  array (
                    'domain' => 'nanohub.org',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  771 =>
                  array (
                    'domain' => 'zntu.edu.ua',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  772 =>
                  array (
                    'domain' => 'zambonet.edu.ph',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  773 =>
                  array (
                    'domain' => 'yzu.edu.cn',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  774 =>
                  array (
                    'domain' => 'yuntech.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  775 =>
                  array (
                    'domain' => 'yu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  776 =>
                  array (
                    'domain' => 'ysu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  777 =>
                  array (
                    'domain' => 'ym.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  778 =>
                  array (
                    'domain' => 'ycrc.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  779 =>
                  array (
                    'domain' => 'ycp.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  780 =>
                  array (
                    'domain' => 'xmu.edu.cn',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  781 =>
                  array (
                    'domain' => 'xjtu.edu.cn',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  782 =>
                  array (
                    'domain' => 'wwu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  783 =>
                  array (
                    'domain' => 'wwc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  784 =>
                  array (
                    'domain' => 'wvu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  785 =>
                  array (
                    'domain' => 'wustl.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  786 =>
                  array (
                    'domain' => 'wse.edu.pl',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  787 =>
                  array (
                    'domain' => 'wright.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  788 =>
                  array (
                    'domain' => 'worcester.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  789 =>
                  array (
                    'domain' => 'wofford.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  790 =>
                  array (
                    'domain' => 'wnmu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  791 =>
                  array (
                    'domain' => 'wnec.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  792 =>
                  array (
                    'domain' => 'wmich.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  793 =>
                  array (
                    'domain' => 'wm.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  794 =>
                  array (
                    'domain' => 'wlc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  795 =>
                  array (
                    'domain' => 'wku.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  796 =>
                  array (
                    'domain' => 'wiu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  797 =>
                  array (
                    'domain' => 'wittenberg.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  798 =>
                  array (
                    'domain' => 'witc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  799 =>
                  array (
                    'domain' => 'winona.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  800 =>
                  array (
                    'domain' => 'wilmcoll.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  801 =>
                  array (
                    'domain' => 'williams.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  802 =>
                  array (
                    'domain' => 'wilkes.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  803 =>
                  array (
                    'domain' => 'wichita.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  804 =>
                  array (
                    'domain' => 'whu.edu.cn',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  805 =>
                  array (
                    'domain' => 'whitworth.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  806 =>
                  array (
                    'domain' => 'whitman.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  807 =>
                  array (
                    'domain' => 'wheaton.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  808 =>
                  array (
                    'domain' => 'westga.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  809 =>
                  array (
                    'domain' => 'wesleyan.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  810 =>
                  array (
                    'domain' => 'wednet.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  811 =>
                  array (
                    'domain' => 'weber.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  812 =>
                  array (
                    'domain' => 'wcu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  813 =>
                  array (
                    'domain' => 'wat.edu.pl',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  814 =>
                  array (
                    'domain' => 'washjeff.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  815 =>
                  array (
                    'domain' => 'washburn.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  816 =>
                  array (
                    'domain' => 'wartburgseminary.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  817 =>
                  array (
                    'domain' => 'warren-wilson.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  818 =>
                  array (
                    'domain' => 'walsh.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  819 =>
                  array (
                    'domain' => 'waldenu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  820 =>
                  array (
                    'domain' => 'vwc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  821 =>
                  array (
                    'domain' => 'vu.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  822 =>
                  array (
                    'domain' => 'vt.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  823 =>
                  array (
                    'domain' => 'vsu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  824 =>
                  array (
                    'domain' => 'vnu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  825 =>
                  array (
                    'domain' => 'vims.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  826 =>
                  array (
                    'domain' => 'villanova.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  827 =>
                  array (
                    'domain' => 'viit.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  828 =>
                  array (
                    'domain' => 'vic.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  829 =>
                  array (
                    'domain' => 'vernoncollege.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  830 =>
                  array (
                    'domain' => 'vccs.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  831 =>
                  array (
                    'domain' => 'vc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  832 =>
                  array (
                    'domain' => 'vassar.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  833 =>
                  array (
                    'domain' => 'valpo.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  834 =>
                  array (
                    'domain' => 'valdosta.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  835 =>
                  array (
                    'domain' => 'uwyo.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  836 =>
                  array (
                    'domain' => 'uwstout.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  837 =>
                  array (
                    'domain' => 'uwsp.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  838 =>
                  array (
                    'domain' => 'uws.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  839 =>
                  array (
                    'domain' => 'uwp.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  840 =>
                  array (
                    'domain' => 'uwm.edu.pl',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  841 =>
                  array (
                    'domain' => 'uwm.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  842 =>
                  array (
                    'domain' => 'uwf.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  843 =>
                  array (
                    'domain' => 'uwec.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  844 =>
                  array (
                    'domain' => 'uwb.edu.pl',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  845 =>
                  array (
                    'domain' => 'uwa.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  846 =>
                  array (
                    'domain' => 'uw.edu.pl',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  847 =>
                  array (
                    'domain' => 'uvsc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  848 =>
                  array (
                    'domain' => 'uvm.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  849 =>
                  array (
                    'domain' => 'uvi.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  850 =>
                  array (
                    'domain' => 'uu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  851 =>
                  array (
                    'domain' => 'utulsa.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  852 =>
                  array (
                    'domain' => 'uts.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  853 =>
                  array (
                    'domain' => 'utn.edu.ec',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  854 =>
                  array (
                    'domain' => 'utmem.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  855 =>
                  array (
                    'domain' => 'utmb.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  856 =>
                  array (
                    'domain' => 'utm.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  857 =>
                  array (
                    'domain' => 'uthscsa.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  858 =>
                  array (
                    'domain' => 'utdallas.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  859 =>
                  array (
                    'domain' => 'utc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  860 =>
                  array (
                    'domain' => 'utas.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  861 =>
                  array (
                    'domain' => 'ust.edu.ph',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  862 =>
                  array (
                    'domain' => 'usra.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  863 =>
                  array (
                    'domain' => 'usna.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  864 =>
                  array (
                    'domain' => 'usmd.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  865 =>
                  array (
                    'domain' => 'usg.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  866 =>
                  array (
                    'domain' => 'usfca.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  867 =>
                  array (
                    'domain' => 'usd.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  868 =>
                  array (
                    'domain' => 'usc.edu.ph',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  869 =>
                  array (
                    'domain' => 'usc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  870 =>
                  array (
                    'domain' => 'us.edu.pl',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  871 =>
                  array (
                    'domain' => 'uri.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  872 =>
                  array (
                    'domain' => 'uq.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  873 =>
                  array (
                    'domain' => 'ups.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  874 =>
                  array (
                    'domain' => 'uprm.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  875 =>
                  array (
                    'domain' => 'uprh.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  876 =>
                  array (
                    'domain' => 'upr.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  877 =>
                  array (
                    'domain' => 'upmc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  878 =>
                  array (
                    'domain' => 'uplb.edu.ph',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  879 =>
                  array (
                    'domain' => 'upf.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  880 =>
                  array (
                    'domain' => 'upd.edu.ph',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  881 =>
                  array (
                    'domain' => 'upc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  882 =>
                  array (
                    'domain' => 'upb.edu.co',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  883 =>
                  array (
                    'domain' => 'up.edu.ph',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  884 =>
                  array (
                    'domain' => 'up.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  885 =>
                  array (
                    'domain' => 'uow.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  886 =>
                  array (
                    'domain' => 'uoregon.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  887 =>
                  array (
                    'domain' => 'uophx.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  888 =>
                  array (
                    'domain' => 'uop.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  889 =>
                  array (
                    'domain' => 'uofs.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  890 =>
                  array (
                    'domain' => 'uoa.edu.er',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  891 =>
                  array (
                    'domain' => 'unt.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  892 =>
                  array (
                    'domain' => 'unsw.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  893 =>
                  array (
                    'domain' => 'uns.edu.ar',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  894 =>
                  array (
                    'domain' => 'unrc.edu.ar',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  895 =>
                  array (
                    'domain' => 'unr.edu.ar',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  896 =>
                  array (
                    'domain' => 'unr.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  897 =>
                  array (
                    'domain' => 'unomaha.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  898 =>
                  array (
                    'domain' => 'unne.edu.ar',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  899 =>
                  array (
                    'domain' => 'unmsm.edu.pe',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  900 =>
                  array (
                    'domain' => 'unmc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  901 =>
                  array (
                    'domain' => 'unlp.edu.ar',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  902 =>
                  array (
                    'domain' => 'unl.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  903 =>
                  array (
                    'domain' => 'unk.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  904 =>
                  array (
                    'domain' => 'universia.edu.ve',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  905 =>
                  array (
                    'domain' => 'univdhaka.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  906 =>
                  array (
                    'domain' => 'univalle.edu.co',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  907 =>
                  array (
                    'domain' => 'unisa.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  908 =>
                  array (
                    'domain' => 'union.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  909 =>
                  array (
                    'domain' => 'uninorte.edu.co',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  910 =>
                  array (
                    'domain' => 'unimelb.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  911 =>
                  array (
                    'domain' => 'unilibrecali.edu.co',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  912 =>
                  array (
                    'domain' => 'uni.edu.pe',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  913 =>
                  array (
                    'domain' => 'uni.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  914 =>
                  array (
                    'domain' => 'unf.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  915 =>
                  array (
                    'domain' => 'uner.edu.ar',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  916 =>
                  array (
                    'domain' => 'une.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  917 =>
                  array (
                    'domain' => 'une.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  918 =>
                  array (
                    'domain' => 'uncw.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  919 =>
                  array (
                    'domain' => 'unco.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  920 =>
                  array (
                    'domain' => 'uncg.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  921 =>
                  array (
                    'domain' => 'unca.edu.ar',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  922 =>
                  array (
                    'domain' => 'unca.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  923 =>
                  array (
                    'domain' => 'unc.edu.ar',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  924 =>
                  array (
                    'domain' => 'unc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  925 =>
                  array (
                    'domain' => 'unan.edu.ni',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  926 =>
                  array (
                    'domain' => 'unam.edu.ar',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  927 =>
                  array (
                    'domain' => 'unad.edu.co',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  928 =>
                  array (
                    'domain' => 'una.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  929 =>
                  array (
                    'domain' => 'umw.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  930 =>
                  array (
                    'domain' => 'umuc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  931 =>
                  array (
                    'domain' => 'umt.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  932 =>
                  array (
                    'domain' => 'umsmed.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  933 =>
                  array (
                    'domain' => 'umsl.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  934 =>
                  array (
                    'domain' => 'umontana.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  935 =>
                  array (
                    'domain' => 'uml.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  936 =>
                  array (
                    'domain' => 'umkc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  937 =>
                  array (
                    'domain' => 'umh.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  938 =>
                  array (
                    'domain' => 'umdnj.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  939 =>
                  array (
                    'domain' => 'umassmed.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  940 =>
                  array (
                    'domain' => 'umassd.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  941 =>
                  array (
                    'domain' => 'umaryland.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  942 =>
                  array (
                    'domain' => 'umaine.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  943 =>
                  array (
                    'domain' => 'um.edu.my',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  944 =>
                  array (
                    'domain' => 'uludag.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  945 =>
                  array (
                    'domain' => 'ulm.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  946 =>
                  array (
                    'domain' => 'uky.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  947 =>
                  array (
                    'domain' => 'uitm.edu.my',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  948 =>
                  array (
                    'domain' => 'uis.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  949 =>
                  array (
                    'domain' => 'uidaho.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  950 =>
                  array (
                    'domain' => 'uhv.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  951 =>
                  array (
                    'domain' => 'uga.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  952 =>
                  array (
                    'domain' => 'ufsj.edu.br',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  953 =>
                  array (
                    'domain' => 'ufcg.edu.br',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  954 =>
                  array (
                    'domain' => 'ufam.edu.br',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  955 =>
                  array (
                    'domain' => 'udea.edu.co',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  956 =>
                  array (
                    'domain' => 'udayton.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  957 =>
                  array (
                    'domain' => 'udallas.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  958 =>
                  array (
                    'domain' => 'uctm.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  959 =>
                  array (
                    'domain' => 'ucsf.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  960 =>
                  array (
                    'domain' => 'ucsc-extension.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  961 =>
                  array (
                    'domain' => 'ucop.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  962 =>
                  array (
                    'domain' => 'ucok.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  963 =>
                  array (
                    'domain' => 'ucmerced.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  964 =>
                  array (
                    'domain' => 'uclv.edu.cu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  965 =>
                  array (
                    'domain' => 'uchsc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  966 =>
                  array (
                    'domain' => 'uchc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  967 =>
                  array (
                    'domain' => 'uceou.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  968 =>
                  array (
                    'domain' => 'uccs.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  969 =>
                  array (
                    'domain' => 'ucatolica.edu.co',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  970 =>
                  array (
                    'domain' => 'ucar.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  971 =>
                  array (
                    'domain' => 'ubaguio.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  972 =>
                  array (
                    'domain' => 'uan.edu.co',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  973 =>
                  array (
                    'domain' => 'uams.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  974 =>
                  array (
                    'domain' => 'ualr.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  975 =>
                  array (
                    'domain' => 'uakron.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  976 =>
                  array (
                    'domain' => 'uah.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  977 =>
                  array (
                    'domain' => 'uab.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  978 =>
                  array (
                    'domain' => 'ua.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  979 =>
                  array (
                    'domain' => 'tyrc.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  980 =>
                  array (
                    'domain' => 'txstate.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  981 =>
                  array (
                    'domain' => 'tvi.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  982 =>
                  array (
                    'domain' => 'tuskegee.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  983 =>
                  array (
                    'domain' => 'tulane.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  984 =>
                  array (
                    'domain' => 'tufts.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  985 =>
                  array (
                    'domain' => 'ttu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  986 =>
                  array (
                    'domain' => 'tsu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  987 =>
                  array (
                    'domain' => 'truman.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  988 =>
                  array (
                    'domain' => 'trnty.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  989 =>
                  array (
                    'domain' => 'triton.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  990 =>
                  array (
                    'domain' => 'trinity.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  991 =>
                  array (
                    'domain' => 'trincoll.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  992 =>
                  array (
                    'domain' => 'tridenttech.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  993 =>
                  array (
                    'domain' => 'tpu.edu.ru',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  994 =>
                  array (
                    'domain' => 'tpc.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  995 =>
                  array (
                    'domain' => 'tp2rc.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  996 =>
                  array (
                    'domain' => 'tp1rc.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  997 =>
                  array (
                    'domain' => 'tp.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  998 =>
                  array (
                    'domain' => 'toronto.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  999 =>
                  array (
                    'domain' => 'tntech.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1000 =>
                  array (
                    'domain' => 'tnrc.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1001 =>
                  array (
                    'domain' => 'tmcc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1002 =>
                  array (
                    'domain' => 'tmc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1003 =>
                  array (
                    'domain' => 'tm.edu.ro',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1004 =>
                  array (
                    'domain' => 'tlu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1005 =>
                  array (
                    'domain' => 'tln.edu.ee',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1006 =>
                  array (
                    'domain' => 'tku.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1007 =>
                  array (
                    'domain' => 'tju.edu.cn',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1008 =>
                  array (
                    'domain' => 'tju.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1009 =>
                  array (
                    'domain' => 'thu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1010 =>
                  array (
                    'domain' => 'tenet.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1011 =>
                  array (
                    'domain' => 'tcu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1012 =>
                  array (
                    'domain' => 'tcu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1013 =>
                  array (
                    'domain' => 'tcnj.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1014 =>
                  array (
                    'domain' => 'tc.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1015 =>
                  array (
                    'domain' => 'tayloru.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1016 =>
                  array (
                    'domain' => 'tarleton.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1017 =>
                  array (
                    'domain' => 'tamucc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1018 =>
                  array (
                    'domain' => 'tamu-commerce.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1019 =>
                  array (
                    'domain' => 'tamiu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1020 =>
                  array (
                    'domain' => 'sysu.edu.cn',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1021 =>
                  array (
                    'domain' => 'swosu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1022 =>
                  array (
                    'domain' => 'swmed.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1023 =>
                  array (
                    'domain' => 'swinburne.edu.my',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1024 =>
                  array (
                    'domain' => 'swin.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1025 =>
                  array (
                    'domain' => 'swau.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1026 =>
                  array (
                    'domain' => 'swarthmore.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1027 =>
                  array (
                    'domain' => 'svsu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1028 =>
                  array (
                    'domain' => 'svcc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1029 =>
                  array (
                    'domain' => 'sunyrockland.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1030 =>
                  array (
                    'domain' => 'sulross.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1031 =>
                  array (
                    'domain' => 'sullivan.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1032 =>
                  array (
                    'domain' => 'suffolk.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1033 =>
                  array (
                    'domain' => 'subr.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1034 =>
                  array (
                    'domain' => 'stuy.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1035 =>
                  array (
                    'domain' => 'stut.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1036 =>
                  array (
                    'domain' => 'stsci.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1037 =>
                  array (
                    'domain' => 'stolaf.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1038 =>
                  array (
                    'domain' => 'stmarytx.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1039 =>
                  array (
                    'domain' => 'stlcop.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1040 =>
                  array (
                    'domain' => 'stikom.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1041 =>
                  array (
                    'domain' => 'stgregorys.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1042 =>
                  array (
                    'domain' => 'stfrancis.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1043 =>
                  array (
                    'domain' => 'stevens.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1044 =>
                  array (
                    'domain' => 'stevens-tech.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1045 =>
                  array (
                    'domain' => 'stetson.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1046 =>
                  array (
                    'domain' => 'stedwards.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1047 =>
                  array (
                    'domain' => 'stcloudstate.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1048 =>
                  array (
                    'domain' => 'stchas.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1049 =>
                  array (
                    'domain' => 'sru.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1050 =>
                  array (
                    'domain' => 'squ.edu.om',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1051 =>
                  array (
                    'domain' => 'spu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1052 =>
                  array (
                    'domain' => 'spelman.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1053 =>
                  array (
                    'domain' => 'spcollege.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1054 =>
                  array (
                    'domain' => 'sp.edu.sg',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1055 =>
                  array (
                    'domain' => 'sou.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1056 =>
                  array (
                    'domain' => 'sonoma.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1057 =>
                  array (
                    'domain' => 'solano.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1058 =>
                  array (
                    'domain' => 'smu.edu.ph',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1059 =>
                  array (
                    'domain' => 'smsu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1060 =>
                  array (
                    'domain' => 'smcm.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1061 =>
                  array (
                    'domain' => 'slu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1062 =>
                  array (
                    'domain' => 'skku.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1063 =>
                  array (
                    'domain' => 'sju.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1064 =>
                  array (
                    'domain' => 'sju.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1065 =>
                  array (
                    'domain' => 'sjtu.edu.cn',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1066 =>
                  array (
                    'domain' => 'sjsmit.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1067 =>
                  array (
                    'domain' => 'sivitanidios.edu.gr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1068 =>
                  array (
                    'domain' => 'siue.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1069 =>
                  array (
                    'domain' => 'sinte.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1070 =>
                  array (
                    'domain' => 'sinica.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1071 =>
                  array (
                    'domain' => 'sinclair.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1072 =>
                  array (
                    'domain' => 'simmons.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1073 =>
                  array (
                    'domain' => 'si.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1074 =>
                  array (
                    'domain' => 'shu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1075 =>
                  array (
                    'domain' => 'shsu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1076 =>
                  array (
                    'domain' => 'ship.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1077 =>
                  array (
                    'domain' => 'shepherd.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1078 =>
                  array (
                    'domain' => 'shawu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1079 =>
                  array (
                    'domain' => 'sfusd.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1080 =>
                  array (
                    'domain' => 'sfsu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1081 =>
                  array (
                    'domain' => 'semo.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1082 =>
                  array (
                    'domain' => 'selu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1083 =>
                  array (
                    'domain' => 'selcuk.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1084 =>
                  array (
                    'domain' => 'seattleu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1085 =>
                  array (
                    'domain' => 'sdu.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1086 =>
                  array (
                    'domain' => 'sdsu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1087 =>
                  array (
                    'domain' => 'sdstate.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1088 =>
                  array (
                    'domain' => 'sdsc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1089 =>
                  array (
                    'domain' => 'scripps.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1090 =>
                  array (
                    'domain' => 'scranton.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1091 =>
                  array (
                    'domain' => 'sckans.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1092 =>
                  array (
                    'domain' => 'sccnc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1093 =>
                  array (
                    'domain' => 'sc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1094 =>
                  array (
                    'domain' => 'santarosa.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1095 =>
                  array (
                    'domain' => 'santafe.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1096 =>
                  array (
                    'domain' => 'sandburg.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1097 =>
                  array (
                    'domain' => 'samford.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1098 =>
                  array (
                    'domain' => 'salve.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1099 =>
                  array (
                    'domain' => 'sals.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1100 =>
                  array (
                    'domain' => 'salk.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1101 =>
                  array (
                    'domain' => 'saintmarys.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1102 =>
                  array (
                    'domain' => 'sabanciuniv.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1103 =>
                  array (
                    'domain' => 'sa.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1104 =>
                  array (
                    'domain' => 'rutgers.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1105 =>
                  array (
                    'domain' => 'rush.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1106 =>
                  array (
                    'domain' => 'rtc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1107 =>
                  array (
                    'domain' => 'rpslmc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1108 =>
                  array (
                    'domain' => 'rp.edu.sg',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1109 =>
                  array (
                    'domain' => 'rose.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1110 =>
                  array (
                    'domain' => 'rose-hulman.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1111 =>
                  array (
                    'domain' => 'rosalindfranklin.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1112 =>
                  array (
                    'domain' => 'roosevelt.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1113 =>
                  array (
                    'domain' => 'rockhurst.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1114 =>
                  array (
                    'domain' => 'rockefeller.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1115 =>
                  array (
                    'domain' => 'rochester.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1116 =>
                  array (
                    'domain' => 'roch.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1117 =>
                  array (
                    'domain' => 'roanoke.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1118 =>
                  array (
                    'domain' => 'rider.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1119 =>
                  array (
                    'domain' => 'richmond.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1120 =>
                  array (
                    'domain' => 'rhodesstate.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1121 =>
                  array (
                    'domain' => 'regis.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1122 =>
                  array (
                    'domain' => 'reed.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1123 =>
                  array (
                    'domain' => 'ranken.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1124 =>
                  array (
                    'domain' => 'radford.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1125 =>
                  array (
                    'domain' => 'qut.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1126 =>
                  array (
                    'domain' => 'quinnipiac.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1127 =>
                  array (
                    'domain' => 'qld.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1128 =>
                  array (
                    'domain' => 'qc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1129 =>
                  array (
                    'domain' => 'pwsz-kalisz.edu.pl',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1130 =>
                  array (
                    'domain' => 'pw.edu.pl',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1131 =>
                  array (
                    'domain' => 'pvam.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1132 =>
                  array (
                    'domain' => 'pupr.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1133 =>
                  array (
                    'domain' => 'puc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1134 =>
                  array (
                    'domain' => 'pu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1135 =>
                  array (
                    'domain' => 'ptsem.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1136 =>
                  array (
                    'domain' => 'ptr.edu.ie',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1137 =>
                  array (
                    'domain' => 'ptloma.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1138 =>
                  array (
                    'domain' => 'psc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1139 =>
                  array (
                    'domain' => 'proxy.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1140 =>
                  array (
                    'domain' => 'pratt.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1141 =>
                  array (
                    'domain' => 'potsdam.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1142 =>
                  array (
                    'domain' => 'pomona.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1143 =>
                  array (
                    'domain' => 'poly.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1144 =>
                  array (
                    'domain' => 'pnc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1145 =>
                  array (
                    'domain' => 'plu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1146 =>
                  array (
                    'domain' => 'planet.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1147 =>
                  array (
                    'domain' => 'pku.edu.cn',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1148 =>
                  array (
                    'domain' => 'pk.edu.pl',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1149 =>
                  array (
                    'domain' => 'pittstate.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1150 =>
                  array (
                    'domain' => 'pinecrest.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1151 =>
                  array (
                    'domain' => 'pie.edu.pl',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1152 =>
                  array (
                    'domain' => 'phoenix.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1153 =>
                  array (
                    'domain' => 'pepperdine.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1154 =>
                  array (
                    'domain' => 'peachnet.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1155 =>
                  array (
                    'domain' => 'pccu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1156 =>
                  array (
                    'domain' => 'parkland.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1157 =>
                  array (
                    'domain' => 'pap.edu.pl',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1158 =>
                  array (
                    'domain' => 'panam.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1159 =>
                  array (
                    'domain' => 'palomar.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1160 =>
                  array (
                    'domain' => 'pacificu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1161 =>
                  array (
                    'domain' => 'owens.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1162 =>
                  array (
                    'domain' => 'ouhk.edu.hk',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1163 =>
                  array (
                    'domain' => 'oswego.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1164 =>
                  array (
                    'domain' => 'osumc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1165 =>
                  array (
                    'domain' => 'osu-okmulgee.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1166 =>
                  array (
                    'domain' => 'osc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1167 =>
                  array (
                    'domain' => 'oru.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1168 =>
                  array (
                    'domain' => 'orst.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1169 =>
                  array (
                    'domain' => 'oregonstate.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1170 =>
                  array (
                    'domain' => 'onu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1171 =>
                  array (
                    'domain' => 'oneonta.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1172 =>
                  array (
                    'domain' => 'omu.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1173 =>
                  array (
                    'domain' => 'olin.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1174 =>
                  array (
                    'domain' => 'olemiss.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1175 =>
                  array (
                    'domain' => 'ohlone.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1176 =>
                  array (
                    'domain' => 'oc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1177 =>
                  array (
                    'domain' => 'oberlin.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1178 =>
                  array (
                    'domain' => 'oakton.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1179 =>
                  array (
                    'domain' => 'oakland.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1180 =>
                  array (
                    'domain' => 'nyp.edu.sg',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1181 =>
                  array (
                    'domain' => 'nwmissouri.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1182 =>
                  array (
                    'domain' => 'nuu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1183 =>
                  array (
                    'domain' => 'nung.edu.ua',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1184 =>
                  array (
                    'domain' => 'nuk.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1185 =>
                  array (
                    'domain' => 'nu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1186 =>
                  array (
                    'domain' => 'ntut.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1187 =>
                  array (
                    'domain' => 'ntust.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1188 =>
                  array (
                    'domain' => 'nttu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1189 =>
                  array (
                    'domain' => 'nttc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1190 =>
                  array (
                    'domain' => 'ntpu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1191 =>
                  array (
                    'domain' => 'ntou.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1192 =>
                  array (
                    'domain' => 'ntnu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1193 =>
                  array (
                    'domain' => 'ntit.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1194 =>
                  array (
                    'domain' => 'ntcpe.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1195 =>
                  array (
                    'domain' => 'ntc.edu.ph',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1196 =>
                  array (
                    'domain' => 'ntc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1197 =>
                  array (
                    'domain' => 'nsysu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1198 =>
                  array (
                    'domain' => 'nsu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1199 =>
                  array (
                    'domain' => 'nrao.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1200 =>
                  array (
                    'domain' => 'npust.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1201 =>
                  array (
                    'domain' => 'nps.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1202 =>
                  array (
                    'domain' => 'nova.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1203 =>
                  array (
                    'domain' => 'norwich.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1204 =>
                  array (
                    'domain' => 'nodak.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1205 =>
                  array (
                    'domain' => 'noao.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1206 =>
                  array (
                    'domain' => 'nnu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1207 =>
                  array (
                    'domain' => 'nmu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1208 =>
                  array (
                    'domain' => 'nmt.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1209 =>
                  array (
                    'domain' => 'nku.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1210 =>
                  array (
                    'domain' => 'njnu.edu.cn',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1211 =>
                  array (
                    'domain' => 'niu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1212 =>
                  array (
                    'domain' => 'nitt.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1213 =>
                  array (
                    'domain' => 'niit.edu.pk',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1214 =>
                  array (
                    'domain' => 'nicholls.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1215 =>
                  array (
                    'domain' => 'nhust.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1216 =>
                  array (
                    'domain' => 'nhu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1217 =>
                  array (
                    'domain' => 'ngcsu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1218 =>
                  array (
                    'domain' => 'nfu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1219 =>
                  array (
                    'domain' => 'newhaven.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1220 =>
                  array (
                    'domain' => 'newenglandconservatory.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1221 =>
                  array (
                    'domain' => 'nevada.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1222 =>
                  array (
                    'domain' => 'neu.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1223 =>
                  array (
                    'domain' => 'neiu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1224 =>
                  array (
                    'domain' => 'neduet.edu.pk',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1225 =>
                  array (
                    'domain' => 'ndmctsgh.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1226 =>
                  array (
                    'domain' => 'ndhu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1227 =>
                  array (
                    'domain' => 'ncyu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1228 =>
                  array (
                    'domain' => 'ncue.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1229 =>
                  array (
                    'domain' => 'ncu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1230 =>
                  array (
                    'domain' => 'ncku.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1231 =>
                  array (
                    'domain' => 'ncit.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1232 =>
                  array (
                    'domain' => 'nchu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1233 =>
                  array (
                    'domain' => 'nccu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1234 =>
                  array (
                    'domain' => 'ncat.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1235 =>
                  array (
                    'domain' => 'naz.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1236 =>
                  array (
                    'domain' => 'nau.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1237 =>
                  array (
                    'domain' => 'mwsu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1238 =>
                  array (
                    'domain' => 'mwsc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1239 =>
                  array (
                    'domain' => 'must.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1240 =>
                  array (
                    'domain' => 'muskingum.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1241 =>
                  array (
                    'domain' => 'musc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1242 =>
                  array (
                    'domain' => 'murdoch.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1243 =>
                  array (
                    'domain' => 'mum.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1244 =>
                  array (
                    'domain' => 'muhlenberg.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1245 =>
                  array (
                    'domain' => 'muctr.edu.ru',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1246 =>
                  array (
                    'domain' => 'mu.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1247 =>
                  array (
                    'domain' => 'mu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1248 =>
                  array (
                    'domain' => 'mtholyoke.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1249 =>
                  array (
                    'domain' => 'msuiit.edu.ph',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1250 =>
                  array (
                    'domain' => 'mstc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1251 =>
                  array (
                    'domain' => 'mssm.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1252 =>
                  array (
                    'domain' => 'msoe.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1253 =>
                  array (
                    'domain' => 'msm.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1254 =>
                  array (
                    'domain' => 'mscd.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1255 =>
                  array (
                    'domain' => 'morehouse.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1256 =>
                  array (
                    'domain' => 'moody.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1257 =>
                  array (
                    'domain' => 'montcalm.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1258 =>
                  array (
                    'domain' => 'monroecc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1259 =>
                  array (
                    'domain' => 'monroe.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1260 =>
                  array (
                    'domain' => 'monmouth.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1261 =>
                  array (
                    'domain' => 'monm.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1262 =>
                  array (
                    'domain' => 'monash.edu.my',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1263 =>
                  array (
                    'domain' => 'monash.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1264 =>
                  array (
                    'domain' => 'moet.edu.vn',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1265 =>
                  array (
                    'domain' => 'moe.edu.sg',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1266 =>
                  array (
                    'domain' => 'mnsu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1267 =>
                  array (
                    'domain' => 'mmu.edu.my',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1268 =>
                  array (
                    'domain' => 'mmc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1269 =>
                  array (
                    'domain' => 'mit.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1270 =>
                  array (
                    'domain' => 'missouriwestern.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1271 =>
                  array (
                    'domain' => 'missouristate.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1272 =>
                  array (
                    'domain' => 'missouri.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1273 =>
                  array (
                    'domain' => 'misericordia.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1274 =>
                  array (
                    'domain' => 'minnesota.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1275 =>
                  array (
                    'domain' => 'mines.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1276 =>
                  array (
                    'domain' => 'mimuw.edu.pl',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1277 =>
                  array (
                    'domain' => 'milligan.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1278 =>
                  array (
                    'domain' => 'millersville.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1279 =>
                  array (
                    'domain' => 'miem.edu.ru',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1280 =>
                  array (
                    'domain' => 'middlebury.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1281 =>
                  array (
                    'domain' => 'michelangelo.edu.br',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1282 =>
                  array (
                    'domain' => 'miami.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1283 =>
                  array (
                    'domain' => 'mfldclin.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1284 =>
                  array (
                    'domain' => 'messiah.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1285 =>
                  array (
                    'domain' => 'mesastate.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1286 =>
                  array (
                    'domain' => 'merrimack.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1287 =>
                  array (
                    'domain' => 'mercynet.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1288 =>
                  array (
                    'domain' => 'mercyhurst.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1289 =>
                  array (
                    'domain' => 'menominee.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1290 =>
                  array (
                    'domain' => 'memphis.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1291 =>
                  array (
                    'domain' => 'meduohio.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1292 =>
                  array (
                    'domain' => 'mcw.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1293 =>
                  array (
                    'domain' => 'mcneese.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1294 =>
                  array (
                    'domain' => 'mcg.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1295 =>
                  array (
                    'domain' => 'mccd.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1296 =>
                  array (
                    'domain' => 'mc3.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1297 =>
                  array (
                    'domain' => 'mbl.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1298 =>
                  array (
                    'domain' => 'mayo.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1299 =>
                  array (
                    'domain' => 'matcmadison.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1300 =>
                  array (
                    'domain' => 'marshall.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1301 =>
                  array (
                    'domain' => 'marmara.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1302 =>
                  array (
                    'domain' => 'marlboro.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1303 =>
                  array (
                    'domain' => 'maricopa.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1304 =>
                  array (
                    'domain' => 'mans.edu.eg',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1305 =>
                  array (
                    'domain' => 'manhattan.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1306 =>
                  array (
                    'domain' => 'manchester.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1307 =>
                  array (
                    'domain' => 'maine.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1308 =>
                  array (
                    'domain' => 'macalester.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1309 =>
                  array (
                    'domain' => 'lyon.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1310 =>
                  array (
                    'domain' => 'lynchburg.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1311 =>
                  array (
                    'domain' => 'luzerne.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1312 =>
                  array (
                    'domain' => 'luther.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1313 =>
                  array (
                    'domain' => 'ludwig.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1314 =>
                  array (
                    'domain' => 'luc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1315 =>
                  array (
                    'domain' => 'ltu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1316 =>
                  array (
                    'domain' => 'loyno.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1317 =>
                  array (
                    'domain' => 'losrios.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1318 =>
                  array (
                    'domain' => 'llumc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1319 =>
                  array (
                    'domain' => 'ljcrf.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1320 =>
                  array (
                    'domain' => 'linfield.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1321 =>
                  array (
                    'domain' => 'liberty.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1322 =>
                  array (
                    'domain' => 'lhup.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1323 =>
                  array (
                    'domain' => 'lfc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1324 =>
                  array (
                    'domain' => 'letu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1325 =>
                  array (
                    'domain' => 'lesley.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1326 =>
                  array (
                    'domain' => 'lemoyne.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1327 =>
                  array (
                    'domain' => 'lclark.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1328 =>
                  array (
                    'domain' => 'lccc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1329 =>
                  array (
                    'domain' => 'lcc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1330 =>
                  array (
                    'domain' => 'latrobe.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1331 =>
                  array (
                    'domain' => 'latech.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1332 =>
                  array (
                    'domain' => 'lamar.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1333 =>
                  array (
                    'domain' => 'lacoe.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1334 =>
                  array (
                    'domain' => 'kyu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1335 =>
                  array (
                    'domain' => 'kutkm.edu.my',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1336 =>
                  array (
                    'domain' => 'kumc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1337 =>
                  array (
                    'domain' => 'kuittho.edu.my',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1338 =>
                  array (
                    'domain' => 'kuas.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1339 =>
                  array (
                    'domain' => 'ku.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1340 =>
                  array (
                    'domain' => 'ku.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1341 =>
                  array (
                    'domain' => 'ktu.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1342 =>
                  array (
                    'domain' => 'ksu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1343 =>
                  array (
                    'domain' => 'ksu.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1344 =>
                  array (
                    'domain' => 'kstu.edu.ua',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1345 =>
                  array (
                    'domain' => 'ks.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1346 =>
                  array (
                    'domain' => 'kpprc.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1347 =>
                  array (
                    'domain' => 'kou.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1348 =>
                  array (
                    'domain' => 'knox.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1349 =>
                  array (
                    'domain' => 'kiet.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1350 =>
                  array (
                    'domain' => 'kh.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1351 =>
                  array (
                    'domain' => 'kgi.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1352 =>
                  array (
                    'domain' => 'kettering.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1353 =>
                  array (
                    'domain' => 'kentlaw.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1354 =>
                  array (
                    'domain' => 'kent.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1355 =>
                  array (
                    'domain' => 'kennesaw.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1356 =>
                  array (
                    'domain' => 'keene.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1357 =>
                  array (
                    'domain' => 'kctcs.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1358 =>
                  array (
                    'domain' => 'karunya.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1359 =>
                  array (
                    'domain' => 'kacst.edu.sa',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1360 =>
                  array (
                    'domain' => 'jsu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1361 =>
                  array (
                    'domain' => 'jmu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1362 =>
                  array (
                    'domain' => 'jlu.edu.cn',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1363 =>
                  array (
                    'domain' => 'jhuapl.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1364 =>
                  array (
                    'domain' => 'jhsph.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1365 =>
                  array (
                    'domain' => 'jhmi.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1366 =>
                  array (
                    'domain' => 'jcu.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1367 =>
                  array (
                    'domain' => 'iyte.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1368 =>
                  array (
                    'domain' => 'iwu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1369 =>
                  array (
                    'domain' => 'iwcc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1370 =>
                  array (
                    'domain' => 'ivytech.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1371 =>
                  array (
                    'domain' => 'iusb.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1372 =>
                  array (
                    'domain' => 'iup.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1373 =>
                  array (
                    'domain' => 'iun.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1374 =>
                  array (
                    'domain' => 'iu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1375 =>
                  array (
                    'domain' => 'itu.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1376 =>
                  array (
                    'domain' => 'itmorelia.edu.mx',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1377 =>
                  array (
                    'domain' => 'isunet.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1378 =>
                  array (
                    'domain' => 'isu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1379 =>
                  array (
                    'domain' => 'istanbul.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1380 =>
                  array (
                    'domain' => 'ist.edu.gr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1381 =>
                  array (
                    'domain' => 'isra.edu.jo',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1382 =>
                  array (
                    'domain' => 'ismm.edu.cu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1383 =>
                  array (
                    'domain' => 'island.edu.hk',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1384 =>
                  array (
                    'domain' => 'is.edu.ro',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1385 =>
                  array (
                    'domain' => 'ircc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1386 =>
                  array (
                    'domain' => 'ips.edu.ar',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1387 =>
                  array (
                    'domain' => 'ipfw.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1388 =>
                  array (
                    'domain' => 'iona.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1389 =>
                  array (
                    'domain' => 'intimal.edu.my',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1390 =>
                  array (
                    'domain' => 'internet2.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1391 =>
                  array (
                    'domain' => 'inonu.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1392 =>
                  array (
                    'domain' => 'indstate.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1393 =>
                  array (
                    'domain' => 'indianhills.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1394 =>
                  array (
                    'domain' => 'imsa.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1395 =>
                  array (
                    'domain' => 'imr.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1396 =>
                  array (
                    'domain' => 'ilstu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1397 =>
                  array (
                    'domain' => 'ilot.edu.pl',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1398 =>
                  array (
                    'domain' => 'ilc.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1399 =>
                  array (
                    'domain' => 'iiu.edu.my',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1400 =>
                  array (
                    'domain' => 'igf.edu.pl',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1401 =>
                  array (
                    'domain' => 'ifpan.edu.pl',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1402 =>
                  array (
                    'domain' => 'ifj.edu.pl',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1403 =>
                  array (
                    'domain' => 'iest.edu.mx',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1404 =>
                  array (
                    'domain' => 'icm.edu.pl',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1405 =>
                  array (
                    'domain' => 'ichf.edu.pl',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1406 =>
                  array (
                    'domain' => 'ibun.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1407 =>
                  array (
                    'domain' => 'ibu.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1408 =>
                  array (
                    'domain' => 'ibngr.edu.pl',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1409 =>
                  array (
                    'domain' => 'hvcc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1410 =>
                  array (
                    'domain' => 'hut.edu.vn',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1411 =>
                  array (
                    'domain' => 'humtec.edu.pe',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1412 =>
                  array (
                    'domain' => 'hsu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1413 =>
                  array (
                    'domain' => 'hsph.edu.vn',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1414 =>
                  array (
                    'domain' => 'hsc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1415 =>
                  array (
                    'domain' => 'howardcc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1416 =>
                  array (
                    'domain' => 'hope.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1417 =>
                  array (
                    'domain' => 'hmc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1418 =>
                  array (
                    'domain' => 'hkbu.edu.hk',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1419 =>
                  array (
                    'domain' => 'hho.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1420 =>
                  array (
                    'domain' => 'hfh.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1421 =>
                  array (
                    'domain' => 'herzing.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1422 =>
                  array (
                    'domain' => 'hcmuttt.edu.vn',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1423 =>
                  array (
                    'domain' => 'hcmuns.edu.vn',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1424 =>
                  array (
                    'domain' => 'hccs.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1425 =>
                  array (
                    'domain' => 'hccfl.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1426 =>
                  array (
                    'domain' => 'hbs.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1427 =>
                  array (
                    'domain' => 'hawaii.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1428 =>
                  array (
                    'domain' => 'haverford.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1429 =>
                  array (
                    'domain' => 'hartford.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1430 =>
                  array (
                    'domain' => 'hamilton.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1431 =>
                  array (
                    'domain' => 'hacettepe.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1432 =>
                  array (
                    'domain' => 'gyte.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1433 =>
                  array (
                    'domain' => 'gwu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1434 =>
                  array (
                    'domain' => 'gu.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1435 =>
                  array (
                    'domain' => 'gsu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1436 =>
                  array (
                    'domain' => 'grinnell.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1437 =>
                  array (
                    'domain' => 'griffith.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1438 =>
                  array (
                    'domain' => 'grcc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1439 =>
                  array (
                    'domain' => 'grayson.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1440 =>
                  array (
                    'domain' => 'govst.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1441 =>
                  array (
                    'domain' => 'gonzaga.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1442 =>
                  array (
                    'domain' => 'gmu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1443 =>
                  array (
                    'domain' => 'giki.edu.pk',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1444 =>
                  array (
                    'domain' => 'gettysburg.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1445 =>
                  array (
                    'domain' => 'georgiasouthern.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1446 =>
                  array (
                    'domain' => 'georgetown.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1447 =>
                  array (
                    'domain' => 'geneseo.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1448 =>
                  array (
                    'domain' => 'geisinger.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1449 =>
                  array (
                    'domain' => 'gcsu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1450 =>
                  array (
                    'domain' => 'gcc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1451 =>
                  array (
                    'domain' => 'gazi.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1452 =>
                  array (
                    'domain' => 'gatewaycc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1453 =>
                  array (
                    'domain' => 'gasou.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1454 =>
                  array (
                    'domain' => 'gantep.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1455 =>
                  array (
                    'domain' => 'galileo.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1456 =>
                  array (
                    'domain' => 'gac.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1457 =>
                  array (
                    'domain' => 'furman.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1458 =>
                  array (
                    'domain' => 'fullerton.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1459 =>
                  array (
                    'domain' => 'fscwv.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1460 =>
                  array (
                    'domain' => 'francis.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1461 =>
                  array (
                    'domain' => 'fq.edu.uy',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1462 =>
                  array (
                    'domain' => 'fmarion.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1463 =>
                  array (
                    'domain' => 'flinders.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1464 =>
                  array (
                    'domain' => 'flcc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1465 =>
                  array (
                    'domain' => 'fit.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1466 =>
                  array (
                    'domain' => 'fisk.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1467 =>
                  array (
                    'domain' => 'fisica.edu.uy',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1468 =>
                  array (
                    'domain' => 'fing.edu.uy',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1469 =>
                  array (
                    'domain' => 'fi.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1470 =>
                  array (
                    'domain' => 'fhda.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1471 =>
                  array (
                    'domain' => 'feu-eastasia.edu.ph',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1472 =>
                  array (
                    'domain' => 'fdltcc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1473 =>
                  array (
                    'domain' => 'fcu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1474 =>
                  array (
                    'domain' => 'fatih.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1475 =>
                  array (
                    'domain' => 'fandm.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1476 =>
                  array (
                    'domain' => 'famu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1477 =>
                  array (
                    'domain' => 'fairfield.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1478 =>
                  array (
                    'domain' => 'exploratorium.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1479 =>
                  array (
                    'domain' => 'exeter.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1480 =>
                  array (
                    'domain' => 'ewu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1481 =>
                  array (
                    'domain' => 'evergreen.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1482 =>
                  array (
                    'domain' => 'evansville.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1483 =>
                  array (
                    'domain' => 'etsu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1484 =>
                  array (
                    'domain' => 'esu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1485 =>
                  array (
                    'domain' => 'espol.edu.ec',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1486 =>
                  array (
                    'domain' => 'esb3-tomazfigueiredo.edu.pt',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1487 =>
                  array (
                    'domain' => 'esb3-idhenrique.edu.pt',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1488 =>
                  array (
                    'domain' => 'erciyes.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1489 =>
                  array (
                    'domain' => 'erau.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1490 =>
                  array (
                    'domain' => 'eq.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1491 =>
                  array (
                    'domain' => 'epcc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1492 =>
                  array (
                    'domain' => 'eou.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1493 =>
                  array (
                    'domain' => 'enmu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1494 =>
                  array (
                    'domain' => 'emporia.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1495 =>
                  array (
                    'domain' => 'emory.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1496 =>
                  array (
                    'domain' => 'emich.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1497 =>
                  array (
                    'domain' => 'emerson.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1498 =>
                  array (
                    'domain' => 'elon.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1499 =>
                  array (
                    'domain' => 'elgin.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1500 =>
                  array (
                    'domain' => 'eku.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1501 =>
                  array (
                    'domain' => 'eiu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1502 =>
                  array (
                    'domain' => 'eitw.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1503 =>
                  array (
                    'domain' => 'einstein.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1504 =>
                  array (
                    'domain' => 'ege.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1505 =>
                  array (
                    'domain' => 'eduhq.edu.sc',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1506 =>
                  array (
                    'domain' => 'educause.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1507 =>
                  array (
                    'domain' => 'edinboro.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1508 =>
                  array (
                    'domain' => 'edgewood.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1509 =>
                  array (
                    'domain' => 'ecu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1510 =>
                  array (
                    'domain' => 'ecpi.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1511 =>
                  array (
                    'domain' => 'ecc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1512 =>
                  array (
                    'domain' => 'eb23-vjuromenha.edu.pt',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1513 =>
                  array (
                    'domain' => 'eb23-qtmarrocos.edu.pt',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1514 =>
                  array (
                    'domain' => 'earlham.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1515 =>
                  array (
                    'domain' => 'eafit.edu.co',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1516 =>
                  array (
                    'domain' => 'dyu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1517 =>
                  array (
                    'domain' => 'duq.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1518 =>
                  array (
                    'domain' => 'dtcc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1519 =>
                  array (
                    'domain' => 'dsu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1520 =>
                  array (
                    'domain' => 'dstc.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1521 =>
                  array (
                    'domain' => 'dri.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1522 =>
                  array (
                    'domain' => 'drexelmed.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1523 =>
                  array (
                    'domain' => 'drexel.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1524 =>
                  array (
                    'domain' => 'dordt.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1525 =>
                  array (
                    'domain' => 'dogus.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1526 =>
                  array (
                    'domain' => 'dodea.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1527 =>
                  array (
                    'domain' => 'dicle.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1528 =>
                  array (
                    'domain' => 'dhsphn.edu.vn',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1529 =>
                  array (
                    'domain' => 'devry.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1530 =>
                  array (
                    'domain' => 'deu.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1531 =>
                  array (
                    'domain' => 'depaul.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1532 =>
                  array (
                    'domain' => 'denison.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1533 =>
                  array (
                    'domain' => 'deakin.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1534 =>
                  array (
                    'domain' => 'dcs.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1535 =>
                  array (
                    'domain' => 'dcccd.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1536 =>
                  array (
                    'domain' => 'davidson.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1537 =>
                  array (
                    'domain' => 'davenport.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1538 =>
                  array (
                    'domain' => 'dartmouth.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1539 =>
                  array (
                    'domain' => 'cy.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1540 =>
                  array (
                    'domain' => 'cwu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1541 =>
                  array (
                    'domain' => 'curtin.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1542 =>
                  array (
                    'domain' => 'cuny.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1543 =>
                  array (
                    'domain' => 'cune.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1544 =>
                  array (
                    'domain' => 'cujae.edu.cu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1545 =>
                  array (
                    'domain' => 'cudenver.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1546 =>
                  array (
                    'domain' => 'cuc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1547 =>
                  array (
                    'domain' => 'cua.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1548 =>
                  array (
                    'domain' => 'cu-portland.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1549 =>
                  array (
                    'domain' => 'ctu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1550 =>
                  array (
                    'domain' => 'csusb.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1551 =>
                  array (
                    'domain' => 'csus.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1552 =>
                  array (
                    'domain' => 'csuohio.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1553 =>
                  array (
                    'domain' => 'csumb.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1554 =>
                  array (
                    'domain' => 'csulb.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1555 =>
                  array (
                    'domain' => 'csufresno.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1556 =>
                  array (
                    'domain' => 'csueastbay.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1557 =>
                  array (
                    'domain' => 'csudh.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1558 =>
                  array (
                    'domain' => 'csuchico.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1559 =>
                  array (
                    'domain' => 'csu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1560 =>
                  array (
                    'domain' => 'csu.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1561 =>
                  array (
                    'domain' => 'csu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1562 =>
                  array (
                    'domain' => 'cshl.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1563 =>
                  array (
                    'domain' => 'cscc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1564 =>
                  array (
                    'domain' => 'criba.edu.ar',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1565 =>
                  array (
                    'domain' => 'creighton.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1566 =>
                  array (
                    'domain' => 'cqu.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1567 =>
                  array (
                    'domain' => 'covenant.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1568 =>
                  array (
                    'domain' => 'cooper.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1569 =>
                  array (
                    'domain' => 'conncoll.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1570 =>
                  array (
                    'domain' => 'colum.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1571 =>
                  array (
                    'domain' => 'colstate.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1572 =>
                  array (
                    'domain' => 'coloradotech.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1573 =>
                  array (
                    'domain' => 'coloradomtn.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1574 =>
                  array (
                    'domain' => 'coloradocollege.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1575 =>
                  array (
                    'domain' => 'collinscollege.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1576 =>
                  array (
                    'domain' => 'colgate.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1577 =>
                  array (
                    'domain' => 'colegiatura.edu.co',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1578 =>
                  array (
                    'domain' => 'colby-sawyer.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1579 =>
                  array (
                    'domain' => 'cofc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1580 =>
                  array (
                    'domain' => 'cod.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1581 =>
                  array (
                    'domain' => 'cobacalc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1582 =>
                  array (
                    'domain' => 'coastalbend.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1583 =>
                  array (
                    'domain' => 'cnm.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1584 =>
                  array (
                    'domain' => 'cna-qatar.edu.qa',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1585 =>
                  array (
                    'domain' => 'cmu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1586 =>
                  array (
                    'domain' => 'cmsu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1587 =>
                  array (
                    'domain' => 'cmich.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1588 =>
                  array (
                    'domain' => 'cma.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1589 =>
                  array (
                    'domain' => 'clu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1590 =>
                  array (
                    'domain' => 'clemson.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1591 =>
                  array (
                    'domain' => 'clarku.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1592 =>
                  array (
                    'domain' => 'clark.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1593 =>
                  array (
                    'domain' => 'clarion.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1594 =>
                  array (
                    'domain' => 'claremont.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1595 =>
                  array (
                    'domain' => 'ckit.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1596 =>
                  array (
                    'domain' => 'cju.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1597 =>
                  array (
                    'domain' => 'cjcu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1598 =>
                  array (
                    'domain' => 'ciw.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1599 =>
                  array (
                    'domain' => 'cityue.edu.hk',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1600 =>
                  array (
                    'domain' => 'cin.edu.uy',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1601 =>
                  array (
                    'domain' => 'cier.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1602 =>
                  array (
                    'domain' => 'cic.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1603 =>
                  array (
                    'domain' => 'chw.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1604 =>
                  array (
                    'domain' => 'chu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1605 =>
                  array (
                    'domain' => 'christcollege.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1606 =>
                  array (
                    'domain' => 'chop.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1607 =>
                  array (
                    'domain' => 'chnu.edu.ua',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1608 =>
                  array (
                    'domain' => 'chemeketa.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1609 =>
                  array (
                    'domain' => 'champlain.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1610 =>
                  array (
                    'domain' => 'cgu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1611 =>
                  array (
                    'domain' => 'cgu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1612 =>
                  array (
                    'domain' => 'cfs.edu.hk',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1613 =>
                  array (
                    'domain' => 'cfcc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1614 =>
                  array (
                    'domain' => 'cdrewu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1615 =>
                  array (
                    'domain' => 'ccut.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1616 =>
                  array (
                    'domain' => 'ccu.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1617 =>
                  array (
                    'domain' => 'ccit.edu.tw',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1618 =>
                  array (
                    'domain' => 'ccc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1619 =>
                  array (
                    'domain' => 'carthage.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1620 =>
                  array (
                    'domain' => 'carleton.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1621 =>
                  array (
                    'domain' => 'canyons.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1622 =>
                  array (
                    'domain' => 'cankaya.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1623 =>
                  array (
                    'domain' => 'canberra.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1624 =>
                  array (
                    'domain' => 'camk.edu.pl',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1625 =>
                  array (
                    'domain' => 'calvinchr.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1626 =>
                  array (
                    'domain' => 'calvin.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1627 =>
                  array (
                    'domain' => 'calstate.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1628 =>
                  array (
                    'domain' => 'callutheran.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1629 =>
                  array (
                    'domain' => 'calarts.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1630 =>
                  array (
                    'domain' => 'cabrillo.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1631 =>
                  array (
                    'domain' => 'byuh.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1632 =>
                  array (
                    'domain' => 'byu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1633 =>
                  array (
                    'domain' => 'bvb.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1634 =>
                  array (
                    'domain' => 'butler.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1635 =>
                  array (
                    'domain' => 'bucknell.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1636 =>
                  array (
                    'domain' => 'bu.edu.ro',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1637 =>
                  array (
                    'domain' => 'brynmawr.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1638 =>
                  array (
                    'domain' => 'bryantstratton.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1639 =>
                  array (
                    'domain' => 'broward.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1640 =>
                  array (
                    'domain' => 'brooklaw.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1641 =>
                  array (
                    'domain' => 'brockport.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1642 =>
                  array (
                    'domain' => 'bridgew.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1643 =>
                  array (
                    'domain' => 'brandeis.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1644 =>
                  array (
                    'domain' => 'boun.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1645 =>
                  array (
                    'domain' => 'boisestate.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1646 =>
                  array (
                    'domain' => 'bluffton.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1647 =>
                  array (
                    'domain' => 'bju.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1648 =>
                  array (
                    'domain' => 'biola.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1649 =>
                  array (
                    'domain' => 'bia.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1650 =>
                  array (
                    'domain' => 'bhc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1651 =>
                  array (
                    'domain' => 'bh.edu.ro',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1652 =>
                  array (
                    'domain' => 'bgsu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1653 =>
                  array (
                    'domain' => 'berry.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1654 =>
                  array (
                    'domain' => 'berklee.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1655 =>
                  array (
                    'domain' => 'bennington.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1656 =>
                  array (
                    'domain' => 'bellevue.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1657 =>
                  array (
                    'domain' => 'bcm.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1658 =>
                  array (
                    'domain' => 'bchs.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1659 =>
                  array (
                    'domain' => 'bcc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1660 =>
                  array (
                    'domain' => 'baylor.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1661 =>
                  array (
                    'domain' => 'bates.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1662 =>
                  array (
                    'domain' => 'baskent.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1663 =>
                  array (
                    'domain' => 'bahcesehir.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1664 =>
                  array (
                    'domain' => 'austincollege.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1665 =>
                  array (
                    'domain' => 'austincc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1666 =>
                  array (
                    'domain' => 'augustana.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1667 =>
                  array (
                    'domain' => 'augie.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1668 =>
                  array (
                    'domain' => 'aucegypt.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1669 =>
                  array (
                    'domain' => 'aub.edu.lb',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1670 =>
                  array (
                    'domain' => 'au.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1671 =>
                  array (
                    'domain' => 'atu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1672 =>
                  array (
                    'domain' => 'athens.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1673 =>
                  array (
                    'domain' => 'asbury.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1674 =>
                  array (
                    'domain' => 'armstrong.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1675 =>
                  array (
                    'domain' => 'aquinas.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1676 =>
                  array (
                    'domain' => 'apu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1677 =>
                  array (
                    'domain' => 'aps.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1678 =>
                  array (
                    'domain' => 'appstate.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1679 =>
                  array (
                    'domain' => 'apollogrp.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1680 =>
                  array (
                    'domain' => 'anu.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1681 =>
                  array (
                    'domain' => 'annauniv.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1682 =>
                  array (
                    'domain' => 'ankara.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1683 =>
                  array (
                    'domain' => 'angelo.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1684 =>
                  array (
                    'domain' => 'anadolu.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1685 =>
                  array (
                    'domain' => 'amwaw.edu.pl',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1686 =>
                  array (
                    'domain' => 'amu.edu.pl',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1687 =>
                  array (
                    'domain' => 'ammanu.edu.jo',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1688 =>
                  array (
                    'domain' => 'ammanacademy.edu.jo',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1689 =>
                  array (
                    'domain' => 'amherst.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1690 =>
                  array (
                    'domain' => 'american.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1691 =>
                  array (
                    'domain' => 'amc.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1692 =>
                  array (
                    'domain' => 'alverno.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1693 =>
                  array (
                    'domain' => 'altamahatech.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1694 =>
                  array (
                    'domain' => 'allegheny.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1695 =>
                  array (
                    'domain' => 'alfredstate.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1696 =>
                  array (
                    'domain' => 'alaska.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1697 =>
                  array (
                    'domain' => 'aku.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1698 =>
                  array (
                    'domain' => 'akdeniz.edu.tr',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1699 =>
                  array (
                    'domain' => 'afit.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1700 =>
                  array (
                    'domain' => 'adnu.edu.ph',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1701 =>
                  array (
                    'domain' => 'admu.edu.ph',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1702 =>
                  array (
                    'domain' => 'adfa.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1703 =>
                  array (
                    'domain' => 'adelphi.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1704 =>
                  array (
                    'domain' => 'adelaide.edu.au',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1705 =>
                  array (
                    'domain' => 'ab.edu.pl',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1706 =>
                  array (
                    'domain' => 'aamu.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1707 =>
                  array (
                    'domain' => 'a-star.edu.sg',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1708 =>
                  array (
                    'domain' => '3sheep.edu',
                    'class' => '1',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1709 =>
                  array (
                    'domain' => 'yorkcounty.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1710 =>
                  array (
                    'domain' => 'york.gov.uk',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1711 =>
                  array (
                    'domain' => 'ymp.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1712 =>
                  array (
                    'domain' => 'wa.gov.au',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1713 =>
                  array (
                    'domain' => 'wa.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1714 =>
                  array (
                    'domain' => 'vssc.gov.in',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1715 =>
                  array (
                    'domain' => 'vpn.gov.ie',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1716 =>
                  array (
                    'domain' => 'virginia.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1717 =>
                  array (
                    'domain' => 'vic.gov.au',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1718 =>
                  array (
                    'domain' => 'va.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1719 =>
                  array (
                    'domain' => 'uspto.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1720 =>
                  array (
                    'domain' => 'usps.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1721 =>
                  array (
                    'domain' => 'usgs.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1722 =>
                  array (
                    'domain' => 'usdoj.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1723 =>
                  array (
                    'domain' => 'uscourts.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1724 =>
                  array (
                    'domain' => 'usbr.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1725 =>
                  array (
                    'domain' => 'umirm.gov.pl',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1726 =>
                  array (
                    'domain' => 'ucia.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1727 =>
                  array (
                    'domain' => 'tva.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1728 =>
                  array (
                    'domain' => 'tubitak.gov.tr',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1729 =>
                  array (
                    'domain' => 'treas.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1730 =>
                  array (
                    'domain' => 'to.gov.br',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1731 =>
                  array (
                    'domain' => 'tas.gov.au',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1732 =>
                  array (
                    'domain' => 'syzefxis.gov.gr',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1733 =>
                  array (
                    'domain' => 'state.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1734 =>
                  array (
                    'domain' => 'srs.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1735 =>
                  array (
                    'domain' => 'sr.gov.yu',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1736 =>
                  array (
                    'domain' => 'sfwmd.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1737 =>
                  array (
                    'domain' => 'sanantonio.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1738 =>
                  array (
                    'domain' => 'rzgw.gov.pl',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1739 =>
                  array (
                    'domain' => 'railnet.gov.in',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1740 =>
                  array (
                    'domain' => 'qld.gov.au',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1741 =>
                  array (
                    'domain' => 'pidc.gov.tw',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1742 =>
                  array (
                    'domain' => 'peacecorps.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1743 =>
                  array (
                    'domain' => 'pbh.gov.br',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1744 =>
                  array (
                    'domain' => 'osti.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1745 =>
                  array (
                    'domain' => 'osis.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1746 =>
                  array (
                    'domain' => 'ornl.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1747 =>
                  array (
                    'domain' => 'orau.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1748 =>
                  array (
                    'domain' => 'nyc.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1749 =>
                  array (
                    'domain' => 'nsw.gov.au',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1750 =>
                  array (
                    'domain' => 'nrel.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1751 =>
                  array (
                    'domain' => 'nrc.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1752 =>
                  array (
                    'domain' => 'norfolk.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1753 =>
                  array (
                    'domain' => 'noaa.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1754 =>
                  array (
                    'domain' => 'nitesl.gov.lk',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1755 =>
                  array (
                    'domain' => 'nik.gov.pl',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1756 =>
                  array (
                    'domain' => 'nersc.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1757 =>
                  array (
                    'domain' => 'nencki.gov.pl',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1758 =>
                  array (
                    'domain' => 'neda.gov.ph',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1759 =>
                  array (
                    'domain' => 'ncifcrf.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1760 =>
                  array (
                    'domain' => 'nasb.gov.by',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1761 =>
                  array (
                    'domain' => 'nas.gov.ua',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1762 =>
                  array (
                    'domain' => 'mte.gov.br',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1763 =>
                  array (
                    'domain' => 'mpf.gov.br',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1764 =>
                  array (
                    'domain' => 'mod.gov.il',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1765 =>
                  array (
                    'domain' => 'mizoram.gov.in',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1766 =>
                  array (
                    'domain' => 'mg.gov.br',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1767 =>
                  array (
                    'domain' => 'maricopa.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1768 =>
                  array (
                    'domain' => 'mam.gov.tr',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1769 =>
                  array (
                    'domain' => 'lnk.gov.cl',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1770 =>
                  array (
                    'domain' => 'llnl.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1771 =>
                  array (
                    'domain' => 'la.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1772 =>
                  array (
                    'domain' => 'kgm.gov.tr',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1773 =>
                  array (
                    'domain' => 'kapl.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1774 =>
                  array (
                    'domain' => 'judicial.gov.tw',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1775 =>
                  array (
                    'domain' => 'johor.gov.my',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1776 =>
                  array (
                    'domain' => 'jccbi.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1777 =>
                  array (
                    'domain' => 'irs.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1778 =>
                  array (
                    'domain' => 'ippt.gov.pl',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1779 =>
                  array (
                    'domain' => 'inti.gov.ar',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1780 =>
                  array (
                    'domain' => 'inmetro.gov.br',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1781 =>
                  array (
                    'domain' => 'iner.gov.tw',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1782 =>
                  array (
                    'domain' => 'inel.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1783 =>
                  array (
                    'domain' => 'il.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1784 =>
                  array (
                    'domain' => 'iimcb.gov.pl',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1785 =>
                  array (
                    'domain' => 'ihs.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1786 =>
                  array (
                    'domain' => 'iem.gov.lv',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1787 =>
                  array (
                    'domain' => 'idsc.gov.eg',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1788 =>
                  array (
                    'domain' => 'ida.gov.sg',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1789 =>
                  array (
                    'domain' => 'house.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1790 =>
                  array (
                    'domain' => 'hmgcc.gov.uk',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1791 =>
                  array (
                    'domain' => 'hants.gov.uk',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1792 =>
                  array (
                    'domain' => 'hanford.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1793 =>
                  array (
                    'domain' => 'gwynedd.gov.uk',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1794 =>
                  array (
                    'domain' => 'gsi.gov.uk',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1795 =>
                  array (
                    'domain' => 'gov66.gov.cz',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1796 =>
                  array (
                    'domain' => 'gov12.gov.cz',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1797 =>
                  array (
                    'domain' => 'gba.gov.ar',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1798 =>
                  array (
                    'domain' => 'gao.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1799 =>
                  array (
                    'domain' => 'ga.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1800 =>
                  array (
                    'domain' => 'frb.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1801 =>
                  array (
                    'domain' => 'finance.gov.mk',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1802 =>
                  array (
                    'domain' => 'fdic.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1803 =>
                  array (
                    'domain' => 'fcc.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1804 =>
                  array (
                    'domain' => 'fairfaxcounty.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1805 =>
                  array (
                    'domain' => 'faa.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1806 =>
                  array (
                    'domain' => 'epa.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1807 =>
                  array (
                    'domain' => 'eop.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1808 =>
                  array (
                    'domain' => 'environment-agency.gov.uk',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1809 =>
                  array (
                    'domain' => 'eln.gov.br',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1810 =>
                  array (
                    'domain' => 'eeoc.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1811 =>
                  array (
                    'domain' => 'ed.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1812 =>
                  array (
                    'domain' => 'dstl.gov.uk',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1813 =>
                  array (
                    'domain' => 'dst.gov.za',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1814 =>
                  array (
                    'domain' => 'dpf.gov.br',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1815 =>
                  array (
                    'domain' => 'dot.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1816 =>
                  array (
                    'domain' => 'doechicago.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1817 =>
                  array (
                    'domain' => 'doeal.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1818 =>
                  array (
                    'domain' => 'doe.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1819 =>
                  array (
                    'domain' => 'doc.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1820 =>
                  array (
                    'domain' => 'dhs.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1821 =>
                  array (
                    'domain' => 'dfat.gov.au',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1822 =>
                  array (
                    'domain' => 'dera.gov.uk',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1823 =>
                  array (
                    'domain' => 'defence.gov.au',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1824 =>
                  array (
                    'domain' => 'dcita.gov.au',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1825 =>
                  array (
                    'domain' => 'dc.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1826 =>
                  array (
                    'domain' => 'csl.gov.uk',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1827 =>
                  array (
                    'domain' => 'cise-nsf.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1828 =>
                  array (
                    'domain' => 'cia.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1829 =>
                  array (
                    'domain' => 'cga.gov.tw',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1830 =>
                  array (
                    'domain' => 'ceride.gov.ar',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1831 =>
                  array (
                    'domain' => 'census.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1832 =>
                  array (
                    'domain' => 'cenpra.gov.br',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1833 =>
                  array (
                    'domain' => 'cat.gov.in',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1834 =>
                  array (
                    'domain' => 'capes.gov.br',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1835 =>
                  array (
                    'domain' => 'cabq.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1836 =>
                  array (
                    'domain' => 'ca.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1837 =>
                  array (
                    'domain' => 'bsmi.gov.tw',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1838 =>
                  array (
                    'domain' => 'brighton-hove.gov.uk',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1839 =>
                  array (
                    'domain' => 'bom.gov.au',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1840 =>
                  array (
                    'domain' => 'blm.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1841 =>
                  array (
                    'domain' => 'bettis.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1842 =>
                  array (
                    'domain' => 'bernco.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1843 =>
                  array (
                    'domain' => 'bdep.gov.br',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1844 =>
                  array (
                    'domain' => 'bart.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1845 =>
                  array (
                    'domain' => 'bank.gov.ua',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1846 =>
                  array (
                    'domain' => 'bacninh.gov.vn',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1847 =>
                  array (
                    'domain' => 'ansto.gov.au',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1848 =>
                  array (
                    'domain' => 'anl.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1849 =>
                  array (
                    'domain' => 'alabama.gov',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1850 =>
                  array (
                    'domain' => 'ac.gov.br',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1851 =>
                  array (
                    'domain' => 'aao.gov.au',
                    'class' => '3',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1852 =>
                  array (
                    'domain' => 'sbcglobal.net',
                    'class' => '4',
                    'country' => 'yy',
                    'state' => 'yy',
                    'name' => 'yy',
                  ),
                  1853 =>
                  array (
                    'domain' => 'bell.ca',
                    'class' => '4',
                    'country' => 'yy',
                    'state' => 'yy',
                    'name' => 'yy',
                  ),
                  1854 =>
                  array (
                    'domain' => 'telecomitalia.it',
                    'class' => '4',
                    'country' => 'yy',
                    'state' => 'yy',
                    'name' => 'yy',
                  ),
                  1855 =>
                  array (
                    'domain' => 'airtelbroadband.in',
                    'class' => '4',
                    'country' => 'yy',
                    'state' => 'yy',
                    'name' => 'yy',
                  ),
                  1856 =>
                  array (
                    'domain' => 'india.net',
                    'class' => '4',
                    'country' => 'yy',
                    'state' => 'yy',
                    'name' => 'yy',
                  ),
                  1857 =>
                  array (
                    'domain' => 'vutbr.cz',
                    'class' => '1',
                    'country' => 'cz',
                    'state' => 'yy',
                    'name' => 'BRNO University of Technology',
                  ),
                  1858 =>
                  array (
                    'domain' => 'sc06.org',
                    'class' => '1',
                    'country' => '',
                    'state' => 'yy',
                    'name' => 'SC06',
                  ),
                  1859 =>
                  array (
                    'domain' => 'live.com',
                    'class' => '5',
                    'country' => 'yy',
                    'state' => 'yy',
                    'name' => 'yy',
                  ),
                  1860 =>
                  array (
                    'domain' => 'phx.gbl',
                    'class' => '5',
                    'country' => 'yy',
                    'state' => 'yy',
                    'name' => 'yy',
                  ),
                  1861 =>
                  array (
                    'domain' => 'bloglines.com',
                    'class' => '5',
                    'country' => 'yy',
                    'state' => 'yy',
                    'name' => 'yy',
                  ),
                  1862 =>
                  array (
                    'domain' => 'become.com',
                    'class' => '5',
                    'country' => 'yy',
                    'state' => 'yy',
                    'name' => 'yy',
                  ),
                  1863 =>
                  array (
                    'domain' => 'quest.net',
                    'class' => '2',
                    'country' => 'yy',
                    'state' => 'yy',
                    'name' => 'yy',
                  ),
                  1864 =>
                  array (
                    'domain' => 'brain.grub.org',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1865 =>
                  array (
                    'domain' => 'cosmixcorp.com',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1866 =>
                  array (
                    'domain' => 'crawl8-public.alexa.com',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1867 =>
                  array (
                    'domain' => 'crawler918.com',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1868 =>
                  array (
                    'domain' => 'girafa.com',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1869 =>
                  array (
                    'domain' => 'hanta.yahoo.com',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1870 =>
                  array (
                    'domain' => 'idle.eidetica.com',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1871 =>
                  array (
                    'domain' => 'live-servers.net',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1872 =>
                  array (
                    'domain' => 'looksmart.net',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1873 =>
                  array (
                    'domain' => 'markwatch.com',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1874 =>
                  array (
                    'domain' => 'metacarta.com',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1875 =>
                  array (
                    'domain' => 'morgue1.corp.yahoo.com',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1876 =>
                  array (
                    'domain' => 'msnbot.msn.com',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1877 =>
                  array (
                    'domain' => 'panchma.tivra.com',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1878 =>
                  array (
                    'domain' => 'tpiol.com',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1879 =>
                  array (
                    'domain' => 'tpiol.tpiol.com',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1880 =>
                  array (
                    'domain' => 'tracerlock.com',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1881 =>
                  array (
                    'domain' => 'webclipping.com',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1882 =>
                  array (
                    'domain' => 'websmostlinked.com',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1883 =>
                  array (
                    'domain' => 'websquash.com',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1884 =>
                  array (
                    'domain' => 'whizbang.com',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1885 =>
                  array (
                    'domain' => 'xs4.kso.co.uk',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1886 =>
                  array (
                    'domain' => 'zeus.nj.nec.com',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1887 =>
                  array (
                    'domain' => 'archive.org',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1888 =>
                  array (
                    'domain' => 'authoritativeweb.com',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1889 =>
                  array (
                    'domain' => 'crawl.yahoo.net',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1890 =>
                  array (
                    'domain' => 'entireweb.com',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1891 =>
                  array (
                    'domain' => 'internetserviceteam.com',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1892 =>
                  array (
                    'domain' => 'paginasamarillas.es',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1893 =>
                  array (
                    'domain' => 'sac.overture.com',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1894 =>
                  array (
                    'domain' => 'san2.attens.net',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                  1895 =>
                  array (
                    'domain' => 'worio.com',
                    'class' => '5',
                    'country' => 'xx',
                    'state' => 'xx',
                    'name' => 'xx',
                  ),
                );
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('domainclasses')) {
            try {
                $statsDb->schema()->createTable('domainclasses')
                    ->tinyInteger('class')->default(0)
                    ->string('name', 64)->default('')
                    ->primaryKey('class')
                    ->uniqueIndex('class_name', ['class', 'name'])
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $rows = array (
                  0 =>
                  array (
                    'class' => '0',
                    'name' => 'Unknown',
                  ),
                  1 =>
                  array (
                    'class' => '1',
                    'name' => 'Educational Institution',
                  ),
                  2 =>
                  array (
                    'class' => '2',
                    'name' => 'Industrial/Corporate',
                  ),
                  3 =>
                  array (
                    'class' => '3',
                    'name' => 'Governmental',
                  ),
                  4 =>
                  array (
                    'class' => '4',
                    'name' => 'Internet Service Provider',
                  ),
                  5 =>
                  array (
                    'class' => '5',
                    'name' => 'Search Engine',
                  ),
                  6 =>
                  array (
                    'class' => '6',
                    'name' => 'Press/Media/Publication',
                  ),
                );
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('summary_user')) {
            try {
                $statsDb->schema()->createTable('summary_user')
                    ->tinyInteger('id')->default(0)
                    ->string('label', 255)->default('')
                    ->integer('plot')->default(0)
                    ->uniqueIndex('label', 'label')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $rows = array (
                  0 =>
                  array (
                    'id' => '1',
                    'label' => 'Total Users {1}',
                    'plot' => '1',
                  ),
                  1 =>
                  array (
                    'id' => '6',
                    'label' => '- Registered Users {2}',
                    'plot' => '1',
                  ),
                  2 =>
                  array (
                    'id' => '7',
                    'label' => '- Unregistered Interactive Users {3}',
                    'plot' => '1',
                  ),
                  3 =>
                  array (
                    'id' => '8',
                    'label' => '- Unregistered Download Users {4}',
                    'plot' => '1',
                  ),
                  4 =>
                  array (
                    'id' => '3',
                    'label' => '- Interactive Users {6}',
                    'plot' => '1',
                  ),
                  5 =>
                  array (
                    'id' => '2',
                    'label' => '- Simulation Users {5}',
                    'plot' => '1',
                  ),
                  6 =>
                  array (
                    'id' => '4',
                    'label' => '- Download Users {7}',
                    'plot' => '1',
                  ),
                );
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('summary_user_vals')) {
            try {
                $statsDb->schema()->createTable('summary_user_vals')
                    ->tinyInteger('rowid')->default(0)
                    ->tinyInteger('colid')->default(0)
                    ->datetime('datetime')->default('0000-00-00 00:00:00')
                    ->tinyInteger('period')->default(1)
                    ->bigInteger('value')->default(0)
                    ->tinyInteger('valfmt')->default(0)
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('summary_andmore')) {
            try {
                $statsDb->schema()->createTable('summary_andmore')
                    ->tinyInteger('id')->default(0)
                    ->string('label', 255)->default('')
                    ->integer('plot')->default(0)
                    ->uniqueIndex('label', 'label')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $rows = array (
                  0 =>
                  array (
                    'id' => '1',
                    'label' => 'Nano 101',
                    'plot' => '0',
                  ),
                  1 =>
                  array (
                    'id' => '2',
                    'label' => 'Nano 501',
                    'plot' => '0',
                  ),
                  2 =>
                  array (
                    'id' => '3',
                    'label' => 'Research Seminars',
                    'plot' => '0',
                  ),
                  3 =>
                  array (
                    'id' => '4',
                    'label' => 'Courses',
                    'plot' => '0',
                  ),
                  4 =>
                  array (
                    'id' => '5',
                    'label' => 'Series',
                    'plot' => '0',
                  ),
                  5 =>
                  array (
                    'id' => '6',
                    'label' => 'Workshops',
                    'plot' => '0',
                  ),
                  6 =>
                  array (
                    'id' => '7',
                    'label' => 'Teaching Materials',
                    'plot' => '0',
                  ),
                  7 =>
                  array (
                    'id' => '8',
                    'label' => 'Other non-interactive Resources',
                    'plot' => '0',
                  ),
                  8 =>
                  array (
                    'id' => '9',
                    'label' => 'Breeze Presentations',
                    'plot' => '0',
                  ),
                  9 =>
                  array (
                    'id' => '10',
                    'label' => 'PDF Files',
                    'plot' => '0',
                  ),
                  10 =>
                  array (
                    'id' => '11',
                    'label' => 'Podcasts',
                    'plot' => '0',
                  ),
                  11 =>
                  array (
                    'id' => '12',
                    'label' => 'Other Documents',
                    'plot' => '0',
                  ),
                );
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('summary_misc')) {
            try {
                $statsDb->schema()->createTable('summary_misc')
                    ->tinyInteger('id')->default(0)
                    ->string('label', 255)->default('')
                    ->integer('plot')->default(0)
                    ->uniqueIndex('label', 'label')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $rows = array (
                  0 =>
                  array (
                    'id' => '1',
                    'label' => 'Domains Served',
                    'plot' => '0',
                  ),
                  1 =>
                  array (
                    'id' => '2',
                    'label' => 'Cummulative Interactive User Sessions {10}',
                    'plot' => '0',
                  ),
                  2 =>
                  array (
                    'id' => '3',
                    'label' => 'Cummulative Session Time',
                    'plot' => '0',
                  ),
                  3 =>
                  array (
                    'id' => '4',
                    'label' => 'Visitors {11}',
                    'plot' => '0',
                  ),
                  4 =>
                  array (
                    'id' => '5',
                    'label' => 'Visits {12}',
                    'plot' => '0',
                  ),
                  5 =>
                  array (
                    'id' => '6',
                    'label' => 'New Accounts',
                    'plot' => '0',
                  ),
                  6 =>
                  array (
                    'id' => '7',
                    'label' => 'Max User Logins',
                    'plot' => '0',
                  ),
                  7 =>
                  array (
                    'id' => '8',
                    'label' => 'Web Server Hits',
                    'plot' => '1',
                  ),
                );
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('summary_simusage')) {
            try {
                $statsDb->schema()->createTable('summary_simusage')
                    ->tinyInteger('id')->default(0)
                    ->string('label', 255)->default('')
                    ->integer('plot')->default(0)
                    ->uniqueIndex('label', 'label')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $rows = array (
                  0 =>
                  array (
                    'id' => '1',
                    'label' => 'Simulation Users {5}',
                    'plot' => '1',
                  ),
                  1 =>
                  array (
                    'id' => '2',
                    'label' => 'Simulation Runs',
                    'plot' => '1',
                  ),
                  2 =>
                  array (
                    'id' => '3',
                    'label' => 'Total CPU Time',
                    'plot' => '0',
                  ),
                  3 =>
                  array (
                    'id' => '4',
                    'label' => 'Total Wall Time',
                    'plot' => '0',
                  ),
                  4 =>
                  array (
                    'id' => '5',
                    'label' => 'Total Interaction Time',
                    'plot' => '0',
                  ),
                  5 =>
                  array (
                    'id' => '6',
                    'label' => 'Users with > 10 mins of CPU Time',
                    'plot' => '0',
                  ),
                  6 =>
                  array (
                    'id' => '7',
                    'label' => 'Avg. Number of Simulation Runs/User',
                    'plot' => '0',
                  ),
                  7 =>
                  array (
                    'id' => '8',
                    'label' => 'Avg. Time between First and Last Simulation',
                    'plot' => '0',
                  ),
                  8 =>
                  array (
                    'id' => '9',
                    'label' => 'Repeat Users with > 10 Simulation Jobs',
                    'plot' => '0',
                  ),
                  9 =>
                  array (
                    'id' => '10',
                    'label' => 'Repeat Users with > 3 Months {9}',
                    'plot' => '0',
                  ),
                );
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('continents')) {
            try {
                $statsDb->schema()->createTable('continents')
                    ->char('continentSHORT', 2)->default('')
                    ->string('continentLONG', 45)->default('')
                    ->uniqueIndex('continentSHORT_continentLONG', ['continentSHORT', 'continentLONG'])
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $rows = array (
                  0 =>
                  array (
                    'continentSHORT' => 'AF',
                    'continentLONG' => 'Africa',
                  ),
                  1 =>
                  array (
                    'continentSHORT' => 'AN',
                    'continentLONG' => 'Antartica',
                  ),
                  2 =>
                  array (
                    'continentSHORT' => 'AS',
                    'continentLONG' => 'Asia',
                  ),
                  3 =>
                  array (
                    'continentSHORT' => 'EU',
                    'continentLONG' => 'Europe',
                  ),
                  4 =>
                  array (
                    'continentSHORT' => 'NA',
                    'continentLONG' => 'North America',
                  ),
                  5 =>
                  array (
                    'continentSHORT' => 'OC',
                    'continentLONG' => 'Oceania',
                  ),
                  6 =>
                  array (
                    'continentSHORT' => 'SA',
                    'continentLONG' => 'South America',
                  ),
                );
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('country_continent')) {
            try {
                $statsDb->schema()->createTable('country_continent')
                    ->char('country', 2)->default('')
                    ->char('continent', 2)->default('')
                    ->primaryKey(['country', 'continent'])
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $rows = array (
                  0 =>
                  array (
                    'country' => 'AD',
                    'continent' => 'EU',
                  ),
                  1 =>
                  array (
                    'country' => 'AE',
                    'continent' => 'AS',
                  ),
                  2 =>
                  array (
                    'country' => 'AF',
                    'continent' => 'AS',
                  ),
                  3 =>
                  array (
                    'country' => 'AG',
                    'continent' => 'NA',
                  ),
                  4 =>
                  array (
                    'country' => 'AI',
                    'continent' => 'NA',
                  ),
                  5 =>
                  array (
                    'country' => 'AL',
                    'continent' => 'EU',
                  ),
                  6 =>
                  array (
                    'country' => 'AM',
                    'continent' => 'EU',
                  ),
                  7 =>
                  array (
                    'country' => 'AN',
                    'continent' => 'NA',
                  ),
                  8 =>
                  array (
                    'country' => 'AO',
                    'continent' => 'AF',
                  ),
                  9 =>
                  array (
                    'country' => 'AQ',
                    'continent' => 'AN',
                  ),
                  10 =>
                  array (
                    'country' => 'AR',
                    'continent' => 'SA',
                  ),
                  11 =>
                  array (
                    'country' => 'AS',
                    'continent' => 'OC',
                  ),
                  12 =>
                  array (
                    'country' => 'AT',
                    'continent' => 'EU',
                  ),
                  13 =>
                  array (
                    'country' => 'AU',
                    'continent' => 'OC',
                  ),
                  14 =>
                  array (
                    'country' => 'AW',
                    'continent' => 'NA',
                  ),
                  15 =>
                  array (
                    'country' => 'AX',
                    'continent' => 'EU',
                  ),
                  16 =>
                  array (
                    'country' => 'AZ',
                    'continent' => 'EU',
                  ),
                  17 =>
                  array (
                    'country' => 'BA',
                    'continent' => 'EU',
                  ),
                  18 =>
                  array (
                    'country' => 'BB',
                    'continent' => 'NA',
                  ),
                  19 =>
                  array (
                    'country' => 'BD',
                    'continent' => 'AS',
                  ),
                  20 =>
                  array (
                    'country' => 'BE',
                    'continent' => 'EU',
                  ),
                  21 =>
                  array (
                    'country' => 'BF',
                    'continent' => 'AF',
                  ),
                  22 =>
                  array (
                    'country' => 'BG',
                    'continent' => 'EU',
                  ),
                  23 =>
                  array (
                    'country' => 'BH',
                    'continent' => 'AS',
                  ),
                  24 =>
                  array (
                    'country' => 'BI',
                    'continent' => 'AF',
                  ),
                  25 =>
                  array (
                    'country' => 'BJ',
                    'continent' => 'AF',
                  ),
                  26 =>
                  array (
                    'country' => 'BM',
                    'continent' => 'NA',
                  ),
                  27 =>
                  array (
                    'country' => 'BN',
                    'continent' => 'AS',
                  ),
                  28 =>
                  array (
                    'country' => 'BO',
                    'continent' => 'SA',
                  ),
                  29 =>
                  array (
                    'country' => 'BR',
                    'continent' => 'SA',
                  ),
                  30 =>
                  array (
                    'country' => 'BS',
                    'continent' => 'NA',
                  ),
                  31 =>
                  array (
                    'country' => 'BT',
                    'continent' => 'AS',
                  ),
                  32 =>
                  array (
                    'country' => 'BV',
                    'continent' => 'AN',
                  ),
                  33 =>
                  array (
                    'country' => 'BW',
                    'continent' => 'AF',
                  ),
                  34 =>
                  array (
                    'country' => 'BY',
                    'continent' => 'EU',
                  ),
                  35 =>
                  array (
                    'country' => 'BZ',
                    'continent' => 'NA',
                  ),
                  36 =>
                  array (
                    'country' => 'CA',
                    'continent' => 'NA',
                  ),
                  37 =>
                  array (
                    'country' => 'CC',
                    'continent' => 'AS',
                  ),
                  38 =>
                  array (
                    'country' => 'CD',
                    'continent' => 'AF',
                  ),
                  39 =>
                  array (
                    'country' => 'CF',
                    'continent' => 'AF',
                  ),
                  40 =>
                  array (
                    'country' => 'CG',
                    'continent' => 'AF',
                  ),
                  41 =>
                  array (
                    'country' => 'CH',
                    'continent' => 'EU',
                  ),
                  42 =>
                  array (
                    'country' => 'CI',
                    'continent' => 'AF',
                  ),
                  43 =>
                  array (
                    'country' => 'CK',
                    'continent' => 'OC',
                  ),
                  44 =>
                  array (
                    'country' => 'CL',
                    'continent' => 'SA',
                  ),
                  45 =>
                  array (
                    'country' => 'CM',
                    'continent' => 'AF',
                  ),
                  46 =>
                  array (
                    'country' => 'CN',
                    'continent' => 'AS',
                  ),
                  47 =>
                  array (
                    'country' => 'CO',
                    'continent' => 'SA',
                  ),
                  48 =>
                  array (
                    'country' => 'CR',
                    'continent' => 'NA',
                  ),
                  49 =>
                  array (
                    'country' => 'CU',
                    'continent' => 'NA',
                  ),
                  50 =>
                  array (
                    'country' => 'CV',
                    'continent' => 'AF',
                  ),
                  51 =>
                  array (
                    'country' => 'CX',
                    'continent' => 'AS',
                  ),
                  52 =>
                  array (
                    'country' => 'CY',
                    'continent' => 'EU',
                  ),
                  53 =>
                  array (
                    'country' => 'CZ',
                    'continent' => 'EU',
                  ),
                  54 =>
                  array (
                    'country' => 'DE',
                    'continent' => 'EU',
                  ),
                  55 =>
                  array (
                    'country' => 'DJ',
                    'continent' => 'AF',
                  ),
                  56 =>
                  array (
                    'country' => 'DK',
                    'continent' => 'EU',
                  ),
                  57 =>
                  array (
                    'country' => 'DM',
                    'continent' => 'NA',
                  ),
                  58 =>
                  array (
                    'country' => 'DO',
                    'continent' => 'NA',
                  ),
                  59 =>
                  array (
                    'country' => 'DZ',
                    'continent' => 'AF',
                  ),
                  60 =>
                  array (
                    'country' => 'EC',
                    'continent' => 'SA',
                  ),
                  61 =>
                  array (
                    'country' => 'EE',
                    'continent' => 'EU',
                  ),
                  62 =>
                  array (
                    'country' => 'EG',
                    'continent' => 'AF',
                  ),
                  63 =>
                  array (
                    'country' => 'EH',
                    'continent' => 'AF',
                  ),
                  64 =>
                  array (
                    'country' => 'ER',
                    'continent' => 'AF',
                  ),
                  65 =>
                  array (
                    'country' => 'ES',
                    'continent' => 'EU',
                  ),
                  66 =>
                  array (
                    'country' => 'ET',
                    'continent' => 'AF',
                  ),
                  67 =>
                  array (
                    'country' => 'FI',
                    'continent' => 'EU',
                  ),
                  68 =>
                  array (
                    'country' => 'FJ',
                    'continent' => 'OC',
                  ),
                  69 =>
                  array (
                    'country' => 'FK',
                    'continent' => 'SA',
                  ),
                  70 =>
                  array (
                    'country' => 'FM',
                    'continent' => 'OC',
                  ),
                  71 =>
                  array (
                    'country' => 'FO',
                    'continent' => 'EU',
                  ),
                  72 =>
                  array (
                    'country' => 'FR',
                    'continent' => 'EU',
                  ),
                  73 =>
                  array (
                    'country' => 'GA',
                    'continent' => 'AF',
                  ),
                  74 =>
                  array (
                    'country' => 'GD',
                    'continent' => 'NA',
                  ),
                  75 =>
                  array (
                    'country' => 'GE',
                    'continent' => 'EU',
                  ),
                  76 =>
                  array (
                    'country' => 'GF',
                    'continent' => 'SA',
                  ),
                  77 =>
                  array (
                    'country' => 'GG',
                    'continent' => 'EU',
                  ),
                  78 =>
                  array (
                    'country' => 'GH',
                    'continent' => 'AF',
                  ),
                  79 =>
                  array (
                    'country' => 'GI',
                    'continent' => 'EU',
                  ),
                  80 =>
                  array (
                    'country' => 'GL',
                    'continent' => 'NA',
                  ),
                  81 =>
                  array (
                    'country' => 'GM',
                    'continent' => 'AF',
                  ),
                  82 =>
                  array (
                    'country' => 'GN',
                    'continent' => 'AF',
                  ),
                  83 =>
                  array (
                    'country' => 'GP',
                    'continent' => 'NA',
                  ),
                  84 =>
                  array (
                    'country' => 'GQ',
                    'continent' => 'AF',
                  ),
                  85 =>
                  array (
                    'country' => 'GR',
                    'continent' => 'EU',
                  ),
                  86 =>
                  array (
                    'country' => 'GS',
                    'continent' => 'EU',
                  ),
                  87 =>
                  array (
                    'country' => 'GT',
                    'continent' => 'NA',
                  ),
                  88 =>
                  array (
                    'country' => 'GU',
                    'continent' => 'OC',
                  ),
                  89 =>
                  array (
                    'country' => 'GW',
                    'continent' => 'AF',
                  ),
                  90 =>
                  array (
                    'country' => 'GY',
                    'continent' => 'SA',
                  ),
                  91 =>
                  array (
                    'country' => 'HK',
                    'continent' => 'AS',
                  ),
                  92 =>
                  array (
                    'country' => 'HM',
                    'continent' => 'OC',
                  ),
                  93 =>
                  array (
                    'country' => 'HN',
                    'continent' => 'NA',
                  ),
                  94 =>
                  array (
                    'country' => 'HR',
                    'continent' => 'EU',
                  ),
                  95 =>
                  array (
                    'country' => 'HT',
                    'continent' => 'NA',
                  ),
                  96 =>
                  array (
                    'country' => 'HU',
                    'continent' => 'EU',
                  ),
                  97 =>
                  array (
                    'country' => 'ID',
                    'continent' => 'AS',
                  ),
                  98 =>
                  array (
                    'country' => 'IE',
                    'continent' => 'EU',
                  ),
                  99 =>
                  array (
                    'country' => 'IL',
                    'continent' => 'AS',
                  ),
                  100 =>
                  array (
                    'country' => 'IM',
                    'continent' => 'EU',
                  ),
                  101 =>
                  array (
                    'country' => 'IN',
                    'continent' => 'AS',
                  ),
                  102 =>
                  array (
                    'country' => 'IO',
                    'continent' => 'AS',
                  ),
                  103 =>
                  array (
                    'country' => 'IQ',
                    'continent' => 'AS',
                  ),
                  104 =>
                  array (
                    'country' => 'IR',
                    'continent' => 'AS',
                  ),
                  105 =>
                  array (
                    'country' => 'IS',
                    'continent' => 'EU',
                  ),
                  106 =>
                  array (
                    'country' => 'IT',
                    'continent' => 'EU',
                  ),
                  107 =>
                  array (
                    'country' => 'JE',
                    'continent' => 'EU',
                  ),
                  108 =>
                  array (
                    'country' => 'JM',
                    'continent' => 'NA',
                  ),
                  109 =>
                  array (
                    'country' => 'JO',
                    'continent' => 'AS',
                  ),
                  110 =>
                  array (
                    'country' => 'JP',
                    'continent' => 'AS',
                  ),
                  111 =>
                  array (
                    'country' => 'KE',
                    'continent' => 'AF',
                  ),
                  112 =>
                  array (
                    'country' => 'KG',
                    'continent' => 'AS',
                  ),
                  113 =>
                  array (
                    'country' => 'KH',
                    'continent' => 'AS',
                  ),
                  114 =>
                  array (
                    'country' => 'KI',
                    'continent' => 'OC',
                  ),
                  115 =>
                  array (
                    'country' => 'KM',
                    'continent' => 'AF',
                  ),
                  116 =>
                  array (
                    'country' => 'KN',
                    'continent' => 'NA',
                  ),
                  117 =>
                  array (
                    'country' => 'KP',
                    'continent' => 'AS',
                  ),
                  118 =>
                  array (
                    'country' => 'KR',
                    'continent' => 'AS',
                  ),
                  119 =>
                  array (
                    'country' => 'KW',
                    'continent' => 'AS',
                  ),
                  120 =>
                  array (
                    'country' => 'KY',
                    'continent' => 'NA',
                  ),
                  121 =>
                  array (
                    'country' => 'KZ',
                    'continent' => 'AS',
                  ),
                  122 =>
                  array (
                    'country' => 'LA',
                    'continent' => 'AS',
                  ),
                  123 =>
                  array (
                    'country' => 'LB',
                    'continent' => 'AS',
                  ),
                  124 =>
                  array (
                    'country' => 'LC',
                    'continent' => 'NA',
                  ),
                  125 =>
                  array (
                    'country' => 'LI',
                    'continent' => 'EU',
                  ),
                  126 =>
                  array (
                    'country' => 'LK',
                    'continent' => 'AS',
                  ),
                  127 =>
                  array (
                    'country' => 'LR',
                    'continent' => 'AF',
                  ),
                  128 =>
                  array (
                    'country' => 'LS',
                    'continent' => 'AF',
                  ),
                  129 =>
                  array (
                    'country' => 'LT',
                    'continent' => 'EU',
                  ),
                  130 =>
                  array (
                    'country' => 'LU',
                    'continent' => 'EU',
                  ),
                  131 =>
                  array (
                    'country' => 'LV',
                    'continent' => 'EU',
                  ),
                  132 =>
                  array (
                    'country' => 'LY',
                    'continent' => 'AF',
                  ),
                  133 =>
                  array (
                    'country' => 'MA',
                    'continent' => 'AF',
                  ),
                  134 =>
                  array (
                    'country' => 'MC',
                    'continent' => 'EU',
                  ),
                  135 =>
                  array (
                    'country' => 'MD',
                    'continent' => 'EU',
                  ),
                  136 =>
                  array (
                    'country' => 'ME',
                    'continent' => 'EU',
                  ),
                  137 =>
                  array (
                    'country' => 'MF',
                    'continent' => 'NA',
                  ),
                  138 =>
                  array (
                    'country' => 'MG',
                    'continent' => 'AF',
                  ),
                  139 =>
                  array (
                    'country' => 'MH',
                    'continent' => 'OC',
                  ),
                  140 =>
                  array (
                    'country' => 'MK',
                    'continent' => 'EU',
                  ),
                  141 =>
                  array (
                    'country' => 'ML',
                    'continent' => 'AF',
                  ),
                  142 =>
                  array (
                    'country' => 'MM',
                    'continent' => 'AS',
                  ),
                  143 =>
                  array (
                    'country' => 'MN',
                    'continent' => 'AS',
                  ),
                  144 =>
                  array (
                    'country' => 'MO',
                    'continent' => 'AS',
                  ),
                  145 =>
                  array (
                    'country' => 'MP',
                    'continent' => 'OC',
                  ),
                  146 =>
                  array (
                    'country' => 'MQ',
                    'continent' => 'NA',
                  ),
                  147 =>
                  array (
                    'country' => 'MR',
                    'continent' => 'AF',
                  ),
                  148 =>
                  array (
                    'country' => 'MS',
                    'continent' => 'NA',
                  ),
                  149 =>
                  array (
                    'country' => 'MT',
                    'continent' => 'EU',
                  ),
                  150 =>
                  array (
                    'country' => 'MU',
                    'continent' => 'AF',
                  ),
                  151 =>
                  array (
                    'country' => 'MV',
                    'continent' => 'AS',
                  ),
                  152 =>
                  array (
                    'country' => 'MW',
                    'continent' => 'AF',
                  ),
                  153 =>
                  array (
                    'country' => 'MX',
                    'continent' => 'NA',
                  ),
                  154 =>
                  array (
                    'country' => 'MY',
                    'continent' => 'AS',
                  ),
                  155 =>
                  array (
                    'country' => 'MZ',
                    'continent' => 'AF',
                  ),
                  156 =>
                  array (
                    'country' => 'NA',
                    'continent' => 'AF',
                  ),
                  157 =>
                  array (
                    'country' => 'NC',
                    'continent' => 'OC',
                  ),
                  158 =>
                  array (
                    'country' => 'NE',
                    'continent' => 'AF',
                  ),
                  159 =>
                  array (
                    'country' => 'NF',
                    'continent' => 'OC',
                  ),
                  160 =>
                  array (
                    'country' => 'NG',
                    'continent' => 'AF',
                  ),
                  161 =>
                  array (
                    'country' => 'NI',
                    'continent' => 'NA',
                  ),
                  162 =>
                  array (
                    'country' => 'NL',
                    'continent' => 'EU',
                  ),
                  163 =>
                  array (
                    'country' => 'NO',
                    'continent' => 'EU',
                  ),
                  164 =>
                  array (
                    'country' => 'NP',
                    'continent' => 'AS',
                  ),
                  165 =>
                  array (
                    'country' => 'NR',
                    'continent' => 'OC',
                  ),
                  166 =>
                  array (
                    'country' => 'NU',
                    'continent' => 'OC',
                  ),
                  167 =>
                  array (
                    'country' => 'NZ',
                    'continent' => 'OC',
                  ),
                  168 =>
                  array (
                    'country' => 'OM',
                    'continent' => 'AS',
                  ),
                  169 =>
                  array (
                    'country' => 'PA',
                    'continent' => 'NA',
                  ),
                  170 =>
                  array (
                    'country' => 'PE',
                    'continent' => 'SA',
                  ),
                  171 =>
                  array (
                    'country' => 'PF',
                    'continent' => 'OC',
                  ),
                  172 =>
                  array (
                    'country' => 'PG',
                    'continent' => 'OC',
                  ),
                  173 =>
                  array (
                    'country' => 'PH',
                    'continent' => 'AS',
                  ),
                  174 =>
                  array (
                    'country' => 'PK',
                    'continent' => 'AS',
                  ),
                  175 =>
                  array (
                    'country' => 'PL',
                    'continent' => 'EU',
                  ),
                  176 =>
                  array (
                    'country' => 'PM',
                    'continent' => 'NA',
                  ),
                  177 =>
                  array (
                    'country' => 'PN',
                    'continent' => 'OC',
                  ),
                  178 =>
                  array (
                    'country' => 'PR',
                    'continent' => 'NA',
                  ),
                  179 =>
                  array (
                    'country' => 'PS',
                    'continent' => 'AS',
                  ),
                  180 =>
                  array (
                    'country' => 'PT',
                    'continent' => 'EU',
                  ),
                  181 =>
                  array (
                    'country' => 'PW',
                    'continent' => 'OC',
                  ),
                  182 =>
                  array (
                    'country' => 'PY',
                    'continent' => 'SA',
                  ),
                  183 =>
                  array (
                    'country' => 'QA',
                    'continent' => 'AS',
                  ),
                  184 =>
                  array (
                    'country' => 'RE',
                    'continent' => 'AF',
                  ),
                  185 =>
                  array (
                    'country' => 'RO',
                    'continent' => 'EU',
                  ),
                  186 =>
                  array (
                    'country' => 'RS',
                    'continent' => 'EU',
                  ),
                  187 =>
                  array (
                    'country' => 'RU',
                    'continent' => 'EU',
                  ),
                  188 =>
                  array (
                    'country' => 'RW',
                    'continent' => 'AF',
                  ),
                  189 =>
                  array (
                    'country' => 'SA',
                    'continent' => 'AS',
                  ),
                  190 =>
                  array (
                    'country' => 'SB',
                    'continent' => 'OC',
                  ),
                  191 =>
                  array (
                    'country' => 'SC',
                    'continent' => 'AF',
                  ),
                  192 =>
                  array (
                    'country' => 'SD',
                    'continent' => 'AF',
                  ),
                  193 =>
                  array (
                    'country' => 'SE',
                    'continent' => 'EU',
                  ),
                  194 =>
                  array (
                    'country' => 'SG',
                    'continent' => 'AS',
                  ),
                  195 =>
                  array (
                    'country' => 'SH',
                    'continent' => 'AF',
                  ),
                  196 =>
                  array (
                    'country' => 'SI',
                    'continent' => 'EU',
                  ),
                  197 =>
                  array (
                    'country' => 'SJ',
                    'continent' => 'EU',
                  ),
                  198 =>
                  array (
                    'country' => 'SK',
                    'continent' => 'EU',
                  ),
                  199 =>
                  array (
                    'country' => 'SL',
                    'continent' => 'AF',
                  ),
                  200 =>
                  array (
                    'country' => 'SM',
                    'continent' => 'EU',
                  ),
                  201 =>
                  array (
                    'country' => 'SN',
                    'continent' => 'AF',
                  ),
                  202 =>
                  array (
                    'country' => 'SO',
                    'continent' => 'AF',
                  ),
                  203 =>
                  array (
                    'country' => 'SR',
                    'continent' => 'SA',
                  ),
                  204 =>
                  array (
                    'country' => 'ST',
                    'continent' => 'AF',
                  ),
                  205 =>
                  array (
                    'country' => 'SV',
                    'continent' => 'NA',
                  ),
                  206 =>
                  array (
                    'country' => 'SY',
                    'continent' => 'EU',
                  ),
                  207 =>
                  array (
                    'country' => 'SZ',
                    'continent' => 'AF',
                  ),
                  208 =>
                  array (
                    'country' => 'TC',
                    'continent' => 'NA',
                  ),
                  209 =>
                  array (
                    'country' => 'TD',
                    'continent' => 'AF',
                  ),
                  210 =>
                  array (
                    'country' => 'TF',
                    'continent' => 'AN',
                  ),
                  211 =>
                  array (
                    'country' => 'TG',
                    'continent' => 'AF',
                  ),
                  212 =>
                  array (
                    'country' => 'TH',
                    'continent' => 'AS',
                  ),
                  213 =>
                  array (
                    'country' => 'TJ',
                    'continent' => 'AS',
                  ),
                  214 =>
                  array (
                    'country' => 'TK',
                    'continent' => 'OC',
                  ),
                  215 =>
                  array (
                    'country' => 'TL',
                    'continent' => 'AS',
                  ),
                  216 =>
                  array (
                    'country' => 'TM',
                    'continent' => 'AS',
                  ),
                  217 =>
                  array (
                    'country' => 'TN',
                    'continent' => 'AF',
                  ),
                  218 =>
                  array (
                    'country' => 'TO',
                    'continent' => 'OC',
                  ),
                  219 =>
                  array (
                    'country' => 'TR',
                    'continent' => 'EU',
                  ),
                  220 =>
                  array (
                    'country' => 'TT',
                    'continent' => 'NA',
                  ),
                  221 =>
                  array (
                    'country' => 'TV',
                    'continent' => 'OC',
                  ),
                  222 =>
                  array (
                    'country' => 'TW',
                    'continent' => 'AS',
                  ),
                  223 =>
                  array (
                    'country' => 'TZ',
                    'continent' => 'AF',
                  ),
                  224 =>
                  array (
                    'country' => 'UA',
                    'continent' => 'EU',
                  ),
                  225 =>
                  array (
                    'country' => 'UG',
                    'continent' => 'AF',
                  ),
                  226 =>
                  array (
                    'country' => 'UK',
                    'continent' => 'EU',
                  ),
                  227 =>
                  array (
                    'country' => 'UM',
                    'continent' => 'NA',
                  ),
                  228 =>
                  array (
                    'country' => 'US',
                    'continent' => 'NA',
                  ),
                  229 =>
                  array (
                    'country' => 'UY',
                    'continent' => 'SA',
                  ),
                  230 =>
                  array (
                    'country' => 'UZ',
                    'continent' => 'AS',
                  ),
                  231 =>
                  array (
                    'country' => 'VA',
                    'continent' => 'EU',
                  ),
                  232 =>
                  array (
                    'country' => 'VC',
                    'continent' => 'NA',
                  ),
                  233 =>
                  array (
                    'country' => 'VE',
                    'continent' => 'SA',
                  ),
                  234 =>
                  array (
                    'country' => 'VG',
                    'continent' => 'EU',
                  ),
                  235 =>
                  array (
                    'country' => 'VI',
                    'continent' => 'NA',
                  ),
                  236 =>
                  array (
                    'country' => 'VN',
                    'continent' => 'AS',
                  ),
                  237 =>
                  array (
                    'country' => 'VU',
                    'continent' => 'OC',
                  ),
                  238 =>
                  array (
                    'country' => 'WF',
                    'continent' => 'OC',
                  ),
                  239 =>
                  array (
                    'country' => 'WS',
                    'continent' => 'OC',
                  ),
                  240 =>
                  array (
                    'country' => 'YE',
                    'continent' => 'AS',
                  ),
                  241 =>
                  array (
                    'country' => 'YT',
                    'continent' => 'AF',
                  ),
                  242 =>
                  array (
                    'country' => 'ZA',
                    'continent' => 'AF',
                  ),
                  243 =>
                  array (
                    'country' => 'ZM',
                    'continent' => 'AF',
                  ),
                  244 =>
                  array (
                    'country' => 'ZW',
                    'continent' => 'AF',
                  ),
                );
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('exclude_list')) {
            try {
                $statsDb->schema()->createTable('exclude_list')
                    ->string('filter', 65)
                    ->string('type', 65)->default('domain')
                    ->string('notes', 120)->nullable()
                    ->uniqueIndex('filter_type', ['filter', 'type'])
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $rows = array (
                  0 =>
                  array (
                    'filter' => 'googlebot.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  1 =>
                  array (
                    'filter' => 'crawl.yahoo.net',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  2 =>
                  array (
                    'filter' => 'search.msn.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  3 =>
                  array (
                    'filter' => 'inktomisearch.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  4 =>
                  array (
                    'filter' => 'msnbot.msn.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  5 =>
                  array (
                    'filter' => '%googlebot.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  6 =>
                  array (
                    'filter' => '%crawl.yahoo.net',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  7 =>
                  array (
                    'filter' => '%search.msn.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  8 =>
                  array (
                    'filter' => '%inktomisearch.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  9 =>
                  array (
                    'filter' => '%msnbot.msn.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  10 =>
                  array (
                    'filter' => 'rtmp1.hubzero.org',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  11 =>
                  array (
                    'filter' => 'spider%.yandex.ru',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  12 =>
                  array (
                    'filter' => 'crawl%.cuill.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  13 =>
                  array (
                    'filter' => 'crawler%.ask.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  14 =>
                  array (
                    'filter' => 'crawl%.dotnetdotcom.org',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  15 =>
                  array (
                    'filter' => '%robot.spinn3r.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  16 =>
                  array (
                    'filter' => 'livebot-%.search.live.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  17 =>
                  array (
                    'filter' => 'arch%.miss.archive.org',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  18 =>
                  array (
                    'filter' => 'crawl%.exabot.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  19 =>
                  array (
                    'filter' => 'crawler.bloglines.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  20 =>
                  array (
                    'filter' => 'msnbot-%.msn.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  21 =>
                  array (
                    'filter' => 'crawler%.fastsearch.net',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  22 =>
                  array (
                    'filter' => '%.robot.spinn3r.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  23 =>
                  array (
                    'filter' => 'spider%.picsearch.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  24 =>
                  array (
                    'filter' => 'spider%.logika.net',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  25 =>
                  array (
                    'filter' => 'spider%.mail.ru',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  26 =>
                  array (
                    'filter' => 'spider%.proxy.aol.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  27 =>
                  array (
                    'filter' => 'spider.chem.uw.edu.pl',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  28 =>
                  array (
                    'filter' => 'crawler4.irl.cs.tamu.edu',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  29 =>
                  array (
                    'filter' => '%.crawl.baidu.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  30 =>
                  array (
                    'filter' => 'crawl%.searchme.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  31 =>
                  array (
                    'filter' => 'speedyspider.entireweb.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  32 =>
                  array (
                    'filter' => 'robot.acoon.de',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  33 =>
                  array (
                    'filter' => 'crawler%.gingersoftware.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  34 =>
                  array (
                    'filter' => 'crawler%.aurarianetworks.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  35 =>
                  array (
                    'filter' => 'crawler.flatlandindustries.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  36 =>
                  array (
                    'filter' => 'robot%.feeds.yandex.net',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  37 =>
                  array (
                    'filter' => 'ng20.exabot.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  38 =>
                  array (
                    'filter' => 'crawler.kalooga.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  39 =>
                  array (
                    'filter' => '%crawler%.cs.tamu.edu',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  40 =>
                  array (
                    'filter' => 'turbospider.yandex.ru',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  41 =>
                  array (
                    'filter' => 'crawlers.looksmart.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  42 =>
                  array (
                    'filter' => 'red-gw2.exabot.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  43 =>
                  array (
                    'filter' => 'robot%.rambler.ru',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  44 =>
                  array (
                    'filter' => '%crawler%.ig.ntnu.no',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  45 =>
                  array (
                    'filter' => 'crawl%.us.archive.org',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  46 =>
                  array (
                    'filter' => '%dnaspider%.mia.lycos.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  47 =>
                  array (
                    'filter' => '%crawler%.x-echo.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  48 =>
                  array (
                    'filter' => 'robot.szukacz.pl',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  49 =>
                  array (
                    'filter' => 'spider.interseek.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  50 =>
                  array (
                    'filter' => 'spider%.szukaj.onet.pl',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  51 =>
                  array (
                    'filter' => '%spider.entireweb.com',
                    'type' => 'host',
                    'notes' => 'bot',
                  ),
                  52 =>
                  array (
                    'filter' => '137.187.22.',
                    'type' => 'ip',
                    'notes' => 'NIH Crawler',
                  ),
                  53 =>
                  array (
                    'filter' => '128.231.86.',
                    'type' => 'ip',
                    'notes' => 'NIH Crawler',
                  ),
                  54 =>
                  array (
                    'filter' => '128.231.88.',
                    'type' => 'ip',
                    'notes' => 'NIH Crawler',
                  ),
                  55 =>
                  array (
                    'filter' => '128.46.16.17',
                    'type' => 'ip',
                    'notes' => 'rtmp1.hubzero.org',
                  ),
                  56 =>
                  array (
                    'filter' => '128.210.7.18',
                    'type' => 'ip',
                    'notes' => 'Purdue Google Appliance',
                  ),
                  57 =>
                  array (
                    'filter' => '128.46.16.59',
                    'type' => 'ip',
                    'notes' => 'Pascal Meunier\\\'s security crawler',
                  ),
                  58 =>
                  array (
                    'filter' => 'zeus.nj.nec.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  59 =>
                  array (
                    'filter' => 'yandex.ru',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  60 =>
                  array (
                    'filter' => 'yandex.net',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  61 =>
                  array (
                    'filter' => 'yahoo.net',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  62 =>
                  array (
                    'filter' => 'yahoo.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  63 =>
                  array (
                    'filter' => 'xs4.kso.co.uk',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  64 =>
                  array (
                    'filter' => 'worio.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  65 =>
                  array (
                    'filter' => 'whizbang.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  66 =>
                  array (
                    'filter' => 'websquash.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  67 =>
                  array (
                    'filter' => 'websmostlinked.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  68 =>
                  array (
                    'filter' => 'webclipping.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  69 =>
                  array (
                    'filter' => 'webbot.org',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  70 =>
                  array (
                    'filter' => 'turnitin.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  71 =>
                  array (
                    'filter' => 'tracerlock.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  72 =>
                  array (
                    'filter' => 'tpiol.tpiol.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  73 =>
                  array (
                    'filter' => 'tpiol.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  74 =>
                  array (
                    'filter' => 'teoma.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  75 =>
                  array (
                    'filter' => 'searchme.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  76 =>
                  array (
                    'filter' => 'san2.attens.net',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  77 =>
                  array (
                    'filter' => 'sac.overture.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  78 =>
                  array (
                    'filter' => 'robotiker.es',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  79 =>
                  array (
                    'filter' => 'rfcrawler.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  80 =>
                  array (
                    'filter' => 'rcac.purdue.edu',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  81 =>
                  array (
                    'filter' => 'punch.purdue.edu',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  82 =>
                  array (
                    'filter' => 'picsearch.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  83 =>
                  array (
                    'filter' => 'phx.gbl',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  84 =>
                  array (
                    'filter' => 'panchma.tivra.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  85 =>
                  array (
                    'filter' => 'paginasamarillas.es',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  86 =>
                  array (
                    'filter' => 'overture.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  87 =>
                  array (
                    'filter' => 'morgue1.corp.yahoo.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  88 =>
                  array (
                    'filter' => 'metacarta.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  89 =>
                  array (
                    'filter' => 'markwatch.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  90 =>
                  array (
                    'filter' => 'looksmart.net',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  91 =>
                  array (
                    'filter' => 'looksmart.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  92 =>
                  array (
                    'filter' => 'live.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  93 =>
                  array (
                    'filter' => 'live-servers.net',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  94 =>
                  array (
                    'filter' => 'linuxhardcore.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  95 =>
                  array (
                    'filter' => 'jeteye.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  96 =>
                  array (
                    'filter' => 'internetserviceteam.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  97 =>
                  array (
                    'filter' => 'inktomi.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  98 =>
                  array (
                    'filter' => 'idle.eidetica.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  99 =>
                  array (
                    'filter' => 'hanta.yahoo.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  100 =>
                  array (
                    'filter' => 'girafa.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  101 =>
                  array (
                    'filter' => 'gigablast.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  102 =>
                  array (
                    'filter' => 'fastsearch.net',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  103 =>
                  array (
                    'filter' => 'exabot.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  104 =>
                  array (
                    'filter' => 'ev1servers.net',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  105 =>
                  array (
                    'filter' => 'entireweb.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  106 =>
                  array (
                    'filter' => 'cuill.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  107 =>
                  array (
                    'filter' => 'crawler918.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  108 =>
                  array (
                    'filter' => 'crawl8-public.alexa.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  109 =>
                  array (
                    'filter' => 'cosmixcorp.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  110 =>
                  array (
                    'filter' => 'brain.grub.org',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  111 =>
                  array (
                    'filter' => 'bloglines.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  112 =>
                  array (
                    'filter' => 'betaspider.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  113 =>
                  array (
                    'filter' => 'become.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  114 =>
                  array (
                    'filter' => 'authoritativeweb.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  115 =>
                  array (
                    'filter' => 'attens.net',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  116 =>
                  array (
                    'filter' => 'ask.com',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  117 =>
                  array (
                    'filter' => 'archive.org',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  118 =>
                  array (
                    'filter' => '67.108.223.130.ptr.us.xo.net',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  119 =>
                  array (
                    'filter' => '67.106.152.131.ptr.us.xo.net',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  120 =>
                  array (
                    'filter' => '66.237.109.194.ptr.us.xo.net',
                    'type' => 'domain',
                    'notes' => 'bot',
                  ),
                  121 =>
                  array (
                    'filter' => 'task=diskusage',
                    'type' => 'url',
                    'notes' => 'Middleware Disk Quota Checker',
                  ),
                  122 =>
                  array (
                    'filter' => 'gsa-purdue-crawler',
                    'type' => 'useragent',
                    'notes' => 'Purdue GSA Crawler',
                  ),
                );
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('bot_useragents')) {
            try {
                $statsDb->schema()->createTable('bot_useragents')
                    ->string('useragent', 255)
                    ->primaryKey('useragent')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }
    }
}
