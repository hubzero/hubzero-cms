<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Geocode\Local;

use Hubzero\Facades\Lang;

class LocalProvider extends \Geocoder\Http\Provider\AbstractHttpProvider implements \Geocoder\Provider\Provider
{
    public static $countries = [
        ['code' => 'AF', 'name' => 'Afghanistan', 'continent' => 'Asia'],
        ['code' => 'AX', 'name' => 'Aland Islands', 'continent' => 'Europe'],
        ['code' => 'AL', 'name' => 'Albania', 'continent' => 'Europe'],
        ['code' => 'DZ', 'name' => 'Algeria', 'continent' => 'Africa'],
        ['code' => 'AS', 'name' => 'American Samoa', 'continent' => 'Oceania'],
        ['code' => 'AD', 'name' => 'Andorra', 'continent' => 'Europe'],
        ['code' => 'AO', 'name' => 'Angola', 'continent' => 'Africa'],
        ['code' => 'AI', 'name' => 'Anguilla', 'continent' => 'North America'],
        ['code' => 'AQ', 'name' => 'Antarctica', 'continent' => 'Antarctica'],
        ['code' => 'AG', 'name' => 'Antigua and Barbuda', 'continent' => 'North America'],
        ['code' => 'AR', 'name' => 'Argentina', 'continent' => 'South America'],
        ['code' => 'AM', 'name' => 'Armenia', 'continent' => 'Asia'],
        ['code' => 'AW', 'name' => 'Aruba', 'continent' => 'North America'],
        ['code' => 'AU', 'name' => 'Australia', 'continent' => 'Oceania'],
        ['code' => 'AT', 'name' => 'Austria', 'continent' => 'Europe'],
        ['code' => 'AZ', 'name' => 'Azerbaijan', 'continent' => 'Asia'],
        ['code' => 'BS', 'name' => 'Bahamas', 'continent' => 'North America'],
        ['code' => 'BH', 'name' => 'Bahrain', 'continent' => 'Asia'],
        ['code' => 'BD', 'name' => 'Bangladesh', 'continent' => 'Asia'],
        ['code' => 'BB', 'name' => 'Barbados', 'continent' => 'North America'],
        ['code' => 'BY', 'name' => 'Belarus', 'continent' => 'Europe'],
        ['code' => 'BE', 'name' => 'Belgium', 'continent' => 'Europe'],
        ['code' => 'BZ', 'name' => 'Belize', 'continent' => 'North America'],
        ['code' => 'BJ', 'name' => 'Benin', 'continent' => 'Africa'],
        ['code' => 'BM', 'name' => 'Bermuda', 'continent' => 'North America'],
        ['code' => 'BT', 'name' => 'Bhutan', 'continent' => 'Asia'],
        ['code' => 'BO', 'name' => 'Bolivia', 'continent' => 'South America'],
        ['code' => 'BA', 'name' => 'Bosnia and Herzegovina', 'continent' => 'Europe'],
        ['code' => 'BW', 'name' => 'Botswana', 'continent' => 'Africa'],
        ['code' => 'BV', 'name' => 'Bouvet Island', 'continent' => 'Antarctica'],
        ['code' => 'BR', 'name' => 'Brazil', 'continent' => 'South America'],
        ['code' => 'IO', 'name' => 'British Indian Ocean Territory', 'continent' => 'Asia'],
        ['code' => 'BN', 'name' => 'Brunei Darussalam', 'continent' => 'Asia'],
        ['code' => 'BG', 'name' => 'Bulgaria', 'continent' => 'Europe'],
        ['code' => 'BF', 'name' => 'Burkina Faso', 'continent' => 'Africa'],
        ['code' => 'BI', 'name' => 'Burundi', 'continent' => 'Africa'],
        ['code' => 'KH', 'name' => 'Cambodia', 'continent' => 'Asia'],
        ['code' => 'CM', 'name' => 'Cameroon', 'continent' => 'Africa'],
        ['code' => 'CA', 'name' => 'Canada', 'continent' => 'North America'],
        ['code' => 'CV', 'name' => 'Cape Verde', 'continent' => 'Africa'],
        ['code' => 'KY', 'name' => 'Cayman Islands', 'continent' => 'North America'],
        ['code' => 'CF', 'name' => 'Central African Republic', 'continent' => 'Africa'],
        ['code' => 'TD', 'name' => 'Chad', 'continent' => 'Africa'],
        ['code' => 'CL', 'name' => 'Chile', 'continent' => 'South America'],
        ['code' => 'CN', 'name' => 'China', 'continent' => 'Asia'],
        ['code' => 'CX', 'name' => 'Christmas Island', 'continent' => 'Asia'],
        ['code' => 'CC', 'name' => 'Cocos (Keeling) Islands', 'continent' => 'Asia'],
        ['code' => 'CO', 'name' => 'Colombia', 'continent' => 'South America'],
        ['code' => 'KM', 'name' => 'Comoros', 'continent' => 'Africa'],
        ['code' => 'CG', 'name' => 'Congo', 'continent' => 'Africa'],
        ['code' => 'CD', 'name' => 'The Democratic Republic of The Congo', 'continent' => 'Africa'],
        ['code' => 'CK', 'name' => 'Cook Islands', 'continent' => 'Oceania'],
        ['code' => 'CR', 'name' => 'Costa Rica', 'continent' => 'North America'],
        ['code' => 'CI', 'name' => 'Cote D\'ivoire', 'continent' => 'Africa'],
        ['code' => 'HR', 'name' => 'Croatia', 'continent' => 'Europe'],
        ['code' => 'CU', 'name' => 'Cuba', 'continent' => 'North America'],
        ['code' => 'CY', 'name' => 'Cyprus', 'continent' => 'Asia'],
        ['code' => 'CZ', 'name' => 'Czech Republic', 'continent' => 'Europe'],
        ['code' => 'DK', 'name' => 'Denmark', 'continent' => 'Europe'],
        ['code' => 'DJ', 'name' => 'Djibouti', 'continent' => 'Africa'],
        ['code' => 'DM', 'name' => 'Dominica', 'continent' => 'North America'],
        ['code' => 'DO', 'name' => 'Dominican Republic', 'continent' => 'North America'],
        ['code' => 'EC', 'name' => 'Ecuador', 'continent' => 'South America'],
        ['code' => 'EG', 'name' => 'Egypt', 'continent' => 'Africa'],
        ['code' => 'SV', 'name' => 'El Salvador', 'continent' => 'North America'],
        ['code' => 'GQ', 'name' => 'Equatorial Guinea', 'continent' => 'Africa'],
        ['code' => 'ER', 'name' => 'Eritrea', 'continent' => 'Africa'],
        ['code' => 'EE', 'name' => 'Estonia', 'continent' => 'Europe'],
        ['code' => 'ET', 'name' => 'Ethiopia', 'continent' => 'Africa'],
        ['code' => 'FK', 'name' => 'Falkland Islands (Malvinas)', 'continent' => 'South America'],
        ['code' => 'FO', 'name' => 'Faroe Islands', 'continent' => 'Europe'],
        ['code' => 'FJ', 'name' => 'Fiji', 'continent' => 'Oceania'],
        ['code' => 'FI', 'name' => 'Finland', 'continent' => 'Europe'],
        ['code' => 'FR', 'name' => 'France', 'continent' => 'Europe'],
        ['code' => 'GF', 'name' => 'French Guiana', 'continent' => 'South America'],
        ['code' => 'PF', 'name' => 'French Polynesia', 'continent' => 'Oceania'],
        ['code' => 'TF', 'name' => 'French Southern Territories', 'continent' => 'Antarctica'],
        ['code' => 'GA', 'name' => 'Gabon', 'continent' => 'Africa'],
        ['code' => 'GM', 'name' => 'Gambia', 'continent' => 'Africa'],
        ['code' => 'GE', 'name' => 'Georgia', 'continent' => 'Asia'],
        ['code' => 'DE', 'name' => 'Germany', 'continent' => 'Europe'],
        ['code' => 'GH', 'name' => 'Ghana', 'continent' => 'Africa'],
        ['code' => 'GI', 'name' => 'Gibraltar', 'continent' => 'Europe'],
        ['code' => 'GR', 'name' => 'Greece', 'continent' => 'Europe'],
        ['code' => 'GL', 'name' => 'Greenland', 'continent' => 'North America'],
        ['code' => 'GD', 'name' => 'Grenada', 'continent' => 'North America'],
        ['code' => 'GP', 'name' => 'Guadeloupe', 'continent' => 'North America'],
        ['code' => 'GU', 'name' => 'Guam', 'continent' => 'Oceania'],
        ['code' => 'GT', 'name' => 'Guatemala', 'continent' => 'North America'],
        ['code' => 'GG', 'name' => 'Guernsey', 'continent' => 'Europe'],
        ['code' => 'GN', 'name' => 'Guinea', 'continent' => 'Africa'],
        ['code' => 'GW', 'name' => 'Guinea-bissau', 'continent' => 'Africa'],
        ['code' => 'GY', 'name' => 'Guyana', 'continent' => 'South America'],
        ['code' => 'HT', 'name' => 'Haiti', 'continent' => 'North America'],
        ['code' => 'HM', 'name' => 'Heard Island and Mcdonald Islands', 'continent' => 'Antarctica'],
        ['code' => 'VA', 'name' => 'Holy See (Vatican City State)', 'continent' => 'Europe'],
        ['code' => 'HN', 'name' => 'Honduras', 'continent' => 'North America'],
        ['code' => 'HK', 'name' => 'Hong Kong', 'continent' => 'Asia'],
        ['code' => 'HU', 'name' => 'Hungary', 'continent' => 'Europe'],
        ['code' => 'IS', 'name' => 'Iceland', 'continent' => 'Europe'],
        ['code' => 'IN', 'name' => 'India', 'continent' => 'Asia'],
        ['code' => 'ID', 'name' => 'Indonesia', 'continent' => 'Asia'],
        ['code' => 'IR', 'name' => 'Iran', 'continent' => 'Asia'],
        ['code' => 'IQ', 'name' => 'Iraq', 'continent' => 'Asia'],
        ['code' => 'IE', 'name' => 'Ireland', 'continent' => 'Europe'],
        ['code' => 'IM', 'name' => 'Isle of Man', 'continent' => 'Europe'],
        ['code' => 'IL', 'name' => 'Israel', 'continent' => 'Asia'],
        ['code' => 'IT', 'name' => 'Italy', 'continent' => 'Europe'],
        ['code' => 'JM', 'name' => 'Jamaica', 'continent' => 'North America'],
        ['code' => 'JP', 'name' => 'Japan', 'continent' => 'Asia'],
        ['code' => 'JE', 'name' => 'Jersey', 'continent' => 'Europe'],
        ['code' => 'JO', 'name' => 'Jordan', 'continent' => 'Asia'],
        ['code' => 'KZ', 'name' => 'Kazakhstan', 'continent' => 'Asia'],
        ['code' => 'KE', 'name' => 'Kenya', 'continent' => 'Africa'],
        ['code' => 'KI', 'name' => 'Kiribati', 'continent' => 'Oceania'],
        ['code' => 'KP', 'name' => 'Korea, Democratic People\'s Republic of', 'continent' => 'Asia'],
        ['code' => 'KR', 'name' => 'Korea, Republic of', 'continent' => 'Asia'],
        ['code' => 'KW', 'name' => 'Kuwait', 'continent' => 'Asia'],
        ['code' => 'KG', 'name' => 'Kyrgyzstan', 'continent' => 'Asia'],
        ['code' => 'LA', 'name' => 'Lao People\'s Democratic Republic', 'continent' => 'Asia'],
        ['code' => 'LV', 'name' => 'Latvia', 'continent' => 'Europe'],
        ['code' => 'LB', 'name' => 'Lebanon', 'continent' => 'Asia'],
        ['code' => 'LS', 'name' => 'Lesotho', 'continent' => 'Africa'],
        ['code' => 'LR', 'name' => 'Liberia', 'continent' => 'Africa'],
        ['code' => 'LY', 'name' => 'Libya', 'continent' => 'Africa'],
        ['code' => 'LI', 'name' => 'Liechtenstein', 'continent' => 'Europe'],
        ['code' => 'LT', 'name' => 'Lithuania', 'continent' => 'Europe'],
        ['code' => 'LU', 'name' => 'Luxembourg', 'continent' => 'Europe'],
        ['code' => 'MO', 'name' => 'Macao', 'continent' => 'Asia'],
        ['code' => 'MK', 'name' => 'Macedonia', 'continent' => 'Europe'],
        ['code' => 'MG', 'name' => 'Madagascar', 'continent' => 'Africa'],
        ['code' => 'MW', 'name' => 'Malawi', 'continent' => 'Africa'],
        ['code' => 'MY', 'name' => 'Malaysia', 'continent' => 'Asia'],
        ['code' => 'MV', 'name' => 'Maldives', 'continent' => 'Asia'],
        ['code' => 'ML', 'name' => 'Mali', 'continent' => 'Africa'],
        ['code' => 'MT', 'name' => 'Malta', 'continent' => 'Europe'],
        ['code' => 'MH', 'name' => 'Marshall Islands', 'continent' => 'Oceania'],
        ['code' => 'MQ', 'name' => 'Martinique', 'continent' => 'North America'],
        ['code' => 'MR', 'name' => 'Mauritania', 'continent' => 'Africa'],
        ['code' => 'MU', 'name' => 'Mauritius', 'continent' => 'Africa'],
        ['code' => 'YT', 'name' => 'Mayotte', 'continent' => 'Africa'],
        ['code' => 'MX', 'name' => 'Mexico', 'continent' => 'North America'],
        ['code' => 'FM', 'name' => 'Micronesia', 'continent' => 'Oceania'],
        ['code' => 'MD', 'name' => 'Moldova', 'continent' => 'Europe'],
        ['code' => 'MC', 'name' => 'Monaco', 'continent' => 'Europe'],
        ['code' => 'MN', 'name' => 'Mongolia', 'continent' => 'Asia'],
        ['code' => 'ME', 'name' => 'Montenegro', 'continent' => 'Europe'],
        ['code' => 'MS', 'name' => 'Montserrat', 'continent' => 'North America'],
        ['code' => 'MA', 'name' => 'Morocco', 'continent' => 'Africa'],
        ['code' => 'MZ', 'name' => 'Mozambique', 'continent' => 'Africa'],
        ['code' => 'MM', 'name' => 'Myanmar', 'continent' => 'Asia'],
        ['code' => 'NA', 'name' => 'Namibia', 'continent' => 'Africa'],
        ['code' => 'NR', 'name' => 'Nauru', 'continent' => 'Oceania'],
        ['code' => 'NP', 'name' => 'Nepal', 'continent' => 'Asia'],
        ['code' => 'NL', 'name' => 'Netherlands', 'continent' => 'Europe'],
        ['code' => 'AN', 'name' => 'Netherlands Antilles', 'continent' => 'North America'],
        ['code' => 'NC', 'name' => 'New Caledonia', 'continent' => 'Oceania'],
        ['code' => 'NZ', 'name' => 'New Zealand', 'continent' => 'Oceania'],
        ['code' => 'NI', 'name' => 'Nicaragua', 'continent' => 'North America'],
        ['code' => 'NE', 'name' => 'Niger', 'continent' => 'Africa'],
        ['code' => 'NG', 'name' => 'Nigeria', 'continent' => 'Africa'],
        ['code' => 'NU', 'name' => 'Niue', 'continent' => 'Oceania'],
        ['code' => 'NF', 'name' => 'Norfolk Island', 'continent' => 'Oceania'],
        ['code' => 'MP', 'name' => 'Northern Mariana Islands', 'continent' => 'Oceania'],
        ['code' => 'NO', 'name' => 'Norway', 'continent' => 'Europe'],
        ['code' => 'OM', 'name' => 'Oman', 'continent' => 'Asia'],
        ['code' => 'PK', 'name' => 'Pakistan', 'continent' => 'Asia'],
        ['code' => 'PW', 'name' => 'Palau', 'continent' => 'Oceania'],
        ['code' => 'PS', 'name' => 'Palestinia', 'continent' => 'Asia'],
        ['code' => 'PA', 'name' => 'Panama', 'continent' => 'North America'],
        ['code' => 'PG', 'name' => 'Papua New Guinea', 'continent' => 'Oceania'],
        ['code' => 'PY', 'name' => 'Paraguay', 'continent' => 'South America'],
        ['code' => 'PE', 'name' => 'Peru', 'continent' => 'South America'],
        ['code' => 'PH', 'name' => 'Philippines', 'continent' => 'Asia'],
        ['code' => 'PN', 'name' => 'Pitcairn', 'continent' => 'Oceania'],
        ['code' => 'PL', 'name' => 'Poland', 'continent' => 'Europe'],
        ['code' => 'PT', 'name' => 'Portugal', 'continent' => 'Europe'],
        ['code' => 'PR', 'name' => 'Puerto Rico', 'continent' => 'North America'],
        ['code' => 'QA', 'name' => 'Qatar', 'continent' => 'Asia'],
        ['code' => 'RE', 'name' => 'Reunion', 'continent' => 'Africa'],
        ['code' => 'RO', 'name' => 'Romania', 'continent' => 'Europe'],
        ['code' => 'RU', 'name' => 'Russian Federation', 'continent' => 'Europe'],
        ['code' => 'RW', 'name' => 'Rwanda', 'continent' => 'Africa'],
        ['code' => 'SH', 'name' => 'Saint Helena', 'continent' => 'Africa'],
        ['code' => 'KN', 'name' => 'Saint Kitts and Nevis', 'continent' => 'North America'],
        ['code' => 'LC', 'name' => 'Saint Lucia', 'continent' => 'North America'],
        ['code' => 'PM', 'name' => 'Saint Pierre and Miquelon', 'continent' => 'North America'],
        ['code' => 'VC', 'name' => 'Saint Vincent and The Grenadines', 'continent' => 'North America'],
        ['code' => 'WS', 'name' => 'Samoa', 'continent' => 'Oceania'],
        ['code' => 'SM', 'name' => 'San Marino', 'continent' => 'Europe'],
        ['code' => 'ST', 'name' => 'Sao Tome and Principe', 'continent' => 'Africa'],
        ['code' => 'SA', 'name' => 'Saudi Arabia', 'continent' => 'Asia'],
        ['code' => 'SN', 'name' => 'Senegal', 'continent' => 'Africa'],
        ['code' => 'RS', 'name' => 'Serbia', 'continent' => 'Europe'],
        ['code' => 'SC', 'name' => 'Seychelles', 'continent' => 'Africa'],
        ['code' => 'SL', 'name' => 'Sierra Leone', 'continent' => 'Africa'],
        ['code' => 'SG', 'name' => 'Singapore', 'continent' => 'Asia'],
        ['code' => 'SK', 'name' => 'Slovakia', 'continent' => 'Europe'],
        ['code' => 'SI', 'name' => 'Slovenia', 'continent' => 'Europe'],
        ['code' => 'SB', 'name' => 'Solomon Islands', 'continent' => 'Oceania'],
        ['code' => 'SO', 'name' => 'Somalia', 'continent' => 'Africa'],
        ['code' => 'ZA', 'name' => 'South Africa', 'continent' => 'Africa'],
        ['code' => 'GS', 'name' => 'South Georgia and The South Sandwich Islands', 'continent' => 'Antarctica'],
        ['code' => 'SS', 'name' => 'South Sudan', 'continent' => 'Africa'],
        ['code' => 'ES', 'name' => 'Spain', 'continent' => 'Europe'],
        ['code' => 'LK', 'name' => 'Sri Lanka', 'continent' => 'Asia'],
        ['code' => 'SD', 'name' => 'Sudan', 'continent' => 'Africa'],
        ['code' => 'SR', 'name' => 'Suriname', 'continent' => 'South America'],
        ['code' => 'SJ', 'name' => 'Svalbard and Jan Mayen', 'continent' => 'Europe'],
        ['code' => 'SZ', 'name' => 'Swaziland', 'continent' => 'Africa'],
        ['code' => 'SE', 'name' => 'Sweden', 'continent' => 'Europe'],
        ['code' => 'CH', 'name' => 'Switzerland', 'continent' => 'Europe'],
        ['code' => 'SY', 'name' => 'Syrian Arab Republic', 'continent' => 'Asia'],
        ['code' => 'TW', 'name' => 'Taiwan', 'continent' => 'Asia'],
        ['code' => 'TJ', 'name' => 'Tajikistan', 'continent' => 'Asia'],
        ['code' => 'TZ', 'name' => 'Tanzania, United Republic of', 'continent' => 'Africa'],
        ['code' => 'TH', 'name' => 'Thailand', 'continent' => 'Asia'],
        ['code' => 'TL', 'name' => 'Timor-leste', 'continent' => 'Asia'],
        ['code' => 'TG', 'name' => 'Togo', 'continent' => 'Africa'],
        ['code' => 'TK', 'name' => 'Tokelau', 'continent' => 'Oceania'],
        ['code' => 'TO', 'name' => 'Tonga', 'continent' => 'Oceania'],
        ['code' => 'TT', 'name' => 'Trinidad and Tobago', 'continent' => 'North America'],
        ['code' => 'TN', 'name' => 'Tunisia', 'continent' => 'Africa'],
        ['code' => 'TR', 'name' => 'Turkey', 'continent' => 'Asia'],
        ['code' => 'TM', 'name' => 'Turkmenistan', 'continent' => 'Asia'],
        ['code' => 'TC', 'name' => 'Turks and Caicos Islands', 'continent' => 'North America'],
        ['code' => 'TV', 'name' => 'Tuvalu', 'continent' => 'Oceania'],
        ['code' => 'UG', 'name' => 'Uganda', 'continent' => 'Africa'],
        ['code' => 'UA', 'name' => 'Ukraine', 'continent' => 'Europe'],
        ['code' => 'AE', 'name' => 'United Arab Emirates', 'continent' => 'Asia'],
        ['code' => 'GB', 'name' => 'United Kingdom', 'continent' => 'Europe'],
        ['code' => 'US', 'name' => 'United States', 'continent' => 'North America'],
        ['code' => 'UM', 'name' => 'United States Minor Outlying Islands', 'continent' => 'Oceania'],
        ['code' => 'UY', 'name' => 'Uruguay', 'continent' => 'South America'],
        ['code' => 'UZ', 'name' => 'Uzbekistan', 'continent' => 'Asia'],
        ['code' => 'VU', 'name' => 'Vanuatu', 'continent' => 'Oceania'],
        ['code' => 'VE', 'name' => 'Venezuela', 'continent' => 'South America'],
        ['code' => 'VN', 'name' => 'Viet Nam', 'continent' => 'Asia'],
        ['code' => 'VG', 'name' => 'Virgin Islands, British', 'continent' => 'North America'],
        ['code' => 'VI', 'name' => 'Virgin Islands, US', 'continent' => 'North America'],
        ['code' => 'WF', 'name' => 'Wallis and Futuna', 'continent' => 'Oceania'],
        ['code' => 'EH', 'name' => 'Western Sahara', 'continent' => 'Africa'],
        ['code' => 'YE', 'name' => 'Yemen', 'continent' => 'Asia'],
        ['code' => 'ZM', 'name' => 'Zambia', 'continent' => 'Africa'],
        ['code' => 'ZW', 'name' => 'Zimbabwe', 'continent' => 'Africa'],
    ];

