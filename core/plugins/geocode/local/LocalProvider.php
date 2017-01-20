<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 *
 * http://geocoder-php.org/Geocoder/
 */

namespace Plugins\Geocode;

use Geocoder\Provider\AbstractProvider;
use Geocoder\Provider\ProviderInterface;
use Geocoder\HttpAdapter\HttpAdapterInterface;
use Geocoder\Exception\NoResultException;
use Geocoder\Exception\InvalidCredentialsException;
use Geocoder\Exception\UnsupportedException;

/**
 * @author Shawn Rice <zooley@purdue.edu>
 */
class LocalProvider extends AbstractProvider implements ProviderInterface
{
	public static $countries = array(
		array('code' => 'AF', 'name' => 'Afghanistan', 'continent' => 'Asia'),
		array('code' => 'AX', 'name' => 'Aland Islands', 'continent' => 'Europe'),
		array('code' => 'AL', 'name' => 'Albania', 'continent' => 'Europe'),
		array('code' => 'DZ', 'name' => 'Algeria', 'continent' => 'Africa'),
		array('code' => 'AS', 'name' => 'American Samoa', 'continent' => 'Oceania'),
		array('code' => 'AD', 'name' => 'Andorra', 'continent' => 'Europe'),
		array('code' => 'AO', 'name' => 'Angola', 'continent' => 'Africa'),
		array('code' => 'AI', 'name' => 'Anguilla', 'continent' => 'North America'),
		array('code' => 'AQ', 'name' => 'Antarctica', 'continent' => 'Antarctica'),
		array('code' => 'AG', 'name' => 'Antigua and Barbuda', 'continent' => 'North America'),
		array('code' => 'AR', 'name' => 'Argentina', 'continent' => 'South America'),
		array('code' => 'AM', 'name' => 'Armenia', 'continent' => 'Asia'),
		array('code' => 'AW', 'name' => 'Aruba', 'continent' => 'North America'),
		array('code' => 'AU', 'name' => 'Australia', 'continent' => 'Oceania'),
		array('code' => 'AT', 'name' => 'Austria', 'continent' => 'Europe'),
		array('code' => 'AZ', 'name' => 'Azerbaijan', 'continent' => 'Asia'),
		array('code' => 'BS', 'name' => 'Bahamas', 'continent' => 'North America'),
		array('code' => 'BH', 'name' => 'Bahrain', 'continent' => 'Asia'),
		array('code' => 'BD', 'name' => 'Bangladesh', 'continent' => 'Asia'),
		array('code' => 'BB', 'name' => 'Barbados', 'continent' => 'North America'),
		array('code' => 'BY', 'name' => 'Belarus', 'continent' => 'Europe'),
		array('code' => 'BE', 'name' => 'Belgium', 'continent' => 'Europe'),
		array('code' => 'BZ', 'name' => 'Belize', 'continent' => 'North America'),
		array('code' => 'BJ', 'name' => 'Benin', 'continent' => 'Africa'),
		array('code' => 'BM', 'name' => 'Bermuda', 'continent' => 'North America'),
		array('code' => 'BT', 'name' => 'Bhutan', 'continent' => 'Asia'),
		array('code' => 'BO', 'name' => 'Bolivia', 'continent' => 'South America'),
		array('code' => 'BA', 'name' => 'Bosnia and Herzegovina', 'continent' => 'Europe'),
		array('code' => 'BW', 'name' => 'Botswana', 'continent' => 'Africa'),
		array('code' => 'BV', 'name' => 'Bouvet Island', 'continent' => 'Antarctica'),
		array('code' => 'BR', 'name' => 'Brazil', 'continent' => 'South America'),
		array('code' => 'IO', 'name' => 'British Indian Ocean Territory', 'continent' => 'Asia'),
		array('code' => 'BN', 'name' => 'Brunei Darussalam', 'continent' => 'Asia'),
		array('code' => 'BG', 'name' => 'Bulgaria', 'continent' => 'Europe'),
		array('code' => 'BF', 'name' => 'Burkina Faso', 'continent' => 'Africa'),
		array('code' => 'BI', 'name' => 'Burundi', 'continent' => 'Africa'),
		array('code' => 'KH', 'name' => 'Cambodia', 'continent' => 'Asia'),
		array('code' => 'CM', 'name' => 'Cameroon', 'continent' => 'Africa'),
		array('code' => 'CA', 'name' => 'Canada', 'continent' => 'North America'),
		array('code' => 'CV', 'name' => 'Cape Verde', 'continent' => 'Africa'),
		array('code' => 'KY', 'name' => 'Cayman Islands', 'continent' => 'North America'),
		array('code' => 'CF', 'name' => 'Central African Republic', 'continent' => 'Africa'),
		array('code' => 'TD', 'name' => 'Chad', 'continent' => 'Africa'),
		array('code' => 'CL', 'name' => 'Chile', 'continent' => 'South America'),
		array('code' => 'CN', 'name' => 'China', 'continent' => 'Asia'),
		array('code' => 'CX', 'name' => 'Christmas Island', 'continent' => 'Asia'),
		array('code' => 'CC', 'name' => 'Cocos (Keeling) Islands', 'continent' => 'Asia'),
		array('code' => 'CO', 'name' => 'Colombia', 'continent' => 'South America'),
		array('code' => 'KM', 'name' => 'Comoros', 'continent' => 'Africa'),
		array('code' => 'CG', 'name' => 'Congo', 'continent' => 'Africa'),
		array('code' => 'CD', 'name' => 'The Democratic Republic of The Congo', 'continent' => 'Africa'),
		array('code' => 'CK', 'name' => 'Cook Islands', 'continent' => 'Oceania'),
		array('code' => 'CR', 'name' => 'Costa Rica', 'continent' => 'North America'),
		array('code' => 'CI', 'name' => 'Cote D\'ivoire', 'continent' => 'Africa'),
		array('code' => 'HR', 'name' => 'Croatia', 'continent' => 'Europe'),
		array('code' => 'CU', 'name' => 'Cuba', 'continent' => 'North America'),
		array('code' => 'CY', 'name' => 'Cyprus', 'continent' => 'Asia'),
		array('code' => 'CZ', 'name' => 'Czech Republic', 'continent' => 'Europe'),
		array('code' => 'DK', 'name' => 'Denmark', 'continent' => 'Europe'),
		array('code' => 'DJ', 'name' => 'Djibouti', 'continent' => 'Africa'),
		array('code' => 'DM', 'name' => 'Dominica', 'continent' => 'North America'),
		array('code' => 'DO', 'name' => 'Dominican Republic', 'continent' => 'North America'),
		array('code' => 'EC', 'name' => 'Ecuador', 'continent' => 'South America'),
		array('code' => 'EG', 'name' => 'Egypt', 'continent' => 'Africa'),
		array('code' => 'SV', 'name' => 'El Salvador', 'continent' => 'North America'),
		array('code' => 'GQ', 'name' => 'Equatorial Guinea', 'continent' => 'Africa'),
		array('code' => 'ER', 'name' => 'Eritrea', 'continent' => 'Africa'),
		array('code' => 'EE', 'name' => 'Estonia', 'continent' => 'Europe'),
		array('code' => 'ET', 'name' => 'Ethiopia', 'continent' => 'Africa'),
		array('code' => 'FK', 'name' => 'Falkland Islands (Malvinas)', 'continent' => 'South America'),
		array('code' => 'FO', 'name' => 'Faroe Islands', 'continent' => 'Europe'),
		array('code' => 'FJ', 'name' => 'Fiji', 'continent' => 'Oceania'),
		array('code' => 'FI', 'name' => 'Finland', 'continent' => 'Europe'),
		array('code' => 'FR', 'name' => 'France', 'continent' => 'Europe'),
		array('code' => 'GF', 'name' => 'French Guiana', 'continent' => 'South America'),
		array('code' => 'PF', 'name' => 'French Polynesia', 'continent' => 'Oceania'),
		array('code' => 'TF', 'name' => 'French Southern Territories', 'continent' => 'Antarctica'),
		array('code' => 'GA', 'name' => 'Gabon', 'continent' => 'Africa'),
		array('code' => 'GM', 'name' => 'Gambia', 'continent' => 'Africa'),
		array('code' => 'GE', 'name' => 'Georgia', 'continent' => 'Asia'),
		array('code' => 'DE', 'name' => 'Germany', 'continent' => 'Europe'),
		array('code' => 'GH', 'name' => 'Ghana', 'continent' => 'Africa'),
		array('code' => 'GI', 'name' => 'Gibraltar', 'continent' => 'Europe'),
		array('code' => 'GR', 'name' => 'Greece', 'continent' => 'Europe'),
		array('code' => 'GL', 'name' => 'Greenland', 'continent' => 'North America'),
		array('code' => 'GD', 'name' => 'Grenada', 'continent' => 'North America'),
		array('code' => 'GP', 'name' => 'Guadeloupe', 'continent' => 'North America'),
		array('code' => 'GU', 'name' => 'Guam', 'continent' => 'Oceania'),
		array('code' => 'GT', 'name' => 'Guatemala', 'continent' => 'North America'),
		array('code' => 'GG', 'name' => 'Guernsey', 'continent' => 'Europe'),
		array('code' => 'GN', 'name' => 'Guinea', 'continent' => 'Africa'),
		array('code' => 'GW', 'name' => 'Guinea-bissau', 'continent' => 'Africa'),
		array('code' => 'GY', 'name' => 'Guyana', 'continent' => 'South America'),
		array('code' => 'HT', 'name' => 'Haiti', 'continent' => 'North America'),
		array('code' => 'HM', 'name' => 'Heard Island and Mcdonald Islands', 'continent' => 'Antarctica'),
		array('code' => 'VA', 'name' => 'Holy See (Vatican City State)', 'continent' => 'Europe'),
		array('code' => 'HN', 'name' => 'Honduras', 'continent' => 'North America'),
		array('code' => 'HK', 'name' => 'Hong Kong', 'continent' => 'Asia'),
		array('code' => 'HU', 'name' => 'Hungary', 'continent' => 'Europe'),
		array('code' => 'IS', 'name' => 'Iceland', 'continent' => 'Europe'),
		array('code' => 'IN', 'name' => 'India', 'continent' => 'Asia'),
		array('code' => 'ID', 'name' => 'Indonesia', 'continent' => 'Asia'),
		array('code' => 'IR', 'name' => 'Iran', 'continent' => 'Asia'),
		array('code' => 'IQ', 'name' => 'Iraq', 'continent' => 'Asia'),
		array('code' => 'IE', 'name' => 'Ireland', 'continent' => 'Europe'),
		array('code' => 'IM', 'name' => 'Isle of Man', 'continent' => 'Europe'),
		array('code' => 'IL', 'name' => 'Israel', 'continent' => 'Asia'),
		array('code' => 'IT', 'name' => 'Italy', 'continent' => 'Europe'),
		array('code' => 'JM', 'name' => 'Jamaica', 'continent' => 'North America'),
		array('code' => 'JP', 'name' => 'Japan', 'continent' => 'Asia'),
		array('code' => 'JE', 'name' => 'Jersey', 'continent' => 'Europe'),
		array('code' => 'JO', 'name' => 'Jordan', 'continent' => 'Asia'),
		array('code' => 'KZ', 'name' => 'Kazakhstan', 'continent' => 'Asia'),
		array('code' => 'KE', 'name' => 'Kenya', 'continent' => 'Africa'),
		array('code' => 'KI', 'name' => 'Kiribati', 'continent' => 'Oceania'),
		array('code' => 'KP', 'name' => 'Korea, Democratic People\'s Republic of', 'continent' => 'Asia'),
		array('code' => 'KR', 'name' => 'Korea, Republic of', 'continent' => 'Asia'),
		array('code' => 'KW', 'name' => 'Kuwait', 'continent' => 'Asia'),
		array('code' => 'KG', 'name' => 'Kyrgyzstan', 'continent' => 'Asia'),
		array('code' => 'LA', 'name' => 'Lao People\'s Democratic Republic', 'continent' => 'Asia'),
		array('code' => 'LV', 'name' => 'Latvia', 'continent' => 'Europe'),
		array('code' => 'LB', 'name' => 'Lebanon', 'continent' => 'Asia'),
		array('code' => 'LS', 'name' => 'Lesotho', 'continent' => 'Africa'),
		array('code' => 'LR', 'name' => 'Liberia', 'continent' => 'Africa'),
		array('code' => 'LY', 'name' => 'Libya', 'continent' => 'Africa'),
		array('code' => 'LI', 'name' => 'Liechtenstein', 'continent' => 'Europe'),
		array('code' => 'LT', 'name' => 'Lithuania', 'continent' => 'Europe'),
		array('code' => 'LU', 'name' => 'Luxembourg', 'continent' => 'Europe'),
		array('code' => 'MO', 'name' => 'Macao', 'continent' => 'Asia'),
		array('code' => 'MK', 'name' => 'Macedonia', 'continent' => 'Europe'),
		array('code' => 'MG', 'name' => 'Madagascar', 'continent' => 'Africa'),
		array('code' => 'MW', 'name' => 'Malawi', 'continent' => 'Africa'),
		array('code' => 'MY', 'name' => 'Malaysia', 'continent' => 'Asia'),
		array('code' => 'MV', 'name' => 'Maldives', 'continent' => 'Asia'),
		array('code' => 'ML', 'name' => 'Mali', 'continent' => 'Africa'),
		array('code' => 'MT', 'name' => 'Malta', 'continent' => 'Europe'),
		array('code' => 'MH', 'name' => 'Marshall Islands', 'continent' => 'Oceania'),
		array('code' => 'MQ', 'name' => 'Martinique', 'continent' => 'North America'),
		array('code' => 'MR', 'name' => 'Mauritania', 'continent' => 'Africa'),
		array('code' => 'MU', 'name' => 'Mauritius', 'continent' => 'Africa'),
		array('code' => 'YT', 'name' => 'Mayotte', 'continent' => 'Africa'),
		array('code' => 'MX', 'name' => 'Mexico', 'continent' => 'North America'),
		array('code' => 'FM', 'name' => 'Micronesia', 'continent' => 'Oceania'),
		array('code' => 'MD', 'name' => 'Moldova', 'continent' => 'Europe'),
		array('code' => 'MC', 'name' => 'Monaco', 'continent' => 'Europe'),
		array('code' => 'MN', 'name' => 'Mongolia', 'continent' => 'Asia'),
		array('code' => 'ME', 'name' => 'Montenegro', 'continent' => 'Europe'),
		array('code' => 'MS', 'name' => 'Montserrat', 'continent' => 'North America'),
		array('code' => 'MA', 'name' => 'Morocco', 'continent' => 'Africa'),
		array('code' => 'MZ', 'name' => 'Mozambique', 'continent' => 'Africa'),
		array('code' => 'MM', 'name' => 'Myanmar', 'continent' => 'Asia'),
		array('code' => 'NA', 'name' => 'Namibia', 'continent' => 'Africa'),
		array('code' => 'NR', 'name' => 'Nauru', 'continent' => 'Oceania'),
		array('code' => 'NP', 'name' => 'Nepal', 'continent' => 'Asia'),
		array('code' => 'NL', 'name' => 'Netherlands', 'continent' => 'Europe'),
		array('code' => 'AN', 'name' => 'Netherlands Antilles', 'continent' => 'North America'),
		array('code' => 'NC', 'name' => 'New Caledonia', 'continent' => 'Oceania'),
		array('code' => 'NZ', 'name' => 'New Zealand', 'continent' => 'Oceania'),
		array('code' => 'NI', 'name' => 'Nicaragua', 'continent' => 'North America'),
		array('code' => 'NE', 'name' => 'Niger', 'continent' => 'Africa'),
		array('code' => 'NG', 'name' => 'Nigeria', 'continent' => 'Africa'),
		array('code' => 'NU', 'name' => 'Niue', 'continent' => 'Oceania'),
		array('code' => 'NF', 'name' => 'Norfolk Island', 'continent' => 'Oceania'),
		array('code' => 'MP', 'name' => 'Northern Mariana Islands', 'continent' => 'Oceania'),
		array('code' => 'NO', 'name' => 'Norway', 'continent' => 'Europe'),
		array('code' => 'OM', 'name' => 'Oman', 'continent' => 'Asia'),
		array('code' => 'PK', 'name' => 'Pakistan', 'continent' => 'Asia'),
		array('code' => 'PW', 'name' => 'Palau', 'continent' => 'Oceania'),
		array('code' => 'PS', 'name' => 'Palestinia', 'continent' => 'Asia'),
		array('code' => 'PA', 'name' => 'Panama', 'continent' => 'North America'),
		array('code' => 'PG', 'name' => 'Papua New Guinea', 'continent' => 'Oceania'),
		array('code' => 'PY', 'name' => 'Paraguay', 'continent' => 'South America'),
		array('code' => 'PE', 'name' => 'Peru', 'continent' => 'South America'),
		array('code' => 'PH', 'name' => 'Philippines', 'continent' => 'Asia'),
		array('code' => 'PN', 'name' => 'Pitcairn', 'continent' => 'Oceania'),
		array('code' => 'PL', 'name' => 'Poland', 'continent' => 'Europe'),
		array('code' => 'PT', 'name' => 'Portugal', 'continent' => 'Europe'),
		array('code' => 'PR', 'name' => 'Puerto Rico', 'continent' => 'North America'),
		array('code' => 'QA', 'name' => 'Qatar', 'continent' => 'Asia'),
		array('code' => 'RE', 'name' => 'Reunion', 'continent' => 'Africa'),
		array('code' => 'RO', 'name' => 'Romania', 'continent' => 'Europe'),
		array('code' => 'RU', 'name' => 'Russian Federation', 'continent' => 'Europe'),
		array('code' => 'RW', 'name' => 'Rwanda', 'continent' => 'Africa'),
		array('code' => 'SH', 'name' => 'Saint Helena', 'continent' => 'Africa'),
		array('code' => 'KN', 'name' => 'Saint Kitts and Nevis', 'continent' => 'North America'),
		array('code' => 'LC', 'name' => 'Saint Lucia', 'continent' => 'North America'),
		array('code' => 'PM', 'name' => 'Saint Pierre and Miquelon', 'continent' => 'North America'),
		array('code' => 'VC', 'name' => 'Saint Vincent and The Grenadines', 'continent' => 'North America'),
		array('code' => 'WS', 'name' => 'Samoa', 'continent' => 'Oceania'),
		array('code' => 'SM', 'name' => 'San Marino', 'continent' => 'Europe'),
		array('code' => 'ST', 'name' => 'Sao Tome and Principe', 'continent' => 'Africa'),
		array('code' => 'SA', 'name' => 'Saudi Arabia', 'continent' => 'Asia'),
		array('code' => 'SN', 'name' => 'Senegal', 'continent' => 'Africa'),
		array('code' => 'RS', 'name' => 'Serbia', 'continent' => 'Europe'),
		array('code' => 'SC', 'name' => 'Seychelles', 'continent' => 'Africa'),
		array('code' => 'SL', 'name' => 'Sierra Leone', 'continent' => 'Africa'),
		array('code' => 'SG', 'name' => 'Singapore', 'continent' => 'Asia'),
		array('code' => 'SK', 'name' => 'Slovakia', 'continent' => 'Europe'),
		array('code' => 'SI', 'name' => 'Slovenia', 'continent' => 'Europe'),
		array('code' => 'SB', 'name' => 'Solomon Islands', 'continent' => 'Oceania'),
		array('code' => 'SO', 'name' => 'Somalia', 'continent' => 'Africa'),
		array('code' => 'ZA', 'name' => 'South Africa', 'continent' => 'Africa'),
		array('code' => 'GS', 'name' => 'South Georgia and The South Sandwich Islands', 'continent' => 'Antarctica'),
		array('code' => 'ES', 'name' => 'Spain', 'continent' => 'Europe'),
		array('code' => 'LK', 'name' => 'Sri Lanka', 'continent' => 'Asia'),
		array('code' => 'SD', 'name' => 'Sudan', 'continent' => 'Africa'),
		array('code' => 'SR', 'name' => 'Suriname', 'continent' => 'South America'),
		array('code' => 'SJ', 'name' => 'Svalbard and Jan Mayen', 'continent' => 'Europe'),
		array('code' => 'SZ', 'name' => 'Swaziland', 'continent' => 'Africa'),
		array('code' => 'SE', 'name' => 'Sweden', 'continent' => 'Europe'),
		array('code' => 'CH', 'name' => 'Switzerland', 'continent' => 'Europe'),
		array('code' => 'SY', 'name' => 'Syrian Arab Republic', 'continent' => 'Asia'),
		array('code' => 'TW', 'name' => 'Taiwan, Province of China', 'continent' => 'Asia'),
		array('code' => 'TJ', 'name' => 'Tajikistan', 'continent' => 'Asia'),
		array('code' => 'TZ', 'name' => 'Tanzania, United Republic of', 'continent' => 'Africa'),
		array('code' => 'TH', 'name' => 'Thailand', 'continent' => 'Asia'),
		array('code' => 'TL', 'name' => 'Timor-leste', 'continent' => 'Asia'),
		array('code' => 'TG', 'name' => 'Togo', 'continent' => 'Africa'),
		array('code' => 'TK', 'name' => 'Tokelau', 'continent' => 'Oceania'),
		array('code' => 'TO', 'name' => 'Tonga', 'continent' => 'Oceania'),
		array('code' => 'TT', 'name' => 'Trinidad and Tobago', 'continent' => 'North America'),
		array('code' => 'TN', 'name' => 'Tunisia', 'continent' => 'Africa'),
		array('code' => 'TR', 'name' => 'Turkey', 'continent' => 'Asia'),
		array('code' => 'TM', 'name' => 'Turkmenistan', 'continent' => 'Asia'),
		array('code' => 'TC', 'name' => 'Turks and Caicos Islands', 'continent' => 'North America'),
		array('code' => 'TV', 'name' => 'Tuvalu', 'continent' => 'Oceania'),
		array('code' => 'UG', 'name' => 'Uganda', 'continent' => 'Africa'),
		array('code' => 'UA', 'name' => 'Ukraine', 'continent' => 'Europe'),
		array('code' => 'AE', 'name' => 'United Arab Emirates', 'continent' => 'Asia'),
		array('code' => 'GB', 'name' => 'United Kingdom', 'continent' => 'Europe'),
		array('code' => 'US', 'name' => 'United States', 'continent' => 'North America'),
		array('code' => 'UM', 'name' => 'United States Minor Outlying Islands', 'continent' => 'Oceania'),
		array('code' => 'UY', 'name' => 'Uruguay', 'continent' => 'South America'),
		array('code' => 'UZ', 'name' => 'Uzbekistan', 'continent' => 'Asia'),
		array('code' => 'VU', 'name' => 'Vanuatu', 'continent' => 'Oceania'),
		array('code' => 'VE', 'name' => 'Venezuela', 'continent' => 'South America'),
		array('code' => 'VN', 'name' => 'Viet Nam', 'continent' => 'Asia'),
		array('code' => 'VG', 'name' => 'Virgin Islands, British', 'continent' => 'North America'),
		array('code' => 'VI', 'name' => 'Virgin Islands, US', 'continent' => 'North America'),
		array('code' => 'WF', 'name' => 'Wallis and Futuna', 'continent' => 'Oceania'),
		array('code' => 'EH', 'name' => 'Western Sahara', 'continent' => 'Africa'),
		array('code' => 'YE', 'name' => 'Yemen', 'continent' => 'Asia'),
		array('code' => 'ZM', 'name' => 'Zambia', 'continent' => 'Africa'),
		array('code' => 'ZW', 'name' => 'Zimbabwe', 'continent' => 'Africa')
	);

