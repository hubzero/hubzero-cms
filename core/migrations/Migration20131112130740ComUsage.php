<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;
use Hubzero\Facades\App;

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
        $siteConfig = App::get('config');

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
            $result = $this->db
                ->getQuery(true)
                ->select('params')
                ->from('#__extensions')
                ->where('element', '=', 'com_usage')
                ->value('params');

            $params = (array) json_decode($result);
        } else {
            $result = $this->db
                ->getQuery(true)
                ->select('params')
                ->from('#__plugins')
                ->where('element', '=', 'com_usage')
                ->value('params');

            $params = [];

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

            $this->db
                ->getQuery(true)
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

            $this->db
                ->getQuery(true)
                ->update('#__plugins')
                ->set(['params' => $params])
                ->where('element', '=', 'com_usage')
                ->execute();
        }

        if (!$statsDb->schema()->tableExists('countries')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('countries')
                    ->string('code', 4)->default('')
                    ->string('name', 128)->default('')
                    ->primaryKey('code')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $rows =  [
                    0
                     => [
                         'code' => 'CN',
                         'name' => 'CHINA',
                     ],
                    1
                     => [
                         'code' => 'AU',
                         'name' => 'AUSTRALIA',
                     ],
                    2
                     => [
                         'code' => 'JP',
                         'name' => 'JAPAN',
                     ],
                    3
                     => [
                         'code' => 'TH',
                         'name' => 'THAILAND',
                     ],
                    4
                     => [
                         'code' => 'IN',
                         'name' => 'INDIA',
                     ],
                    5
                     => [
                         'code' => 'MY',
                         'name' => 'MALAYSIA',
                     ],
                    6
                     => [
                         'code' => 'KR',
                         'name' => 'KOREA, REPUBLIC OF',
                     ],
                    7
                     => [
                         'code' => 'HK',
                         'name' => 'HONG KONG',
                     ],
                    8
                     => [
                         'code' => 'TW',
                         'name' => 'TAIWAN',
                     ],
                    9
                     => [
                         'code' => 'PH',
                         'name' => 'PHILIPPINES',
                     ],
                    10
                     => [
                         'code' => 'VN',
                         'name' => 'VIET NAM',
                     ],
                    11
                     => [
                         'code' => 'FR',
                         'name' => 'FRANCE',
                     ],
                    12
                     => [
                         'code' => 'UK',
                         'name' => 'UNITED KINGDOM',
                     ],
                    13
                     => [
                         'code' => 'DE',
                         'name' => 'GERMANY',
                     ],
                    14
                     => [
                         'code' => 'SE',
                         'name' => 'SWEDEN',
                     ],
                    15
                     => [
                         'code' => 'IT',
                         'name' => 'ITALY',
                     ],
                    16
                     => [
                         'code' => 'ES',
                         'name' => 'SPAIN',
                     ],
                    17
                     => [
                         'code' => 'AT',
                         'name' => 'AUSTRIA',
                     ],
                    18
                     => [
                         'code' => 'NL',
                         'name' => 'NETHERLANDS',
                     ],
                    19
                     => [
                         'code' => 'AE',
                         'name' => 'UNITED ARAB EMIRATES',
                     ],
                    20
                     => [
                         'code' => 'IL',
                         'name' => 'ISRAEL',
                     ],
                    21
                     => [
                         'code' => 'UA',
                         'name' => 'UKRAINE',
                     ],
                    22
                     => [
                         'code' => 'CZ',
                         'name' => 'CZECH REPUBLIC',
                     ],
                    23
                     => [
                         'code' => 'RU',
                         'name' => 'RUSSIAN FEDERATION',
                     ],
                    24
                     => [
                         'code' => 'KZ',
                         'name' => 'KAZAKHSTAN',
                     ],
                    25
                     => [
                         'code' => 'PT',
                         'name' => 'PORTUGAL',
                     ],
                    26
                     => [
                         'code' => 'GR',
                         'name' => 'GREECE',
                     ],
                    27
                     => [
                         'code' => 'SA',
                         'name' => 'SAUDI ARABIA',
                     ],
                    28
                     => [
                         'code' => 'DK',
                         'name' => 'DENMARK',
                     ],
                    29
                     => [
                         'code' => 'IR',
                         'name' => 'IRAN, ISLAMIC REPUBLIC OF',
                     ],
                    30
                     => [
                         'code' => 'NO',
                         'name' => 'NORWAY',
                     ],
                    31
                     => [
                         'code' => 'US',
                         'name' => 'UNITED STATES',
                     ],
                    32
                     => [
                         'code' => 'CA',
                         'name' => 'CANADA',
                     ],
                    33
                     => [
                         'code' => 'MX',
                         'name' => 'MEXICO',
                     ],
                    34
                     => [
                         'code' => 'BM',
                         'name' => 'BERMUDA',
                     ],
                    35
                     => [
                         'code' => 'VI',
                         'name' => 'VIRGIN ISLANDS, U.S.',
                     ],
                    36
                     => [
                         'code' => 'PR',
                         'name' => 'PUERTO RICO',
                     ],
                    37
                     => [
                         'code' => 'NZ',
                         'name' => 'NEW ZEALAND',
                     ],
                    38
                     => [
                         'code' => 'SG',
                         'name' => 'SINGAPORE',
                     ],
                    39
                     => [
                         'code' => 'ID',
                         'name' => 'INDONESIA',
                     ],
                    40
                     => [
                         'code' => 'NP',
                         'name' => 'NEPAL',
                     ],
                    41
                     => [
                         'code' => 'PG',
                         'name' => 'PAPUA NEW GUINEA',
                     ],
                    42
                     => [
                         'code' => 'PK',
                         'name' => 'PAKISTAN',
                     ],
                    43
                     => [
                         'code' => 'CH',
                         'name' => 'SWITZERLAND',
                     ],
                    44
                     => [
                         'code' => 'IE',
                         'name' => 'IRELAND',
                     ],
                    45
                     => [
                         'code' => 'BS',
                         'name' => 'BAHAMAS',
                     ],
                    46
                     => [
                         'code' => 'VC',
                         'name' => 'SAINT VINCENT AND THE GRENADINES',
                     ],
                    47
                     => [
                         'code' => 'AR',
                         'name' => 'ARGENTINA',
                     ],
                    48
                     => [
                         'code' => 'UY',
                         'name' => 'URUGUAY',
                     ],
                    49
                     => [
                         'code' => 'DM',
                         'name' => 'DOMINICA',
                     ],
                    50
                     => [
                         'code' => 'BD',
                         'name' => 'BANGLADESH',
                     ],
                    51
                     => [
                         'code' => 'TK',
                         'name' => 'TOKELAU',
                     ],
                    52
                     => [
                         'code' => 'KH',
                         'name' => 'CAMBODIA',
                     ],
                    53
                     => [
                         'code' => 'MO',
                         'name' => 'MACAO',
                     ],
                    54
                     => [
                         'code' => 'MV',
                         'name' => 'MALDIVES',
                     ],
                    55
                     => [
                         'code' => 'AF',
                         'name' => 'AFGHANISTAN',
                     ],
                    56
                     => [
                         'code' => 'NC',
                         'name' => 'NEW CALEDONIA',
                     ],
                    57
                     => [
                         'code' => 'FJ',
                         'name' => 'FIJI',
                     ],
                    58
                     => [
                         'code' => 'MN',
                         'name' => 'MONGOLIA',
                     ],
                    59
                     => [
                         'code' => 'WF',
                         'name' => 'WALLIS AND FUTUNA',
                     ],
                    60
                     => [
                         'code' => 'PL',
                         'name' => 'POLAND',
                     ],
                    61
                     => [
                         'code' => 'RO',
                         'name' => 'ROMANIA',
                     ],
                    62
                     => [
                         'code' => 'TR',
                         'name' => 'TURKEY',
                     ],
                    63
                     => [
                         'code' => 'SK',
                         'name' => 'SLOVAKIA',
                     ],
                    64
                     => [
                         'code' => 'MK',
                         'name' => 'MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF',
                     ],
                    65
                     => [
                         'code' => 'FI',
                         'name' => 'FINLAND',
                     ],
                    66
                     => [
                         'code' => 'AM',
                         'name' => 'ARMENIA',
                     ],
                    67
                     => [
                         'code' => 'SI',
                         'name' => 'SLOVENIA',
                     ],
                    68
                     => [
                         'code' => 'SY',
                         'name' => 'SYRIAN ARAB REPUBLIC',
                     ],
                    69
                     => [
                         'code' => 'LI',
                         'name' => 'LIECHTENSTEIN',
                     ],
                    70
                     => [
                         'code' => 'QA',
                         'name' => 'QATAR',
                     ],
                    71
                     => [
                         'code' => 'BE',
                         'name' => 'BELGIUM',
                     ],
                    72
                     => [
                         'code' => 'NG',
                         'name' => 'NIGERIA',
                     ],
                    73
                     => [
                         'code' => 'BG',
                         'name' => 'BULGARIA',
                     ],
                    74
                     => [
                         'code' => 'IS',
                         'name' => 'ICELAND',
                     ],
                    75
                     => [
                         'code' => 'AL',
                         'name' => 'ALBANIA',
                     ],
                    76
                     => [
                         'code' => 'CY',
                         'name' => 'CYPRUS',
                     ],
                    77
                     => [
                         'code' => 'LU',
                         'name' => 'LUXEMBOURG',
                     ],
                    78
                     => [
                         'code' => 'HU',
                         'name' => 'HUNGARY',
                     ],
                    79
                     => [
                         'code' => 'EE',
                         'name' => 'ESTONIA',
                     ],
                    80
                     => [
                         'code' => 'BY',
                         'name' => 'BELARUS',
                     ],
                    81
                     => [
                         'code' => 'LV',
                         'name' => 'LATVIA',
                     ],
                    82
                     => [
                         'code' => 'IQ',
                         'name' => 'IRAQ',
                     ],
                    83
                     => [
                         'code' => 'KG',
                         'name' => 'KYRGYZSTAN',
                     ],
                    84
                     => [
                         'code' => 'MD',
                         'name' => 'MOLDOVA, REPUBLIC OF',
                     ],
                    85
                     => [
                         'code' => 'YE',
                         'name' => 'YEMEN',
                     ],
                    86
                     => [
                         'code' => 'LT',
                         'name' => 'LITHUANIA',
                     ],
                    87
                     => [
                         'code' => 'HR',
                         'name' => 'CROATIA',
                     ],
                    88
                     => [
                         'code' => 'BA',
                         'name' => 'BOSNIA AND HERZEGOVINA',
                     ],
                    89
                     => [
                         'code' => 'UZ',
                         'name' => 'UZBEKISTAN',
                     ],
                    90
                     => [
                         'code' => 'GE',
                         'name' => 'GEORGIA',
                     ],
                    91
                     => [
                         'code' => 'AZ',
                         'name' => 'AZERBAIJAN',
                     ],
                    92
                     => [
                         'code' => 'JE',
                         'name' => 'JERSEY',
                     ],
                    93
                     => [
                         'code' => 'SM',
                         'name' => 'SAN MARINO',
                     ],
                    94
                     => [
                         'code' => 'BR',
                         'name' => 'BRAZIL',
                     ],
                    95
                     => [
                         'code' => 'SJ',
                         'name' => 'SVALBARD AND JAN MAYEN',
                     ],
                    96
                     => [
                         'code' => 'ZA',
                         'name' => 'SOUTH AFRICA',
                     ],
                    97
                     => [
                         'code' => 'VE',
                         'name' => 'VENEZUELA, BOLIVARIAN REPUBLIC OF',
                     ],
                    98
                     => [
                         'code' => 'CO',
                         'name' => 'COLOMBIA',
                     ],
                    99
                     => [
                         'code' => 'EG',
                         'name' => 'EGYPT',
                     ],
                    100
                     => [
                         'code' => 'CL',
                         'name' => 'CHILE',
                     ],
                    101
                     => [
                         'code' => 'DZ',
                         'name' => 'ALGERIA',
                     ],
                    102
                     => [
                         'code' => 'PE',
                         'name' => 'PERU',
                     ],
                    103
                     => [
                         'code' => 'KW',
                         'name' => 'KUWAIT',
                     ],
                    104
                     => [
                         'code' => 'MA',
                         'name' => 'MOROCCO',
                     ],
                    105
                     => [
                         'code' => 'AO',
                         'name' => 'ANGOLA',
                     ],
                    106
                     => [
                         'code' => 'LY',
                         'name' => 'LIBYAN ARAB JAMAHIRIYA',
                     ],
                    107
                     => [
                         'code' => 'SD',
                         'name' => 'SUDAN',
                     ],
                    108
                     => [
                         'code' => 'EC',
                         'name' => 'ECUADOR',
                     ],
                    109
                     => [
                         'code' => 'OM',
                         'name' => 'OMAN',
                     ],
                    110
                     => [
                         'code' => 'DO',
                         'name' => 'DOMINICAN REPUBLIC',
                     ],
                    111
                     => [
                         'code' => 'LK',
                         'name' => 'SRI LANKA',
                     ],
                    112
                     => [
                         'code' => 'TN',
                         'name' => 'TUNISIA',
                     ],
                    113
                     => [
                         'code' => 'GT',
                         'name' => 'GUATEMALA',
                     ],
                    114
                     => [
                         'code' => 'LB',
                         'name' => 'LEBANON',
                     ],
                    115
                     => [
                         'code' => 'RS',
                         'name' => 'SERBIA',
                     ],
                    116
                     => [
                         'code' => 'MM',
                         'name' => 'MYANMAR',
                     ],
                    117
                     => [
                         'code' => 'CR',
                         'name' => 'COSTA RICA',
                     ],
                    118
                     => [
                         'code' => 'KE',
                         'name' => 'KENYA',
                     ],
                    119
                     => [
                         'code' => 'ET',
                         'name' => 'ETHIOPIA',
                     ],
                    120
                     => [
                         'code' => 'PA',
                         'name' => 'PANAMA',
                     ],
                    121
                     => [
                         'code' => 'JO',
                         'name' => 'JORDAN',
                     ],
                    122
                     => [
                         'code' => 'TZ',
                         'name' => 'TANZANIA, UNITED REPUBLIC OF',
                     ],
                    123
                     => [
                         'code' => 'CI',
                         'name' => 'COTE DIVOIRE',
                     ],
                    124
                     => [
                         'code' => 'CM',
                         'name' => 'CAMEROON',
                     ],
                    125
                     => [
                         'code' => 'SV',
                         'name' => 'EL SALVADOR',
                     ],
                    126
                     => [
                         'code' => 'BH',
                         'name' => 'BAHRAIN',
                     ],
                    127
                     => [
                         'code' => 'TT',
                         'name' => 'TRINIDAD AND TOBAGO',
                     ],
                    128
                     => [
                         'code' => 'BO',
                         'name' => 'BOLIVIA, PLURINATIONAL STATE OF',
                     ],
                    129
                     => [
                         'code' => 'GH',
                         'name' => 'GHANA',
                     ],
                    130
                     => [
                         'code' => 'PY',
                         'name' => 'PARAGUAY',
                     ],
                    131
                     => [
                         'code' => 'UG',
                         'name' => 'UGANDA',
                     ],
                    132
                     => [
                         'code' => 'ZM',
                         'name' => 'ZAMBIA',
                     ],
                    133
                     => [
                         'code' => 'HN',
                         'name' => 'HONDURAS',
                     ],
                    134
                     => [
                         'code' => 'GQ',
                         'name' => 'EQUATORIAL GUINEA',
                     ],
                    135
                     => [
                         'code' => 'JM',
                         'name' => 'JAMAICA',
                     ],
                    136
                     => [
                         'code' => 'AX',
                         'name' => 'ALAND ISLANDS',
                     ],
                    137
                     => [
                         'code' => 'AD',
                         'name' => 'ANDORRA',
                     ],
                    138
                     => [
                         'code' => 'FO',
                         'name' => 'FAROE ISLANDS',
                     ],
                    139
                     => [
                         'code' => 'GI',
                         'name' => 'GIBRALTAR',
                     ],
                    140
                     => [
                         'code' => 'GL',
                         'name' => 'GREENLAND',
                     ],
                    141
                     => [
                         'code' => 'GG',
                         'name' => 'GUERNSEY',
                     ],
                    142
                     => [
                         'code' => 'VA',
                         'name' => 'HOLY SEE (VATICAN CITY STATE',
                     ],
                    143
                     => [
                         'code' => 'IM',
                         'name' => 'ISLE OF MAN',
                     ],
                    144
                     => [
                         'code' => 'MT',
                         'name' => 'MALTA',
                     ],
                    145
                     => [
                         'code' => 'MC',
                         'name' => 'MONACO',
                     ],
                    146
                     => [
                         'code' => 'ME',
                         'name' => 'MONTENEGRO',
                     ],
                    147
                     => [
                         'code' => 'PS',
                         'name' => 'PALESTINIAN TERRITORY, OCCUPIED',
                     ],
                    148
                     => [
                         'code' => 'TJ',
                         'name' => 'TAJIKISTAN',
                     ],
                    149
                     => [
                         'code' => 'TM',
                         'name' => 'TURKMENISTAN',
                     ],
                    150
                     => [
                         'code' => 'CD',
                         'name' => 'CONGO, THE DEMOCRATIC REPUBLIC OF THE',
                     ],
                    151
                     => [
                         'code' => 'AN',
                         'name' => 'NETHERLANDS ANTILLES',
                     ],
                    152
                     => [
                         'code' => 'BZ',
                         'name' => 'BELIZE',
                     ],
                    153
                     => [
                         'code' => 'SN',
                         'name' => 'SENEGAL',
                     ],
                    154
                     => [
                         'code' => 'MG',
                         'name' => 'MADAGASCAR',
                     ],
                    155
                     => [
                         'code' => 'NA',
                         'name' => 'NAMIBIA',
                     ],
                    156
                     => [
                         'code' => 'MW',
                         'name' => 'MALAWI',
                     ],
                    157
                     => [
                         'code' => 'GA',
                         'name' => 'GABON',
                     ],
                    158
                     => [
                         'code' => 'ML',
                         'name' => 'MALI',
                     ],
                    159
                     => [
                         'code' => 'BJ',
                         'name' => 'BENIN',
                     ],
                    160
                     => [
                         'code' => 'TD',
                         'name' => 'CHAD',
                     ],
                    161
                     => [
                         'code' => 'BW',
                         'name' => 'BOTSWANA',
                     ],
                    162
                     => [
                         'code' => 'CV',
                         'name' => 'CAPE VERDE',
                     ],
                    163
                     => [
                         'code' => 'RW',
                         'name' => 'RWANDA',
                     ],
                    164
                     => [
                         'code' => 'CG',
                         'name' => 'CONGO',
                     ],
                    165
                     => [
                         'code' => 'MZ',
                         'name' => 'MOZAMBIQUE',
                     ],
                    166
                     => [
                         'code' => 'GM',
                         'name' => 'GAMBIA',
                     ],
                    167
                     => [
                         'code' => 'LS',
                         'name' => 'LESOTHO',
                     ],
                    168
                     => [
                         'code' => 'MU',
                         'name' => 'MAURITIUS',
                     ],
                    169
                     => [
                         'code' => 'ZW',
                         'name' => 'ZIMBABWE',
                     ],
                    170
                     => [
                         'code' => 'BF',
                         'name' => 'BURKINA FASO',
                     ],
                    171
                     => [
                         'code' => 'SL',
                         'name' => 'SIERRA LEONE',
                     ],
                    172
                     => [
                         'code' => 'SO',
                         'name' => 'SOMALIA',
                     ],
                    173
                     => [
                         'code' => 'NE',
                         'name' => 'NIGER',
                     ],
                    174
                     => [
                         'code' => 'CF',
                         'name' => 'CENTRAL AFRICAN REPUBLIC',
                     ],
                    175
                     => [
                         'code' => 'SZ',
                         'name' => 'SWAZILAND',
                     ],
                    176
                     => [
                         'code' => 'TG',
                         'name' => 'TOGO',
                     ],
                    177
                     => [
                         'code' => 'BI',
                         'name' => 'BURUNDI',
                     ],
                    178
                     => [
                         'code' => 'SC',
                         'name' => 'SEYCHELLES',
                     ],
                    179
                     => [
                         'code' => 'GN',
                         'name' => 'GUINEA',
                     ],
                    180
                     => [
                         'code' => 'GW',
                         'name' => 'GUINEA-BISSAU',
                     ],
                    181
                     => [
                         'code' => 'LR',
                         'name' => 'LIBERIA',
                     ],
                    182
                     => [
                         'code' => 'MR',
                         'name' => 'MAURITANIA',
                     ],
                    183
                     => [
                         'code' => 'DJ',
                         'name' => 'DJIBOUTI',
                     ],
                    184
                     => [
                         'code' => 'RE',
                         'name' => 'REUNION',
                     ],
                    185
                     => [
                         'code' => 'NI',
                         'name' => 'NICARAGUA',
                     ],
                    186
                     => [
                         'code' => 'CU',
                         'name' => 'CUBA',
                     ],
                    187
                     => [
                         'code' => 'KY',
                         'name' => 'CAYMAN ISLANDS',
                     ],
                    188
                     => [
                         'code' => 'VG',
                         'name' => 'VIRGIN ISLANDS, BRITISH',
                     ],
                    189
                     => [
                         'code' => 'MH',
                         'name' => 'MARSHALL ISLANDS',
                     ],
                    190
                     => [
                         'code' => 'AQ',
                         'name' => 'ANTARCTICA',
                     ],
                    191
                     => [
                         'code' => 'BB',
                         'name' => 'BARBADOS',
                     ],
                    192
                     => [
                         'code' => 'AW',
                         'name' => 'ARUBA',
                     ],
                    193
                     => [
                         'code' => 'AI',
                         'name' => 'ANGUILLA',
                     ],
                    194
                     => [
                         'code' => 'KN',
                         'name' => 'SAINT KITTS AND NEVIS',
                     ],
                    195
                     => [
                         'code' => 'GD',
                         'name' => 'GRENADA',
                     ],
                    196
                     => [
                         'code' => 'LC',
                         'name' => 'SAINT LUCIA',
                     ],
                    197
                     => [
                         'code' => 'MS',
                         'name' => 'MONTSERRAT',
                     ],
                    198
                     => [
                         'code' => 'TC',
                         'name' => 'TURKS AND CAICOS ISLANDS',
                     ],
                    199
                     => [
                         'code' => 'AG',
                         'name' => 'ANTIGUA AND BARBUDA',
                     ],
                    200
                     => [
                         'code' => 'TV',
                         'name' => 'TUVALU',
                     ],
                    201
                     => [
                         'code' => 'PF',
                         'name' => 'FRENCH POLYNESIA',
                     ],
                    202
                     => [
                         'code' => 'SB',
                         'name' => 'SOLOMON ISLANDS',
                     ],
                    203
                     => [
                         'code' => 'VU',
                         'name' => 'VANUATU',
                     ],
                    204
                     => [
                         'code' => 'ER',
                         'name' => 'ERITREA',
                     ],
                    205
                     => [
                         'code' => 'HT',
                         'name' => 'HAITI',
                     ],
                    206
                     => [
                         'code' => 'SH',
                         'name' => 'SAINT HELENA, ASCENSION AND TRISTAN DA CUNHA',
                     ],
                    207
                     => [
                         'code' => 'FM',
                         'name' => 'MICRONESIA, FEDERATED STATES OF',
                     ],
                    208
                     => [
                         'code' => 'EH',
                         'name' => 'WESTERN SAHARA',
                     ],
                    209
                     => [
                         'code' => 'CX',
                         'name' => 'CHRISTMAS ISLAND',
                     ],
                    210
                     => [
                         'code' => 'LA',
                         'name' => 'LAO PEOPLES DEMOCRATIC REPUBLIC',
                     ],
                    211
                     => [
                         'code' => 'IO',
                         'name' => 'BRITISH INDIAN OCEAN TERRITORY',
                     ],
                    212
                     => [
                         'code' => 'GU',
                         'name' => 'GUAM',
                     ],
                    213
                     => [
                         'code' => 'WS',
                         'name' => 'SAMOA',
                     ],
                    214
                     => [
                         'code' => 'SR',
                         'name' => 'SURINAME',
                     ],
                    215
                     => [
                         'code' => 'CK',
                         'name' => 'COOK ISLANDS',
                     ],
                    216
                     => [
                         'code' => 'KI',
                         'name' => 'KIRIBATI',
                     ],
                    217
                     => [
                         'code' => 'NU',
                         'name' => 'NIUE',
                     ],
                    218
                     => [
                         'code' => 'TO',
                         'name' => 'TONGA',
                     ],
                    219
                     => [
                         'code' => 'TF',
                         'name' => 'FRENCH SOUTHERN TERRITORIES',
                     ],
                    220
                     => [
                         'code' => 'MQ',
                         'name' => 'MARTINIQUE',
                     ],
                    221
                     => [
                         'code' => 'YT',
                         'name' => 'MAYOTTE',
                     ],
                    222
                     => [
                         'code' => 'NF',
                         'name' => 'NORFOLK ISLAND',
                     ],
                    223
                     => [
                         'code' => 'AS',
                         'name' => 'AMERICAN SAMOA',
                     ],
                    224
                     => [
                         'code' => 'BN',
                         'name' => 'BRUNEI DARUSSALAM',
                     ],
                    225
                     => [
                         'code' => 'BT',
                         'name' => 'BHUTAN',
                     ],
                    226
                     => [
                         'code' => 'BV',
                         'name' => 'BOUVET ISLAND',
                     ],
                    227
                     => [
                         'code' => 'CC',
                         'name' => 'COCOS (KEELING',
                     ],
                    228
                     => [
                         'code' => 'FK',
                         'name' => 'FALKLAND ISLANDS (MALVINAS',
                     ],
                    229
                     => [
                         'code' => 'GF',
                         'name' => 'FRENCH GUIANA',
                     ],
                    230
                     => [
                         'code' => 'GP',
                         'name' => 'GUADELOUPE',
                     ],
                    231
                     => [
                         'code' => 'GS',
                         'name' => 'SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS',
                     ],
                    232
                     => [
                         'code' => 'GY',
                         'name' => 'GUYANA',
                     ],
                    233
                     => [
                         'code' => 'HM',
                         'name' => 'HEARD ISLAND AND MCDONALD ISLANDS',
                     ],
                    234
                     => [
                         'code' => 'MP',
                         'name' => 'NORTHERN MARIANA ISLANDS',
                     ],
                    235
                     => [
                         'code' => 'PW',
                         'name' => 'PALAU',
                     ],
                    236
                     => [
                         'code' => 'UM',
                         'name' => 'UNITED STATES MINOR OUTLYING ISLANDS',
                     ],
                    237
                     => [
                         'code' => 'KP',
                         'name' => 'KOREA, DEMOCRATIC PEOPLES REPUBLIC OF',
                     ],
                    238
                     => [
                         'code' => 'NR',
                         'name' => 'NAURU',
                     ],
                    239
                     => [
                         'code' => 'PM',
                         'name' => 'SAINT PIERRE AND MIQUELON',
                     ],
                    240
                     => [
                         'code' => 'MF',
                         'name' => 'SAINT MARTIN',
                     ],
                    241
                     => [
                         'code' => 'KM',
                         'name' => 'COMOROS',
                     ],
                    242
                     => [
                         'code' => 'TL',
                         'name' => 'TIMOR-LESTE',
                     ],
                    243
                     => [
                         'code' => 'ST',
                         'name' => 'SAO TOME AND PRINCIPE',
                     ],
                ];
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('sessionlog_metrics')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('sessionlog_metrics')
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
                $statsDb
                    ->schema()
                    ->createTable('#__xprofiles_metrics')
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
                $statsDb
                    ->schema()
                    ->createTable('summary_andmore_vals')
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
                $statsDb
                    ->schema()
                    ->createTable('summary_misc_vals')
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
                $statsDb
                    ->schema()
                    ->createTable('summary_simusage_vals')
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
                $statsDb
                    ->schema()
                    ->createTable('toolstart')
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
                $statsDb
                    ->schema()
                    ->createTable('userlogin')
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
                $statsDb
                    ->schema()
                    ->createTable('web')
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
                $statsDb
                    ->schema()
                    ->createTable('webhits')
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
                $statsDb
                    ->schema()
                    ->createTable('websessions')
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
                $statsDb
                    ->schema()
                    ->createTable('domainclass')
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

                $rows =  [
                    0
                     => [
                         'domain' => 'cnea.gov.ar',
                         'class' => '3',
                         'country' => 'ar',
                         'state' => '',
                         'name' => 'Comision Nacional de la Energia Atomica',
                     ],
                    1
                     => [
                         'domain' => 'surfer.at',
                         'class' => '4',
                         'country' => 'at',
                         'state' => '',
                         'name' => 'Telekabel Wien GmbH, Vienna',
                     ],
                    2
                     => [
                         'domain' => 'teleweb.at',
                         'class' => '4',
                         'country' => 'at',
                         'state' => '',
                         'name' => 'Telekabel Wien GmbH, Wien',
                     ],
                    3
                     => [
                         'domain' => 'univie.ac.at',
                         'class' => '1',
                         'country' => 'at',
                         'state' => '',
                         'name' => 'Universitaet Wien Zentraler Informatikdienst',
                     ],
                    4
                     => [
                         'domain' => 'tuwien.ac.at',
                         'class' => '1',
                         'country' => 'at',
                         'state' => '',
                         'name' => 'Vienna University of Technology',
                     ],
                    5
                     => [
                         'domain' => 'ecu.edu.au',
                         'class' => '1',
                         'country' => 'au',
                         'state' => '',
                         'name' => 'Edith Cowan University, Perth',
                     ],
                    6
                     => [
                         'domain' => 'tu-graz.ac.at',
                         'class' => '1',
                         'country' => 'au',
                         'state' => '',
                         'name' => 'Graz University of Technology, Graz',
                     ],
                    7
                     => [
                         'domain' => 'mq.edu.au',
                         'class' => '1',
                         'country' => 'au',
                         'state' => '',
                         'name' => 'Macquarie University, Sydney',
                     ],
                    8
                     => [
                         'domain' => 'optusnet.com.au',
                         'class' => '4',
                         'country' => 'au',
                         'state' => '',
                         'name' => 'Optus Administration Pty Ltd',
                     ],
                    9
                     => [
                         'domain' => 'ozemail.com.au',
                         'class' => '4',
                         'country' => 'au',
                         'state' => '',
                         'name' => 'OzEmail Pty Ltd',
                     ],
                    10
                     => [
                         'domain' => 'rmit.edu.au',
                         'class' => '1',
                         'country' => 'au',
                         'state' => '',
                         'name' => 'RMIT University, Melbourne',
                     ],
                    11
                     => [
                         'domain' => 'tpgi.com.au',
                         'class' => '4',
                         'country' => 'au',
                         'state' => '',
                         'name' => 'TPG Internet',
                     ],
                    12
                     => [
                         'domain' => 'telstra.com.au',
                         'class' => '4',
                         'country' => 'au',
                         'state' => '',
                         'name' => 'Telecom Australia (Telstra',
                     ],
                    13
                     => [
                         'domain' => 'newcastle.edu.au',
                         'class' => '1',
                         'country' => 'au',
                         'state' => '',
                         'name' => 'University of Newcastle',
                     ],
                    14
                     => [
                         'domain' => 'usyd.edu.au',
                         'class' => '1',
                         'country' => 'au',
                         'state' => '',
                         'name' => 'University of Sydney',
                     ],
                    15
                     => [
                         'domain' => 'azerin.com',
                         'class' => '0',
                         'country' => 'az',
                         'state' => '',
                         'name' => 'AZERIN AZERBAYCAN-TURKIYE BILGI MUESSESESI, Baku',
                     ],
                    16
                     => [
                         'domain' => 'imec.be',
                         'class' => '0',
                         'country' => 'be',
                         'state' => '',
                         'name' => 'BELNET, Brussels',
                     ],
                    17
                     => [
                         'domain' => 'skynet.be',
                         'class' => '4',
                         'country' => 'be',
                         'state' => '',
                         'name' => 'Belgacom sa/nv, Brussels',
                     ],
                    18
                     => [
                         'domain' => 'coditel.net',
                         'class' => '0',
                         'country' => 'be',
                         'state' => '',
                         'name' => 'CODITEL, Brussels',
                     ],
                    19
                     => [
                         'domain' => 'globaltt.com',
                         'class' => '4',
                         'country' => 'be',
                         'state' => '',
                         'name' => 'Global Telephone & Telecommunication S. A.',
                     ],
                    20
                     => [
                         'domain' => 'kuleuven.ac.be',
                         'class' => '1',
                         'country' => 'be',
                         'state' => '',
                         'name' => 'Katholieke Universiteit Leuven',
                     ],
                    21
                     => [
                         'domain' => 'tele2.be',
                         'class' => '4',
                         'country' => 'be',
                         'state' => '',
                         'name' => 'Tele2 Belgium S.A.',
                     ],
                    22
                     => [
                         'domain' => 'ucl.ac.be',
                         'class' => '1',
                         'country' => 'be',
                         'state' => '',
                         'name' => 'Universite Catholique de Louvain',
                     ],
                    23
                     => [
                         'domain' => 'brasiltelecom.net.br',
                         'class' => '4',
                         'country' => 'br',
                         'state' => '',
                         'name' => 'Brasil Telecom S. A.',
                     ],
                    24
                     => [
                         'domain' => 'virtua.com.br',
                         'class' => '4',
                         'country' => 'br',
                         'state' => '',
                         'name' => 'NET Servios de Communicao S.A.',
                     ],
                    25
                     => [
                         'domain' => 'puc-rio.br',
                         'class' => '1',
                         'country' => 'br',
                         'state' => '',
                         'name' => 'PONTIFICIA UNIVERSIDADE CATOLICA DO RIO DE JANEIRO',
                     ],
                    26
                     => [
                         'domain' => 'ajato.com.br',
                         'class' => '0',
                         'country' => 'br',
                         'state' => '',
                         'name' => 'Rede Ajato Ltda',
                     ],
                    27
                     => [
                         'domain' => 'telesp.net.br',
                         'class' => '4',
                         'country' => 'br',
                         'state' => '',
                         'name' => 'TELECOMUNICACOES DE SAO PAULO S.A.',
                     ],
                    28
                     => [
                         'domain' => 'tdatabrasil.net.br',
                         'class' => '4',
                         'country' => 'br',
                         'state' => '',
                         'name' => 'TELEFONICA EMPRESAS S/A, Sao Paulo',
                     ],
                    29
                     => [
                         'domain' => 'veloxzone.com.br',
                         'class' => '4',
                         'country' => 'br',
                         'state' => '',
                         'name' => 'Telemar Norte Leste S.A., Rio de Janeiro',
                     ],
                    30
                     => [
                         'domain' => 'unicamp.br',
                         'class' => '1',
                         'country' => 'br',
                         'state' => '',
                         'name' => 'UNIVERSIDADE ESTADUAL DE CAMPINAS',
                     ],
                    31
                     => [
                         'domain' => 'ufc.br',
                         'class' => '1',
                         'country' => 'br',
                         'state' => '',
                         'name' => 'Universidade Federal do Ceara',
                     ],
                    32
                     => [
                         'domain' => 'tche.br',
                         'class' => '1',
                         'country' => 'br',
                         'state' => '',
                         'name' => 'Universidade Federal do Rio Grande do Sul',
                     ],
                    33
                     => [
                         'domain' => 'usp.br',
                         'class' => '1',
                         'country' => 'br',
                         'state' => '',
                         'name' => 'University de Sao Paulo',
                     ],
                    34
                     => [
                         'domain' => 'shawcable.net',
                         'class' => '4',
                         'country' => 'ca',
                         'state' => 'ab',
                         'name' => 'Shaw Cablesystems G.P.',
                     ],
                    35
                     => [
                         'domain' => 'telus.net',
                         'class' => '4',
                         'country' => 'ca',
                         'state' => 'ab',
                         'name' => 'Telus Communications Inc.',
                     ],
                    36
                     => [
                         'domain' => 'ualberta.ca',
                         'class' => '1',
                         'country' => 'ca',
                         'state' => 'ab',
                         'name' => 'University of Alberta',
                     ],
                    37
                     => [
                         'domain' => 'ubc.ca',
                         'class' => '1',
                         'country' => 'ca',
                         'state' => 'bc',
                         'name' => 'University of British Columbia',
                     ],
                    38
                     => [
                         'domain' => 'dal.ca',
                         'class' => '1',
                         'country' => 'ca',
                         'state' => 'ns',
                         'name' => 'Dalhousie University, Halifax',
                     ],
                    39
                     => [
                         'domain' => 'carleton.ca',
                         'class' => '1',
                         'country' => 'ca',
                         'state' => 'on',
                         'name' => 'Carleton University, Ottawa',
                     ],
                    40
                     => [
                         'domain' => 'iasl.com',
                         'class' => '4',
                         'country' => 'ca',
                         'state' => 'on',
                         'name' => 'Chunghwa Telecom Co., Ltd.',
                     ],
                    41
                     => [
                         'domain' => 'iplink.net',
                         'class' => '4',
                         'country' => 'ca',
                         'state' => 'on',
                         'name' => 'Interlink',
                     ],
                    42
                     => [
                         'domain' => 'mcmaster.ca',
                         'class' => '1',
                         'country' => 'ca',
                         'state' => 'on',
                         'name' => 'McMaster University, Hamilton',
                     ],
                    43
                     => [
                         'domain' => 'nrc.ca',
                         'class' => '3',
                         'country' => 'ca',
                         'state' => 'on',
                         'name' => 'National Research Council Canada, Ottawa',
                     ],
                    44
                     => [
                         'domain' => 'rogers.com',
                         'class' => '4',
                         'country' => 'ca',
                         'state' => 'on',
                         'name' => 'Rogers Communications Inc.',
                     ],
                    45
                     => [
                         'domain' => 'utoronto.ca',
                         'class' => '1',
                         'country' => 'ca',
                         'state' => 'on',
                         'name' => 'University of Toronto',
                     ],
                    46
                     => [
                         'domain' => 'uwindsor.ca',
                         'class' => '1',
                         'country' => 'ca',
                         'state' => 'on',
                         'name' => 'University of Windsor',
                     ],
                    47
                     => [
                         'domain' => 'sympatico.ca',
                         'class' => '4',
                         'country' => 'ca',
                         'state' => 'qc',
                         'name' => 'Bell Canada',
                     ],
                    48
                     => [
                         'domain' => 'concordia.ca',
                         'class' => '1',
                         'country' => 'ca',
                         'state' => 'qc',
                         'name' => 'Concordia University, Montreal',
                     ],
                    49
                     => [
                         'domain' => 'polymtl.ca',
                         'class' => '1',
                         'country' => 'ca',
                         'state' => 'qc',
                         'name' => 'Ecole Polytechnique de Montreal',
                     ],
                    50
                     => [
                         'domain' => 'ericsson.ca',
                         'class' => '2',
                         'country' => 'ca',
                         'state' => 'qc',
                         'name' => 'Ericsson Canada Inc.',
                     ],
                    51
                     => [
                         'domain' => 'mcgill.ca',
                         'class' => '1',
                         'country' => 'ca',
                         'state' => 'qc',
                         'name' => 'McGill University, Montreal',
                     ],
                    52
                     => [
                         'domain' => 'ulaval.ca',
                         'class' => '1',
                         'country' => 'ca',
                         'state' => 'qc',
                         'name' => 'Universite Laval, Quebec',
                     ],
                    53
                     => [
                         'domain' => 'usherb.ca',
                         'class' => '1',
                         'country' => 'ca',
                         'state' => 'qc',
                         'name' => 'Universite de Sherbrooke',
                     ],
                    54
                     => [
                         'domain' => 'uqtr.ca',
                         'class' => '1',
                         'country' => 'ca',
                         'state' => 'qc',
                         'name' => 'University du Quebec la Trois-Rivieres',
                     ],
                    55
                     => [
                         'domain' => 'videotron.ca',
                         'class' => '4',
                         'country' => 'ca',
                         'state' => 'qc',
                         'name' => 'Videotron Ltd., Montreal',
                     ],
                    56
                     => [
                         'domain' => 'sasknet.sk.ca',
                         'class' => '4',
                         'country' => 'ca',
                         'state' => 'sk',
                         'name' => 'SaskTel corp., Regina',
                     ],
                    57
                     => [
                         'domain' => 'hispeed.ch',
                         'class' => '4',
                         'country' => 'ch',
                         'state' => '',
                         'name' => 'Cablecom GmbH',
                     ],
                    58
                     => [
                         'domain' => 'csfb.com',
                         'class' => '2',
                         'country' => 'ch',
                         'state' => '',
                         'name' => 'Credit Suisse Group',
                     ],
                    59
                     => [
                         'domain' => 'ethz.ch',
                         'class' => '1',
                         'country' => 'ch',
                         'state' => '',
                         'name' => 'Eidgenossische Technische Hochschule Zurich',
                     ],
                    60
                     => [
                         'domain' => 'psi.ch',
                         'class' => '1',
                         'country' => 'ch',
                         'state' => '',
                         'name' => 'Paul Scherrer Institute',
                     ],
                    61
                     => [
                         'domain' => 'epfl.ch',
                         'class' => '2',
                         'country' => 'ch',
                         'state' => '',
                         'name' => 'Swiss Federal Institute of Technology',
                     ],
                    62
                     => [
                         'domain' => 'unige.ch',
                         'class' => '1',
                         'country' => 'ch',
                         'state' => '',
                         'name' => 'University de Geneve',
                     ],
                    63
                     => [
                         'domain' => 'unibas.ch',
                         'class' => '1',
                         'country' => 'ch',
                         'state' => '',
                         'name' => 'University of Basel',
                     ],
                    64
                     => [
                         'domain' => 'bta.net.cn',
                         'class' => '4',
                         'country' => 'cn',
                         'state' => '',
                         'name' => 'Bajing Telecom',
                     ],
                    65
                     => [
                         'domain' => 'tijmu.edu.cn',
                         'class' => '1',
                         'country' => 'cn',
                         'state' => '',
                         'name' => 'Tianjin Medical University',
                     ],
                    66
                     => [
                         'domain' => 'tsinghua.edu.cn',
                         'class' => '1',
                         'country' => 'cn',
                         'state' => '',
                         'name' => 'Tsinghua University',
                     ],
                    67
                     => [
                         'domain' => 'ysu.edu.cn',
                         'class' => '1',
                         'country' => 'cn',
                         'state' => '',
                         'name' => 'Yanshan University',
                     ],
                    68
                     => [
                         'domain' => 'jiitindia.org',
                         'class' => '0',
                         'country' => 'cn',
                         'state' => '',
                         'name' => 'no organization name',
                     ],
                    69
                     => [
                         'domain' => 'sh.cn',
                         'class' => '4',
                         'country' => 'cn',
                         'state' => '',
                         'name' => 'unknwon china ISP',
                     ],
                    70
                     => [
                         'domain' => 'uniweb.net.co',
                         'class' => '4',
                         'country' => 'co',
                         'state' => '',
                         'name' => 'UNIWEB',
                     ],
                    71
                     => [
                         'domain' => 'unal.edu.co',
                         'class' => '1',
                         'country' => 'co',
                         'state' => '',
                         'name' => 'Universidad Nacional de Colombia, Bogata',
                     ],
                    72
                     => [
                         'domain' => 'uniandes.edu.co',
                         'class' => '1',
                         'country' => 'co',
                         'state' => '',
                         'name' => 'Universidad de los Andes, Bogata',
                     ],
                    73
                     => [
                         'domain' => 'ucy.ac.cy',
                         'class' => '1',
                         'country' => 'cy',
                         'state' => '',
                         'name' => 'University of Cyprus',
                     ],
                    74
                     => [
                         'domain' => 'fh-giessen.de',
                         'class' => '1',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'FH Giessen-Friedberg University of Applied Sciences',
                     ],
                    75
                     => [
                         'domain' => 'kfa-juelich.de',
                         'class' => '2',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Forschungszentrum Julich in der Helmholtz',
                     ],
                    76
                     => [
                         'domain' => 'fhg.de',
                         'class' => '2',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Fraunhofer-Gesellschaft',
                     ],
                    77
                     => [
                         'domain' => 'gatel.net',
                         'class' => '4',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Global Access Telecommunications, Inc.',
                     ],
                    78
                     => [
                         'domain' => 'hu-berlin.de',
                         'class' => '1',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Humboldt-Universitat zu Berlin',
                     ],
                    79
                     => [
                         'domain' => 'teleport-iabg.de',
                         'class' => '4',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'IABG-Infocom-Teleport',
                     ],
                    80
                     => [
                         'domain' => 'lrz-muenchen.de',
                         'class' => '2',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Leibniz-Rechenzentrum Munchen',
                     ],
                    81
                     => [
                         'domain' => 'arcor-ip.net',
                         'class' => '0',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Mannesmann Arcor AG & Co, Eschborn',
                     ],
                    82
                     => [
                         'domain' => 'mpg.de',
                         'class' => '1',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Max Plank Institute',
                     ],
                    83
                     => [
                         'domain' => 'mpi-stuttgart.mpg.de',
                         'class' => '1',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Max Plank Institute Stuttgart',
                     ],
                    84
                     => [
                         'domain' => 'mpis.mpg.de',
                         'class' => '1',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Max Plank Institute Stuttgart',
                     ],
                    85
                     => [
                         'domain' => 'mpipks-dresden.mpg.de',
                         'class' => '1',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Max Plank Institute fur Physik Komplexer Systeme',
                     ],
                    86
                     => [
                         'domain' => 'aktivanet.de',
                         'class' => '4',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Preiswerter und Leistungsfaehig',
                     ],
                    87
                     => [
                         'domain' => 'rwth-aachen.de',
                         'class' => '1',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'RWTH-Aachen University',
                     ],
                    88
                     => [
                         'domain' => 'sbs.de',
                         'class' => '2',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Siemens',
                     ],
                    89
                     => [
                         'domain' => 't-dialin.net',
                         'class' => '4',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'T-Online International AG',
                     ],
                    90
                     => [
                         'domain' => 'tu-bs.de',
                         'class' => '1',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Technische Universitat Braunschweig',
                     ],
                    91
                     => [
                         'domain' => 'tu-chemnitz.de',
                         'class' => '1',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Technische Universitat Chemnitz',
                     ],
                    92
                     => [
                         'domain' => 'tu-darmstadt.de',
                         'class' => '1',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Technische Universitat Darmstadt',
                     ],
                    93
                     => [
                         'domain' => 'tu-ilmenau.de',
                         'class' => '1',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Technische Universitat Ilmenau',
                     ],
                    94
                     => [
                         'domain' => 'tu-muenchen.de',
                         'class' => '1',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Technische Universitat Muenchen',
                     ],
                    95
                     => [
                         'domain' => 'tiscali.de',
                         'class' => '4',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Tiscali SpA',
                     ],
                    96
                     => [
                         'domain' => 'uni-duisburg.de',
                         'class' => '1',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Universitat Duisburg-Essen',
                     ],
                    97
                     => [
                         'domain' => 'uni-essen.de',
                         'class' => '1',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Universitat Duisburg-Essen',
                     ],
                    98
                     => [
                         'domain' => 'unibw-hamburg.de',
                         'class' => '1',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Universitat Duisburg-Essen',
                     ],
                    99
                     => [
                         'domain' => 'uni-erlangen.de',
                         'class' => '1',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Universitat Erlangen-Nur',
                     ],
                    100
                     => [
                         'domain' => 'uni-hamburg.de',
                         'class' => '1',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Universitat Hamburg',
                     ],
                    101
                     => [
                         'domain' => 'uni-hannover.de',
                         'class' => '1',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Universitat Hannover-Startseite',
                     ],
                    102
                     => [
                         'domain' => 'uni-jena.de',
                         'class' => '1',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Universitat Jena',
                     ],
                    103
                     => [
                         'domain' => 'uni-konstanz.de',
                         'class' => '1',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Universitat Konstanz',
                     ],
                    104
                     => [
                         'domain' => 'uni-leipzig.de',
                         'class' => '1',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Universitat Leipzig',
                     ],
                    105
                     => [
                         'domain' => 'uni-paderborn.de',
                         'class' => '1',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Universitat Paderborn',
                     ],
                    106
                     => [
                         'domain' => 'uni-wuerzburg.de',
                         'class' => '1',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'Universitat Wurzburg',
                     ],
                    107
                     => [
                         'domain' => 'mcbone.net',
                         'class' => '4',
                         'country' => 'de',
                         'state' => '',
                         'name' => 'freenet Cityline GmbH',
                     ],
                    108
                     => [
                         'domain' => 't-ipconnect.de',
                         'class' => '4',
                         'country' => 'de',
                         'state' => '',
                         'name' => 't-ipconnect.de',
                     ],
                    109
                     => [
                         'domain' => 'dtu.dk',
                         'class' => '1',
                         'country' => 'dk',
                         'state' => '',
                         'name' => 'Danmarks Tekniske Universitet, Kongens Lnngby',
                     ],
                    110
                     => [
                         'domain' => 'tele.dk',
                         'class' => '4',
                         'country' => 'dk',
                         'state' => '',
                         'name' => 'Tele Danmark',
                     ],
                    111
                     => [
                         'domain' => 'starman.ee',
                         'class' => '4',
                         'country' => 'ee',
                         'state' => '',
                         'name' => 'Starman Kaabeltelevisiooni AS',
                     ],
                    112
                     => [
                         'domain' => 'ttu.ee',
                         'class' => '1',
                         'country' => 'ee',
                         'state' => '',
                         'name' => 'Tallinn University of Technology',
                     ],
                    113
                     => [
                         'domain' => 'link.net',
                         'class' => '4',
                         'country' => 'eg',
                         'state' => '',
                         'name' => 'Link Egypt',
                     ],
                    114
                     => [
                         'domain' => 'tedata.net',
                         'class' => '0',
                         'country' => 'eg',
                         'state' => '',
                         'name' => 'TE Data',
                     ],
                    115
                     => [
                         'domain' => 'auna.net',
                         'class' => '0',
                         'country' => 'es',
                         'state' => '',
                         'name' => 'Auna Telecomunicaciones, S.A., Barcelona',
                     ],
                    116
                     => [
                         'domain' => 'csic.es',
                         'class' => '2',
                         'country' => 'es',
                         'state' => '',
                         'name' => 'Consejo Superior de Investigaciones Cientificas, Madrid',
                     ],
                    117
                     => [
                         'domain' => 'intervip.com.br',
                         'class' => '0',
                         'country' => 'es',
                         'state' => '',
                         'name' => 'INTERVIP INFORMATICA LTDA',
                     ],
                    118
                     => [
                         'domain' => 'jazztel.es',
                         'class' => '4',
                         'country' => 'es',
                         'state' => '',
                         'name' => 'Jazztel, Madrid',
                     ],
                    119
                     => [
                         'domain' => 'rima-tde.net',
                         'class' => '4',
                         'country' => 'es',
                         'state' => '',
                         'name' => 'TELEFONICA, S.A., Madrid',
                     ],
                    120
                     => [
                         'domain' => 'tecnun.es',
                         'class' => '1',
                         'country' => 'es',
                         'state' => '',
                         'name' => 'Technologico de la Universidad de Navarra',
                     ],
                    121
                     => [
                         'domain' => 'upm.es',
                         'class' => '1',
                         'country' => 'es',
                         'state' => '',
                         'name' => 'Universidad Politecnica de Madrid',
                     ],
                    122
                     => [
                         'domain' => 'usal.es',
                         'class' => '1',
                         'country' => 'es',
                         'state' => '',
                         'name' => 'Universidad Politecnica de Salamanca',
                     ],
                    123
                     => [
                         'domain' => 'urv.es',
                         'class' => '1',
                         'country' => 'es',
                         'state' => '',
                         'name' => 'Universidad Politecnica de Tarragona',
                     ],
                    124
                     => [
                         'domain' => 'upv.es',
                         'class' => '1',
                         'country' => 'es',
                         'state' => '',
                         'name' => 'Universidad Politecnica de Valencia',
                     ],
                    125
                     => [
                         'domain' => 'ugr.es',
                         'class' => '1',
                         'country' => 'es',
                         'state' => '',
                         'name' => 'Universidad de Granada',
                     ],
                    126
                     => [
                         'domain' => 'uab.es',
                         'class' => '1',
                         'country' => 'es',
                         'state' => '',
                         'name' => 'Universitat Autonoma de Barcelona',
                     ],
                    127
                     => [
                         'domain' => 'uam.es',
                         'class' => '1',
                         'country' => 'es',
                         'state' => '',
                         'name' => 'Universitat Autonoma de Madrid',
                     ],
                    128
                     => [
                         'domain' => 'jyu.fi',
                         'class' => '1',
                         'country' => 'fi',
                         'state' => '',
                         'name' => 'University of Jyvaskylan',
                     ],
                    129
                     => [
                         'domain' => 'suomi.net',
                         'class' => '4',
                         'country' => 'fi',
                         'state' => '',
                         'name' => 'oulu telephone company',
                     ],
                    130
                     => [
                         'domain' => 'noos.net',
                         'class' => '0',
                         'country' => 'fr',
                         'state' => '',
                         'name' => 'Auxipar, Paris',
                     ],
                    131
                     => [
                         'domain' => 'ceram.fr',
                         'class' => '1',
                         'country' => 'fr',
                         'state' => '',
                         'name' => 'CERAM Sophia Anitpolis European School of Business',
                     ],
                    132
                     => [
                         'domain' => 'univ-lyon1.fr',
                         'class' => '1',
                         'country' => 'fr',
                         'state' => '',
                         'name' => 'Centre Informatique Scientifique et Medical de l\\\'Universite Claude Bernard Lyon 1',
                     ],
                    133
                     => [
                         'domain' => 'cea.fr',
                         'class' => '3',
                         'country' => 'fr',
                         'state' => '',
                         'name' => 'Commissariat a L\\\'Energie Atomique',
                     ],
                    134
                     => [
                         'domain' => 'ec-lyon.fr',
                         'class' => '1',
                         'country' => 'fr',
                         'state' => '',
                         'name' => 'Ecole Centrale de Lyon',
                     ],
                    135
                     => [
                         'domain' => 'enserg.fr',
                         'class' => '2',
                         'country' => 'fr',
                         'state' => '',
                         'name' => 'Ecole Nationale Superieure d\\\'Electricite et de Radioelectricite de Grenoble',
                     ],
                    136
                     => [
                         'domain' => 'ensta.fr',
                         'class' => '2',
                         'country' => 'fr',
                         'state' => '',
                         'name' => 'Ecole Nationale Superieure des Techniques Avancees',
                     ],
                    137
                     => [
                         'domain' => 'exabot.com',
                         'class' => '5',
                         'country' => 'fr',
                         'state' => '',
                         'name' => 'Exalead S.A., Paris',
                     ],
                    138
                     => [
                         'domain' => 'dir.com',
                         'class' => '0',
                         'country' => 'fr',
                         'state' => '',
                         'name' => 'FERMIC SA',
                     ],
                    139
                     => [
                         'domain' => 'insa-lyon.fr',
                         'class' => '1',
                         'country' => 'fr',
                         'state' => '',
                         'name' => 'Institut National des Sciences Appliquees de Lyon',
                     ],
                    140
                     => [
                         'domain' => 'insa-toulouse.fr',
                         'class' => '1',
                         'country' => 'fr',
                         'state' => '',
                         'name' => 'Institut National des Sciences Appliquuees de Toulouse',
                     ],
                    141
                     => [
                         'domain' => 'inist.fr',
                         'class' => '3',
                         'country' => 'fr',
                         'state' => '',
                         'name' => 'Institut de l\\\'Information Scientifique et Technique',
                     ],
                    142
                     => [
                         'domain' => 'kaptech.net',
                         'class' => '0',
                         'country' => 'fr',
                         'state' => '',
                         'name' => 'KAPTECH',
                     ],
                    143
                     => [
                         'domain' => 'laas.fr',
                         'class' => '2',
                         'country' => 'fr',
                         'state' => '',
                         'name' => 'Laboratoire d\\\'Automatique et d\\\'Analyse des Systemes du CNRS',
                     ],
                    144
                     => [
                         'domain' => 'noos.fr',
                         'class' => '4',
                         'country' => 'fr',
                         'state' => '',
                         'name' => 'NOOS (Lyonnaise Communications',
                     ],
                    145
                     => [
                         'domain' => 'nerim.net',
                         'class' => '4',
                         'country' => 'fr',
                         'state' => '',
                         'name' => 'Nerim Networks, Paris',
                     ],
                    146
                     => [
                         'domain' => 'proxad.net',
                         'class' => '4',
                         'country' => 'fr',
                         'state' => '',
                         'name' => 'ONLINE, Paris',
                     ],
                    147
                     => [
                         'domain' => 'univ-mrs.fr',
                         'class' => '1',
                         'country' => 'fr',
                         'state' => '',
                         'name' => 'Reseau Informatique',
                     ],
                    148
                     => [
                         'domain' => 'st.com',
                         'class' => '2',
                         'country' => 'fr',
                         'state' => '',
                         'name' => 'STMicroelectronics',
                     ],
                    149
                     => [
                         'domain' => 'club-internet.fr',
                         'class' => '4',
                         'country' => 'fr',
                         'state' => '',
                         'name' => 'T-Online France, Paris',
                     ],
                    150
                     => [
                         'domain' => 'tele2.fr',
                         'class' => '4',
                         'country' => 'fr',
                         'state' => '',
                         'name' => 'Tele2 France S.A.',
                     ],
                    151
                     => [
                         'domain' => 'u-psud.fr',
                         'class' => '1',
                         'country' => 'fr',
                         'state' => '',
                         'name' => 'Universite Paris Sud, Orsay',
                     ],
                    152
                     => [
                         'domain' => 'ups-tlse.fr',
                         'class' => '1',
                         'country' => 'fr',
                         'state' => '',
                         'name' => 'Universite Paul Sabatier de Toulous',
                     ],
                    153
                     => [
                         'domain' => 'univ-nantes.fr',
                         'class' => '1',
                         'country' => 'fr',
                         'state' => '',
                         'name' => 'Universite de Nantes, Nantes',
                     ],
                    154
                     => [
                         'domain' => 'uvsq.fr',
                         'class' => '1',
                         'country' => 'fr',
                         'state' => '',
                         'name' => 'Universite de Versailles-Saint-Quentin-en-Yvelines',
                     ],
                    155
                     => [
                         'domain' => 'wanadoo.fr',
                         'class' => '4',
                         'country' => 'fr',
                         'state' => '',
                         'name' => 'Wanadoo France',
                     ],
                    156
                     => [
                         'domain' => 'baesystems.com',
                         'class' => '2',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'BAE Systems plc',
                     ],
                    157
                     => [
                         'domain' => 'btopenworld.com',
                         'class' => '4',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'British Telecommunications plc',
                     ],
                    158
                     => [
                         'domain' => 'btcentralplus.com',
                         'class' => '4',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'British Telecommunications plc, London',
                     ],
                    159
                     => [
                         'domain' => 'brunel.ac.uk',
                         'class' => '1',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'Brunel University',
                     ],
                    160
                     => [
                         'domain' => 'clara.net',
                         'class' => '0',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'ClaraNET Ltd.',
                     ],
                    161
                     => [
                         'domain' => 'dl.ac.uk',
                         'class' => '1',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'Daresbury Laboratory',
                     ],
                    162
                     => [
                         'domain' => 'dmu.ac.uk',
                         'class' => '1',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'De Montfort University, Leicester',
                     ],
                    163
                     => [
                         'domain' => 'ed.ac.uk',
                         'class' => '1',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'Edinburgh University',
                     ],
                    164
                     => [
                         'domain' => 'lancs.ac.uk',
                         'class' => '1',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'Lancaster University',
                     ],
                    165
                     => [
                         'domain' => 'leeds.ac.uk',
                         'class' => '1',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'Leeds University',
                     ],
                    166
                     => [
                         'domain' => 'npl.co.uk',
                         'class' => '0',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'NPL Management Ltd',
                     ],
                    167
                     => [
                         'domain' => 'oracle.co.uk',
                         'class' => '2',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'Oracle Ltd',
                     ],
                    168
                     => [
                         'domain' => 'plus.com',
                         'class' => '0',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'PlusNet Technologies Ltd',
                     ],
                    169
                     => [
                         'domain' => 'qinetiq.com',
                         'class' => '0',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'QinetiQ',
                     ],
                    170
                     => [
                         'domain' => 'qmul.ac.uk',
                         'class' => '1',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'Queen Mary and Westfield College, University of London',
                     ],
                    171
                     => [
                         'domain' => 'qub.ac.uk',
                         'class' => '1',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'Queens University Belfast',
                     ],
                    172
                     => [
                         'domain' => 'rhul.ac.uk',
                         'class' => '1',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'Royal Holloway- University of London',
                     ],
                    173
                     => [
                         'domain' => 'soton.ac.uk',
                         'class' => '1',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'Southampton University',
                     ],
                    174
                     => [
                         'domain' => 'surrey.ac.uk',
                         'class' => '1',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'Surrey University',
                     ],
                    175
                     => [
                         'domain' => 'blueyonder.co.uk',
                         'class' => '4',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'Telewest Communications Networks Limited',
                     ],
                    176
                     => [
                         'domain' => 'ucl.ac.uk',
                         'class' => '1',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'University College London',
                     ],
                    177
                     => [
                         'domain' => 'cam.ac.uk',
                         'class' => '1',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'University of Cambridge',
                     ],
                    178
                     => [
                         'domain' => 'gla.ac.uk',
                         'class' => '1',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'University of Glasgow',
                     ],
                    179
                     => [
                         'domain' => 'luton.ac.uk',
                         'class' => '1',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'University of Luton',
                     ],
                    180
                     => [
                         'domain' => 'rdg.ac.uk',
                         'class' => '1',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'University of Reading',
                     ],
                    181
                     => [
                         'domain' => 'swan.ac.uk',
                         'class' => '1',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'University of Wales Swansea',
                     ],
                    182
                     => [
                         'domain' => 'zen.co.uk',
                         'class' => '4',
                         'country' => 'gb',
                         'state' => '',
                         'name' => 'Zen Internet Ltd',
                     ],
                    183
                     => [
                         'domain' => 'gtu.edu.ge',
                         'class' => '1',
                         'country' => 'ge',
                         'state' => '',
                         'name' => 'Georgian Technical University',
                     ],
                    184
                     => [
                         'domain' => 'demokritos.gr',
                         'class' => '2',
                         'country' => 'gr',
                         'state' => '',
                         'name' => 'National Centre of Scientific Research \\"DEMOKRITOS\\"',
                     ],
                    185
                     => [
                         'domain' => 'uoa.gr',
                         'class' => '1',
                         'country' => 'gr',
                         'state' => '',
                         'name' => 'National & Kapodistrian University of Athens',
                     ],
                    186
                     => [
                         'domain' => 'otenet.gr',
                         'class' => '4',
                         'country' => 'gr',
                         'state' => '',
                         'name' => 'OTEnet',
                     ],
                    187
                     => [
                         'domain' => 'tellas.gr',
                         'class' => '4',
                         'country' => 'gr',
                         'state' => '',
                         'name' => 'Tellas',
                     ],
                    188
                     => [
                         'domain' => 'cuhk.edu.hk',
                         'class' => '1',
                         'country' => 'hk',
                         'state' => '',
                         'name' => 'Chinese University of Hong Kong Shatin',
                     ],
                    189
                     => [
                         'domain' => 'cityu.edu.hk',
                         'class' => '1',
                         'country' => 'hk',
                         'state' => '',
                         'name' => 'City University of Hong Kong',
                     ],
                    190
                     => [
                         'domain' => 'polyu.edu.hk',
                         'class' => '1',
                         'country' => 'hk',
                         'state' => '',
                         'name' => 'Hong Kong Polytechnic University',
                     ],
                    191
                     => [
                         'domain' => 'ust.hk',
                         'class' => '1',
                         'country' => 'hk',
                         'state' => '',
                         'name' => 'Hong Kong University of Science and Technology',
                     ],
                    192
                     => [
                         'domain' => 'netvigator.com',
                         'class' => '0',
                         'country' => 'hk',
                         'state' => '',
                         'name' => 'PCCW-HKT Datacom Services Limited',
                     ],
                    193
                     => [
                         'domain' => 'hku.hk',
                         'class' => '1',
                         'country' => 'hk',
                         'state' => '',
                         'name' => 'University of Hong Kong',
                     ],
                    194
                     => [
                         'domain' => 'ifs.hr',
                         'class' => '1',
                         'country' => 'hr',
                         'state' => '',
                         'name' => 'Institute of Physics, Zagreb',
                     ],
                    195
                     => [
                         'domain' => 'ui.edu',
                         'class' => '1',
                         'country' => 'id',
                         'state' => '',
                         'name' => 'University of Indonesia, Jawa Borat',
                     ],
                    196
                     => [
                         'domain' => 'biu.ac.il',
                         'class' => '1',
                         'country' => 'il',
                         'state' => '',
                         'name' => 'Bar-Ilan University, Ramat-Gan',
                     ],
                    197
                     => [
                         'domain' => 'barak.net.il',
                         'class' => '4',
                         'country' => 'il',
                         'state' => '',
                         'name' => 'Barak I.T.C., Rosh Ha\\\'ayin',
                     ],
                    198
                     => [
                         'domain' => 'bezeqint.net',
                         'class' => '0',
                         'country' => 'il',
                         'state' => '',
                         'name' => 'Bezeq International',
                     ],
                    199
                     => [
                         'domain' => 'knet.co.il',
                         'class' => '0',
                         'country' => 'il',
                         'state' => '',
                         'name' => 'Mishkei Hatakam, Tel-Aviv',
                     ],
                    200
                     => [
                         'domain' => 'netvision.net.il',
                         'class' => '4',
                         'country' => 'il',
                         'state' => '',
                         'name' => 'NetVision LTD., Haifa',
                     ],
                    201
                     => [
                         'domain' => 'tau.ac.il',
                         'class' => '1',
                         'country' => 'il',
                         'state' => '',
                         'name' => 'Tel Aviv University, Tel Aviv',
                     ],
                    202
                     => [
                         'domain' => 'weizmann.ac.il',
                         'class' => '1',
                         'country' => 'il',
                         'state' => '',
                         'name' => 'Weizmann Institute of Science, Rehovot',
                     ],
                    203
                     => [
                         'domain' => 'amity.edu',
                         'class' => '1',
                         'country' => 'in',
                         'state' => '',
                         'name' => 'Amity Business School, Noida',
                     ],
                    204
                     => [
                         'domain' => 'bol.net.in',
                         'class' => '4',
                         'country' => 'in',
                         'state' => '',
                         'name' => 'Bharat online, New Delhi',
                     ],
                    205
                     => [
                         'domain' => 'touchtelindia.net',
                         'class' => '4',
                         'country' => 'in',
                         'state' => '',
                         'name' => 'Bharti Tele- Ventures limited, New Delhi',
                     ],
                    206
                     => [
                         'domain' => 'eth.net',
                         'class' => '4',
                         'country' => 'in',
                         'state' => '',
                         'name' => 'Dishnet DSL Ltd., Chennai',
                     ],
                    207
                     => [
                         'domain' => 'ernet.in',
                         'class' => '4',
                         'country' => 'in',
                         'state' => '',
                         'name' => 'ERNET',
                     ],
                    208
                     => [
                         'domain' => 'in2cable.com',
                         'class' => '0',
                         'country' => 'in',
                         'state' => '',
                         'name' => 'In2cable (India',
                     ],
                    209
                     => [
                         'domain' => 'iitb.ac.in',
                         'class' => '1',
                         'country' => 'in',
                         'state' => '',
                         'name' => 'Indian Institute of Technology Bombay',
                     ],
                    210
                     => [
                         'domain' => 'iitm.ac.in',
                         'class' => '1',
                         'country' => 'in',
                         'state' => '',
                         'name' => 'Indian Institute of Technology Madras',
                     ],
                    211
                     => [
                         'domain' => 'da-iict.org',
                         'class' => '2',
                         'country' => 'in',
                         'state' => '',
                         'name' => 'Institute Of Information and Communication Technology',
                     ],
                    212
                     => [
                         'domain' => 'jncasr.ac.in',
                         'class' => '1',
                         'country' => 'in',
                         'state' => '',
                         'name' => 'Jawaharial Nehru Centre for Advanced Scientific Research',
                     ],
                    213
                     => [
                         'domain' => 'jnu.ac.in',
                         'class' => '1',
                         'country' => 'in',
                         'state' => '',
                         'name' => 'Jawaharial Nehru University',
                     ],
                    214
                     => [
                         'domain' => 'tunmail.com',
                         'class' => '4',
                         'country' => 'in',
                         'state' => '',
                         'name' => 'Metro Cable Internet Services, New Delhi',
                     ],
                    215
                     => [
                         'domain' => 'bose.res.in',
                         'class' => '2',
                         'country' => 'in',
                         'state' => '',
                         'name' => 'S.N. Bose National Centre for Basic Sciences, Kolkata',
                     ],
                    216
                     => [
                         'domain' => 'sify.net',
                         'class' => '0',
                         'country' => 'in',
                         'state' => '',
                         'name' => 'Satyam Infoway Limited, Chennai',
                     ],
                    217
                     => [
                         'domain' => 'vsnl.net.in',
                         'class' => '4',
                         'country' => 'in',
                         'state' => '',
                         'name' => 'TATA indicom',
                     ],
                    218
                     => [
                         'domain' => 'jdvu.ac.in',
                         'class' => '1',
                         'country' => 'in',
                         'state' => '',
                         'name' => 'jdvu.ac.in',
                     ],
                    219
                     => [
                         'domain' => 'kashanu.ac.ir',
                         'class' => '1',
                         'country' => 'ir',
                         'state' => '',
                         'name' => 'Kashan University',
                     ],
                    220
                     => [
                         'domain' => 'sinet.ir',
                         'class' => '0',
                         'country' => 'ir',
                         'state' => '',
                         'name' => 'Soroush Interactive Network',
                     ],
                    221
                     => [
                         'domain' => 'cnr.it',
                         'class' => '2',
                         'country' => 'it',
                         'state' => '',
                         'name' => 'Consiglio Nazionale delle Ricerche',
                     ],
                    222
                     => [
                         'domain' => 'enea.it',
                         'class' => '2',
                         'country' => 'it',
                         'state' => '',
                         'name' => 'ENEA Italian National Agency for New Technologies, Energy and the Environment',
                     ],
                    223
                     => [
                         'domain' => 'fastres.net',
                         'class' => '0',
                         'country' => 'it',
                         'state' => '',
                         'name' => 'FASTWEB S.P.A., Milano',
                     ],
                    224
                     => [
                         'domain' => 'trieste.it',
                         'class' => '0',
                         'country' => 'it',
                         'state' => '',
                         'name' => 'Geographical Domain Trieste',
                     ],
                    225
                     => [
                         'domain' => 'infn.it',
                         'class' => '3',
                         'country' => 'it',
                         'state' => '',
                         'name' => 'Istituto Nazionale di Fisica Nucleare, Bologna',
                     ],
                    226
                     => [
                         'domain' => 'libero.it',
                         'class' => '1',
                         'country' => 'it',
                         'state' => '',
                         'name' => 'Italia Online S.p.A.',
                     ],
                    227
                     => [
                         'domain' => 'garr.it',
                         'class' => '1',
                         'country' => 'it',
                         'state' => '',
                         'name' => 'Italian Academic and Research Network',
                     ],
                    228
                     => [
                         'domain' => 'eutelia.it',
                         'class' => '0',
                         'country' => 'it',
                         'state' => '',
                         'name' => 'Plugit Spa, Arezzo',
                     ],
                    229
                     => [
                         'domain' => 'polimi.it',
                         'class' => '1',
                         'country' => 'it',
                         'state' => '',
                         'name' => 'Politecnico di Milano',
                     ],
                    230
                     => [
                         'domain' => 'sns.it',
                         'class' => '0',
                         'country' => 'it',
                         'state' => '',
                         'name' => 'Scuola Normale Superiore di Pisa',
                     ],
                    231
                     => [
                         'domain' => 'interbusiness.it',
                         'class' => '4',
                         'country' => 'it',
                         'state' => '',
                         'name' => 'Telecom Italia S.p.A., Roma',
                     ],
                    232
                     => [
                         'domain' => 'tiscali.it',
                         'class' => '4',
                         'country' => 'it',
                         'state' => '',
                         'name' => 'Tiscali SpA',
                     ],
                    233
                     => [
                         'domain' => 'unibo.it',
                         'class' => '1',
                         'country' => 'it',
                         'state' => '',
                         'name' => 'Universita\\\' degli Studi di Bologna',
                     ],
                    234
                     => [
                         'domain' => 'unile.it',
                         'class' => '1',
                         'country' => 'it',
                         'state' => '',
                         'name' => 'Universita\\\' degli Studi di Lecce',
                     ],
                    235
                     => [
                         'domain' => 'unimib.it',
                         'class' => '1',
                         'country' => 'it',
                         'state' => '',
                         'name' => 'Universita\\\' degli Studi di Milano - Bicocca',
                     ],
                    236
                     => [
                         'domain' => 'unimo.it',
                         'class' => '1',
                         'country' => 'it',
                         'state' => '',
                         'name' => 'Universita\\\' degli Studi di Modena',
                     ],
                    237
                     => [
                         'domain' => 'unipr.it',
                         'class' => '1',
                         'country' => 'it',
                         'state' => '',
                         'name' => 'Universita\\\' degli Studi di Parma',
                     ],
                    238
                     => [
                         'domain' => 'unipg.it',
                         'class' => '1',
                         'country' => 'it',
                         'state' => '',
                         'name' => 'Universita\\\' degli Studi di Perugia',
                     ],
                    239
                     => [
                         'domain' => 'unipi.it',
                         'class' => '1',
                         'country' => 'it',
                         'state' => '',
                         'name' => 'Universita\\\' degli Studi di Pisa',
                     ],
                    240
                     => [
                         'domain' => 'uniroma1.it',
                         'class' => '1',
                         'country' => 'it',
                         'state' => '',
                         'name' => 'Universita\\\' degli Studi di Roma La Sapienza',
                     ],
                    241
                     => [
                         'domain' => 'uniroma2.it',
                         'class' => '1',
                         'country' => 'it',
                         'state' => '',
                         'name' => 'Universita\\\' degli Studi di Roma Tor Vergata',
                     ],
                    242
                     => [
                         'domain' => 'unict.it',
                         'class' => '1',
                         'country' => 'it',
                         'state' => '',
                         'name' => 'Universita\\\' di Catania',
                     ],
                    243
                     => [
                         'domain' => 'univ-lille1.fr',
                         'class' => '1',
                         'country' => 'it',
                         'state' => '',
                         'name' => 'Universite des Sciences et Technologies de Lille',
                     ],
                    244
                     => [
                         'domain' => 'accsnet.ne.jp',
                         'class' => '4',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'ACCS NET',
                     ],
                    245
                     => [
                         'domain' => 'asahi-net.or.jp',
                         'class' => '4',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'ASAHI NET',
                     ],
                    246
                     => [
                         'domain' => 'ayu.ne.jp',
                         'class' => '4',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'AYUNET',
                     ],
                    247
                     => [
                         'domain' => 'cybernet.co.jp',
                         'class' => '0',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'CYBERNET SYSTEMS CO., LTD.',
                     ],
                    248
                     => [
                         'domain' => 'dion.ne.jp',
                         'class' => '4',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'DION',
                     ],
                    249
                     => [
                         'domain' => 'yournet.ne.jp',
                         'class' => '4',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'FreeBit.Net Service',
                     ],
                    250
                     => [
                         'domain' => 'fujitsu.co.jp',
                         'class' => '2',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'Fujitsu Limited',
                     ],
                    251
                     => [
                         'domain' => 'goo.ne.jp',
                         'class' => '4',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'GOO',
                     ],
                    252
                     => [
                         'domain' => 'hi-ho.ne.jp',
                         'class' => '4',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'Hi-HO Internet Service',
                     ],
                    253
                     => [
                         'domain' => 'hokudai.ac.jp',
                         'class' => '1',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'Hokkaido University',
                     ],
                    254
                     => [
                         'domain' => 'infoweb.ne.jp',
                         'class' => '4',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'InfoWeb',
                     ],
                    255
                     => [
                         'domain' => 'jst.go.jp',
                         'class' => '3',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'Japan Science and Technology Agency',
                     ],
                    256
                     => [
                         'domain' => 'kcn.ne.jp',
                         'class' => '4',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'KCN-Net Service',
                     ],
                    257
                     => [
                         'domain' => 'kobe-u.ac.jp',
                         'class' => '1',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'Kobe University',
                     ],
                    258
                     => [
                         'domain' => 'kyoto-u.ac.jp',
                         'class' => '1',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'Kyoto University',
                     ],
                    259
                     => [
                         'domain' => 'mei.co.jp',
                         'class' => '2',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'Matsushita Electric Industrial Co., Ltd.',
                     ],
                    260
                     => [
                         'domain' => 'mesh.ad.jp',
                         'class' => '2',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'NEC Corporation',
                     ],
                    261
                     => [
                         'domain' => 'nec.co.jp',
                         'class' => '2',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'NEC Corporation',
                     ],
                    262
                     => [
                         'domain' => 'nims.go.jp',
                         'class' => '3',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'National Institute for Materials Science',
                     ],
                    263
                     => [
                         'domain' => 'ocn.ne.jp',
                         'class' => '4',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'Open Computer Network',
                     ],
                    264
                     => [
                         'domain' => 'odn.ne.jp',
                         'class' => '4',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'Open Data Network',
                     ],
                    265
                     => [
                         'domain' => 'osaka-u.ac.jp',
                         'class' => '1',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'Osaka University',
                     ],
                    266
                     => [
                         'domain' => 'plala.or.jp',
                         'class' => '0',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'PLALA',
                     ],
                    267
                     => [
                         'domain' => 'renesas.com',
                         'class' => '2',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'Renesas Technology Corp., Tokyo',
                     ],
                    268
                     => [
                         'domain' => 'sec.or.jp',
                         'class' => '2',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'Sapporo Electronics and Industries Cultivation',
                     ],
                    269
                     => [
                         'domain' => 'sel.co.jp',
                         'class' => '2',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'Semiconductor Energy Laboratory Co., Ltd.',
                     ],
                    270
                     => [
                         'domain' => 'sharp.co.jp',
                         'class' => '2',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'Sharp Corporation',
                     ],
                    271
                     => [
                         'domain' => 'shu.ac.uk',
                         'class' => '1',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'Sheffield Hallam University',
                     ],
                    272
                     => [
                         'domain' => 'so-net.ne.jp',
                         'class' => '4',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'So-net Service',
                     ],
                    273
                     => [
                         'domain' => 'bbtec.net',
                         'class' => '0',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'SoftbankBB Corp., Tokyo',
                     ],
                    274
                     => [
                         'domain' => 'sony.co.jp',
                         'class' => '2',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'Sony Corporation',
                     ],
                    275
                     => [
                         'domain' => 't-com.ne.jp',
                         'class' => '4',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'T-COM ADSL Service',
                     ],
                    276
                     => [
                         'domain' => 'tohoku.ac.jp',
                         'class' => '1',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'Tohoku University',
                     ],
                    277
                     => [
                         'domain' => 'titech.ac.jp',
                         'class' => '1',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'Tokyo Institute of Technology',
                     ],
                    278
                     => [
                         'domain' => 'pu-toyama.ac.jp',
                         'class' => '1',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'Toyama Prefectural University',
                     ],
                    279
                     => [
                         'domain' => 'u-tokyo.ac.jp',
                         'class' => '1',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'University of Tokyo',
                     ],
                    280
                     => [
                         'domain' => 'tsukuba.ac.jp',
                         'class' => '1',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'University of Tsukuba',
                     ],
                    281
                     => [
                         'domain' => 'eonet.ne.jp',
                         'class' => '4',
                         'country' => 'jp',
                         'state' => '',
                         'name' => 'eonet',
                     ],
                    282
                     => [
                         'domain' => 'ewha.ac.kr',
                         'class' => '1',
                         'country' => 'kr',
                         'state' => '',
                         'name' => 'EWHA University',
                     ],
                    283
                     => [
                         'domain' => 'gist.ac.kr',
                         'class' => '1',
                         'country' => 'kr',
                         'state' => '',
                         'name' => 'Gwangju Institute of Scinece and Technology',
                     ],
                    284
                     => [
                         'domain' => 'hananet.net',
                         'class' => '4',
                         'country' => 'kr',
                         'state' => '',
                         'name' => 'Hanaro Telecom, Inc',
                     ],
                    285
                     => [
                         'domain' => 'inha.ac.kr',
                         'class' => '1',
                         'country' => 'kr',
                         'state' => '',
                         'name' => 'INHA University, Nam-gu Incheon',
                     ],
                    286
                     => [
                         'domain' => 'icu.ac.kr',
                         'class' => '1',
                         'country' => 'kr',
                         'state' => '',
                         'name' => 'Information and Communications University, Daejeon',
                     ],
                    287
                     => [
                         'domain' => 'korea.ac.kr',
                         'class' => '1',
                         'country' => 'kr',
                         'state' => '',
                         'name' => 'Korea University Anam-dong',
                     ],
                    288
                     => [
                         'domain' => 'kaist.ac.kr',
                         'class' => '1',
                         'country' => 'kr',
                         'state' => '',
                         'name' => 'Korean Advanced Institute of Science and Technology',
                     ],
                    289
                     => [
                         'domain' => 'kjist.ac.kr',
                         'class' => '1',
                         'country' => 'kr',
                         'state' => '',
                         'name' => 'Kwangju Institute of Science & Technology',
                     ],
                    290
                     => [
                         'domain' => 'kyungwon.ac.kr',
                         'class' => '1',
                         'country' => 'kr',
                         'state' => '',
                         'name' => 'Kyungwon University',
                     ],
                    291
                     => [
                         'domain' => 'postech.ac.kr',
                         'class' => '1',
                         'country' => 'kr',
                         'state' => '',
                         'name' => 'Pohang Kyungbuk Korea System Management Team',
                     ],
                    292
                     => [
                         'domain' => 'samsung.co.kr',
                         'class' => '2',
                         'country' => 'kr',
                         'state' => '',
                         'name' => 'Samsung Networks Inc, Seoul',
                     ],
                    293
                     => [
                         'domain' => 'snu.ac.kr',
                         'class' => '1',
                         'country' => 'kr',
                         'state' => '',
                         'name' => 'Seoul National University, Seoul',
                     ],
                    294
                     => [
                         'domain' => 'skku.ac.kr',
                         'class' => '1',
                         'country' => 'kr',
                         'state' => '',
                         'name' => 'SungKyunKwan University, Seoul',
                     ],
                    295
                     => [
                         'domain' => 'mii.lt',
                         'class' => '1',
                         'country' => 'lt',
                         'state' => '',
                         'name' => 'Institute of Mathematics and Informatics, Vilnius',
                     ],
                    296
                     => [
                         'domain' => 'rst.lt',
                         'class' => '2',
                         'country' => 'lt',
                         'state' => '',
                         'name' => 'Rytu Skirstomieji Tinklai',
                     ],
                    297
                     => [
                         'domain' => 'vtex.lt',
                         'class' => '0',
                         'country' => 'lt',
                         'state' => '',
                         'name' => 'VTeX Typesetting Services',
                     ],
                    298
                     => [
                         'domain' => 'restena.lu',
                         'class' => '0',
                         'country' => 'lu',
                         'state' => '',
                         'name' => 'Fondation RESTENA',
                     ],
                    299
                     => [
                         'domain' => 'aui.ma',
                         'class' => '1',
                         'country' => 'ma',
                         'state' => '',
                         'name' => 'Al Akhawayn University in Ifrane',
                     ],
                    300
                     => [
                         'domain' => 'unet.com.mk',
                         'class' => '4',
                         'country' => 'mk',
                         'state' => '',
                         'name' => 'Unet Ineternet, Skopje',
                     ],
                    301
                     => [
                         'domain' => 'dial.net.mx',
                         'class' => '4',
                         'country' => 'mx',
                         'state' => '',
                         'name' => 'Avantel',
                     ],
                    302
                     => [
                         'domain' => 'ipicyt.edu.mx',
                         'class' => '1',
                         'country' => 'mx',
                         'state' => '',
                         'name' => 'ipicyt, San Luis Potosi',
                     ],
                    303
                     => [
                         'domain' => 'issuecrawler.net',
                         'class' => '0',
                         'country' => 'nl',
                         'state' => '',
                         'name' => 'Govcom.org Foundation',
                     ],
                    304
                     => [
                         'domain' => 'planet.nl',
                         'class' => '4',
                         'country' => 'nl',
                         'state' => '',
                         'name' => 'Planet Media Group N.V.',
                     ],
                    305
                     => [
                         'domain' => 'quicknet.nl',
                         'class' => '4',
                         'country' => 'nl',
                         'state' => '',
                         'name' => 'QuickNet B. V., Alkmaar',
                     ],
                    306
                     => [
                         'domain' => 'rug.nl',
                         'class' => '1',
                         'country' => 'nl',
                         'state' => '',
                         'name' => 'Rijksuniversiteit Groningen',
                     ],
                    307
                     => [
                         'domain' => 'tudelft.nl',
                         'class' => '1',
                         'country' => 'nl',
                         'state' => '',
                         'name' => 'Technische Universiteit Delft, Delft',
                     ],
                    308
                     => [
                         'domain' => 'tue.nl',
                         'class' => '1',
                         'country' => 'nl',
                         'state' => '',
                         'name' => 'Technische Universiteit Eindhoven',
                     ],
                    309
                     => [
                         'domain' => 'chello.nl',
                         'class' => '4',
                         'country' => 'nl',
                         'state' => '',
                         'name' => 'UPC Broadband N.V',
                     ],
                    310
                     => [
                         'domain' => 'utwente.nl',
                         'class' => '1',
                         'country' => 'nl',
                         'state' => '',
                         'name' => 'Universiteit Twente',
                     ],
                    311
                     => [
                         'domain' => 'uu.nl',
                         'class' => '1',
                         'country' => 'nl',
                         'state' => '',
                         'name' => 'Utrecht University',
                     ],
                    312
                     => [
                         'domain' => 'sulphurcanyon.com',
                         'class' => '4',
                         'country' => 'nm',
                         'state' => '',
                         'name' => 'Sulphur Canyon Internet Services',
                     ],
                    313
                     => [
                         'domain' => 'chipcon.no',
                         'class' => '2',
                         'country' => 'no',
                         'state' => '',
                         'name' => 'Chipcon AS',
                     ],
                    314
                     => [
                         'domain' => 'fastsearch.net',
                         'class' => '5',
                         'country' => 'no',
                         'state' => '',
                         'name' => 'Fast Search and Transfer ASA',
                     ],
                    315
                     => [
                         'domain' => 'picsearch.com',
                         'class' => '5',
                         'country' => 'no',
                         'state' => '',
                         'name' => 'Hammarby Fabriksv',
                     ],
                    316
                     => [
                         'domain' => 'nextgentel.com',
                         'class' => '0',
                         'country' => 'no',
                         'state' => '',
                         'name' => 'NextGenTel AS',
                     ],
                    317
                     => [
                         'domain' => 'unik.no',
                         'class' => '1',
                         'country' => 'no',
                         'state' => '',
                         'name' => 'UNIVERSITETSSTUDIENE P305 KJELLER',
                     ],
                    318
                     => [
                         'domain' => 'uib.no',
                         'class' => '1',
                         'country' => 'no',
                         'state' => '',
                         'name' => 'Universitetet i Bergen',
                     ],
                    319
                     => [
                         'domain' => 'ntc.net.np',
                         'class' => '4',
                         'country' => 'np',
                         'state' => '',
                         'name' => 'Napal Telecom Co. Ltd.',
                     ],
                    320
                     => [
                         'domain' => 'clarkson.edu',
                         'class' => '1',
                         'country' => 'ny',
                         'state' => '',
                         'name' => 'Clarkson University',
                     ],
                    321
                     => [
                         'domain' => 'xtra.co.nz',
                         'class' => '4',
                         'country' => 'nz',
                         'state' => '',
                         'name' => 'Telecom IP Limited',
                     ],
                    322
                     => [
                         'domain' => 'paradise.net.nz',
                         'class' => '4',
                         'country' => 'nz',
                         'state' => '',
                         'name' => 'TelstraClear Limited',
                     ],
                    323
                     => [
                         'domain' => 'cwru.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'oh',
                         'name' => 'Case Western Reserve University',
                     ],
                    324
                     => [
                         'domain' => 'telmex.com.pe',
                         'class' => '4',
                         'country' => 'pe',
                         'state' => '',
                         'name' => 'Telmex',
                     ],
                    325
                     => [
                         'domain' => 'pieas.edu.pk',
                         'class' => '1',
                         'country' => 'pk',
                         'state' => '',
                         'name' => 'Pakistan Institute of engineering & Applied Sciences',
                     ],
                    326
                     => [
                         'domain' => 'agh.edu.pl',
                         'class' => '1',
                         'country' => 'pl',
                         'state' => '',
                         'name' => 'Akademia Gorniczo-Hutnicza, Krakow',
                     ],
                    327
                     => [
                         'domain' => 'aster.pl',
                         'class' => '4',
                         'country' => 'pl',
                         'state' => '',
                         'name' => 'Aster City Cable SP. Z O.O., Warszawa',
                     ],
                    328
                     => [
                         'domain' => 'e-wro.net.pl',
                         'class' => '0',
                         'country' => 'pl',
                         'state' => '',
                         'name' => 'DCG DOMINAS CONSULTING GROUP SP. Z O.O., Wroclaw',
                     ],
                    329
                     => [
                         'domain' => 'lublin.pl',
                         'class' => '0',
                         'country' => 'pl',
                         'state' => '',
                         'name' => 'LUBMAN UMCS SP. Z O.O.',
                     ],
                    330
                     => [
                         'domain' => 'pp.com.pl',
                         'class' => '0',
                         'country' => 'pl',
                         'state' => '',
                         'name' => 'PETER PAN KOMPUTERY',
                     ],
                    331
                     => [
                         'domain' => 'tpnet.pl',
                         'class' => '0',
                         'country' => 'pl',
                         'state' => '',
                         'name' => 'TP S.A., Warszawa',
                     ],
                    332
                     => [
                         'domain' => 'kv.net.pl',
                         'class' => '0',
                         'country' => 'pl',
                         'state' => '',
                         'name' => 'TYTUS.NET, Krakow',
                     ],
                    333
                     => [
                         'domain' => 'poznan.pl',
                         'class' => '1',
                         'country' => 'pl',
                         'state' => '',
                         'name' => 'UNIWERSYTET IM.ADAMA MICKIEWICZA, Poznan',
                     ],
                    334
                     => [
                         'domain' => 'uj.edu.pl',
                         'class' => '1',
                         'country' => 'pl',
                         'state' => '',
                         'name' => 'UNIWERSYTET JAGIELLONSKI, Krakow',
                     ],
                    335
                     => [
                         'domain' => 'wroc.pl',
                         'class' => '0',
                         'country' => 'pl',
                         'state' => '',
                         'name' => 'WROCLAWSKIE CENTRUM SIECIOWO-SUPERKOMPUTEROWE PWR., Wroclav',
                     ],
                    336
                     => [
                         'domain' => 'fuw.edu.pl',
                         'class' => '1',
                         'country' => 'pl',
                         'state' => '',
                         'name' => 'WYDZIAL FIZYKI UNIWERSYTETU WARSZAWSKIEGO, Warszawa',
                     ],
                    337
                     => [
                         'domain' => 'coqui.net',
                         'class' => '4',
                         'country' => 'pr',
                         'state' => '',
                         'name' => 'Puerto Rico Telephone Company',
                     ],
                    338
                     => [
                         'domain' => 'uprr.pr',
                         'class' => '1',
                         'country' => 'pr',
                         'state' => '',
                         'name' => 'University of Puerto Rico, Rio Piedras',
                     ],
                    339
                     => [
                         'domain' => 'edil.pub.ro',
                         'class' => '2',
                         'country' => 'ro',
                         'state' => '',
                         'name' => 'EDIL Microelectronics R&D Centre, Bucharest',
                     ],
                    340
                     => [
                         'domain' => 'metav.ro',
                         'class' => '0',
                         'country' => 'ro',
                         'state' => '',
                         'name' => 'Metav S.A., Bucharest',
                     ],
                    341
                     => [
                         'domain' => 'ras.ru',
                         'class' => '1',
                         'country' => 'ru',
                         'state' => '',
                         'name' => 'Center for Science Telecommunications and Information technologies of RAS',
                     ],
                    342
                     => [
                         'domain' => 'imvs.ru',
                         'class' => '2',
                         'country' => 'ru',
                         'state' => '',
                         'name' => 'Institute of Microprocessor Computer Systems State Company',
                     ],
                    343
                     => [
                         'domain' => 'mrsu.ru',
                         'class' => '1',
                         'country' => 'ru',
                         'state' => '',
                         'name' => 'Mordovian State University N.P.Ogareva',
                     ],
                    344
                     => [
                         'domain' => 'miet.ru',
                         'class' => '1',
                         'country' => 'ru',
                         'state' => '',
                         'name' => 'Moscow Institute of Electronic Technology',
                     ],
                    345
                     => [
                         'domain' => 'sci-nnov.ru',
                         'class' => '0',
                         'country' => 'ru',
                         'state' => '',
                         'name' => 'Sandy Info',
                     ],
                    346
                     => [
                         'domain' => 'nsc.ru',
                         'class' => '1',
                         'country' => 'ru',
                         'state' => '',
                         'name' => 'Siberian Branch of Russian Academy of Science',
                     ],
                    347
                     => [
                         'domain' => 'rssi.ru',
                         'class' => '1',
                         'country' => 'ru',
                         'state' => '',
                         'name' => 'Space Research Institute of Russian Academy of Sciences',
                     ],
                    348
                     => [
                         'domain' => 'icomtex.ru',
                         'class' => '0',
                         'country' => 'ru',
                         'state' => '',
                         'name' => 'icomtex.ru',
                     ],
                    349
                     => [
                         'domain' => 'isu.net.sa',
                         'class' => '4',
                         'country' => 'sa',
                         'state' => '',
                         'name' => 'Internet Services Unit',
                     ],
                    350
                     => [
                         'domain' => 'bredbandsbolaget.se',
                         'class' => '4',
                         'country' => 'se',
                         'state' => '',
                         'name' => 'Bredbandsbolaget',
                     ],
                    351
                     => [
                         'domain' => 'chalmers.se',
                         'class' => '1',
                         'country' => 'se',
                         'state' => '',
                         'name' => 'Chalmers Tekneska Hogskola, Goteborg',
                     ],
                    352
                     => [
                         'domain' => 'liu.se',
                         'class' => '1',
                         'country' => 'se',
                         'state' => '',
                         'name' => 'Linkopings Universitet',
                     ],
                    353
                     => [
                         'domain' => 'lu.se',
                         'class' => '1',
                         'country' => 'se',
                         'state' => '',
                         'name' => 'Lunds university',
                     ],
                    354
                     => [
                         'domain' => 'mh.se',
                         'class' => '1',
                         'country' => 'se',
                         'state' => '',
                         'name' => 'Mid Sweden University',
                     ],
                    355
                     => [
                         'domain' => 'kth.se',
                         'class' => '1',
                         'country' => 'se',
                         'state' => '',
                         'name' => 'Royal Institute of Technology, Stockholm',
                     ],
                    356
                     => [
                         'domain' => 'siceit.se',
                         'class' => '4',
                         'country' => 'se',
                         'state' => '',
                         'name' => 'SizeIT Drift AB',
                     ],
                    357
                     => [
                         'domain' => 'songnetworks.se',
                         'class' => '4',
                         'country' => 'se',
                         'state' => '',
                         'name' => 'TDC Song',
                     ],
                    358
                     => [
                         'domain' => 'telia.com',
                         'class' => '0',
                         'country' => 'se',
                         'state' => '',
                         'name' => 'TeliaSonera AB',
                     ],
                    359
                     => [
                         'domain' => 'skanova.com',
                         'class' => '0',
                         'country' => 'se',
                         'state' => '',
                         'name' => 'TeliaSonera AB, Stockholm',
                     ],
                    360
                     => [
                         'domain' => 'chello.se',
                         'class' => '4',
                         'country' => 'se',
                         'state' => '',
                         'name' => 'UPC Broadband N.V',
                     ],
                    361
                     => [
                         'domain' => 'uu.se',
                         'class' => '1',
                         'country' => 'se',
                         'state' => '',
                         'name' => 'Uppsala universitet',
                     ],
                    362
                     => [
                         'domain' => 'ntu.edu.sg',
                         'class' => '1',
                         'country' => 'sg',
                         'state' => '',
                         'name' => 'Namyang Technological University',
                     ],
                    363
                     => [
                         'domain' => 'nus.edu.sg',
                         'class' => '1',
                         'country' => 'sg',
                         'state' => '',
                         'name' => 'National University of Singapore',
                     ],
                    364
                     => [
                         'domain' => 'pacific.net.sg',
                         'class' => '4',
                         'country' => 'sg',
                         'state' => '',
                         'name' => 'Pacific Internet',
                     ],
                    365
                     => [
                         'domain' => 'singnet.com.sg',
                         'class' => '4',
                         'country' => 'sg',
                         'state' => '',
                         'name' => 'SingTel',
                     ],
                    366
                     => [
                         'domain' => 'mystarhub.com.sg',
                         'class' => '4',
                         'country' => 'sg',
                         'state' => '',
                         'name' => 'Starhub',
                     ],
                    367
                     => [
                         'domain' => 'maxonline.com.sg',
                         'class' => '4',
                         'country' => 'sg',
                         'state' => '',
                         'name' => 'maxonline.com.sg',
                     ],
                    368
                     => [
                         'domain' => 'siol.net',
                         'class' => '0',
                         'country' => 'si',
                         'state' => '',
                         'name' => 'SiOL d.o.o',
                     ],
                    369
                     => [
                         'domain' => 'telecom.sk',
                         'class' => '4',
                         'country' => 'sk',
                         'state' => '',
                         'name' => 'Slovak Telecom a.s.',
                     ],
                    370
                     => [
                         'domain' => 'uniba.sk',
                         'class' => '1',
                         'country' => 'sk',
                         'state' => '',
                         'name' => 'Univerzita Komenskeho v Bratislave',
                     ],
                    371
                     => [
                         'domain' => 'asianet.co.th',
                         'class' => '4',
                         'country' => 'th',
                         'state' => '',
                         'name' => 'Asia Infornet co.,ltd',
                     ],
                    372
                     => [
                         'domain' => 'chula.ac.th',
                         'class' => '1',
                         'country' => 'th',
                         'state' => '',
                         'name' => 'Chulalongkorn University, Bangkok',
                     ],
                    373
                     => [
                         'domain' => 'mahidol.ac.th',
                         'class' => '1',
                         'country' => 'th',
                         'state' => '',
                         'name' => 'Mahidol University, Bangkok',
                     ],
                    374
                     => [
                         'domain' => 'psu.ac.th',
                         'class' => '1',
                         'country' => 'th',
                         'state' => '',
                         'name' => 'Prince of Songkla University, Songkhla',
                     ],
                    375
                     => [
                         'domain' => 'bilkent.edu.tr',
                         'class' => '1',
                         'country' => 'tr',
                         'state' => '',
                         'name' => 'Bilkent University, Ankara',
                     ],
                    376
                     => [
                         'domain' => 'doruk.net.tr',
                         'class' => '4',
                         'country' => 'tr',
                         'state' => '',
                         'name' => 'Doruknet',
                     ],
                    377
                     => [
                         'domain' => 'metu.edu.tr',
                         'class' => '1',
                         'country' => 'tr',
                         'state' => '',
                         'name' => 'Middle East Technical University, Ankara',
                     ],
                    378
                     => [
                         'domain' => 'atlas.net.tr',
                         'class' => '4',
                         'country' => 'tr',
                         'state' => '',
                         'name' => 'Telekom Atlas Online',
                     ],
                    379
                     => [
                         'domain' => 'ttnet.net.tr',
                         'class' => '4',
                         'country' => 'tr',
                         'state' => '',
                         'name' => 'Turk Telekom A.S.',
                     ],
                    380
                     => [
                         'domain' => 'ebix.net.tw',
                         'class' => '4',
                         'country' => 'tw',
                         'state' => '',
                         'name' => 'Asia Pacific Online Service Inc',
                     ],
                    381
                     => [
                         'domain' => 'hinet.net',
                         'class' => '4',
                         'country' => 'tw',
                         'state' => '',
                         'name' => 'Chunghwa Telecom Co., Ltd.',
                     ],
                    382
                     => [
                         'domain' => 'seed.net.tw',
                         'class' => '0',
                         'country' => 'tw',
                         'state' => '',
                         'name' => 'Digital United Inc.',
                     ],
                    383
                     => [
                         'domain' => 'fju.edu.tw',
                         'class' => '1',
                         'country' => 'tw',
                         'state' => '',
                         'name' => 'Fu Jen Catholic University, Taipei',
                     ],
                    384
                     => [
                         'domain' => 'itri.org.tw',
                         'class' => '2',
                         'country' => 'tw',
                         'state' => '',
                         'name' => 'Industrial Technology Research Institute',
                     ],
                    385
                     => [
                         'domain' => 'mtksg.com',
                         'class' => '0',
                         'country' => 'tw',
                         'state' => '',
                         'name' => 'Mediatek Singapore Pte Ltd',
                     ],
                    386
                     => [
                         'domain' => 'hcrc.edu.tw',
                         'class' => '1',
                         'country' => 'tw',
                         'state' => '',
                         'name' => 'Ministry of Education Computer Center',
                     ],
                    387
                     => [
                         'domain' => 'ncnu.edu.tw',
                         'class' => '1',
                         'country' => 'tw',
                         'state' => '',
                         'name' => 'National Chi Nan University',
                     ],
                    388
                     => [
                         'domain' => 'nctu.edu.tw',
                         'class' => '1',
                         'country' => 'tw',
                         'state' => '',
                         'name' => 'National Chiao Tung University',
                     ],
                    389
                     => [
                         'domain' => 'nsc.gov.tw',
                         'class' => '3',
                         'country' => 'tw',
                         'state' => '',
                         'name' => 'National Science Council',
                     ],
                    390
                     => [
                         'domain' => 'ntu.edu.tw',
                         'class' => '1',
                         'country' => 'tw',
                         'state' => '',
                         'name' => 'National Taiwan University, Taipei',
                     ],
                    391
                     => [
                         'domain' => 'ntcu.net',
                         'class' => '1',
                         'country' => 'tw',
                         'state' => '',
                         'name' => 'Northern Taiwan Community University',
                     ],
                    392
                     => [
                         'domain' => 'nthu.edu.tw',
                         'class' => '1',
                         'country' => 'tw',
                         'state' => '',
                         'name' => 'Northern Taiwan Community University',
                     ],
                    393
                     => [
                         'domain' => 'roam.com.tw',
                         'class' => '0',
                         'country' => 'tw',
                         'state' => '',
                         'name' => 'ROAM Multimedia Co. Ltd.',
                     ],
                    394
                     => [
                         'domain' => 'roam.net.tw',
                         'class' => '0',
                         'country' => 'tw',
                         'state' => '',
                         'name' => 'ROAM Multimedia Co. Ltd.',
                     ],
                    395
                     => [
                         'domain' => 'stic.gov.tw',
                         'class' => '3',
                         'country' => 'tw',
                         'state' => '',
                         'name' => 'Science & Technology Policy Research and Information Center',
                     ],
                    396
                     => [
                         'domain' => 'so-net.net.tw',
                         'class' => '4',
                         'country' => 'tw',
                         'state' => '',
                         'name' => 'Sony Network Taiwan Limited.',
                     ],
                    397
                     => [
                         'domain' => 'tfn.net.tw',
                         'class' => '4',
                         'country' => 'tw',
                         'state' => '',
                         'name' => 'Taiwan Fixed Network Co.,Ltd., Taipei',
                     ],
                    398
                     => [
                         'domain' => 'tsmc.com.tw',
                         'class' => '2',
                         'country' => 'tw',
                         'state' => '',
                         'name' => 'Taiwan Semiconductor Manufacturing Company, Ltd.',
                     ],
                    399
                     => [
                         'domain' => 'umc.com',
                         'class' => '2',
                         'country' => 'tw',
                         'state' => '',
                         'name' => 'United Microelectronics Corp.',
                     ],
                    400
                     => [
                         'domain' => 'bestnet.ua',
                         'class' => '0',
                         'country' => 'ua',
                         'state' => '',
                         'name' => 'BestNet, Kharkov',
                     ],
                    401
                     => [
                         'domain' => 'kharkov.ua',
                         'class' => '0',
                         'country' => 'ua',
                         'state' => '',
                         'name' => 'Geographical domain for Kharkov',
                     ],
                    402
                     => [
                         'domain' => 'kiev.ua',
                         'class' => '0',
                         'country' => 'ua',
                         'state' => '',
                         'name' => 'Geographical domain for Kiev',
                     ],
                    403
                     => [
                         'domain' => 'gluk.org',
                         'class' => '0',
                         'country' => 'ua',
                         'state' => '',
                         'name' => 'GlasNet-Ukraine, Ltd.',
                     ],
                    404
                     => [
                         'domain' => 'ntl.com',
                         'class' => '4',
                         'country' => 'uk',
                         'state' => '',
                         'name' => 'NTL Internet Ltd',
                     ],
                    405
                     => [
                         'domain' => 'ntli.net',
                         'class' => '0',
                         'country' => 'uk',
                         'state' => '',
                         'name' => 'NTL Internet Ltd',
                     ],
                    406
                     => [
                         'domain' => 'lanl.gov',
                         'class' => '3',
                         'country' => 'us',
                         'state' => '',
                         'name' => 'Los Alamos National Labs',
                     ],
                    407
                     => [
                         'domain' => 'nasa.gov',
                         'class' => '3',
                         'country' => 'us',
                         'state' => '',
                         'name' => 'NASA',
                     ],
                    408
                     => [
                         'domain' => 'nist.gov',
                         'class' => '3',
                         'country' => 'us',
                         'state' => '',
                         'name' => 'NIST',
                     ],
                    409
                     => [
                         'domain' => 'nih.gov',
                         'class' => '3',
                         'country' => 'us',
                         'state' => '',
                         'name' => 'National Institute of Health',
                     ],
                    410
                     => [
                         'domain' => 'nsf.gov',
                         'class' => '3',
                         'country' => 'us',
                         'state' => '',
                         'name' => 'National Science Foundation',
                     ],
                    411
                     => [
                         'domain' => 'af.mil',
                         'class' => '3',
                         'country' => 'us',
                         'state' => '',
                         'name' => 'US Airforce',
                     ],
                    412
                     => [
                         'domain' => 'army.mil',
                         'class' => '3',
                         'country' => 'us',
                         'state' => '',
                         'name' => 'US Army',
                     ],
                    413
                     => [
                         'domain' => 'usda.gov',
                         'class' => '3',
                         'country' => 'us',
                         'state' => '',
                         'name' => 'US Department of Agriculture',
                     ],
                    414
                     => [
                         'domain' => 'navy.mil',
                         'class' => '3',
                         'country' => 'us',
                         'state' => '',
                         'name' => 'US Navy',
                     ],
                    415
                     => [
                         'domain' => 'nipr.mil',
                         'class' => '3',
                         'country' => 'us',
                         'state' => '',
                         'name' => 'Unknown Military',
                     ],
                    416
                     => [
                         'domain' => 'uaf.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ak',
                         'name' => 'University of Alaska Fairbanks',
                     ],
                    417
                     => [
                         'domain' => 'cfdrc.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'al',
                         'name' => 'CFD Research Corporation',
                     ],
                    418
                     => [
                         'domain' => 'usouthal.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'al',
                         'name' => 'University of South Alabama',
                     ],
                    419
                     => [
                         'domain' => 'alltel.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'ar',
                         'name' => 'ALLTEL Communications',
                     ],
                    420
                     => [
                         'domain' => 'asu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'az',
                         'name' => 'Arizona State University',
                     ],
                    421
                     => [
                         'domain' => 'jetstreamwireless.com',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'az',
                         'name' => 'JetStream Wireless',
                     ],
                    422
                     => [
                         'domain' => 'arizona.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'az',
                         'name' => 'University of Arizona',
                     ],
                    423
                     => [
                         'domain' => 'attens.net',
                         'class' => '5',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'AT&T Enhanced Network Services',
                     ],
                    424
                     => [
                         'domain' => 'accelrys.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Accelrys',
                     ],
                    425
                     => [
                         'domain' => 'atgi.net',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Advanced Telcom Group',
                     ],
                    426
                     => [
                         'domain' => 'agilent.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Agilent Technologies',
                     ],
                    427
                     => [
                         'domain' => 'alexa.com',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Alexa Internet',
                     ],
                    428
                     => [
                         'domain' => 'amat.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Applied Materials',
                     ],
                    429
                     => [
                         'domain' => 'ask.com',
                         'class' => '5',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Ask Jeeves, Inc.',
                     ],
                    430
                     => [
                         'domain' => 'teoma.com',
                         'class' => '5',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Ask Jeeves, Inc.',
                     ],
                    431
                     => [
                         'domain' => 'calpoly.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Cal Poly State University',
                     ],
                    432
                     => [
                         'domain' => 'caltech.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'California Institute of Technology',
                     ],
                    433
                     => [
                         'domain' => 'csun.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'California State University, Northridge',
                     ],
                    434
                     => [
                         'domain' => 'covad.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Covad Communications Company',
                     ],
                    435
                     => [
                         'domain' => 'googlebot.com',
                         'class' => '5',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Google Inc.',
                     ],
                    436
                     => [
                         'domain' => 'intel.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Intel Corporation',
                     ],
                    437
                     => [
                         'domain' => 'jeteye.com',
                         'class' => '5',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'JetEye Technologies Inc.',
                     ],
                    438
                     => [
                         'domain' => 'looksmart.com',
                         'class' => '5',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Looksmart, LTD',
                     ],
                    439
                     => [
                         'domain' => 'mediaserve.net',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'MediaServe LLC',
                     ],
                    440
                     => [
                         'domain' => 'o1.com',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Option One Communications',
                     ],
                    441
                     => [
                         'domain' => 'av.com',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Overture Services Inc.',
                     ],
                    442
                     => [
                         'domain' => 'overture.com',
                         'class' => '5',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Overture Services Inc.',
                     ],
                    443
                     => [
                         'domain' => 'pe.net',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Press-Enterprise Co.',
                     ],
                    444
                     => [
                         'domain' => 'saic.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'SAIC',
                     ],
                    445
                     => [
                         'domain' => 'silvaco.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'SILVACO Data Systems',
                     ],
                    446
                     => [
                         'domain' => 'sjsu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'San Jose State University',
                     ],
                    447
                     => [
                         'domain' => 'scu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Santa Clara University',
                     ],
                    448
                     => [
                         'domain' => 'seagate.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Seagate Technology, LLC.',
                     ],
                    449
                     => [
                         'domain' => 'sensitron.net',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Sensitron',
                     ],
                    450
                     => [
                         'domain' => 'spectrolab.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Spectrolab',
                     ],
                    451
                     => [
                         'domain' => 'stanford.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Stanford University',
                     ],
                    452
                     => [
                         'domain' => 'sun.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Sun Microsystems, Inc.',
                     ],
                    453
                     => [
                         'domain' => 'synopsys.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Synopsys, Inc.',
                     ],
                    454
                     => [
                         'domain' => 'telepacific.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'TelePacific Cummunications',
                     ],
                    455
                     => [
                         'domain' => 'isi.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'USC/Information Sciences Institute',
                     ],
                    456
                     => [
                         'domain' => 'berkeley.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'University of California at Berkeley',
                     ],
                    457
                     => [
                         'domain' => 'ucdavis.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'University of California at Davis',
                     ],
                    458
                     => [
                         'domain' => 'uci.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'University of California, Irvine',
                     ],
                    459
                     => [
                         'domain' => 'ucla.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'University of California, Los Angles',
                     ],
                    460
                     => [
                         'domain' => 'ucr.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'University of California, Riverside',
                     ],
                    461
                     => [
                         'domain' => 'ucsd.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'University of California, San Diego',
                     ],
                    462
                     => [
                         'domain' => 'ucsb.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'University of California, Santa Barbara',
                     ],
                    463
                     => [
                         'domain' => 'wj.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Watkins-Johnson Company',
                     ],
                    464
                     => [
                         'domain' => 'algx.net',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'XO Communications',
                     ],
                    465
                     => [
                         'domain' => 'xo.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'XO Communications, Inc.',
                     ],
                    466
                     => [
                         'domain' => 'xilinx.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Xilinx, Inc.',
                     ],
                    467
                     => [
                         'domain' => 'inktomi.com',
                         'class' => '5',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Yahoo! Inc.',
                     ],
                    468
                     => [
                         'domain' => 'inktomisearch.com',
                         'class' => '5',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Yahoo! Inc.',
                     ],
                    469
                     => [
                         'domain' => 'yahoo.com',
                         'class' => '5',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Yahoo! Inc.',
                     ],
                    470
                     => [
                         'domain' => 'turnitin.com',
                         'class' => '5',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'iParadigms Inc.',
                     ],
                    471
                     => [
                         'domain' => 'lcinet.net',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'linkLINE Communications, Inc.',
                     ],
                    472
                     => [
                         'domain' => 'colostate.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'co',
                         'name' => 'Colorado State University',
                     ],
                    473
                     => [
                         'domain' => 'level3.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'co',
                         'name' => 'Level 3 Communications, Inc',
                     ],
                    474
                     => [
                         'domain' => 'qwest.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'co',
                         'name' => 'Qwest Communications International Inc.',
                     ],
                    475
                     => [
                         'domain' => 'twtelecom.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'co',
                         'name' => 'Time Warner Telecom',
                     ],
                    476
                     => [
                         'domain' => 'colorado.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'co',
                         'name' => 'University of Colorado',
                     ],
                    477
                     => [
                         'domain' => 'virtela.com',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'co',
                         'name' => 'Virtela Communications, Inc.',
                     ],
                    478
                     => [
                         'domain' => 'patmedia.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'ct',
                         'name' => 'Patriot Media & Communications, LLC',
                     ],
                    479
                     => [
                         'domain' => 'uconn.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ct',
                         'name' => 'University of Connecticut',
                     ],
                    480
                     => [
                         'domain' => 'howard.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'dc',
                         'name' => 'Howard University',
                     ],
                    481
                     => [
                         'domain' => 'udel.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'de',
                         'name' => 'University of Delaware',
                     ],
                    482
                     => [
                         'domain' => 'fau.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'fl',
                         'name' => 'Florida Atlantic University',
                     ],
                    483
                     => [
                         'domain' => 'fsu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'fl',
                         'name' => 'Florida State University',
                     ],
                    484
                     => [
                         'domain' => 'ucf.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'fl',
                         'name' => 'University of Central Florida',
                     ],
                    485
                     => [
                         'domain' => 'ufl.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'fl',
                         'name' => 'University of Florida',
                     ],
                    486
                     => [
                         'domain' => 'usf.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'fl',
                         'name' => 'University of South Florida',
                     ],
                    487
                     => [
                         'domain' => 'bellsouth.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'ga',
                         'name' => 'Bellsouth Internet Services',
                     ],
                    488
                     => [
                         'domain' => 'cdc.gov',
                         'class' => '3',
                         'country' => 'us',
                         'state' => 'ga',
                         'name' => 'Centers for Disease Control and Prevention',
                     ],
                    489
                     => [
                         'domain' => 'cau.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ga',
                         'name' => 'Clark Atlanta University',
                     ],
                    490
                     => [
                         'domain' => 'coastalnow.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'ga',
                         'name' => 'Coastal Communications',
                     ],
                    491
                     => [
                         'domain' => 'cox.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'ga',
                         'name' => 'Cox Communications',
                     ],
                    492
                     => [
                         'domain' => 'earthlink.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'ga',
                         'name' => 'Earthlink, Inc.',
                     ],
                    493
                     => [
                         'domain' => 'lightspeed.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'ga',
                         'name' => 'Earthlink, Inc.',
                     ],
                    494
                     => [
                         'domain' => 'mindspring.com',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'ga',
                         'name' => 'Earthlink, Inc.',
                     ],
                    495
                     => [
                         'domain' => 'gatech.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ga',
                         'name' => 'Georgia Institute of Technology',
                     ],
                    496
                     => [
                         'domain' => 'knology.net',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'ga',
                         'name' => 'KNOLOGY Holdings, Inc.',
                     ],
                    497
                     => [
                         'domain' => 'lordaecksargent.com',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'ga',
                         'name' => 'Lord, Aeck & Sargent',
                     ],
                    498
                     => [
                         'domain' => 'noment.net',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'ga',
                         'name' => 'Noment Networks',
                     ],
                    499
                     => [
                         'domain' => 'ameslab.gov',
                         'class' => '3',
                         'country' => 'us',
                         'state' => 'ia',
                         'name' => 'DOE Ames Laboratory (Iowa State',
                     ],
                    500
                     => [
                         'domain' => 'iastate.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ia',
                         'name' => 'Iowa State University',
                     ],
                    501
                     => [
                         'domain' => 'micron.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'id',
                         'name' => 'Micron Technology',
                     ],
                    502
                     => [
                         'domain' => 'tcd.ie',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ie',
                         'name' => 'University of Dublin Trinity College',
                     ],
                    503
                     => [
                         'domain' => 'prserv.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'il',
                         'name' => 'AT&T Global Network Services',
                     ],
                    504
                     => [
                         'domain' => 'bradley.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'il',
                         'name' => 'Bradley University',
                     ],
                    505
                     => [
                         'domain' => 'dmisinetworks.com',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'il',
                         'name' => 'Fusion Broadband',
                     ],
                    506
                     => [
                         'domain' => 'dmisinetworks.net',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'il',
                         'name' => 'Fusion Broadband',
                     ],
                    507
                     => [
                         'domain' => 'iit.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'il',
                         'name' => 'Illinois Institute of Technology',
                     ],
                    508
                     => [
                         'domain' => 'flexabit.net',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'il',
                         'name' => 'McLeodUSA',
                     ],
                    509
                     => [
                         'domain' => 'motorola.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'il',
                         'name' => 'Motorola Inc.',
                     ],
                    510
                     => [
                         'domain' => 'sc03.org',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'il',
                         'name' => 'National Center for Supercomputing Applications',
                     ],
                    511
                     => [
                         'domain' => 'niu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'il',
                         'name' => 'Northern Illinois University',
                     ],
                    512
                     => [
                         'domain' => 'northwestern.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'il',
                         'name' => 'Northwestern University',
                     ],
                    513
                     => [
                         'domain' => 'procommcable.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'il',
                         'name' => 'Procomm Solutions',
                     ],
                    514
                     => [
                         'domain' => 'scnet.net',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'il',
                         'name' => 'Server Central Network',
                     ],
                    515
                     => [
                         'domain' => 'siu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'il',
                         'name' => 'Southern Illinois University',
                     ],
                    516
                     => [
                         'domain' => 'popsite.net',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'il',
                         'name' => 'StarNet, Inc.',
                     ],
                    517
                     => [
                         'domain' => 'travelclick.net',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'il',
                         'name' => 'TravelCLICK Inc.',
                     ],
                    518
                     => [
                         'domain' => 'uchicago.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'il',
                         'name' => 'University of Chicago',
                     ],
                    519
                     => [
                         'domain' => 'uic.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'il',
                         'name' => 'University of Illinois at Chicago',
                     ],
                    520
                     => [
                         'domain' => 'uiuc.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'il',
                         'name' => 'University of Illinois at Urbana Champaign',
                     ],
                    521
                     => [
                         'domain' => 'bsu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'in',
                         'name' => 'Ball State University',
                     ],
                    522
                     => [
                         'domain' => 'bloomingdaletel.com',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'in',
                         'name' => 'Bloomingdale Telephone',
                     ],
                    523
                     => [
                         'domain' => 'ffni.com',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'in',
                         'name' => 'Fairnet, LLC.',
                     ],
                    524
                     => [
                         'domain' => 'in-motion.net',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'in',
                         'name' => 'In-Motion Inc.',
                     ],
                    525
                     => [
                         'domain' => 'indiana.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'in',
                         'name' => 'Indiana University',
                     ],
                    526
                     => [
                         'domain' => 'iupui.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'in',
                         'name' => 'Indiana University-Purdue University at Indianapolis',
                     ],
                    527
                     => [
                         'domain' => 'ssi-pci.net',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'in',
                         'name' => 'NameMyDots.Com, Inc.',
                     ],
                    528
                     => [
                         'domain' => 'purdue.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'in',
                         'name' => 'Purdue University',
                     ],
                    529
                     => [
                         'domain' => 'riverwalk-apts.com',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'in',
                         'name' => 'River Walk Apartments',
                     ],
                    530
                     => [
                         'domain' => 'nd.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'in',
                         'name' => 'University of Notre Dame',
                     ],
                    531
                     => [
                         'domain' => 'wintek.com',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'in',
                         'name' => 'Wintek Corporation',
                     ],
                    532
                     => [
                         'domain' => 'ksu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ks',
                         'name' => 'Kansas State University',
                     ],
                    533
                     => [
                         'domain' => 'dialsprint.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'ks',
                         'name' => 'Sprint Communications Company L.P.',
                     ],
                    534
                     => [
                         'domain' => 'sprintbbd.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'ks',
                         'name' => 'Sprint Communications Company L.P.',
                     ],
                    535
                     => [
                         'domain' => 'spcsdns.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'ks',
                         'name' => 'Sprint PCS',
                     ],
                    536
                     => [
                         'domain' => 'louisville.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ky',
                         'name' => 'University of Louisville',
                     ],
                    537
                     => [
                         'domain' => 'lsu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'la',
                         'name' => 'Louisiana State University',
                     ],
                    538
                     => [
                         'domain' => 'louisiana.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'la',
                         'name' => 'University of Louisiana at Lafayette',
                     ],
                    539
                     => [
                         'domain' => 'bu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ma',
                         'name' => 'Boston University',
                     ],
                    540
                     => [
                         'domain' => 'conversent.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'ma',
                         'name' => 'Conversent Communications',
                     ],
                    541
                     => [
                         'domain' => 'harvard.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ma',
                         'name' => 'Harvard University',
                     ],
                    542
                     => [
                         'domain' => 'hhpublications.com',
                         'class' => '6',
                         'country' => 'us',
                         'state' => 'ma',
                         'name' => 'Horizon House Publications',
                     ],
                    543
                     => [
                         'domain' => 'mitre.org',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ma',
                         'name' => 'MITRE Corporation',
                     ],
                    544
                     => [
                         'domain' => 'mit.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ma',
                         'name' => 'Massachusetts Institute of Technology',
                     ],
                    545
                     => [
                         'domain' => 'neu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ma',
                         'name' => 'Northeastern University',
                     ],
                    546
                     => [
                         'domain' => 'raytheon.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ma',
                         'name' => 'Raytheon Company',
                     ],
                    547
                     => [
                         'domain' => 'townisp.com',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'ma',
                         'name' => 'SHREWSBURY ELECTRIC & COMMUNITY CABLE',
                     ],
                    548
                     => [
                         'domain' => 'umass.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ma',
                         'name' => 'University of Massachusetts',
                     ],
                    549
                     => [
                         'domain' => 'wpi.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ma',
                         'name' => 'Worcester Polytechnic Institute',
                     ],
                    550
                     => [
                         'domain' => 'w3.org',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ma',
                         'name' => 'World Wide Web Consortium',
                     ],
                    551
                     => [
                         'domain' => 'chesapeake.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'md',
                         'name' => 'Chesapeake College',
                     ],
                    552
                     => [
                         'domain' => 'dmv.com',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'md',
                         'name' => 'DelMarVa OnLine',
                     ],
                    553
                     => [
                         'domain' => 'morgan.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'md',
                         'name' => 'Morgan State University',
                     ],
                    554
                     => [
                         'domain' => 'tsi-telsys.com',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'md',
                         'name' => 'TSI Telsys, Inc.',
                     ],
                    555
                     => [
                         'domain' => 'umd.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'md',
                         'name' => 'University of Maryland',
                     ],
                    556
                     => [
                         'domain' => 'delphi.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'mi',
                         'name' => 'Delphi Automotive Systems',
                     ],
                    557
                     => [
                         'domain' => 'dow.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'mi',
                         'name' => 'Dow Chemical Company',
                     ],
                    558
                     => [
                         'domain' => 'gvsu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'mi',
                         'name' => 'Grand Valley State University',
                     ],
                    559
                     => [
                         'domain' => 'msu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'mi',
                         'name' => 'Michigan State University',
                     ],
                    560
                     => [
                         'domain' => 'mtu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'mi',
                         'name' => 'Michigan Technological University',
                     ],
                    561
                     => [
                         'domain' => 'qltd.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'mi',
                         'name' => 'QLTD',
                     ],
                    562
                     => [
                         'domain' => 'ussignalcom.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'mi',
                         'name' => 'RVP Development',
                     ],
                    563
                     => [
                         'domain' => 'umich.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'mi',
                         'name' => 'University of Michigan',
                     ],
                    564
                     => [
                         'domain' => 'mmm.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'mn',
                         'name' => '3M Company',
                     ],
                    565
                     => [
                         'domain' => 'umn.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'mn',
                         'name' => 'University of Minnesota',
                     ],
                    566
                     => [
                         'domain' => 'charter.com',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'mo',
                         'name' => 'Charter Communications',
                     ],
                    567
                     => [
                         'domain' => 'charterpipeline.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'mo',
                         'name' => 'Charter Communications',
                     ],
                    568
                     => [
                         'domain' => 'charter-stl.com',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'mo',
                         'name' => 'Charter Communications, St Louis',
                     ],
                    569
                     => [
                         'domain' => 'umr.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'mo',
                         'name' => 'University of Missouri-Rolla',
                     ],
                    570
                     => [
                         'domain' => 'jsums.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ms',
                         'name' => 'Jackson State University',
                     ],
                    571
                     => [
                         'domain' => 'msstate.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ms',
                         'name' => 'Mississippi State University',
                     ],
                    572
                     => [
                         'domain' => 'montana.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'mt',
                         'name' => 'Montana State University',
                     ],
                    573
                     => [
                         'domain' => 'duke.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'nc',
                         'name' => 'Duke University',
                     ],
                    574
                     => [
                         'domain' => 'goodrich.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'nc',
                         'name' => 'Goodrich Corporation',
                     ],
                    575
                     => [
                         'domain' => 'ncsu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'nc',
                         'name' => 'North Carolina State University',
                     ],
                    576
                     => [
                         'domain' => 'uslec.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'nc',
                         'name' => 'USLEC Corp.',
                     ],
                    577
                     => [
                         'domain' => 'uncc.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'nc',
                         'name' => 'University of North Carolina at Charlotte',
                     ],
                    578
                     => [
                         'domain' => 'alhyde.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'nj',
                         'name' => 'A.L.Hyde Company',
                     ],
                    579
                     => [
                         'domain' => 'att.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'nj',
                         'name' => 'AT&T Corp.',
                     ],
                    580
                     => [
                         'domain' => 'adp.com',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'nj',
                         'name' => 'Automatic Data Processing, Inc.',
                     ],
                    581
                     => [
                         'domain' => 'ge.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'nj',
                         'name' => 'General Electric Company',
                     ],
                    582
                     => [
                         'domain' => 'njit.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'nj',
                         'name' => 'New Jersey Institute of Technology',
                     ],
                    583
                     => [
                         'domain' => 'pppl.gov',
                         'class' => '3',
                         'country' => 'us',
                         'state' => 'nj',
                         'name' => 'Princeton Plasma Physics Laboratory',
                     ],
                    584
                     => [
                         'domain' => 'princeton.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'nj',
                         'name' => 'Princeton University',
                     ],
                    585
                     => [
                         'domain' => 'rcn.com',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'nj',
                         'name' => 'RCN',
                     ],
                    586
                     => [
                         'domain' => 'smartcity.com',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'nj',
                         'name' => 'Smart City Solutions',
                     ],
                    587
                     => [
                         'domain' => 'gigablast.com',
                         'class' => '5',
                         'country' => 'us',
                         'state' => 'nm',
                         'name' => 'Matt Wells',
                     ],
                    588
                     => [
                         'domain' => 'nmsu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'nm',
                         'name' => 'New Mexico State University',
                     ],
                    589
                     => [
                         'domain' => 'unlv.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'nv',
                         'name' => 'University of Nevada at Las Vegas',
                     ],
                    590
                     => [
                         'domain' => 'attbi.com',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'AT&T Corp. (Comcast',
                     ],
                    591
                     => [
                         'domain' => 'bnl.gov',
                         'class' => '3',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'Brookhaven National Laboratory',
                     ],
                    592
                     => [
                         'domain' => 'optonline.net',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'CSC Holdings, Inc',
                     ],
                    593
                     => [
                         'domain' => 'choiceone.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'Choice One OnLine, Inc.',
                     ],
                    594
                     => [
                         'domain' => 'cloud9.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'Cloud 9 Internet',
                     ],
                    595
                     => [
                         'domain' => 'columbia.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'Columbia University',
                     ],
                    596
                     => [
                         'domain' => 'cornell.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'Cornell University',
                     ],
                    597
                     => [
                         'domain' => 'corning.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'Corning Incorporated',
                     ],
                    598
                     => [
                         'domain' => 'deshaw.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'D. E. Shaw & Co., L. P.',
                     ],
                    599
                     => [
                         'domain' => 'kodak.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'Eastman Kodak',
                     ],
                    600
                     => [
                         'domain' => 'edelman.com',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'Edelman Public Relations',
                     ],
                    601
                     => [
                         'domain' => 'ibm.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'IBM Corporation',
                     ],
                    602
                     => [
                         'domain' => 'insightbb.com',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'Insight Communications',
                     ],
                    603
                     => [
                         'domain' => 'mchsi.com',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'Mediacom Communications Corporation',
                     ],
                    604
                     => [
                         'domain' => 'midtel.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'Middleburg Telephone Co.',
                     ],
                    605
                     => [
                         'domain' => 'rpi.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'Rensselaer Polytechnic Institute',
                     ],
                    606
                     => [
                         'domain' => 'rit.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'Rochester Institute of Technology',
                     ],
                    607
                     => [
                         'domain' => 'russreyn.com',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'Russell Reynolds Associates Inc',
                     ],
                    608
                     => [
                         'domain' => 'sunyit.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'SUNY Institute of Technology at Utica/Rome',
                     ],
                    609
                     => [
                         'domain' => 'binghamton.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'SUNY at Binghamton',
                     ],
                    610
                     => [
                         'domain' => 'sunysb.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'SUNY at Stony Brook',
                     ],
                    611
                     => [
                         'domain' => 'buffalo.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'State University of New York at Buffalo',
                     ],
                    612
                     => [
                         'domain' => 'stonybrook.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'State University of New York/Stony Brook',
                     ],
                    613
                     => [
                         'domain' => 'syr.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'Syracuse University',
                     ],
                    614
                     => [
                         'domain' => 'xerox.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'Xerox Corporation',
                     ],
                    615
                     => [
                         'domain' => 'fuse.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'oh',
                         'name' => 'Cincinnati Bell Telephone',
                     ],
                    616
                     => [
                         'domain' => 'convergys.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'oh',
                         'name' => 'Convergys Inc',
                     ],
                    617
                     => [
                         'domain' => 'muohio.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'oh',
                         'name' => 'Miami University',
                     ],
                    618
                     => [
                         'domain' => 'ohio-state.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'oh',
                         'name' => 'Ohio State University',
                     ],
                    619
                     => [
                         'domain' => 'ohiou.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'oh',
                         'name' => 'Ohio University',
                     ],
                    620
                     => [
                         'domain' => 'sierralobo.com',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'oh',
                         'name' => 'Sierra Lobo, Inc.',
                     ],
                    621
                     => [
                         'domain' => 'uc.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'oh',
                         'name' => 'University of Cincinnati',
                     ],
                    622
                     => [
                         'domain' => 'utoledo.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'oh',
                         'name' => 'University of Toledo',
                     ],
                    623
                     => [
                         'domain' => 'okstate.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ok',
                         'name' => 'Oklahoma State University',
                     ],
                    624
                     => [
                         'domain' => 'ou.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ok',
                         'name' => 'University of Oklahoma',
                     ],
                    625
                     => [
                         'domain' => 'mentorg.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'or',
                         'name' => 'Mentor Graphics Corporation',
                     ],
                    626
                     => [
                         'domain' => 'ogi.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'or',
                         'name' => 'OGI School of Science and Engineering',
                     ],
                    627
                     => [
                         'domain' => 'pdx.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'or',
                         'name' => 'Portland State University',
                     ],
                    628
                     => [
                         'domain' => 'adelphia.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'pa',
                         'name' => 'Adelphia Communications Corp.',
                     ],
                    629
                     => [
                         'domain' => 'agere.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'pa',
                         'name' => 'Agere Systems, Inc.',
                     ],
                    630
                     => [
                         'domain' => 'zoominternet.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'pa',
                         'name' => 'Armstrong Cable Services',
                     ],
                    631
                     => [
                         'domain' => 'cmu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'pa',
                         'name' => 'Carnegie-Mellon University',
                     ],
                    632
                     => [
                         'domain' => 'comcast.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'pa',
                         'name' => 'Comcast Corporation',
                     ],
                    633
                     => [
                         'domain' => 'lutron.com',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'pa',
                         'name' => 'Lutron Electronics Co., Inc.',
                     ],
                    634
                     => [
                         'domain' => 'psu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'pa',
                         'name' => 'Pennsylvania State University',
                     ],
                    635
                     => [
                         'domain' => 'temple.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'pa',
                         'name' => 'Temple University',
                     ],
                    636
                     => [
                         'domain' => 'upenn.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'pa',
                         'name' => 'University of Pennsylvania',
                     ],
                    637
                     => [
                         'domain' => 'pitt.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'pa',
                         'name' => 'University of Pittsburgh',
                     ],
                    638
                     => [
                         'domain' => 'hks.com',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'ri',
                         'name' => 'ABAQUS, Inc',
                     ],
                    639
                     => [
                         'domain' => 'brown.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ri',
                         'name' => 'Brown University',
                     ],
                    640
                     => [
                         'domain' => 'lodgenet.net',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'sd',
                         'name' => 'LodgeNet Entertainment Company',
                     ],
                    641
                     => [
                         'domain' => 'cti-pet.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'tn',
                         'name' => 'CTI Molecular Imaging, Inc.',
                     ],
                    642
                     => [
                         'domain' => 'utk.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'tn',
                         'name' => 'University of Tennessee',
                     ],
                    643
                     => [
                         'domain' => 'vanderbilt.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'tn',
                         'name' => 'Vanderbilt University',
                     ],
                    644
                     => [
                         'domain' => 'cox-internet.com',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'Cox Communications',
                     ],
                    645
                     => [
                         'domain' => 'ev1servers.net',
                         'class' => '5',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'Ev1Servers.net',
                     ],
                    646
                     => [
                         'domain' => 'freescale.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'Freescale Semiconductor, Inc.',
                     ],
                    647
                     => [
                         'domain' => 'rice.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'Rice University',
                     ],
                    648
                     => [
                         'domain' => 'ameritech.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'SBC Internet Services, Inc.',
                     ],
                    649
                     => [
                         'domain' => 'pacbell.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'SBC Internet Services, Inc.',
                     ],
                    650
                     => [
                         'domain' => 'snet.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'SBC Internet Services, Inc.',
                     ],
                    651
                     => [
                         'domain' => 'swbell.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'SBC Internet Services, Inc.',
                     ],
                    652
                     => [
                         'domain' => 'swri.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'Southwest Research Institute, San Antonio',
                     ],
                    653
                     => [
                         'domain' => 'tamu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'Texas A&M University',
                     ],
                    654
                     => [
                         'domain' => 'tamuk.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'Texas A&M University - Kingsville',
                     ],
                    655
                     => [
                         'domain' => 'ti.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'Texas Instruments, Inc.',
                     ],
                    656
                     => [
                         'domain' => 'ttu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'Texas Tech University',
                     ],
                    657
                     => [
                         'domain' => 'theplanet.com',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'The Planet Internet Services, Inc.',
                     ],
                    658
                     => [
                         'domain' => 'uh.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'University of Houston',
                     ],
                    659
                     => [
                         'domain' => 'uta.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'University of Texas at Arlington',
                     ],
                    660
                     => [
                         'domain' => 'utexas.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'University of Texas at Austin',
                     ],
                    661
                     => [
                         'domain' => 'utep.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'University of Texas at El Paso',
                     ],
                    662
                     => [
                         'domain' => 'utsa.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'University of Texas at San Antonio',
                     ],
                    663
                     => [
                         'domain' => 'verizon.net',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'Verison',
                     ],
                    664
                     => [
                         'domain' => 'dsl-verizon.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'Verizon',
                     ],
                    665
                     => [
                         'domain' => 'vzavenue.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'Verizon',
                     ],
                    666
                     => [
                         'domain' => 'usu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ut',
                         'name' => 'Utah State University',
                     ],
                    667
                     => [
                         'domain' => 'stsn.com',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'ut',
                         'name' => 'iBAHN (formerly STSN',
                     ],
                    668
                     => [
                         'domain' => 'aol.com',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'va',
                         'name' => 'America Online, Inc.',
                     ],
                    669
                     => [
                         'domain' => 'bnsi.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'va',
                         'name' => 'Broadband Network Services Inc.',
                     ],
                    670
                     => [
                         'domain' => 'cryptic.net',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'va',
                         'name' => 'Cryptic Net Communications',
                     ],
                    671
                     => [
                         'domain' => 'gmai.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'va',
                         'name' => 'GMA Industries, Inc.',
                     ],
                    672
                     => [
                         'domain' => 'jrmtech.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'va',
                         'name' => 'Joseph Russ Moulton, jr.',
                     ],
                    673
                     => [
                         'domain' => 'marketscore.com',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'va',
                         'name' => 'Marketscore Inc',
                     ],
                    674
                     => [
                         'domain' => 'ntc-com.net',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'va',
                         'name' => 'NTC, LLC',
                     ],
                    675
                     => [
                         'domain' => 'newskies.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'va',
                         'name' => 'New Skies Networks, Inc.',
                     ],
                    676
                     => [
                         'domain' => 'rr.com',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'va',
                         'name' => 'Road Runner Hold Co, LLC',
                     ],
                    677
                     => [
                         'domain' => 'src.org',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'va',
                         'name' => 'Semiconductor Research Corporation',
                     ],
                    678
                     => [
                         'domain' => 'sysplan.com',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'va',
                         'name' => 'System Planning Corporation',
                     ],
                    679
                     => [
                         'domain' => 'uu.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'va',
                         'name' => 'UUNET Technologies, Inc.',
                     ],
                    680
                     => [
                         'domain' => 'virginia.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'va',
                         'name' => 'University of Virginia',
                     ],
                    681
                     => [
                         'domain' => 'gte.net',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'va',
                         'name' => 'Verizon',
                     ],
                    682
                     => [
                         'domain' => 'cray.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'wa',
                         'name' => 'Cray Inc.',
                     ],
                    683
                     => [
                         'domain' => 'ncplus.net',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'wa',
                         'name' => 'Komko, Inc',
                     ],
                    684
                     => [
                         'domain' => 'search.msn.com',
                         'class' => '5',
                         'country' => 'us',
                         'state' => 'wa',
                         'name' => 'Microsoft',
                     ],
                    685
                     => [
                         'domain' => 'msn.com',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'wa',
                         'name' => 'Microsoft Corporation',
                     ],
                    686
                     => [
                         'domain' => 'transedge.com',
                         'class' => '0',
                         'country' => 'us',
                         'state' => 'wa',
                         'name' => 'New Edge Networks.',
                     ],
                    687
                     => [
                         'domain' => 'nctv.com',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'wa',
                         'name' => 'Northland Cable Television',
                     ],
                    688
                     => [
                         'domain' => 'pnl.gov',
                         'class' => '3',
                         'country' => 'us',
                         'state' => 'wa',
                         'name' => 'Pacific Northwest National Laboratory',
                     ],
                    689
                     => [
                         'domain' => 'tmodns.net',
                         'class' => '4',
                         'country' => 'us',
                         'state' => 'wa',
                         'name' => 'T-Mobile USA',
                     ],
                    690
                     => [
                         'domain' => 'washington.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'wa',
                         'name' => 'University of Washington',
                     ],
                    691
                     => [
                         'domain' => 'wsu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'wa',
                         'name' => 'Washington State University',
                     ],
                    692
                     => [
                         'domain' => 'wisc.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'wi',
                         'name' => 'University of Wisconsin',
                     ],
                    693
                     => [
                         'domain' => 'cantv.net',
                         'class' => '0',
                         'country' => 've',
                         'state' => '',
                         'name' => 'CANTV Servicios, Caracas',
                     ],
                    694
                     => [
                         'domain' => 'genesisbci.net',
                         'class' => '0',
                         'country' => 've',
                         'state' => '',
                         'name' => 'Genesis Telecom C.A.',
                     ],
                    695
                     => [
                         'domain' => 'bg.ac.yu',
                         'class' => '1',
                         'country' => 'yu',
                         'state' => '',
                         'name' => 'Univerzitet u Beogradu',
                     ],
                    696
                     => [
                         'domain' => 'saix.net',
                         'class' => '0',
                         'country' => 'za',
                         'state' => '',
                         'name' => 'Telkom SA Ltd',
                     ],
                    697
                     => [
                         'domain' => 'sfu.ca',
                         'class' => '1',
                         'country' => 'ca',
                         'state' => 'bc',
                         'name' => 'Simon Fraser University',
                     ],
                    698
                     => [
                         'domain' => 'uwaterloo.ca',
                         'class' => '1',
                         'country' => 'ca',
                         'state' => 'on',
                         'name' => 'University of Waterloo',
                     ],
                    699
                     => [
                         'domain' => 'auburn.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'al',
                         'name' => 'Auburn University',
                     ],
                    700
                     => [
                         'domain' => 'uark.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ar',
                         'name' => 'University of Arkansas, Fayetteville',
                     ],
                    701
                     => [
                         'domain' => 'csupomona.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'California State Polytechnic University, Pomona',
                     ],
                    702
                     => [
                         'domain' => 'glendale.cc.ca.us',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Glendale Community College',
                     ],
                    703
                     => [
                         'domain' => 'untd.com',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'United Online, Inc.',
                     ],
                    704
                     => [
                         'domain' => 'ucsc.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'University of California, Santa Cruz',
                     ],
                    705
                     => [
                         'domain' => 'du.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'co',
                         'name' => 'University of Denver',
                     ],
                    706
                     => [
                         'domain' => 'yale.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ct',
                         'name' => 'Yale University',
                     ],
                    707
                     => [
                         'domain' => 'fiu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'fl',
                         'name' => 'Florida International University',
                     ],
                    708
                     => [
                         'domain' => 'uiowa.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ia',
                         'name' => 'University of Iowa',
                     ],
                    709
                     => [
                         'domain' => 'sxu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'il',
                         'name' => 'Saint Xavier University',
                     ],
                    710
                     => [
                         'domain' => 'uno.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'la',
                         'name' => 'University of New Orleans',
                     ],
                    711
                     => [
                         'domain' => 'xula.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'la',
                         'name' => 'Xavier University of Louisiana',
                     ],
                    712
                     => [
                         'domain' => 'bc.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ma',
                         'name' => 'Boston College',
                     ],
                    713
                     => [
                         'domain' => 'kopin.com',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ma',
                         'name' => 'Kopin Corporation',
                     ],
                    714
                     => [
                         'domain' => 'jhu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'md',
                         'name' => 'Johns Hopkins University',
                     ],
                    715
                     => [
                         'domain' => 'umbc.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'md',
                         'name' => 'University of Maryland Baltimore County',
                     ],
                    716
                     => [
                         'domain' => 'wayne.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'mi',
                         'name' => 'Wayne State University',
                     ],
                    717
                     => [
                         'domain' => 'mnscu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'mn',
                         'name' => 'Minnesota State Colleges and Universities',
                     ],
                    718
                     => [
                         'domain' => 'wfu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'nc',
                         'name' => 'Wake Forest University',
                     ],
                    719
                     => [
                         'domain' => 'unh.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'nh',
                         'name' => 'University of New Hampshire',
                     ],
                    720
                     => [
                         'domain' => 'rowan.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'nj',
                         'name' => 'Rowan College of New Jersey',
                     ],
                    721
                     => [
                         'domain' => 'unm.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'nm',
                         'name' => 'University of New Mexico',
                     ],
                    722
                     => [
                         'domain' => 'nyit.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'New York Institute of Technology',
                     ],
                    723
                     => [
                         'domain' => 'nyu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'New York University',
                     ],
                    724
                     => [
                         'domain' => 'albany.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'State University of New York, Albany',
                     ],
                    725
                     => [
                         'domain' => 'usma.army.mil',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'US Military Academy at West Point',
                     ],
                    726
                     => [
                         'domain' => 'lehigh.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'pa',
                         'name' => 'Lehigh University',
                     ],
                    727
                     => [
                         'domain' => 'sdsmt.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'sd',
                         'name' => 'South Dakota School of Mines & Technology',
                     ],
                    728
                     => [
                         'domain' => 'cbu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'tn',
                         'name' => 'Christian Brothers University',
                     ],
                    729
                     => [
                         'domain' => 'accd.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'Alamo Community College District',
                     ],
                    730
                     => [
                         'domain' => 'smu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'Southern Methodist University',
                     ],
                    731
                     => [
                         'domain' => 'utah.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'ut',
                         'name' => 'University of Utah',
                     ],
                    732
                     => [
                         'domain' => 'odu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'va',
                         'name' => 'Old Dominion University',
                     ],
                    733
                     => [
                         'domain' => 'vcu.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'va',
                         'name' => 'Virginia Commonwealth University',
                     ],
                    734
                     => [
                         'domain' => 'cvtc.edu',
                         'class' => '1',
                         'country' => 'us',
                         'state' => 'wi',
                         'name' => 'Chippewa Valley Technical College',
                     ],
                    735
                     => [
                         'domain' => 'celestica.com',
                         'class' => '2',
                         'country' => 'ca',
                         'state' => 'on',
                         'name' => 'Celestica, Inc',
                     ],
                    736
                     => [
                         'domain' => 'sogetel.net',
                         'class' => '2',
                         'country' => 'ca',
                         'state' => 'qb',
                         'name' => 'Sogetel inc.',
                     ],
                    737
                     => [
                         'domain' => 'cypress.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Cypress Semiconductor',
                     ],
                    738
                     => [
                         'domain' => 'gat.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'General Atomics',
                     ],
                    739
                     => [
                         'domain' => 'hp.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Hewlett-Packard Company',
                     ],
                    740
                     => [
                         'domain' => 'northropgrumman.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Northrop Grumman Corporation',
                     ],
                    741
                     => [
                         'domain' => 'qualcomm.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Qualcomm Inc.',
                     ],
                    742
                     => [
                         'domain' => 'quantum.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Quantum Corporation',
                     ],
                    743
                     => [
                         'domain' => 'rdl.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Research & Development Laboratories',
                     ],
                    744
                     => [
                         'domain' => 'itnes.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'co',
                         'name' => 'ITN Energy Systems',
                     ],
                    745
                     => [
                         'domain' => 'lilly.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'in',
                         'name' => 'Eli Lilly and Company',
                     ],
                    746
                     => [
                         'domain' => 'guidant.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'in',
                         'name' => 'Guidant Corporation',
                     ],
                    747
                     => [
                         'domain' => 'htch.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'mn',
                         'name' => 'Hutchinson Technology Inc.',
                     ],
                    748
                     => [
                         'domain' => 'rea-alp.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'mn',
                         'name' => 'Runestone Electric Association',
                     ],
                    749
                     => [
                         'domain' => 'honeywell.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'nj',
                         'name' => 'Honeywell International Inc',
                     ],
                    750
                     => [
                         'domain' => 'fluentenergy.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'ny',
                         'name' => 'Fluent Energy',
                     ],
                    751
                     => [
                         'domain' => 'spectral-sys.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'oh',
                         'name' => 'Spectral Systems Inc',
                     ],
                    752
                     => [
                         'domain' => 'cmicro.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'or',
                         'name' => 'Cascade Microtech Inc.',
                     ],
                    753
                     => [
                         'domain' => 'mxim.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'or',
                         'name' => 'Maxim Integrated Products',
                     ],
                    754
                     => [
                         'domain' => 'nanomat.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'pa',
                         'name' => 'Nanomat, Inc.',
                     ],
                    755
                     => [
                         'domain' => 'amd.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'Advanced Micro Devices, Inc.',
                     ],
                    756
                     => [
                         'domain' => 'natinst.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'National Instruments Corporation',
                     ],
                    757
                     => [
                         'domain' => 'sematech.org',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'tx',
                         'name' => 'Sematech',
                     ],
                    758
                     => [
                         'domain' => 'fedsources.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'va',
                         'name' => 'Federal Sources Inc.',
                     ],
                    759
                     => [
                         'domain' => 'scitor.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'va',
                         'name' => 'Scitor Corporation',
                     ],
                    760
                     => [
                         'domain' => 'cit.org',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'va',
                         'name' => 'Virginia\\\'s Center for Innovation',
                     ],
                    761
                     => [
                         'domain' => 'boeing.com',
                         'class' => '2',
                         'country' => 'us',
                         'state' => 'wa',
                         'name' => 'Boeing Company',
                     ],
                    762
                     => [
                         'domain' => 'lbl.gov',
                         'class' => '3',
                         'country' => 'us',
                         'state' => 'ca',
                         'name' => 'Lawernce Berkeley National Laboratory',
                     ],
                    763
                     => [
                         'domain' => 'fda.gov',
                         'class' => '3',
                         'country' => 'us',
                         'state' => 'dc',
                         'name' => 'Food and Drug Administration',
                     ],
                    764
                     => [
                         'domain' => 'anl-external.org',
                         'class' => '3',
                         'country' => 'us',
                         'state' => 'il',
                         'name' => 'Argonne National Lab',
                     ],
                    765
                     => [
                         'domain' => 'fnal.gov',
                         'class' => '3',
                         'country' => 'us',
                         'state' => 'il',
                         'name' => 'Fermi National Laboratory',
                     ],
                    766
                     => [
                         'domain' => 'state.nj.us',
                         'class' => '3',
                         'country' => 'us',
                         'state' => 'nj',
                         'name' => 'NJ Office of Information Technology',
                     ],
                    767
                     => [
                         'domain' => 'sandia.gov',
                         'class' => '3',
                         'country' => 'us',
                         'state' => 'nm',
                         'name' => 'Sandia Natational Laboratory',
                     ],
                    768
                     => [
                         'domain' => 'wpafb.af.mil',
                         'class' => '3',
                         'country' => 'us',
                         'state' => 'oh',
                         'name' => 'Wright Patterson AFB',
                     ],
                    769
                     => [
                         'domain' => 'iisc.ernet.in',
                         'class' => '1',
                         'country' => 'in',
                         'state' => '',
                         'name' => 'Indian Institute of Science',
                     ],
                    770
                     => [
                         'domain' => 'nanohub.org',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    771
                     => [
                         'domain' => 'zntu.edu.ua',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    772
                     => [
                         'domain' => 'zambonet.edu.ph',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    773
                     => [
                         'domain' => 'yzu.edu.cn',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    774
                     => [
                         'domain' => 'yuntech.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    775
                     => [
                         'domain' => 'yu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    776
                     => [
                         'domain' => 'ysu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    777
                     => [
                         'domain' => 'ym.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    778
                     => [
                         'domain' => 'ycrc.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    779
                     => [
                         'domain' => 'ycp.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    780
                     => [
                         'domain' => 'xmu.edu.cn',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    781
                     => [
                         'domain' => 'xjtu.edu.cn',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    782
                     => [
                         'domain' => 'wwu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    783
                     => [
                         'domain' => 'wwc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    784
                     => [
                         'domain' => 'wvu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    785
                     => [
                         'domain' => 'wustl.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    786
                     => [
                         'domain' => 'wse.edu.pl',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    787
                     => [
                         'domain' => 'wright.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    788
                     => [
                         'domain' => 'worcester.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    789
                     => [
                         'domain' => 'wofford.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    790
                     => [
                         'domain' => 'wnmu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    791
                     => [
                         'domain' => 'wnec.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    792
                     => [
                         'domain' => 'wmich.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    793
                     => [
                         'domain' => 'wm.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    794
                     => [
                         'domain' => 'wlc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    795
                     => [
                         'domain' => 'wku.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    796
                     => [
                         'domain' => 'wiu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    797
                     => [
                         'domain' => 'wittenberg.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    798
                     => [
                         'domain' => 'witc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    799
                     => [
                         'domain' => 'winona.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    800
                     => [
                         'domain' => 'wilmcoll.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    801
                     => [
                         'domain' => 'williams.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    802
                     => [
                         'domain' => 'wilkes.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    803
                     => [
                         'domain' => 'wichita.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    804
                     => [
                         'domain' => 'whu.edu.cn',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    805
                     => [
                         'domain' => 'whitworth.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    806
                     => [
                         'domain' => 'whitman.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    807
                     => [
                         'domain' => 'wheaton.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    808
                     => [
                         'domain' => 'westga.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    809
                     => [
                         'domain' => 'wesleyan.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    810
                     => [
                         'domain' => 'wednet.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    811
                     => [
                         'domain' => 'weber.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    812
                     => [
                         'domain' => 'wcu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    813
                     => [
                         'domain' => 'wat.edu.pl',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    814
                     => [
                         'domain' => 'washjeff.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    815
                     => [
                         'domain' => 'washburn.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    816
                     => [
                         'domain' => 'wartburgseminary.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    817
                     => [
                         'domain' => 'warren-wilson.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    818
                     => [
                         'domain' => 'walsh.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    819
                     => [
                         'domain' => 'waldenu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    820
                     => [
                         'domain' => 'vwc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    821
                     => [
                         'domain' => 'vu.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    822
                     => [
                         'domain' => 'vt.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    823
                     => [
                         'domain' => 'vsu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    824
                     => [
                         'domain' => 'vnu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    825
                     => [
                         'domain' => 'vims.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    826
                     => [
                         'domain' => 'villanova.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    827
                     => [
                         'domain' => 'viit.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    828
                     => [
                         'domain' => 'vic.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    829
                     => [
                         'domain' => 'vernoncollege.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    830
                     => [
                         'domain' => 'vccs.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    831
                     => [
                         'domain' => 'vc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    832
                     => [
                         'domain' => 'vassar.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    833
                     => [
                         'domain' => 'valpo.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    834
                     => [
                         'domain' => 'valdosta.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    835
                     => [
                         'domain' => 'uwyo.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    836
                     => [
                         'domain' => 'uwstout.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    837
                     => [
                         'domain' => 'uwsp.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    838
                     => [
                         'domain' => 'uws.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    839
                     => [
                         'domain' => 'uwp.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    840
                     => [
                         'domain' => 'uwm.edu.pl',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    841
                     => [
                         'domain' => 'uwm.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    842
                     => [
                         'domain' => 'uwf.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    843
                     => [
                         'domain' => 'uwec.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    844
                     => [
                         'domain' => 'uwb.edu.pl',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    845
                     => [
                         'domain' => 'uwa.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    846
                     => [
                         'domain' => 'uw.edu.pl',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    847
                     => [
                         'domain' => 'uvsc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    848
                     => [
                         'domain' => 'uvm.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    849
                     => [
                         'domain' => 'uvi.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    850
                     => [
                         'domain' => 'uu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    851
                     => [
                         'domain' => 'utulsa.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    852
                     => [
                         'domain' => 'uts.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    853
                     => [
                         'domain' => 'utn.edu.ec',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    854
                     => [
                         'domain' => 'utmem.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    855
                     => [
                         'domain' => 'utmb.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    856
                     => [
                         'domain' => 'utm.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    857
                     => [
                         'domain' => 'uthscsa.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    858
                     => [
                         'domain' => 'utdallas.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    859
                     => [
                         'domain' => 'utc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    860
                     => [
                         'domain' => 'utas.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    861
                     => [
                         'domain' => 'ust.edu.ph',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    862
                     => [
                         'domain' => 'usra.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    863
                     => [
                         'domain' => 'usna.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    864
                     => [
                         'domain' => 'usmd.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    865
                     => [
                         'domain' => 'usg.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    866
                     => [
                         'domain' => 'usfca.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    867
                     => [
                         'domain' => 'usd.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    868
                     => [
                         'domain' => 'usc.edu.ph',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    869
                     => [
                         'domain' => 'usc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    870
                     => [
                         'domain' => 'us.edu.pl',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    871
                     => [
                         'domain' => 'uri.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    872
                     => [
                         'domain' => 'uq.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    873
                     => [
                         'domain' => 'ups.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    874
                     => [
                         'domain' => 'uprm.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    875
                     => [
                         'domain' => 'uprh.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    876
                     => [
                         'domain' => 'upr.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    877
                     => [
                         'domain' => 'upmc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    878
                     => [
                         'domain' => 'uplb.edu.ph',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    879
                     => [
                         'domain' => 'upf.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    880
                     => [
                         'domain' => 'upd.edu.ph',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    881
                     => [
                         'domain' => 'upc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    882
                     => [
                         'domain' => 'upb.edu.co',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    883
                     => [
                         'domain' => 'up.edu.ph',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    884
                     => [
                         'domain' => 'up.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    885
                     => [
                         'domain' => 'uow.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    886
                     => [
                         'domain' => 'uoregon.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    887
                     => [
                         'domain' => 'uophx.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    888
                     => [
                         'domain' => 'uop.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    889
                     => [
                         'domain' => 'uofs.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    890
                     => [
                         'domain' => 'uoa.edu.er',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    891
                     => [
                         'domain' => 'unt.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    892
                     => [
                         'domain' => 'unsw.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    893
                     => [
                         'domain' => 'uns.edu.ar',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    894
                     => [
                         'domain' => 'unrc.edu.ar',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    895
                     => [
                         'domain' => 'unr.edu.ar',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    896
                     => [
                         'domain' => 'unr.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    897
                     => [
                         'domain' => 'unomaha.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    898
                     => [
                         'domain' => 'unne.edu.ar',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    899
                     => [
                         'domain' => 'unmsm.edu.pe',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    900
                     => [
                         'domain' => 'unmc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    901
                     => [
                         'domain' => 'unlp.edu.ar',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    902
                     => [
                         'domain' => 'unl.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    903
                     => [
                         'domain' => 'unk.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    904
                     => [
                         'domain' => 'universia.edu.ve',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    905
                     => [
                         'domain' => 'univdhaka.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    906
                     => [
                         'domain' => 'univalle.edu.co',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    907
                     => [
                         'domain' => 'unisa.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    908
                     => [
                         'domain' => 'union.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    909
                     => [
                         'domain' => 'uninorte.edu.co',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    910
                     => [
                         'domain' => 'unimelb.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    911
                     => [
                         'domain' => 'unilibrecali.edu.co',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    912
                     => [
                         'domain' => 'uni.edu.pe',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    913
                     => [
                         'domain' => 'uni.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    914
                     => [
                         'domain' => 'unf.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    915
                     => [
                         'domain' => 'uner.edu.ar',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    916
                     => [
                         'domain' => 'une.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    917
                     => [
                         'domain' => 'une.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    918
                     => [
                         'domain' => 'uncw.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    919
                     => [
                         'domain' => 'unco.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    920
                     => [
                         'domain' => 'uncg.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    921
                     => [
                         'domain' => 'unca.edu.ar',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    922
                     => [
                         'domain' => 'unca.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    923
                     => [
                         'domain' => 'unc.edu.ar',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    924
                     => [
                         'domain' => 'unc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    925
                     => [
                         'domain' => 'unan.edu.ni',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    926
                     => [
                         'domain' => 'unam.edu.ar',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    927
                     => [
                         'domain' => 'unad.edu.co',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    928
                     => [
                         'domain' => 'una.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    929
                     => [
                         'domain' => 'umw.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    930
                     => [
                         'domain' => 'umuc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    931
                     => [
                         'domain' => 'umt.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    932
                     => [
                         'domain' => 'umsmed.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    933
                     => [
                         'domain' => 'umsl.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    934
                     => [
                         'domain' => 'umontana.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    935
                     => [
                         'domain' => 'uml.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    936
                     => [
                         'domain' => 'umkc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    937
                     => [
                         'domain' => 'umh.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    938
                     => [
                         'domain' => 'umdnj.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    939
                     => [
                         'domain' => 'umassmed.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    940
                     => [
                         'domain' => 'umassd.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    941
                     => [
                         'domain' => 'umaryland.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    942
                     => [
                         'domain' => 'umaine.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    943
                     => [
                         'domain' => 'um.edu.my',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    944
                     => [
                         'domain' => 'uludag.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    945
                     => [
                         'domain' => 'ulm.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    946
                     => [
                         'domain' => 'uky.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    947
                     => [
                         'domain' => 'uitm.edu.my',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    948
                     => [
                         'domain' => 'uis.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    949
                     => [
                         'domain' => 'uidaho.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    950
                     => [
                         'domain' => 'uhv.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    951
                     => [
                         'domain' => 'uga.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    952
                     => [
                         'domain' => 'ufsj.edu.br',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    953
                     => [
                         'domain' => 'ufcg.edu.br',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    954
                     => [
                         'domain' => 'ufam.edu.br',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    955
                     => [
                         'domain' => 'udea.edu.co',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    956
                     => [
                         'domain' => 'udayton.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    957
                     => [
                         'domain' => 'udallas.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    958
                     => [
                         'domain' => 'uctm.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    959
                     => [
                         'domain' => 'ucsf.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    960
                     => [
                         'domain' => 'ucsc-extension.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    961
                     => [
                         'domain' => 'ucop.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    962
                     => [
                         'domain' => 'ucok.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    963
                     => [
                         'domain' => 'ucmerced.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    964
                     => [
                         'domain' => 'uclv.edu.cu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    965
                     => [
                         'domain' => 'uchsc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    966
                     => [
                         'domain' => 'uchc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    967
                     => [
                         'domain' => 'uceou.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    968
                     => [
                         'domain' => 'uccs.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    969
                     => [
                         'domain' => 'ucatolica.edu.co',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    970
                     => [
                         'domain' => 'ucar.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    971
                     => [
                         'domain' => 'ubaguio.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    972
                     => [
                         'domain' => 'uan.edu.co',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    973
                     => [
                         'domain' => 'uams.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    974
                     => [
                         'domain' => 'ualr.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    975
                     => [
                         'domain' => 'uakron.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    976
                     => [
                         'domain' => 'uah.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    977
                     => [
                         'domain' => 'uab.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    978
                     => [
                         'domain' => 'ua.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    979
                     => [
                         'domain' => 'tyrc.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    980
                     => [
                         'domain' => 'txstate.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    981
                     => [
                         'domain' => 'tvi.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    982
                     => [
                         'domain' => 'tuskegee.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    983
                     => [
                         'domain' => 'tulane.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    984
                     => [
                         'domain' => 'tufts.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    985
                     => [
                         'domain' => 'ttu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    986
                     => [
                         'domain' => 'tsu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    987
                     => [
                         'domain' => 'truman.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    988
                     => [
                         'domain' => 'trnty.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    989
                     => [
                         'domain' => 'triton.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    990
                     => [
                         'domain' => 'trinity.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    991
                     => [
                         'domain' => 'trincoll.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    992
                     => [
                         'domain' => 'tridenttech.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    993
                     => [
                         'domain' => 'tpu.edu.ru',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    994
                     => [
                         'domain' => 'tpc.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    995
                     => [
                         'domain' => 'tp2rc.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    996
                     => [
                         'domain' => 'tp1rc.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    997
                     => [
                         'domain' => 'tp.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    998
                     => [
                         'domain' => 'toronto.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    999
                     => [
                         'domain' => 'tntech.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1000
                     => [
                         'domain' => 'tnrc.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1001
                     => [
                         'domain' => 'tmcc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1002
                     => [
                         'domain' => 'tmc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1003
                     => [
                         'domain' => 'tm.edu.ro',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1004
                     => [
                         'domain' => 'tlu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1005
                     => [
                         'domain' => 'tln.edu.ee',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1006
                     => [
                         'domain' => 'tku.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1007
                     => [
                         'domain' => 'tju.edu.cn',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1008
                     => [
                         'domain' => 'tju.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1009
                     => [
                         'domain' => 'thu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1010
                     => [
                         'domain' => 'tenet.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1011
                     => [
                         'domain' => 'tcu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1012
                     => [
                         'domain' => 'tcu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1013
                     => [
                         'domain' => 'tcnj.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1014
                     => [
                         'domain' => 'tc.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1015
                     => [
                         'domain' => 'tayloru.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1016
                     => [
                         'domain' => 'tarleton.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1017
                     => [
                         'domain' => 'tamucc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1018
                     => [
                         'domain' => 'tamu-commerce.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1019
                     => [
                         'domain' => 'tamiu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1020
                     => [
                         'domain' => 'sysu.edu.cn',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1021
                     => [
                         'domain' => 'swosu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1022
                     => [
                         'domain' => 'swmed.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1023
                     => [
                         'domain' => 'swinburne.edu.my',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1024
                     => [
                         'domain' => 'swin.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1025
                     => [
                         'domain' => 'swau.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1026
                     => [
                         'domain' => 'swarthmore.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1027
                     => [
                         'domain' => 'svsu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1028
                     => [
                         'domain' => 'svcc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1029
                     => [
                         'domain' => 'sunyrockland.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1030
                     => [
                         'domain' => 'sulross.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1031
                     => [
                         'domain' => 'sullivan.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1032
                     => [
                         'domain' => 'suffolk.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1033
                     => [
                         'domain' => 'subr.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1034
                     => [
                         'domain' => 'stuy.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1035
                     => [
                         'domain' => 'stut.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1036
                     => [
                         'domain' => 'stsci.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1037
                     => [
                         'domain' => 'stolaf.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1038
                     => [
                         'domain' => 'stmarytx.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1039
                     => [
                         'domain' => 'stlcop.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1040
                     => [
                         'domain' => 'stikom.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1041
                     => [
                         'domain' => 'stgregorys.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1042
                     => [
                         'domain' => 'stfrancis.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1043
                     => [
                         'domain' => 'stevens.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1044
                     => [
                         'domain' => 'stevens-tech.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1045
                     => [
                         'domain' => 'stetson.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1046
                     => [
                         'domain' => 'stedwards.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1047
                     => [
                         'domain' => 'stcloudstate.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1048
                     => [
                         'domain' => 'stchas.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1049
                     => [
                         'domain' => 'sru.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1050
                     => [
                         'domain' => 'squ.edu.om',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1051
                     => [
                         'domain' => 'spu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1052
                     => [
                         'domain' => 'spelman.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1053
                     => [
                         'domain' => 'spcollege.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1054
                     => [
                         'domain' => 'sp.edu.sg',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1055
                     => [
                         'domain' => 'sou.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1056
                     => [
                         'domain' => 'sonoma.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1057
                     => [
                         'domain' => 'solano.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1058
                     => [
                         'domain' => 'smu.edu.ph',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1059
                     => [
                         'domain' => 'smsu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1060
                     => [
                         'domain' => 'smcm.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1061
                     => [
                         'domain' => 'slu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1062
                     => [
                         'domain' => 'skku.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1063
                     => [
                         'domain' => 'sju.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1064
                     => [
                         'domain' => 'sju.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1065
                     => [
                         'domain' => 'sjtu.edu.cn',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1066
                     => [
                         'domain' => 'sjsmit.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1067
                     => [
                         'domain' => 'sivitanidios.edu.gr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1068
                     => [
                         'domain' => 'siue.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1069
                     => [
                         'domain' => 'sinte.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1070
                     => [
                         'domain' => 'sinica.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1071
                     => [
                         'domain' => 'sinclair.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1072
                     => [
                         'domain' => 'simmons.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1073
                     => [
                         'domain' => 'si.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1074
                     => [
                         'domain' => 'shu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1075
                     => [
                         'domain' => 'shsu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1076
                     => [
                         'domain' => 'ship.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1077
                     => [
                         'domain' => 'shepherd.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1078
                     => [
                         'domain' => 'shawu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1079
                     => [
                         'domain' => 'sfusd.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1080
                     => [
                         'domain' => 'sfsu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1081
                     => [
                         'domain' => 'semo.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1082
                     => [
                         'domain' => 'selu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1083
                     => [
                         'domain' => 'selcuk.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1084
                     => [
                         'domain' => 'seattleu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1085
                     => [
                         'domain' => 'sdu.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1086
                     => [
                         'domain' => 'sdsu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1087
                     => [
                         'domain' => 'sdstate.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1088
                     => [
                         'domain' => 'sdsc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1089
                     => [
                         'domain' => 'scripps.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1090
                     => [
                         'domain' => 'scranton.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1091
                     => [
                         'domain' => 'sckans.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1092
                     => [
                         'domain' => 'sccnc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1093
                     => [
                         'domain' => 'sc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1094
                     => [
                         'domain' => 'santarosa.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1095
                     => [
                         'domain' => 'santafe.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1096
                     => [
                         'domain' => 'sandburg.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1097
                     => [
                         'domain' => 'samford.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1098
                     => [
                         'domain' => 'salve.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1099
                     => [
                         'domain' => 'sals.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1100
                     => [
                         'domain' => 'salk.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1101
                     => [
                         'domain' => 'saintmarys.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1102
                     => [
                         'domain' => 'sabanciuniv.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1103
                     => [
                         'domain' => 'sa.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1104
                     => [
                         'domain' => 'rutgers.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1105
                     => [
                         'domain' => 'rush.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1106
                     => [
                         'domain' => 'rtc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1107
                     => [
                         'domain' => 'rpslmc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1108
                     => [
                         'domain' => 'rp.edu.sg',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1109
                     => [
                         'domain' => 'rose.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1110
                     => [
                         'domain' => 'rose-hulman.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1111
                     => [
                         'domain' => 'rosalindfranklin.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1112
                     => [
                         'domain' => 'roosevelt.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1113
                     => [
                         'domain' => 'rockhurst.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1114
                     => [
                         'domain' => 'rockefeller.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1115
                     => [
                         'domain' => 'rochester.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1116
                     => [
                         'domain' => 'roch.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1117
                     => [
                         'domain' => 'roanoke.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1118
                     => [
                         'domain' => 'rider.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1119
                     => [
                         'domain' => 'richmond.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1120
                     => [
                         'domain' => 'rhodesstate.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1121
                     => [
                         'domain' => 'regis.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1122
                     => [
                         'domain' => 'reed.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1123
                     => [
                         'domain' => 'ranken.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1124
                     => [
                         'domain' => 'radford.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1125
                     => [
                         'domain' => 'qut.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1126
                     => [
                         'domain' => 'quinnipiac.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1127
                     => [
                         'domain' => 'qld.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1128
                     => [
                         'domain' => 'qc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1129
                     => [
                         'domain' => 'pwsz-kalisz.edu.pl',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1130
                     => [
                         'domain' => 'pw.edu.pl',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1131
                     => [
                         'domain' => 'pvam.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1132
                     => [
                         'domain' => 'pupr.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1133
                     => [
                         'domain' => 'puc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1134
                     => [
                         'domain' => 'pu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1135
                     => [
                         'domain' => 'ptsem.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1136
                     => [
                         'domain' => 'ptr.edu.ie',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1137
                     => [
                         'domain' => 'ptloma.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1138
                     => [
                         'domain' => 'psc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1139
                     => [
                         'domain' => 'proxy.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1140
                     => [
                         'domain' => 'pratt.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1141
                     => [
                         'domain' => 'potsdam.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1142
                     => [
                         'domain' => 'pomona.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1143
                     => [
                         'domain' => 'poly.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1144
                     => [
                         'domain' => 'pnc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1145
                     => [
                         'domain' => 'plu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1146
                     => [
                         'domain' => 'planet.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1147
                     => [
                         'domain' => 'pku.edu.cn',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1148
                     => [
                         'domain' => 'pk.edu.pl',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1149
                     => [
                         'domain' => 'pittstate.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1150
                     => [
                         'domain' => 'pinecrest.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1151
                     => [
                         'domain' => 'pie.edu.pl',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1152
                     => [
                         'domain' => 'phoenix.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1153
                     => [
                         'domain' => 'pepperdine.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1154
                     => [
                         'domain' => 'peachnet.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1155
                     => [
                         'domain' => 'pccu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1156
                     => [
                         'domain' => 'parkland.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1157
                     => [
                         'domain' => 'pap.edu.pl',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1158
                     => [
                         'domain' => 'panam.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1159
                     => [
                         'domain' => 'palomar.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1160
                     => [
                         'domain' => 'pacificu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1161
                     => [
                         'domain' => 'owens.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1162
                     => [
                         'domain' => 'ouhk.edu.hk',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1163
                     => [
                         'domain' => 'oswego.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1164
                     => [
                         'domain' => 'osumc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1165
                     => [
                         'domain' => 'osu-okmulgee.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1166
                     => [
                         'domain' => 'osc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1167
                     => [
                         'domain' => 'oru.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1168
                     => [
                         'domain' => 'orst.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1169
                     => [
                         'domain' => 'oregonstate.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1170
                     => [
                         'domain' => 'onu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1171
                     => [
                         'domain' => 'oneonta.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1172
                     => [
                         'domain' => 'omu.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1173
                     => [
                         'domain' => 'olin.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1174
                     => [
                         'domain' => 'olemiss.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1175
                     => [
                         'domain' => 'ohlone.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1176
                     => [
                         'domain' => 'oc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1177
                     => [
                         'domain' => 'oberlin.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1178
                     => [
                         'domain' => 'oakton.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1179
                     => [
                         'domain' => 'oakland.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1180
                     => [
                         'domain' => 'nyp.edu.sg',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1181
                     => [
                         'domain' => 'nwmissouri.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1182
                     => [
                         'domain' => 'nuu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1183
                     => [
                         'domain' => 'nung.edu.ua',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1184
                     => [
                         'domain' => 'nuk.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1185
                     => [
                         'domain' => 'nu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1186
                     => [
                         'domain' => 'ntut.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1187
                     => [
                         'domain' => 'ntust.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1188
                     => [
                         'domain' => 'nttu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1189
                     => [
                         'domain' => 'nttc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1190
                     => [
                         'domain' => 'ntpu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1191
                     => [
                         'domain' => 'ntou.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1192
                     => [
                         'domain' => 'ntnu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1193
                     => [
                         'domain' => 'ntit.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1194
                     => [
                         'domain' => 'ntcpe.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1195
                     => [
                         'domain' => 'ntc.edu.ph',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1196
                     => [
                         'domain' => 'ntc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1197
                     => [
                         'domain' => 'nsysu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1198
                     => [
                         'domain' => 'nsu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1199
                     => [
                         'domain' => 'nrao.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1200
                     => [
                         'domain' => 'npust.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1201
                     => [
                         'domain' => 'nps.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1202
                     => [
                         'domain' => 'nova.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1203
                     => [
                         'domain' => 'norwich.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1204
                     => [
                         'domain' => 'nodak.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1205
                     => [
                         'domain' => 'noao.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1206
                     => [
                         'domain' => 'nnu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1207
                     => [
                         'domain' => 'nmu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1208
                     => [
                         'domain' => 'nmt.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1209
                     => [
                         'domain' => 'nku.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1210
                     => [
                         'domain' => 'njnu.edu.cn',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1211
                     => [
                         'domain' => 'niu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1212
                     => [
                         'domain' => 'nitt.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1213
                     => [
                         'domain' => 'niit.edu.pk',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1214
                     => [
                         'domain' => 'nicholls.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1215
                     => [
                         'domain' => 'nhust.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1216
                     => [
                         'domain' => 'nhu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1217
                     => [
                         'domain' => 'ngcsu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1218
                     => [
                         'domain' => 'nfu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1219
                     => [
                         'domain' => 'newhaven.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1220
                     => [
                         'domain' => 'newenglandconservatory.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1221
                     => [
                         'domain' => 'nevada.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1222
                     => [
                         'domain' => 'neu.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1223
                     => [
                         'domain' => 'neiu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1224
                     => [
                         'domain' => 'neduet.edu.pk',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1225
                     => [
                         'domain' => 'ndmctsgh.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1226
                     => [
                         'domain' => 'ndhu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1227
                     => [
                         'domain' => 'ncyu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1228
                     => [
                         'domain' => 'ncue.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1229
                     => [
                         'domain' => 'ncu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1230
                     => [
                         'domain' => 'ncku.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1231
                     => [
                         'domain' => 'ncit.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1232
                     => [
                         'domain' => 'nchu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1233
                     => [
                         'domain' => 'nccu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1234
                     => [
                         'domain' => 'ncat.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1235
                     => [
                         'domain' => 'naz.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1236
                     => [
                         'domain' => 'nau.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1237
                     => [
                         'domain' => 'mwsu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1238
                     => [
                         'domain' => 'mwsc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1239
                     => [
                         'domain' => 'must.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1240
                     => [
                         'domain' => 'muskingum.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1241
                     => [
                         'domain' => 'musc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1242
                     => [
                         'domain' => 'murdoch.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1243
                     => [
                         'domain' => 'mum.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1244
                     => [
                         'domain' => 'muhlenberg.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1245
                     => [
                         'domain' => 'muctr.edu.ru',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1246
                     => [
                         'domain' => 'mu.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1247
                     => [
                         'domain' => 'mu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1248
                     => [
                         'domain' => 'mtholyoke.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1249
                     => [
                         'domain' => 'msuiit.edu.ph',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1250
                     => [
                         'domain' => 'mstc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1251
                     => [
                         'domain' => 'mssm.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1252
                     => [
                         'domain' => 'msoe.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1253
                     => [
                         'domain' => 'msm.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1254
                     => [
                         'domain' => 'mscd.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1255
                     => [
                         'domain' => 'morehouse.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1256
                     => [
                         'domain' => 'moody.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1257
                     => [
                         'domain' => 'montcalm.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1258
                     => [
                         'domain' => 'monroecc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1259
                     => [
                         'domain' => 'monroe.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1260
                     => [
                         'domain' => 'monmouth.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1261
                     => [
                         'domain' => 'monm.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1262
                     => [
                         'domain' => 'monash.edu.my',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1263
                     => [
                         'domain' => 'monash.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1264
                     => [
                         'domain' => 'moet.edu.vn',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1265
                     => [
                         'domain' => 'moe.edu.sg',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1266
                     => [
                         'domain' => 'mnsu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1267
                     => [
                         'domain' => 'mmu.edu.my',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1268
                     => [
                         'domain' => 'mmc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1269
                     => [
                         'domain' => 'mit.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1270
                     => [
                         'domain' => 'missouriwestern.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1271
                     => [
                         'domain' => 'missouristate.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1272
                     => [
                         'domain' => 'missouri.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1273
                     => [
                         'domain' => 'misericordia.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1274
                     => [
                         'domain' => 'minnesota.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1275
                     => [
                         'domain' => 'mines.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1276
                     => [
                         'domain' => 'mimuw.edu.pl',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1277
                     => [
                         'domain' => 'milligan.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1278
                     => [
                         'domain' => 'millersville.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1279
                     => [
                         'domain' => 'miem.edu.ru',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1280
                     => [
                         'domain' => 'middlebury.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1281
                     => [
                         'domain' => 'michelangelo.edu.br',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1282
                     => [
                         'domain' => 'miami.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1283
                     => [
                         'domain' => 'mfldclin.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1284
                     => [
                         'domain' => 'messiah.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1285
                     => [
                         'domain' => 'mesastate.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1286
                     => [
                         'domain' => 'merrimack.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1287
                     => [
                         'domain' => 'mercynet.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1288
                     => [
                         'domain' => 'mercyhurst.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1289
                     => [
                         'domain' => 'menominee.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1290
                     => [
                         'domain' => 'memphis.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1291
                     => [
                         'domain' => 'meduohio.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1292
                     => [
                         'domain' => 'mcw.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1293
                     => [
                         'domain' => 'mcneese.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1294
                     => [
                         'domain' => 'mcg.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1295
                     => [
                         'domain' => 'mccd.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1296
                     => [
                         'domain' => 'mc3.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1297
                     => [
                         'domain' => 'mbl.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1298
                     => [
                         'domain' => 'mayo.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1299
                     => [
                         'domain' => 'matcmadison.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1300
                     => [
                         'domain' => 'marshall.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1301
                     => [
                         'domain' => 'marmara.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1302
                     => [
                         'domain' => 'marlboro.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1303
                     => [
                         'domain' => 'maricopa.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1304
                     => [
                         'domain' => 'mans.edu.eg',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1305
                     => [
                         'domain' => 'manhattan.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1306
                     => [
                         'domain' => 'manchester.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1307
                     => [
                         'domain' => 'maine.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1308
                     => [
                         'domain' => 'macalester.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1309
                     => [
                         'domain' => 'lyon.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1310
                     => [
                         'domain' => 'lynchburg.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1311
                     => [
                         'domain' => 'luzerne.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1312
                     => [
                         'domain' => 'luther.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1313
                     => [
                         'domain' => 'ludwig.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1314
                     => [
                         'domain' => 'luc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1315
                     => [
                         'domain' => 'ltu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1316
                     => [
                         'domain' => 'loyno.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1317
                     => [
                         'domain' => 'losrios.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1318
                     => [
                         'domain' => 'llumc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1319
                     => [
                         'domain' => 'ljcrf.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1320
                     => [
                         'domain' => 'linfield.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1321
                     => [
                         'domain' => 'liberty.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1322
                     => [
                         'domain' => 'lhup.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1323
                     => [
                         'domain' => 'lfc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1324
                     => [
                         'domain' => 'letu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1325
                     => [
                         'domain' => 'lesley.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1326
                     => [
                         'domain' => 'lemoyne.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1327
                     => [
                         'domain' => 'lclark.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1328
                     => [
                         'domain' => 'lccc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1329
                     => [
                         'domain' => 'lcc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1330
                     => [
                         'domain' => 'latrobe.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1331
                     => [
                         'domain' => 'latech.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1332
                     => [
                         'domain' => 'lamar.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1333
                     => [
                         'domain' => 'lacoe.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1334
                     => [
                         'domain' => 'kyu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1335
                     => [
                         'domain' => 'kutkm.edu.my',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1336
                     => [
                         'domain' => 'kumc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1337
                     => [
                         'domain' => 'kuittho.edu.my',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1338
                     => [
                         'domain' => 'kuas.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1339
                     => [
                         'domain' => 'ku.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1340
                     => [
                         'domain' => 'ku.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1341
                     => [
                         'domain' => 'ktu.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1342
                     => [
                         'domain' => 'ksu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1343
                     => [
                         'domain' => 'ksu.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1344
                     => [
                         'domain' => 'kstu.edu.ua',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1345
                     => [
                         'domain' => 'ks.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1346
                     => [
                         'domain' => 'kpprc.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1347
                     => [
                         'domain' => 'kou.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1348
                     => [
                         'domain' => 'knox.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1349
                     => [
                         'domain' => 'kiet.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1350
                     => [
                         'domain' => 'kh.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1351
                     => [
                         'domain' => 'kgi.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1352
                     => [
                         'domain' => 'kettering.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1353
                     => [
                         'domain' => 'kentlaw.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1354
                     => [
                         'domain' => 'kent.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1355
                     => [
                         'domain' => 'kennesaw.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1356
                     => [
                         'domain' => 'keene.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1357
                     => [
                         'domain' => 'kctcs.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1358
                     => [
                         'domain' => 'karunya.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1359
                     => [
                         'domain' => 'kacst.edu.sa',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1360
                     => [
                         'domain' => 'jsu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1361
                     => [
                         'domain' => 'jmu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1362
                     => [
                         'domain' => 'jlu.edu.cn',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1363
                     => [
                         'domain' => 'jhuapl.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1364
                     => [
                         'domain' => 'jhsph.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1365
                     => [
                         'domain' => 'jhmi.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1366
                     => [
                         'domain' => 'jcu.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1367
                     => [
                         'domain' => 'iyte.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1368
                     => [
                         'domain' => 'iwu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1369
                     => [
                         'domain' => 'iwcc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1370
                     => [
                         'domain' => 'ivytech.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1371
                     => [
                         'domain' => 'iusb.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1372
                     => [
                         'domain' => 'iup.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1373
                     => [
                         'domain' => 'iun.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1374
                     => [
                         'domain' => 'iu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1375
                     => [
                         'domain' => 'itu.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1376
                     => [
                         'domain' => 'itmorelia.edu.mx',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1377
                     => [
                         'domain' => 'isunet.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1378
                     => [
                         'domain' => 'isu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1379
                     => [
                         'domain' => 'istanbul.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1380
                     => [
                         'domain' => 'ist.edu.gr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1381
                     => [
                         'domain' => 'isra.edu.jo',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1382
                     => [
                         'domain' => 'ismm.edu.cu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1383
                     => [
                         'domain' => 'island.edu.hk',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1384
                     => [
                         'domain' => 'is.edu.ro',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1385
                     => [
                         'domain' => 'ircc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1386
                     => [
                         'domain' => 'ips.edu.ar',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1387
                     => [
                         'domain' => 'ipfw.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1388
                     => [
                         'domain' => 'iona.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1389
                     => [
                         'domain' => 'intimal.edu.my',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1390
                     => [
                         'domain' => 'internet2.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1391
                     => [
                         'domain' => 'inonu.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1392
                     => [
                         'domain' => 'indstate.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1393
                     => [
                         'domain' => 'indianhills.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1394
                     => [
                         'domain' => 'imsa.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1395
                     => [
                         'domain' => 'imr.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1396
                     => [
                         'domain' => 'ilstu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1397
                     => [
                         'domain' => 'ilot.edu.pl',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1398
                     => [
                         'domain' => 'ilc.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1399
                     => [
                         'domain' => 'iiu.edu.my',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1400
                     => [
                         'domain' => 'igf.edu.pl',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1401
                     => [
                         'domain' => 'ifpan.edu.pl',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1402
                     => [
                         'domain' => 'ifj.edu.pl',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1403
                     => [
                         'domain' => 'iest.edu.mx',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1404
                     => [
                         'domain' => 'icm.edu.pl',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1405
                     => [
                         'domain' => 'ichf.edu.pl',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1406
                     => [
                         'domain' => 'ibun.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1407
                     => [
                         'domain' => 'ibu.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1408
                     => [
                         'domain' => 'ibngr.edu.pl',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1409
                     => [
                         'domain' => 'hvcc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1410
                     => [
                         'domain' => 'hut.edu.vn',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1411
                     => [
                         'domain' => 'humtec.edu.pe',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1412
                     => [
                         'domain' => 'hsu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1413
                     => [
                         'domain' => 'hsph.edu.vn',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1414
                     => [
                         'domain' => 'hsc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1415
                     => [
                         'domain' => 'howardcc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1416
                     => [
                         'domain' => 'hope.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1417
                     => [
                         'domain' => 'hmc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1418
                     => [
                         'domain' => 'hkbu.edu.hk',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1419
                     => [
                         'domain' => 'hho.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1420
                     => [
                         'domain' => 'hfh.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1421
                     => [
                         'domain' => 'herzing.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1422
                     => [
                         'domain' => 'hcmuttt.edu.vn',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1423
                     => [
                         'domain' => 'hcmuns.edu.vn',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1424
                     => [
                         'domain' => 'hccs.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1425
                     => [
                         'domain' => 'hccfl.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1426
                     => [
                         'domain' => 'hbs.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1427
                     => [
                         'domain' => 'hawaii.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1428
                     => [
                         'domain' => 'haverford.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1429
                     => [
                         'domain' => 'hartford.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1430
                     => [
                         'domain' => 'hamilton.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1431
                     => [
                         'domain' => 'hacettepe.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1432
                     => [
                         'domain' => 'gyte.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1433
                     => [
                         'domain' => 'gwu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1434
                     => [
                         'domain' => 'gu.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1435
                     => [
                         'domain' => 'gsu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1436
                     => [
                         'domain' => 'grinnell.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1437
                     => [
                         'domain' => 'griffith.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1438
                     => [
                         'domain' => 'grcc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1439
                     => [
                         'domain' => 'grayson.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1440
                     => [
                         'domain' => 'govst.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1441
                     => [
                         'domain' => 'gonzaga.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1442
                     => [
                         'domain' => 'gmu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1443
                     => [
                         'domain' => 'giki.edu.pk',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1444
                     => [
                         'domain' => 'gettysburg.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1445
                     => [
                         'domain' => 'georgiasouthern.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1446
                     => [
                         'domain' => 'georgetown.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1447
                     => [
                         'domain' => 'geneseo.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1448
                     => [
                         'domain' => 'geisinger.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1449
                     => [
                         'domain' => 'gcsu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1450
                     => [
                         'domain' => 'gcc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1451
                     => [
                         'domain' => 'gazi.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1452
                     => [
                         'domain' => 'gatewaycc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1453
                     => [
                         'domain' => 'gasou.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1454
                     => [
                         'domain' => 'gantep.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1455
                     => [
                         'domain' => 'galileo.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1456
                     => [
                         'domain' => 'gac.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1457
                     => [
                         'domain' => 'furman.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1458
                     => [
                         'domain' => 'fullerton.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1459
                     => [
                         'domain' => 'fscwv.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1460
                     => [
                         'domain' => 'francis.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1461
                     => [
                         'domain' => 'fq.edu.uy',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1462
                     => [
                         'domain' => 'fmarion.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1463
                     => [
                         'domain' => 'flinders.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1464
                     => [
                         'domain' => 'flcc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1465
                     => [
                         'domain' => 'fit.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1466
                     => [
                         'domain' => 'fisk.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1467
                     => [
                         'domain' => 'fisica.edu.uy',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1468
                     => [
                         'domain' => 'fing.edu.uy',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1469
                     => [
                         'domain' => 'fi.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1470
                     => [
                         'domain' => 'fhda.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1471
                     => [
                         'domain' => 'feu-eastasia.edu.ph',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1472
                     => [
                         'domain' => 'fdltcc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1473
                     => [
                         'domain' => 'fcu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1474
                     => [
                         'domain' => 'fatih.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1475
                     => [
                         'domain' => 'fandm.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1476
                     => [
                         'domain' => 'famu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1477
                     => [
                         'domain' => 'fairfield.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1478
                     => [
                         'domain' => 'exploratorium.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1479
                     => [
                         'domain' => 'exeter.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1480
                     => [
                         'domain' => 'ewu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1481
                     => [
                         'domain' => 'evergreen.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1482
                     => [
                         'domain' => 'evansville.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1483
                     => [
                         'domain' => 'etsu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1484
                     => [
                         'domain' => 'esu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1485
                     => [
                         'domain' => 'espol.edu.ec',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1486
                     => [
                         'domain' => 'esb3-tomazfigueiredo.edu.pt',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1487
                     => [
                         'domain' => 'esb3-idhenrique.edu.pt',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1488
                     => [
                         'domain' => 'erciyes.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1489
                     => [
                         'domain' => 'erau.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1490
                     => [
                         'domain' => 'eq.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1491
                     => [
                         'domain' => 'epcc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1492
                     => [
                         'domain' => 'eou.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1493
                     => [
                         'domain' => 'enmu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1494
                     => [
                         'domain' => 'emporia.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1495
                     => [
                         'domain' => 'emory.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1496
                     => [
                         'domain' => 'emich.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1497
                     => [
                         'domain' => 'emerson.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1498
                     => [
                         'domain' => 'elon.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1499
                     => [
                         'domain' => 'elgin.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1500
                     => [
                         'domain' => 'eku.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1501
                     => [
                         'domain' => 'eiu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1502
                     => [
                         'domain' => 'eitw.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1503
                     => [
                         'domain' => 'einstein.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1504
                     => [
                         'domain' => 'ege.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1505
                     => [
                         'domain' => 'eduhq.edu.sc',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1506
                     => [
                         'domain' => 'educause.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1507
                     => [
                         'domain' => 'edinboro.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1508
                     => [
                         'domain' => 'edgewood.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1509
                     => [
                         'domain' => 'ecu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1510
                     => [
                         'domain' => 'ecpi.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1511
                     => [
                         'domain' => 'ecc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1512
                     => [
                         'domain' => 'eb23-vjuromenha.edu.pt',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1513
                     => [
                         'domain' => 'eb23-qtmarrocos.edu.pt',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1514
                     => [
                         'domain' => 'earlham.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1515
                     => [
                         'domain' => 'eafit.edu.co',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1516
                     => [
                         'domain' => 'dyu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1517
                     => [
                         'domain' => 'duq.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1518
                     => [
                         'domain' => 'dtcc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1519
                     => [
                         'domain' => 'dsu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1520
                     => [
                         'domain' => 'dstc.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1521
                     => [
                         'domain' => 'dri.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1522
                     => [
                         'domain' => 'drexelmed.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1523
                     => [
                         'domain' => 'drexel.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1524
                     => [
                         'domain' => 'dordt.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1525
                     => [
                         'domain' => 'dogus.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1526
                     => [
                         'domain' => 'dodea.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1527
                     => [
                         'domain' => 'dicle.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1528
                     => [
                         'domain' => 'dhsphn.edu.vn',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1529
                     => [
                         'domain' => 'devry.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1530
                     => [
                         'domain' => 'deu.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1531
                     => [
                         'domain' => 'depaul.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1532
                     => [
                         'domain' => 'denison.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1533
                     => [
                         'domain' => 'deakin.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1534
                     => [
                         'domain' => 'dcs.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1535
                     => [
                         'domain' => 'dcccd.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1536
                     => [
                         'domain' => 'davidson.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1537
                     => [
                         'domain' => 'davenport.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1538
                     => [
                         'domain' => 'dartmouth.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1539
                     => [
                         'domain' => 'cy.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1540
                     => [
                         'domain' => 'cwu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1541
                     => [
                         'domain' => 'curtin.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1542
                     => [
                         'domain' => 'cuny.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1543
                     => [
                         'domain' => 'cune.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1544
                     => [
                         'domain' => 'cujae.edu.cu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1545
                     => [
                         'domain' => 'cudenver.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1546
                     => [
                         'domain' => 'cuc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1547
                     => [
                         'domain' => 'cua.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1548
                     => [
                         'domain' => 'cu-portland.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1549
                     => [
                         'domain' => 'ctu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1550
                     => [
                         'domain' => 'csusb.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1551
                     => [
                         'domain' => 'csus.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1552
                     => [
                         'domain' => 'csuohio.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1553
                     => [
                         'domain' => 'csumb.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1554
                     => [
                         'domain' => 'csulb.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1555
                     => [
                         'domain' => 'csufresno.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1556
                     => [
                         'domain' => 'csueastbay.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1557
                     => [
                         'domain' => 'csudh.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1558
                     => [
                         'domain' => 'csuchico.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1559
                     => [
                         'domain' => 'csu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1560
                     => [
                         'domain' => 'csu.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1561
                     => [
                         'domain' => 'csu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1562
                     => [
                         'domain' => 'cshl.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1563
                     => [
                         'domain' => 'cscc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1564
                     => [
                         'domain' => 'criba.edu.ar',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1565
                     => [
                         'domain' => 'creighton.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1566
                     => [
                         'domain' => 'cqu.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1567
                     => [
                         'domain' => 'covenant.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1568
                     => [
                         'domain' => 'cooper.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1569
                     => [
                         'domain' => 'conncoll.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1570
                     => [
                         'domain' => 'colum.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1571
                     => [
                         'domain' => 'colstate.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1572
                     => [
                         'domain' => 'coloradotech.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1573
                     => [
                         'domain' => 'coloradomtn.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1574
                     => [
                         'domain' => 'coloradocollege.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1575
                     => [
                         'domain' => 'collinscollege.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1576
                     => [
                         'domain' => 'colgate.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1577
                     => [
                         'domain' => 'colegiatura.edu.co',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1578
                     => [
                         'domain' => 'colby-sawyer.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1579
                     => [
                         'domain' => 'cofc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1580
                     => [
                         'domain' => 'cod.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1581
                     => [
                         'domain' => 'cobacalc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1582
                     => [
                         'domain' => 'coastalbend.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1583
                     => [
                         'domain' => 'cnm.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1584
                     => [
                         'domain' => 'cna-qatar.edu.qa',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1585
                     => [
                         'domain' => 'cmu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1586
                     => [
                         'domain' => 'cmsu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1587
                     => [
                         'domain' => 'cmich.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1588
                     => [
                         'domain' => 'cma.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1589
                     => [
                         'domain' => 'clu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1590
                     => [
                         'domain' => 'clemson.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1591
                     => [
                         'domain' => 'clarku.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1592
                     => [
                         'domain' => 'clark.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1593
                     => [
                         'domain' => 'clarion.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1594
                     => [
                         'domain' => 'claremont.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1595
                     => [
                         'domain' => 'ckit.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1596
                     => [
                         'domain' => 'cju.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1597
                     => [
                         'domain' => 'cjcu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1598
                     => [
                         'domain' => 'ciw.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1599
                     => [
                         'domain' => 'cityue.edu.hk',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1600
                     => [
                         'domain' => 'cin.edu.uy',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1601
                     => [
                         'domain' => 'cier.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1602
                     => [
                         'domain' => 'cic.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1603
                     => [
                         'domain' => 'chw.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1604
                     => [
                         'domain' => 'chu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1605
                     => [
                         'domain' => 'christcollege.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1606
                     => [
                         'domain' => 'chop.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1607
                     => [
                         'domain' => 'chnu.edu.ua',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1608
                     => [
                         'domain' => 'chemeketa.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1609
                     => [
                         'domain' => 'champlain.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1610
                     => [
                         'domain' => 'cgu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1611
                     => [
                         'domain' => 'cgu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1612
                     => [
                         'domain' => 'cfs.edu.hk',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1613
                     => [
                         'domain' => 'cfcc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1614
                     => [
                         'domain' => 'cdrewu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1615
                     => [
                         'domain' => 'ccut.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1616
                     => [
                         'domain' => 'ccu.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1617
                     => [
                         'domain' => 'ccit.edu.tw',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1618
                     => [
                         'domain' => 'ccc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1619
                     => [
                         'domain' => 'carthage.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1620
                     => [
                         'domain' => 'carleton.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1621
                     => [
                         'domain' => 'canyons.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1622
                     => [
                         'domain' => 'cankaya.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1623
                     => [
                         'domain' => 'canberra.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1624
                     => [
                         'domain' => 'camk.edu.pl',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1625
                     => [
                         'domain' => 'calvinchr.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1626
                     => [
                         'domain' => 'calvin.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1627
                     => [
                         'domain' => 'calstate.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1628
                     => [
                         'domain' => 'callutheran.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1629
                     => [
                         'domain' => 'calarts.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1630
                     => [
                         'domain' => 'cabrillo.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1631
                     => [
                         'domain' => 'byuh.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1632
                     => [
                         'domain' => 'byu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1633
                     => [
                         'domain' => 'bvb.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1634
                     => [
                         'domain' => 'butler.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1635
                     => [
                         'domain' => 'bucknell.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1636
                     => [
                         'domain' => 'bu.edu.ro',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1637
                     => [
                         'domain' => 'brynmawr.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1638
                     => [
                         'domain' => 'bryantstratton.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1639
                     => [
                         'domain' => 'broward.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1640
                     => [
                         'domain' => 'brooklaw.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1641
                     => [
                         'domain' => 'brockport.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1642
                     => [
                         'domain' => 'bridgew.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1643
                     => [
                         'domain' => 'brandeis.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1644
                     => [
                         'domain' => 'boun.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1645
                     => [
                         'domain' => 'boisestate.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1646
                     => [
                         'domain' => 'bluffton.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1647
                     => [
                         'domain' => 'bju.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1648
                     => [
                         'domain' => 'biola.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1649
                     => [
                         'domain' => 'bia.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1650
                     => [
                         'domain' => 'bhc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1651
                     => [
                         'domain' => 'bh.edu.ro',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1652
                     => [
                         'domain' => 'bgsu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1653
                     => [
                         'domain' => 'berry.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1654
                     => [
                         'domain' => 'berklee.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1655
                     => [
                         'domain' => 'bennington.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1656
                     => [
                         'domain' => 'bellevue.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1657
                     => [
                         'domain' => 'bcm.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1658
                     => [
                         'domain' => 'bchs.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1659
                     => [
                         'domain' => 'bcc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1660
                     => [
                         'domain' => 'baylor.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1661
                     => [
                         'domain' => 'bates.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1662
                     => [
                         'domain' => 'baskent.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1663
                     => [
                         'domain' => 'bahcesehir.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1664
                     => [
                         'domain' => 'austincollege.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1665
                     => [
                         'domain' => 'austincc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1666
                     => [
                         'domain' => 'augustana.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1667
                     => [
                         'domain' => 'augie.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1668
                     => [
                         'domain' => 'aucegypt.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1669
                     => [
                         'domain' => 'aub.edu.lb',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1670
                     => [
                         'domain' => 'au.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1671
                     => [
                         'domain' => 'atu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1672
                     => [
                         'domain' => 'athens.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1673
                     => [
                         'domain' => 'asbury.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1674
                     => [
                         'domain' => 'armstrong.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1675
                     => [
                         'domain' => 'aquinas.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1676
                     => [
                         'domain' => 'apu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1677
                     => [
                         'domain' => 'aps.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1678
                     => [
                         'domain' => 'appstate.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1679
                     => [
                         'domain' => 'apollogrp.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1680
                     => [
                         'domain' => 'anu.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1681
                     => [
                         'domain' => 'annauniv.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1682
                     => [
                         'domain' => 'ankara.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1683
                     => [
                         'domain' => 'angelo.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1684
                     => [
                         'domain' => 'anadolu.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1685
                     => [
                         'domain' => 'amwaw.edu.pl',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1686
                     => [
                         'domain' => 'amu.edu.pl',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1687
                     => [
                         'domain' => 'ammanu.edu.jo',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1688
                     => [
                         'domain' => 'ammanacademy.edu.jo',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1689
                     => [
                         'domain' => 'amherst.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1690
                     => [
                         'domain' => 'american.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1691
                     => [
                         'domain' => 'amc.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1692
                     => [
                         'domain' => 'alverno.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1693
                     => [
                         'domain' => 'altamahatech.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1694
                     => [
                         'domain' => 'allegheny.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1695
                     => [
                         'domain' => 'alfredstate.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1696
                     => [
                         'domain' => 'alaska.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1697
                     => [
                         'domain' => 'aku.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1698
                     => [
                         'domain' => 'akdeniz.edu.tr',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1699
                     => [
                         'domain' => 'afit.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1700
                     => [
                         'domain' => 'adnu.edu.ph',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1701
                     => [
                         'domain' => 'admu.edu.ph',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1702
                     => [
                         'domain' => 'adfa.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1703
                     => [
                         'domain' => 'adelphi.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1704
                     => [
                         'domain' => 'adelaide.edu.au',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1705
                     => [
                         'domain' => 'ab.edu.pl',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1706
                     => [
                         'domain' => 'aamu.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1707
                     => [
                         'domain' => 'a-star.edu.sg',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1708
                     => [
                         'domain' => '3sheep.edu',
                         'class' => '1',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1709
                     => [
                         'domain' => 'yorkcounty.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1710
                     => [
                         'domain' => 'york.gov.uk',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1711
                     => [
                         'domain' => 'ymp.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1712
                     => [
                         'domain' => 'wa.gov.au',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1713
                     => [
                         'domain' => 'wa.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1714
                     => [
                         'domain' => 'vssc.gov.in',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1715
                     => [
                         'domain' => 'vpn.gov.ie',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1716
                     => [
                         'domain' => 'virginia.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1717
                     => [
                         'domain' => 'vic.gov.au',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1718
                     => [
                         'domain' => 'va.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1719
                     => [
                         'domain' => 'uspto.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1720
                     => [
                         'domain' => 'usps.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1721
                     => [
                         'domain' => 'usgs.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1722
                     => [
                         'domain' => 'usdoj.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1723
                     => [
                         'domain' => 'uscourts.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1724
                     => [
                         'domain' => 'usbr.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1725
                     => [
                         'domain' => 'umirm.gov.pl',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1726
                     => [
                         'domain' => 'ucia.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1727
                     => [
                         'domain' => 'tva.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1728
                     => [
                         'domain' => 'tubitak.gov.tr',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1729
                     => [
                         'domain' => 'treas.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1730
                     => [
                         'domain' => 'to.gov.br',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1731
                     => [
                         'domain' => 'tas.gov.au',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1732
                     => [
                         'domain' => 'syzefxis.gov.gr',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1733
                     => [
                         'domain' => 'state.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1734
                     => [
                         'domain' => 'srs.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1735
                     => [
                         'domain' => 'sr.gov.yu',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1736
                     => [
                         'domain' => 'sfwmd.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1737
                     => [
                         'domain' => 'sanantonio.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1738
                     => [
                         'domain' => 'rzgw.gov.pl',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1739
                     => [
                         'domain' => 'railnet.gov.in',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1740
                     => [
                         'domain' => 'qld.gov.au',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1741
                     => [
                         'domain' => 'pidc.gov.tw',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1742
                     => [
                         'domain' => 'peacecorps.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1743
                     => [
                         'domain' => 'pbh.gov.br',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1744
                     => [
                         'domain' => 'osti.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1745
                     => [
                         'domain' => 'osis.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1746
                     => [
                         'domain' => 'ornl.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1747
                     => [
                         'domain' => 'orau.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1748
                     => [
                         'domain' => 'nyc.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1749
                     => [
                         'domain' => 'nsw.gov.au',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1750
                     => [
                         'domain' => 'nrel.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1751
                     => [
                         'domain' => 'nrc.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1752
                     => [
                         'domain' => 'norfolk.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1753
                     => [
                         'domain' => 'noaa.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1754
                     => [
                         'domain' => 'nitesl.gov.lk',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1755
                     => [
                         'domain' => 'nik.gov.pl',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1756
                     => [
                         'domain' => 'nersc.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1757
                     => [
                         'domain' => 'nencki.gov.pl',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1758
                     => [
                         'domain' => 'neda.gov.ph',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1759
                     => [
                         'domain' => 'ncifcrf.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1760
                     => [
                         'domain' => 'nasb.gov.by',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1761
                     => [
                         'domain' => 'nas.gov.ua',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1762
                     => [
                         'domain' => 'mte.gov.br',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1763
                     => [
                         'domain' => 'mpf.gov.br',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1764
                     => [
                         'domain' => 'mod.gov.il',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1765
                     => [
                         'domain' => 'mizoram.gov.in',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1766
                     => [
                         'domain' => 'mg.gov.br',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1767
                     => [
                         'domain' => 'maricopa.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1768
                     => [
                         'domain' => 'mam.gov.tr',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1769
                     => [
                         'domain' => 'lnk.gov.cl',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1770
                     => [
                         'domain' => 'llnl.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1771
                     => [
                         'domain' => 'la.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1772
                     => [
                         'domain' => 'kgm.gov.tr',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1773
                     => [
                         'domain' => 'kapl.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1774
                     => [
                         'domain' => 'judicial.gov.tw',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1775
                     => [
                         'domain' => 'johor.gov.my',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1776
                     => [
                         'domain' => 'jccbi.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1777
                     => [
                         'domain' => 'irs.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1778
                     => [
                         'domain' => 'ippt.gov.pl',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1779
                     => [
                         'domain' => 'inti.gov.ar',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1780
                     => [
                         'domain' => 'inmetro.gov.br',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1781
                     => [
                         'domain' => 'iner.gov.tw',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1782
                     => [
                         'domain' => 'inel.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1783
                     => [
                         'domain' => 'il.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1784
                     => [
                         'domain' => 'iimcb.gov.pl',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1785
                     => [
                         'domain' => 'ihs.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1786
                     => [
                         'domain' => 'iem.gov.lv',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1787
                     => [
                         'domain' => 'idsc.gov.eg',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1788
                     => [
                         'domain' => 'ida.gov.sg',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1789
                     => [
                         'domain' => 'house.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1790
                     => [
                         'domain' => 'hmgcc.gov.uk',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1791
                     => [
                         'domain' => 'hants.gov.uk',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1792
                     => [
                         'domain' => 'hanford.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1793
                     => [
                         'domain' => 'gwynedd.gov.uk',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1794
                     => [
                         'domain' => 'gsi.gov.uk',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1795
                     => [
                         'domain' => 'gov66.gov.cz',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1796
                     => [
                         'domain' => 'gov12.gov.cz',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1797
                     => [
                         'domain' => 'gba.gov.ar',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1798
                     => [
                         'domain' => 'gao.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1799
                     => [
                         'domain' => 'ga.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1800
                     => [
                         'domain' => 'frb.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1801
                     => [
                         'domain' => 'finance.gov.mk',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1802
                     => [
                         'domain' => 'fdic.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1803
                     => [
                         'domain' => 'fcc.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1804
                     => [
                         'domain' => 'fairfaxcounty.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1805
                     => [
                         'domain' => 'faa.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1806
                     => [
                         'domain' => 'epa.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1807
                     => [
                         'domain' => 'eop.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1808
                     => [
                         'domain' => 'environment-agency.gov.uk',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1809
                     => [
                         'domain' => 'eln.gov.br',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1810
                     => [
                         'domain' => 'eeoc.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1811
                     => [
                         'domain' => 'ed.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1812
                     => [
                         'domain' => 'dstl.gov.uk',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1813
                     => [
                         'domain' => 'dst.gov.za',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1814
                     => [
                         'domain' => 'dpf.gov.br',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1815
                     => [
                         'domain' => 'dot.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1816
                     => [
                         'domain' => 'doechicago.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1817
                     => [
                         'domain' => 'doeal.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1818
                     => [
                         'domain' => 'doe.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1819
                     => [
                         'domain' => 'doc.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1820
                     => [
                         'domain' => 'dhs.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1821
                     => [
                         'domain' => 'dfat.gov.au',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1822
                     => [
                         'domain' => 'dera.gov.uk',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1823
                     => [
                         'domain' => 'defence.gov.au',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1824
                     => [
                         'domain' => 'dcita.gov.au',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1825
                     => [
                         'domain' => 'dc.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1826
                     => [
                         'domain' => 'csl.gov.uk',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1827
                     => [
                         'domain' => 'cise-nsf.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1828
                     => [
                         'domain' => 'cia.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1829
                     => [
                         'domain' => 'cga.gov.tw',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1830
                     => [
                         'domain' => 'ceride.gov.ar',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1831
                     => [
                         'domain' => 'census.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1832
                     => [
                         'domain' => 'cenpra.gov.br',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1833
                     => [
                         'domain' => 'cat.gov.in',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1834
                     => [
                         'domain' => 'capes.gov.br',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1835
                     => [
                         'domain' => 'cabq.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1836
                     => [
                         'domain' => 'ca.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1837
                     => [
                         'domain' => 'bsmi.gov.tw',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1838
                     => [
                         'domain' => 'brighton-hove.gov.uk',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1839
                     => [
                         'domain' => 'bom.gov.au',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1840
                     => [
                         'domain' => 'blm.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1841
                     => [
                         'domain' => 'bettis.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1842
                     => [
                         'domain' => 'bernco.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1843
                     => [
                         'domain' => 'bdep.gov.br',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1844
                     => [
                         'domain' => 'bart.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1845
                     => [
                         'domain' => 'bank.gov.ua',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1846
                     => [
                         'domain' => 'bacninh.gov.vn',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1847
                     => [
                         'domain' => 'ansto.gov.au',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1848
                     => [
                         'domain' => 'anl.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1849
                     => [
                         'domain' => 'alabama.gov',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1850
                     => [
                         'domain' => 'ac.gov.br',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1851
                     => [
                         'domain' => 'aao.gov.au',
                         'class' => '3',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1852
                     => [
                         'domain' => 'sbcglobal.net',
                         'class' => '4',
                         'country' => 'yy',
                         'state' => 'yy',
                         'name' => 'yy',
                     ],
                    1853
                     => [
                         'domain' => 'bell.ca',
                         'class' => '4',
                         'country' => 'yy',
                         'state' => 'yy',
                         'name' => 'yy',
                     ],
                    1854
                     => [
                         'domain' => 'telecomitalia.it',
                         'class' => '4',
                         'country' => 'yy',
                         'state' => 'yy',
                         'name' => 'yy',
                     ],
                    1855
                     => [
                         'domain' => 'airtelbroadband.in',
                         'class' => '4',
                         'country' => 'yy',
                         'state' => 'yy',
                         'name' => 'yy',
                     ],
                    1856
                     => [
                         'domain' => 'india.net',
                         'class' => '4',
                         'country' => 'yy',
                         'state' => 'yy',
                         'name' => 'yy',
                     ],
                    1857
                     => [
                         'domain' => 'vutbr.cz',
                         'class' => '1',
                         'country' => 'cz',
                         'state' => 'yy',
                         'name' => 'BRNO University of Technology',
                     ],
                    1858
                     => [
                         'domain' => 'sc06.org',
                         'class' => '1',
                         'country' => '',
                         'state' => 'yy',
                         'name' => 'SC06',
                     ],
                    1859
                     => [
                         'domain' => 'live.com',
                         'class' => '5',
                         'country' => 'yy',
                         'state' => 'yy',
                         'name' => 'yy',
                     ],
                    1860
                     => [
                         'domain' => 'phx.gbl',
                         'class' => '5',
                         'country' => 'yy',
                         'state' => 'yy',
                         'name' => 'yy',
                     ],
                    1861
                     => [
                         'domain' => 'bloglines.com',
                         'class' => '5',
                         'country' => 'yy',
                         'state' => 'yy',
                         'name' => 'yy',
                     ],
                    1862
                     => [
                         'domain' => 'become.com',
                         'class' => '5',
                         'country' => 'yy',
                         'state' => 'yy',
                         'name' => 'yy',
                     ],
                    1863
                     => [
                         'domain' => 'quest.net',
                         'class' => '2',
                         'country' => 'yy',
                         'state' => 'yy',
                         'name' => 'yy',
                     ],
                    1864
                     => [
                         'domain' => 'brain.grub.org',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1865
                     => [
                         'domain' => 'cosmixcorp.com',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1866
                     => [
                         'domain' => 'crawl8-public.alexa.com',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1867
                     => [
                         'domain' => 'crawler918.com',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1868
                     => [
                         'domain' => 'girafa.com',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1869
                     => [
                         'domain' => 'hanta.yahoo.com',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1870
                     => [
                         'domain' => 'idle.eidetica.com',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1871
                     => [
                         'domain' => 'live-servers.net',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1872
                     => [
                         'domain' => 'looksmart.net',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1873
                     => [
                         'domain' => 'markwatch.com',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1874
                     => [
                         'domain' => 'metacarta.com',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1875
                     => [
                         'domain' => 'morgue1.corp.yahoo.com',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1876
                     => [
                         'domain' => 'msnbot.msn.com',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1877
                     => [
                         'domain' => 'panchma.tivra.com',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1878
                     => [
                         'domain' => 'tpiol.com',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1879
                     => [
                         'domain' => 'tpiol.tpiol.com',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1880
                     => [
                         'domain' => 'tracerlock.com',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1881
                     => [
                         'domain' => 'webclipping.com',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1882
                     => [
                         'domain' => 'websmostlinked.com',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1883
                     => [
                         'domain' => 'websquash.com',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1884
                     => [
                         'domain' => 'whizbang.com',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1885
                     => [
                         'domain' => 'xs4.kso.co.uk',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1886
                     => [
                         'domain' => 'zeus.nj.nec.com',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1887
                     => [
                         'domain' => 'archive.org',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1888
                     => [
                         'domain' => 'authoritativeweb.com',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1889
                     => [
                         'domain' => 'crawl.yahoo.net',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1890
                     => [
                         'domain' => 'entireweb.com',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1891
                     => [
                         'domain' => 'internetserviceteam.com',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1892
                     => [
                         'domain' => 'paginasamarillas.es',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1893
                     => [
                         'domain' => 'sac.overture.com',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1894
                     => [
                         'domain' => 'san2.attens.net',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                    1895
                     => [
                         'domain' => 'worio.com',
                         'class' => '5',
                         'country' => 'xx',
                         'state' => 'xx',
                         'name' => 'xx',
                     ],
                ];
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('domainclasses')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('domainclasses')
                    ->tinyInteger('class')->default(0)
                    ->string('name', 64)->default('')
                    ->primaryKey('class')
                    ->uniqueIndex('class_name', ['class', 'name'])
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $rows =  [
                    0
                     => [
                         'class' => '0',
                         'name' => 'Unknown',
                     ],
                    1
                     => [
                         'class' => '1',
                         'name' => 'Educational Institution',
                     ],
                    2
                     => [
                         'class' => '2',
                         'name' => 'Industrial/Corporate',
                     ],
                    3
                     => [
                         'class' => '3',
                         'name' => 'Governmental',
                     ],
                    4
                     => [
                         'class' => '4',
                         'name' => 'Internet Service Provider',
                     ],
                    5
                     => [
                         'class' => '5',
                         'name' => 'Search Engine',
                     ],
                    6
                     => [
                         'class' => '6',
                         'name' => 'Press/Media/Publication',
                     ],
                ];
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('summary_user')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('summary_user')
                    ->tinyInteger('id')->default(0)
                    ->string('label', 255)->default('')
                    ->integer('plot')->default(0)
                    ->uniqueIndex('label', 'label')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $rows =  [
                    0
                     => [
                         'id' => '1',
                         'label' => 'Total Users {1}',
                         'plot' => '1',
                     ],
                    1
                     => [
                         'id' => '6',
                         'label' => '- Registered Users {2}',
                         'plot' => '1',
                     ],
                    2
                     => [
                         'id' => '7',
                         'label' => '- Unregistered Interactive Users {3}',
                         'plot' => '1',
                     ],
                    3
                     => [
                         'id' => '8',
                         'label' => '- Unregistered Download Users {4}',
                         'plot' => '1',
                     ],
                    4
                     => [
                         'id' => '3',
                         'label' => '- Interactive Users {6}',
                         'plot' => '1',
                     ],
                    5
                     => [
                         'id' => '2',
                         'label' => '- Simulation Users {5}',
                         'plot' => '1',
                     ],
                    6
                     => [
                         'id' => '4',
                         'label' => '- Download Users {7}',
                         'plot' => '1',
                     ],
                ];
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('summary_user_vals')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('summary_user_vals')
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
                $statsDb
                    ->schema()
                    ->createTable('summary_andmore')
                    ->tinyInteger('id')->default(0)
                    ->string('label', 255)->default('')
                    ->integer('plot')->default(0)
                    ->uniqueIndex('label', 'label')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $rows =  [
                    0
                     => [
                         'id' => '1',
                         'label' => 'Nano 101',
                         'plot' => '0',
                     ],
                    1
                     => [
                         'id' => '2',
                         'label' => 'Nano 501',
                         'plot' => '0',
                     ],
                    2
                     => [
                         'id' => '3',
                         'label' => 'Research Seminars',
                         'plot' => '0',
                     ],
                    3
                     => [
                         'id' => '4',
                         'label' => 'Courses',
                         'plot' => '0',
                     ],
                    4
                     => [
                         'id' => '5',
                         'label' => 'Series',
                         'plot' => '0',
                     ],
                    5
                     => [
                         'id' => '6',
                         'label' => 'Workshops',
                         'plot' => '0',
                     ],
                    6
                     => [
                         'id' => '7',
                         'label' => 'Teaching Materials',
                         'plot' => '0',
                     ],
                    7
                     => [
                         'id' => '8',
                         'label' => 'Other non-interactive Resources',
                         'plot' => '0',
                     ],
                    8
                     => [
                         'id' => '9',
                         'label' => 'Breeze Presentations',
                         'plot' => '0',
                     ],
                    9
                     => [
                         'id' => '10',
                         'label' => 'PDF Files',
                         'plot' => '0',
                     ],
                    10
                     => [
                         'id' => '11',
                         'label' => 'Podcasts',
                         'plot' => '0',
                     ],
                    11
                     => [
                         'id' => '12',
                         'label' => 'Other Documents',
                         'plot' => '0',
                     ],
                ];
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('summary_misc')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('summary_misc')
                    ->tinyInteger('id')->default(0)
                    ->string('label', 255)->default('')
                    ->integer('plot')->default(0)
                    ->uniqueIndex('label', 'label')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $rows =  [
                    0
                     => [
                         'id' => '1',
                         'label' => 'Domains Served',
                         'plot' => '0',
                     ],
                    1
                     => [
                         'id' => '2',
                         'label' => 'Cummulative Interactive User Sessions {10}',
                         'plot' => '0',
                     ],
                    2
                     => [
                         'id' => '3',
                         'label' => 'Cummulative Session Time',
                         'plot' => '0',
                     ],
                    3
                     => [
                         'id' => '4',
                         'label' => 'Visitors {11}',
                         'plot' => '0',
                     ],
                    4
                     => [
                         'id' => '5',
                         'label' => 'Visits {12}',
                         'plot' => '0',
                     ],
                    5
                     => [
                         'id' => '6',
                         'label' => 'New Accounts',
                         'plot' => '0',
                     ],
                    6
                     => [
                         'id' => '7',
                         'label' => 'Max User Logins',
                         'plot' => '0',
                     ],
                    7
                     => [
                         'id' => '8',
                         'label' => 'Web Server Hits',
                         'plot' => '1',
                     ],
                ];
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('summary_simusage')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('summary_simusage')
                    ->tinyInteger('id')->default(0)
                    ->string('label', 255)->default('')
                    ->integer('plot')->default(0)
                    ->uniqueIndex('label', 'label')
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $rows =  [
                    0
                     => [
                         'id' => '1',
                         'label' => 'Simulation Users {5}',
                         'plot' => '1',
                     ],
                    1
                     => [
                         'id' => '2',
                         'label' => 'Simulation Runs',
                         'plot' => '1',
                     ],
                    2
                     => [
                         'id' => '3',
                         'label' => 'Total CPU Time',
                         'plot' => '0',
                     ],
                    3
                     => [
                         'id' => '4',
                         'label' => 'Total Wall Time',
                         'plot' => '0',
                     ],
                    4
                     => [
                         'id' => '5',
                         'label' => 'Total Interaction Time',
                         'plot' => '0',
                     ],
                    5
                     => [
                         'id' => '6',
                         'label' => 'Users with > 10 mins of CPU Time',
                         'plot' => '0',
                     ],
                    6
                     => [
                         'id' => '7',
                         'label' => 'Avg. Number of Simulation Runs/User',
                         'plot' => '0',
                     ],
                    7
                     => [
                         'id' => '8',
                         'label' => 'Avg. Time between First and Last Simulation',
                         'plot' => '0',
                     ],
                    8
                     => [
                         'id' => '9',
                         'label' => 'Repeat Users with > 10 Simulation Jobs',
                         'plot' => '0',
                     ],
                    9
                     => [
                         'id' => '10',
                         'label' => 'Repeat Users with > 3 Months {9}',
                         'plot' => '0',
                     ],
                ];
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('continents')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('continents')
                    ->char('continentSHORT', 2)->default('')
                    ->string('continentLONG', 45)->default('')
                    ->uniqueIndex('continentSHORT_continentLONG', ['continentSHORT', 'continentLONG'])
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $rows =  [
                    0
                     => [
                         'continentSHORT' => 'AF',
                         'continentLONG' => 'Africa',
                     ],
                    1
                     => [
                         'continentSHORT' => 'AN',
                         'continentLONG' => 'Antartica',
                     ],
                    2
                     => [
                         'continentSHORT' => 'AS',
                         'continentLONG' => 'Asia',
                     ],
                    3
                     => [
                         'continentSHORT' => 'EU',
                         'continentLONG' => 'Europe',
                     ],
                    4
                     => [
                         'continentSHORT' => 'NA',
                         'continentLONG' => 'North America',
                     ],
                    5
                     => [
                         'continentSHORT' => 'OC',
                         'continentLONG' => 'Oceania',
                     ],
                    6
                     => [
                         'continentSHORT' => 'SA',
                         'continentLONG' => 'South America',
                     ],
                ];
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('country_continent')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('country_continent')
                    ->char('country', 2)->default('')
                    ->char('continent', 2)->default('')
                    ->primaryKey(['country', 'continent'])
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $rows =  [
                    0
                     => [
                         'country' => 'AD',
                         'continent' => 'EU',
                     ],
                    1
                     => [
                         'country' => 'AE',
                         'continent' => 'AS',
                     ],
                    2
                     => [
                         'country' => 'AF',
                         'continent' => 'AS',
                     ],
                    3
                     => [
                         'country' => 'AG',
                         'continent' => 'NA',
                     ],
                    4
                     => [
                         'country' => 'AI',
                         'continent' => 'NA',
                     ],
                    5
                     => [
                         'country' => 'AL',
                         'continent' => 'EU',
                     ],
                    6
                     => [
                         'country' => 'AM',
                         'continent' => 'EU',
                     ],
                    7
                     => [
                         'country' => 'AN',
                         'continent' => 'NA',
                     ],
                    8
                     => [
                         'country' => 'AO',
                         'continent' => 'AF',
                     ],
                    9
                     => [
                         'country' => 'AQ',
                         'continent' => 'AN',
                     ],
                    10
                     => [
                         'country' => 'AR',
                         'continent' => 'SA',
                     ],
                    11
                     => [
                         'country' => 'AS',
                         'continent' => 'OC',
                     ],
                    12
                     => [
                         'country' => 'AT',
                         'continent' => 'EU',
                     ],
                    13
                     => [
                         'country' => 'AU',
                         'continent' => 'OC',
                     ],
                    14
                     => [
                         'country' => 'AW',
                         'continent' => 'NA',
                     ],
                    15
                     => [
                         'country' => 'AX',
                         'continent' => 'EU',
                     ],
                    16
                     => [
                         'country' => 'AZ',
                         'continent' => 'EU',
                     ],
                    17
                     => [
                         'country' => 'BA',
                         'continent' => 'EU',
                     ],
                    18
                     => [
                         'country' => 'BB',
                         'continent' => 'NA',
                     ],
                    19
                     => [
                         'country' => 'BD',
                         'continent' => 'AS',
                     ],
                    20
                     => [
                         'country' => 'BE',
                         'continent' => 'EU',
                     ],
                    21
                     => [
                         'country' => 'BF',
                         'continent' => 'AF',
                     ],
                    22
                     => [
                         'country' => 'BG',
                         'continent' => 'EU',
                     ],
                    23
                     => [
                         'country' => 'BH',
                         'continent' => 'AS',
                     ],
                    24
                     => [
                         'country' => 'BI',
                         'continent' => 'AF',
                     ],
                    25
                     => [
                         'country' => 'BJ',
                         'continent' => 'AF',
                     ],
                    26
                     => [
                         'country' => 'BM',
                         'continent' => 'NA',
                     ],
                    27
                     => [
                         'country' => 'BN',
                         'continent' => 'AS',
                     ],
                    28
                     => [
                         'country' => 'BO',
                         'continent' => 'SA',
                     ],
                    29
                     => [
                         'country' => 'BR',
                         'continent' => 'SA',
                     ],
                    30
                     => [
                         'country' => 'BS',
                         'continent' => 'NA',
                     ],
                    31
                     => [
                         'country' => 'BT',
                         'continent' => 'AS',
                     ],
                    32
                     => [
                         'country' => 'BV',
                         'continent' => 'AN',
                     ],
                    33
                     => [
                         'country' => 'BW',
                         'continent' => 'AF',
                     ],
                    34
                     => [
                         'country' => 'BY',
                         'continent' => 'EU',
                     ],
                    35
                     => [
                         'country' => 'BZ',
                         'continent' => 'NA',
                     ],
                    36
                     => [
                         'country' => 'CA',
                         'continent' => 'NA',
                     ],
                    37
                     => [
                         'country' => 'CC',
                         'continent' => 'AS',
                     ],
                    38
                     => [
                         'country' => 'CD',
                         'continent' => 'AF',
                     ],
                    39
                     => [
                         'country' => 'CF',
                         'continent' => 'AF',
                     ],
                    40
                     => [
                         'country' => 'CG',
                         'continent' => 'AF',
                     ],
                    41
                     => [
                         'country' => 'CH',
                         'continent' => 'EU',
                     ],
                    42
                     => [
                         'country' => 'CI',
                         'continent' => 'AF',
                     ],
                    43
                     => [
                         'country' => 'CK',
                         'continent' => 'OC',
                     ],
                    44
                     => [
                         'country' => 'CL',
                         'continent' => 'SA',
                     ],
                    45
                     => [
                         'country' => 'CM',
                         'continent' => 'AF',
                     ],
                    46
                     => [
                         'country' => 'CN',
                         'continent' => 'AS',
                     ],
                    47
                     => [
                         'country' => 'CO',
                         'continent' => 'SA',
                     ],
                    48
                     => [
                         'country' => 'CR',
                         'continent' => 'NA',
                     ],
                    49
                     => [
                         'country' => 'CU',
                         'continent' => 'NA',
                     ],
                    50
                     => [
                         'country' => 'CV',
                         'continent' => 'AF',
                     ],
                    51
                     => [
                         'country' => 'CX',
                         'continent' => 'AS',
                     ],
                    52
                     => [
                         'country' => 'CY',
                         'continent' => 'EU',
                     ],
                    53
                     => [
                         'country' => 'CZ',
                         'continent' => 'EU',
                     ],
                    54
                     => [
                         'country' => 'DE',
                         'continent' => 'EU',
                     ],
                    55
                     => [
                         'country' => 'DJ',
                         'continent' => 'AF',
                     ],
                    56
                     => [
                         'country' => 'DK',
                         'continent' => 'EU',
                     ],
                    57
                     => [
                         'country' => 'DM',
                         'continent' => 'NA',
                     ],
                    58
                     => [
                         'country' => 'DO',
                         'continent' => 'NA',
                     ],
                    59
                     => [
                         'country' => 'DZ',
                         'continent' => 'AF',
                     ],
                    60
                     => [
                         'country' => 'EC',
                         'continent' => 'SA',
                     ],
                    61
                     => [
                         'country' => 'EE',
                         'continent' => 'EU',
                     ],
                    62
                     => [
                         'country' => 'EG',
                         'continent' => 'AF',
                     ],
                    63
                     => [
                         'country' => 'EH',
                         'continent' => 'AF',
                     ],
                    64
                     => [
                         'country' => 'ER',
                         'continent' => 'AF',
                     ],
                    65
                     => [
                         'country' => 'ES',
                         'continent' => 'EU',
                     ],
                    66
                     => [
                         'country' => 'ET',
                         'continent' => 'AF',
                     ],
                    67
                     => [
                         'country' => 'FI',
                         'continent' => 'EU',
                     ],
                    68
                     => [
                         'country' => 'FJ',
                         'continent' => 'OC',
                     ],
                    69
                     => [
                         'country' => 'FK',
                         'continent' => 'SA',
                     ],
                    70
                     => [
                         'country' => 'FM',
                         'continent' => 'OC',
                     ],
                    71
                     => [
                         'country' => 'FO',
                         'continent' => 'EU',
                     ],
                    72
                     => [
                         'country' => 'FR',
                         'continent' => 'EU',
                     ],
                    73
                     => [
                         'country' => 'GA',
                         'continent' => 'AF',
                     ],
                    74
                     => [
                         'country' => 'GD',
                         'continent' => 'NA',
                     ],
                    75
                     => [
                         'country' => 'GE',
                         'continent' => 'EU',
                     ],
                    76
                     => [
                         'country' => 'GF',
                         'continent' => 'SA',
                     ],
                    77
                     => [
                         'country' => 'GG',
                         'continent' => 'EU',
                     ],
                    78
                     => [
                         'country' => 'GH',
                         'continent' => 'AF',
                     ],
                    79
                     => [
                         'country' => 'GI',
                         'continent' => 'EU',
                     ],
                    80
                     => [
                         'country' => 'GL',
                         'continent' => 'NA',
                     ],
                    81
                     => [
                         'country' => 'GM',
                         'continent' => 'AF',
                     ],
                    82
                     => [
                         'country' => 'GN',
                         'continent' => 'AF',
                     ],
                    83
                     => [
                         'country' => 'GP',
                         'continent' => 'NA',
                     ],
                    84
                     => [
                         'country' => 'GQ',
                         'continent' => 'AF',
                     ],
                    85
                     => [
                         'country' => 'GR',
                         'continent' => 'EU',
                     ],
                    86
                     => [
                         'country' => 'GS',
                         'continent' => 'EU',
                     ],
                    87
                     => [
                         'country' => 'GT',
                         'continent' => 'NA',
                     ],
                    88
                     => [
                         'country' => 'GU',
                         'continent' => 'OC',
                     ],
                    89
                     => [
                         'country' => 'GW',
                         'continent' => 'AF',
                     ],
                    90
                     => [
                         'country' => 'GY',
                         'continent' => 'SA',
                     ],
                    91
                     => [
                         'country' => 'HK',
                         'continent' => 'AS',
                     ],
                    92
                     => [
                         'country' => 'HM',
                         'continent' => 'OC',
                     ],
                    93
                     => [
                         'country' => 'HN',
                         'continent' => 'NA',
                     ],
                    94
                     => [
                         'country' => 'HR',
                         'continent' => 'EU',
                     ],
                    95
                     => [
                         'country' => 'HT',
                         'continent' => 'NA',
                     ],
                    96
                     => [
                         'country' => 'HU',
                         'continent' => 'EU',
                     ],
                    97
                     => [
                         'country' => 'ID',
                         'continent' => 'AS',
                     ],
                    98
                     => [
                         'country' => 'IE',
                         'continent' => 'EU',
                     ],
                    99
                     => [
                         'country' => 'IL',
                         'continent' => 'AS',
                     ],
                    100
                     => [
                         'country' => 'IM',
                         'continent' => 'EU',
                     ],
                    101
                     => [
                         'country' => 'IN',
                         'continent' => 'AS',
                     ],
                    102
                     => [
                         'country' => 'IO',
                         'continent' => 'AS',
                     ],
                    103
                     => [
                         'country' => 'IQ',
                         'continent' => 'AS',
                     ],
                    104
                     => [
                         'country' => 'IR',
                         'continent' => 'AS',
                     ],
                    105
                     => [
                         'country' => 'IS',
                         'continent' => 'EU',
                     ],
                    106
                     => [
                         'country' => 'IT',
                         'continent' => 'EU',
                     ],
                    107
                     => [
                         'country' => 'JE',
                         'continent' => 'EU',
                     ],
                    108
                     => [
                         'country' => 'JM',
                         'continent' => 'NA',
                     ],
                    109
                     => [
                         'country' => 'JO',
                         'continent' => 'AS',
                     ],
                    110
                     => [
                         'country' => 'JP',
                         'continent' => 'AS',
                     ],
                    111
                     => [
                         'country' => 'KE',
                         'continent' => 'AF',
                     ],
                    112
                     => [
                         'country' => 'KG',
                         'continent' => 'AS',
                     ],
                    113
                     => [
                         'country' => 'KH',
                         'continent' => 'AS',
                     ],
                    114
                     => [
                         'country' => 'KI',
                         'continent' => 'OC',
                     ],
                    115
                     => [
                         'country' => 'KM',
                         'continent' => 'AF',
                     ],
                    116
                     => [
                         'country' => 'KN',
                         'continent' => 'NA',
                     ],
                    117
                     => [
                         'country' => 'KP',
                         'continent' => 'AS',
                     ],
                    118
                     => [
                         'country' => 'KR',
                         'continent' => 'AS',
                     ],
                    119
                     => [
                         'country' => 'KW',
                         'continent' => 'AS',
                     ],
                    120
                     => [
                         'country' => 'KY',
                         'continent' => 'NA',
                     ],
                    121
                     => [
                         'country' => 'KZ',
                         'continent' => 'AS',
                     ],
                    122
                     => [
                         'country' => 'LA',
                         'continent' => 'AS',
                     ],
                    123
                     => [
                         'country' => 'LB',
                         'continent' => 'AS',
                     ],
                    124
                     => [
                         'country' => 'LC',
                         'continent' => 'NA',
                     ],
                    125
                     => [
                         'country' => 'LI',
                         'continent' => 'EU',
                     ],
                    126
                     => [
                         'country' => 'LK',
                         'continent' => 'AS',
                     ],
                    127
                     => [
                         'country' => 'LR',
                         'continent' => 'AF',
                     ],
                    128
                     => [
                         'country' => 'LS',
                         'continent' => 'AF',
                     ],
                    129
                     => [
                         'country' => 'LT',
                         'continent' => 'EU',
                     ],
                    130
                     => [
                         'country' => 'LU',
                         'continent' => 'EU',
                     ],
                    131
                     => [
                         'country' => 'LV',
                         'continent' => 'EU',
                     ],
                    132
                     => [
                         'country' => 'LY',
                         'continent' => 'AF',
                     ],
                    133
                     => [
                         'country' => 'MA',
                         'continent' => 'AF',
                     ],
                    134
                     => [
                         'country' => 'MC',
                         'continent' => 'EU',
                     ],
                    135
                     => [
                         'country' => 'MD',
                         'continent' => 'EU',
                     ],
                    136
                     => [
                         'country' => 'ME',
                         'continent' => 'EU',
                     ],
                    137
                     => [
                         'country' => 'MF',
                         'continent' => 'NA',
                     ],
                    138
                     => [
                         'country' => 'MG',
                         'continent' => 'AF',
                     ],
                    139
                     => [
                         'country' => 'MH',
                         'continent' => 'OC',
                     ],
                    140
                     => [
                         'country' => 'MK',
                         'continent' => 'EU',
                     ],
                    141
                     => [
                         'country' => 'ML',
                         'continent' => 'AF',
                     ],
                    142
                     => [
                         'country' => 'MM',
                         'continent' => 'AS',
                     ],
                    143
                     => [
                         'country' => 'MN',
                         'continent' => 'AS',
                     ],
                    144
                     => [
                         'country' => 'MO',
                         'continent' => 'AS',
                     ],
                    145
                     => [
                         'country' => 'MP',
                         'continent' => 'OC',
                     ],
                    146
                     => [
                         'country' => 'MQ',
                         'continent' => 'NA',
                     ],
                    147
                     => [
                         'country' => 'MR',
                         'continent' => 'AF',
                     ],
                    148
                     => [
                         'country' => 'MS',
                         'continent' => 'NA',
                     ],
                    149
                     => [
                         'country' => 'MT',
                         'continent' => 'EU',
                     ],
                    150
                     => [
                         'country' => 'MU',
                         'continent' => 'AF',
                     ],
                    151
                     => [
                         'country' => 'MV',
                         'continent' => 'AS',
                     ],
                    152
                     => [
                         'country' => 'MW',
                         'continent' => 'AF',
                     ],
                    153
                     => [
                         'country' => 'MX',
                         'continent' => 'NA',
                     ],
                    154
                     => [
                         'country' => 'MY',
                         'continent' => 'AS',
                     ],
                    155
                     => [
                         'country' => 'MZ',
                         'continent' => 'AF',
                     ],
                    156
                     => [
                         'country' => 'NA',
                         'continent' => 'AF',
                     ],
                    157
                     => [
                         'country' => 'NC',
                         'continent' => 'OC',
                     ],
                    158
                     => [
                         'country' => 'NE',
                         'continent' => 'AF',
                     ],
                    159
                     => [
                         'country' => 'NF',
                         'continent' => 'OC',
                     ],
                    160
                     => [
                         'country' => 'NG',
                         'continent' => 'AF',
                     ],
                    161
                     => [
                         'country' => 'NI',
                         'continent' => 'NA',
                     ],
                    162
                     => [
                         'country' => 'NL',
                         'continent' => 'EU',
                     ],
                    163
                     => [
                         'country' => 'NO',
                         'continent' => 'EU',
                     ],
                    164
                     => [
                         'country' => 'NP',
                         'continent' => 'AS',
                     ],
                    165
                     => [
                         'country' => 'NR',
                         'continent' => 'OC',
                     ],
                    166
                     => [
                         'country' => 'NU',
                         'continent' => 'OC',
                     ],
                    167
                     => [
                         'country' => 'NZ',
                         'continent' => 'OC',
                     ],
                    168
                     => [
                         'country' => 'OM',
                         'continent' => 'AS',
                     ],
                    169
                     => [
                         'country' => 'PA',
                         'continent' => 'NA',
                     ],
                    170
                     => [
                         'country' => 'PE',
                         'continent' => 'SA',
                     ],
                    171
                     => [
                         'country' => 'PF',
                         'continent' => 'OC',
                     ],
                    172
                     => [
                         'country' => 'PG',
                         'continent' => 'OC',
                     ],
                    173
                     => [
                         'country' => 'PH',
                         'continent' => 'AS',
                     ],
                    174
                     => [
                         'country' => 'PK',
                         'continent' => 'AS',
                     ],
                    175
                     => [
                         'country' => 'PL',
                         'continent' => 'EU',
                     ],
                    176
                     => [
                         'country' => 'PM',
                         'continent' => 'NA',
                     ],
                    177
                     => [
                         'country' => 'PN',
                         'continent' => 'OC',
                     ],
                    178
                     => [
                         'country' => 'PR',
                         'continent' => 'NA',
                     ],
                    179
                     => [
                         'country' => 'PS',
                         'continent' => 'AS',
                     ],
                    180
                     => [
                         'country' => 'PT',
                         'continent' => 'EU',
                     ],
                    181
                     => [
                         'country' => 'PW',
                         'continent' => 'OC',
                     ],
                    182
                     => [
                         'country' => 'PY',
                         'continent' => 'SA',
                     ],
                    183
                     => [
                         'country' => 'QA',
                         'continent' => 'AS',
                     ],
                    184
                     => [
                         'country' => 'RE',
                         'continent' => 'AF',
                     ],
                    185
                     => [
                         'country' => 'RO',
                         'continent' => 'EU',
                     ],
                    186
                     => [
                         'country' => 'RS',
                         'continent' => 'EU',
                     ],
                    187
                     => [
                         'country' => 'RU',
                         'continent' => 'EU',
                     ],
                    188
                     => [
                         'country' => 'RW',
                         'continent' => 'AF',
                     ],
                    189
                     => [
                         'country' => 'SA',
                         'continent' => 'AS',
                     ],
                    190
                     => [
                         'country' => 'SB',
                         'continent' => 'OC',
                     ],
                    191
                     => [
                         'country' => 'SC',
                         'continent' => 'AF',
                     ],
                    192
                     => [
                         'country' => 'SD',
                         'continent' => 'AF',
                     ],
                    193
                     => [
                         'country' => 'SE',
                         'continent' => 'EU',
                     ],
                    194
                     => [
                         'country' => 'SG',
                         'continent' => 'AS',
                     ],
                    195
                     => [
                         'country' => 'SH',
                         'continent' => 'AF',
                     ],
                    196
                     => [
                         'country' => 'SI',
                         'continent' => 'EU',
                     ],
                    197
                     => [
                         'country' => 'SJ',
                         'continent' => 'EU',
                     ],
                    198
                     => [
                         'country' => 'SK',
                         'continent' => 'EU',
                     ],
                    199
                     => [
                         'country' => 'SL',
                         'continent' => 'AF',
                     ],
                    200
                     => [
                         'country' => 'SM',
                         'continent' => 'EU',
                     ],
                    201
                     => [
                         'country' => 'SN',
                         'continent' => 'AF',
                     ],
                    202
                     => [
                         'country' => 'SO',
                         'continent' => 'AF',
                     ],
                    203
                     => [
                         'country' => 'SR',
                         'continent' => 'SA',
                     ],
                    204
                     => [
                         'country' => 'ST',
                         'continent' => 'AF',
                     ],
                    205
                     => [
                         'country' => 'SV',
                         'continent' => 'NA',
                     ],
                    206
                     => [
                         'country' => 'SY',
                         'continent' => 'EU',
                     ],
                    207
                     => [
                         'country' => 'SZ',
                         'continent' => 'AF',
                     ],
                    208
                     => [
                         'country' => 'TC',
                         'continent' => 'NA',
                     ],
                    209
                     => [
                         'country' => 'TD',
                         'continent' => 'AF',
                     ],
                    210
                     => [
                         'country' => 'TF',
                         'continent' => 'AN',
                     ],
                    211
                     => [
                         'country' => 'TG',
                         'continent' => 'AF',
                     ],
                    212
                     => [
                         'country' => 'TH',
                         'continent' => 'AS',
                     ],
                    213
                     => [
                         'country' => 'TJ',
                         'continent' => 'AS',
                     ],
                    214
                     => [
                         'country' => 'TK',
                         'continent' => 'OC',
                     ],
                    215
                     => [
                         'country' => 'TL',
                         'continent' => 'AS',
                     ],
                    216
                     => [
                         'country' => 'TM',
                         'continent' => 'AS',
                     ],
                    217
                     => [
                         'country' => 'TN',
                         'continent' => 'AF',
                     ],
                    218
                     => [
                         'country' => 'TO',
                         'continent' => 'OC',
                     ],
                    219
                     => [
                         'country' => 'TR',
                         'continent' => 'EU',
                     ],
                    220
                     => [
                         'country' => 'TT',
                         'continent' => 'NA',
                     ],
                    221
                     => [
                         'country' => 'TV',
                         'continent' => 'OC',
                     ],
                    222
                     => [
                         'country' => 'TW',
                         'continent' => 'AS',
                     ],
                    223
                     => [
                         'country' => 'TZ',
                         'continent' => 'AF',
                     ],
                    224
                     => [
                         'country' => 'UA',
                         'continent' => 'EU',
                     ],
                    225
                     => [
                         'country' => 'UG',
                         'continent' => 'AF',
                     ],
                    226
                     => [
                         'country' => 'UK',
                         'continent' => 'EU',
                     ],
                    227
                     => [
                         'country' => 'UM',
                         'continent' => 'NA',
                     ],
                    228
                     => [
                         'country' => 'US',
                         'continent' => 'NA',
                     ],
                    229
                     => [
                         'country' => 'UY',
                         'continent' => 'SA',
                     ],
                    230
                     => [
                         'country' => 'UZ',
                         'continent' => 'AS',
                     ],
                    231
                     => [
                         'country' => 'VA',
                         'continent' => 'EU',
                     ],
                    232
                     => [
                         'country' => 'VC',
                         'continent' => 'NA',
                     ],
                    233
                     => [
                         'country' => 'VE',
                         'continent' => 'SA',
                     ],
                    234
                     => [
                         'country' => 'VG',
                         'continent' => 'EU',
                     ],
                    235
                     => [
                         'country' => 'VI',
                         'continent' => 'NA',
                     ],
                    236
                     => [
                         'country' => 'VN',
                         'continent' => 'AS',
                     ],
                    237
                     => [
                         'country' => 'VU',
                         'continent' => 'OC',
                     ],
                    238
                     => [
                         'country' => 'WF',
                         'continent' => 'OC',
                     ],
                    239
                     => [
                         'country' => 'WS',
                         'continent' => 'OC',
                     ],
                    240
                     => [
                         'country' => 'YE',
                         'continent' => 'AS',
                     ],
                    241
                     => [
                         'country' => 'YT',
                         'continent' => 'AF',
                     ],
                    242
                     => [
                         'country' => 'ZA',
                         'continent' => 'AF',
                     ],
                    243
                     => [
                         'country' => 'ZM',
                         'continent' => 'AF',
                     ],
                    244
                     => [
                         'country' => 'ZW',
                         'continent' => 'AF',
                     ],
                ];
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('exclude_list')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('exclude_list')
                    ->string('filter', 65)
                    ->string('type', 65)->default('domain')
                    ->string('notes', 120)->nullable()
                    ->uniqueIndex('filter_type', ['filter', 'type'])
                    ->engine('MyISAM')
                    ->charset('utf8')
                    ->execute();

                $rows =  [
                    0
                     => [
                         'filter' => 'googlebot.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    1
                     => [
                         'filter' => 'crawl.yahoo.net',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    2
                     => [
                         'filter' => 'search.msn.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    3
                     => [
                         'filter' => 'inktomisearch.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    4
                     => [
                         'filter' => 'msnbot.msn.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    5
                     => [
                         'filter' => '%googlebot.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    6
                     => [
                         'filter' => '%crawl.yahoo.net',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    7
                     => [
                         'filter' => '%search.msn.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    8
                     => [
                         'filter' => '%inktomisearch.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    9
                     => [
                         'filter' => '%msnbot.msn.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    10
                     => [
                         'filter' => 'rtmp1.hubzero.org',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    11
                     => [
                         'filter' => 'spider%.yandex.ru',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    12
                     => [
                         'filter' => 'crawl%.cuill.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    13
                     => [
                         'filter' => 'crawler%.ask.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    14
                     => [
                         'filter' => 'crawl%.dotnetdotcom.org',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    15
                     => [
                         'filter' => '%robot.spinn3r.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    16
                     => [
                         'filter' => 'livebot-%.search.live.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    17
                     => [
                         'filter' => 'arch%.miss.archive.org',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    18
                     => [
                         'filter' => 'crawl%.exabot.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    19
                     => [
                         'filter' => 'crawler.bloglines.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    20
                     => [
                         'filter' => 'msnbot-%.msn.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    21
                     => [
                         'filter' => 'crawler%.fastsearch.net',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    22
                     => [
                         'filter' => '%.robot.spinn3r.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    23
                     => [
                         'filter' => 'spider%.picsearch.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    24
                     => [
                         'filter' => 'spider%.logika.net',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    25
                     => [
                         'filter' => 'spider%.mail.ru',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    26
                     => [
                         'filter' => 'spider%.proxy.aol.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    27
                     => [
                         'filter' => 'spider.chem.uw.edu.pl',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    28
                     => [
                         'filter' => 'crawler4.irl.cs.tamu.edu',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    29
                     => [
                         'filter' => '%.crawl.baidu.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    30
                     => [
                         'filter' => 'crawl%.searchme.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    31
                     => [
                         'filter' => 'speedyspider.entireweb.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    32
                     => [
                         'filter' => 'robot.acoon.de',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    33
                     => [
                         'filter' => 'crawler%.gingersoftware.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    34
                     => [
                         'filter' => 'crawler%.aurarianetworks.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    35
                     => [
                         'filter' => 'crawler.flatlandindustries.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    36
                     => [
                         'filter' => 'robot%.feeds.yandex.net',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    37
                     => [
                         'filter' => 'ng20.exabot.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    38
                     => [
                         'filter' => 'crawler.kalooga.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    39
                     => [
                         'filter' => '%crawler%.cs.tamu.edu',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    40
                     => [
                         'filter' => 'turbospider.yandex.ru',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    41
                     => [
                         'filter' => 'crawlers.looksmart.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    42
                     => [
                         'filter' => 'red-gw2.exabot.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    43
                     => [
                         'filter' => 'robot%.rambler.ru',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    44
                     => [
                         'filter' => '%crawler%.ig.ntnu.no',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    45
                     => [
                         'filter' => 'crawl%.us.archive.org',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    46
                     => [
                         'filter' => '%dnaspider%.mia.lycos.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    47
                     => [
                         'filter' => '%crawler%.x-echo.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    48
                     => [
                         'filter' => 'robot.szukacz.pl',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    49
                     => [
                         'filter' => 'spider.interseek.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    50
                     => [
                         'filter' => 'spider%.szukaj.onet.pl',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    51
                     => [
                         'filter' => '%spider.entireweb.com',
                         'type' => 'host',
                         'notes' => 'bot',
                     ],
                    52
                     => [
                         'filter' => '137.187.22.',
                         'type' => 'ip',
                         'notes' => 'NIH Crawler',
                     ],
                    53
                     => [
                         'filter' => '128.231.86.',
                         'type' => 'ip',
                         'notes' => 'NIH Crawler',
                     ],
                    54
                     => [
                         'filter' => '128.231.88.',
                         'type' => 'ip',
                         'notes' => 'NIH Crawler',
                     ],
                    55
                     => [
                         'filter' => '128.46.16.17',
                         'type' => 'ip',
                         'notes' => 'rtmp1.hubzero.org',
                     ],
                    56
                     => [
                         'filter' => '128.210.7.18',
                         'type' => 'ip',
                         'notes' => 'Purdue Google Appliance',
                     ],
                    57
                     => [
                         'filter' => '128.46.16.59',
                         'type' => 'ip',
                         'notes' => 'Pascal Meunier\\\'s security crawler',
                     ],
                    58
                     => [
                         'filter' => 'zeus.nj.nec.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    59
                     => [
                         'filter' => 'yandex.ru',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    60
                     => [
                         'filter' => 'yandex.net',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    61
                     => [
                         'filter' => 'yahoo.net',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    62
                     => [
                         'filter' => 'yahoo.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    63
                     => [
                         'filter' => 'xs4.kso.co.uk',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    64
                     => [
                         'filter' => 'worio.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    65
                     => [
                         'filter' => 'whizbang.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    66
                     => [
                         'filter' => 'websquash.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    67
                     => [
                         'filter' => 'websmostlinked.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    68
                     => [
                         'filter' => 'webclipping.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    69
                     => [
                         'filter' => 'webbot.org',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    70
                     => [
                         'filter' => 'turnitin.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    71
                     => [
                         'filter' => 'tracerlock.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    72
                     => [
                         'filter' => 'tpiol.tpiol.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    73
                     => [
                         'filter' => 'tpiol.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    74
                     => [
                         'filter' => 'teoma.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    75
                     => [
                         'filter' => 'searchme.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    76
                     => [
                         'filter' => 'san2.attens.net',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    77
                     => [
                         'filter' => 'sac.overture.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    78
                     => [
                         'filter' => 'robotiker.es',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    79
                     => [
                         'filter' => 'rfcrawler.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    80
                     => [
                         'filter' => 'rcac.purdue.edu',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    81
                     => [
                         'filter' => 'punch.purdue.edu',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    82
                     => [
                         'filter' => 'picsearch.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    83
                     => [
                         'filter' => 'phx.gbl',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    84
                     => [
                         'filter' => 'panchma.tivra.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    85
                     => [
                         'filter' => 'paginasamarillas.es',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    86
                     => [
                         'filter' => 'overture.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    87
                     => [
                         'filter' => 'morgue1.corp.yahoo.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    88
                     => [
                         'filter' => 'metacarta.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    89
                     => [
                         'filter' => 'markwatch.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    90
                     => [
                         'filter' => 'looksmart.net',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    91
                     => [
                         'filter' => 'looksmart.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    92
                     => [
                         'filter' => 'live.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    93
                     => [
                         'filter' => 'live-servers.net',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    94
                     => [
                         'filter' => 'linuxhardcore.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    95
                     => [
                         'filter' => 'jeteye.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    96
                     => [
                         'filter' => 'internetserviceteam.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    97
                     => [
                         'filter' => 'inktomi.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    98
                     => [
                         'filter' => 'idle.eidetica.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    99
                     => [
                         'filter' => 'hanta.yahoo.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    100
                     => [
                         'filter' => 'girafa.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    101
                     => [
                         'filter' => 'gigablast.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    102
                     => [
                         'filter' => 'fastsearch.net',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    103
                     => [
                         'filter' => 'exabot.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    104
                     => [
                         'filter' => 'ev1servers.net',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    105
                     => [
                         'filter' => 'entireweb.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    106
                     => [
                         'filter' => 'cuill.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    107
                     => [
                         'filter' => 'crawler918.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    108
                     => [
                         'filter' => 'crawl8-public.alexa.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    109
                     => [
                         'filter' => 'cosmixcorp.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    110
                     => [
                         'filter' => 'brain.grub.org',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    111
                     => [
                         'filter' => 'bloglines.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    112
                     => [
                         'filter' => 'betaspider.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    113
                     => [
                         'filter' => 'become.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    114
                     => [
                         'filter' => 'authoritativeweb.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    115
                     => [
                         'filter' => 'attens.net',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    116
                     => [
                         'filter' => 'ask.com',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    117
                     => [
                         'filter' => 'archive.org',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    118
                     => [
                         'filter' => '67.108.223.130.ptr.us.xo.net',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    119
                     => [
                         'filter' => '67.106.152.131.ptr.us.xo.net',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    120
                     => [
                         'filter' => '66.237.109.194.ptr.us.xo.net',
                         'type' => 'domain',
                         'notes' => 'bot',
                     ],
                    121
                     => [
                         'filter' => 'task=diskusage',
                         'type' => 'url',
                         'notes' => 'Middleware Disk Quota Checker',
                     ],
                    122
                     => [
                         'filter' => 'gsa-purdue-crawler',
                         'type' => 'useragent',
                         'notes' => 'Purdue GSA Crawler',
                     ],
                ];
            } catch (\Exception $e) {
                // Internally catch errors and only return a warning.
                $this->setError('Failed to create stats table. Try running again with elevated privileges', 'warning');
                return false;
            }
        }

        if (!$statsDb->schema()->tableExists('bot_useragents')) {
            try {
                $statsDb
                    ->schema()
                    ->createTable('bot_useragents')
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