    /**
     * @var string
     */
    private $type = null;

    /**
     * @param HttpAdapterInterface $adapter An HTTP adapter.
     * @param string               $type    Data to lookup
     */
    public function __construct(\Psr\Http\Client\ClientInterface $adapter, $type = 'countries')
    {
        parent::__construct($adapter, null);

        $this->type   = strtolower($type);
    }

    /**
     * {@inheritDoc}
     */
    public function getGeocodedData($address)
    {
        if (!in_array($this->type, ['countries', 'country', 'continent'])) {
            throw new \Geocoder\Exception\UnsupportedOperation(Lang::txt('The LocalProvider does not support "%s".', $this->type));
        }

        $retriever = '_get' . ucfirst($this->type);

        return $this->$retriever($address);
    }

    /**
     * Get the full name for a country based on country code
     *
     * @param  string $code Two letter country code
     * @return array
     */
    protected function _getCountry($code)
    {
        if ($code) {
            $code = strtoupper($code);

            foreach (self::$countries as $country) {
                if ($country['code'] == $code) {
                    $builder = new \Geocoder\Model\AddressBuilder($this->getName());
                    $builder->setCountry($country['name']);
                    $builder->setCountryCode($country['code']);
                    $builder->setLocality($country['continent']);
                    return new \Geocoder\Model\AddressCollection([$builder->build()]);
                }
            }
        }

        return new \Geocoder\Model\AddressCollection([]);
    }