	/**
	 * @var string
	 */
	private $type = null;

	/**
	 * @param HttpAdapterInterface $adapter An HTTP adapter.
	 * @param string               $type    Data to lookup
	 */
	public function __construct(HttpAdapterInterface $adapter, $type='countries')
	{
		parent::__construct($adapter, null);

		$this->type   = strtolower($type);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getGeocodedData($address)
	{
		if (!in_array($this->type, array('countries', 'country')))
		{
			throw new UnsupportedException(\Lang::txt('The LocalProvider does not support "%s".', $this->type));
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
		if ($code)
		{
			$code = strtoupper($code);

			foreach (self::$countries as $country)
			{
				if ($country['code'] == $code)
				{
					return $country;
				}
			}
		}
		return $code;
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
		if (!in_array($address, array('all', 'asia', 'north america', 'south america', 'europe', 'australia', 'africa', 'antarctica', 'oceania')))
		{
			$address = 'all';
		}

		// No continent? Return the whole list
		if (!$address || $address == 'all')
		{
			return self::$countries;
		}

		// Subset of countries by continent
		$countries = array();

		foreach (self::$countries as $row)
		{
			if (strtolower($row['continent']) == $address)
			{
				array_push($countries, $row);
			}
		}

		return $countries;
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

		if ($address)
		{
			$key = 'name';

			if (strlen($address) == 2)
			{
				$address = strtoupper($address);
				$key = 'code';
			}

			foreach (self::$countries as $country)
			{
				if ($country[$key] == $address)
				{
					return $country['continent'];
				}
			}
		}

		return $address;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getReversedData(array $coordinates)
	{
		throw new UnsupportedException('The LocalProvider is not able to do reverse geocoding.');
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName()
	{
		return 'local';
	}
}