    /**
     * Get a list of countries and their two-letter codes
     *
     * @param  string $address Address
     * @return array
     */
    protected function _getCountries($address)
    {
        $address = strtolower(trim($address));

        // Make sure it's a valid continent
        if (!in_array($address, ['all', 'asia', 'north america', 'south america', 'europe', 'australia', 'africa', 'antarctica', 'oceania'])) {
            $address = 'all';
        }

        // No continent? Return the whole list
        if (!$address || $address == 'all') {
            $countries = self::$countries;
        } else {
            // Subset of countries by continent
            $countries = [];

            foreach (self::$countries as $row) {
                if (strtolower($row['continent']) == $address) {
                    array_push($countries, $row);
                }
            }
        }

        $builder = new \Geocoder\Model\AddressBuilder($this->getName());

        $addresses = [];

        foreach ($countries as $country) {
            $builder->setCountry($country['name']);
            $builder->setCountryCode($country['code']);
            $builder->setLocality($country['continent']);

            $addresses[] = $builder->build();
        }

        return new \Geocoder\Model\AddressCollection($addresses);
    }

    /**
     * Get the continent for a country based on name or code
     *
     * @param  string $address
     * @return string
     */
    protected function _getContinent($address)
    {
        $address = trim($address);

        if ($address) {
            $key = 'name';

            if (strlen($address) == 2) {
                $address = strtoupper($address);
                $key = 'code';
            }

            foreach (self::$countries as $country) {
                if ($country[$key] == $address) {
                    $builder = new \Geocoder\Model\AddressBuilder($this->getName());
                    $builder->setLocality($country['continent']);
                    return new \Geocoder\Model\AddressCollection([$builder->build()]);
                }
            }
        }

        return new \Geocoder\Model\AddressCollection([]);
    }

    /**
     * {@inheritDoc}
     */
    public function getReversedData(array $coordinates)
    {
        throw new \Geocoder\Exception\UnsupportedOperation('The LocalProvider is not able to do reverse geocoding.');
    }

    /**
     * {@inheritdoc}
     */
    public function geocodeQuery(\Geocoder\Query\GeocodeQuery $query): \Geocoder\Collection
    {
        $address = $query->getText();

        return $this->getGeocodedData($address);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseQuery(\Geocoder\Query\ReverseQuery $query): \Geocoder\Collection
    {
        throw new \Geocoder\Exception\UnsupportedOperation('The LocalProvider is not able to do reverse geocoding.');
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'local';
    }
}
