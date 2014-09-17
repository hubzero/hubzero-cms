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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$text = ($this->task == 'edit' ? JText::_('JACTION_EDIT') : JText::_('JACTION_CREATE'));
if (!$this->tmpl)
{
	JToolBarHelper::title(JText::_('COM_TOOLS') . ': ' . JText::_('COM_TOOLS_ZONES') . ': ' . JText::_('COM_TOOLS_LOCATIONS') . ': ' . $text, 'tools.png');
	JToolBarHelper::save();
	JToolBarHelper::cancel();
}

JHTML::_('behavior.framework');

$countries = array(
		array('code' => '', 'name' => '- Select - ', 'continent' => 'AS'),
		array('code' => '', 'name' => '- Select - ', 'continent' => 'NA'),
		array('code' => '', 'name' => '- Select - ', 'continent' => 'SA'),
		array('code' => '', 'name' => '- Select - ', 'continent' => 'AF'),
		array('code' => '', 'name' => '- Select - ', 'continent' => 'EU'),
		array('code' => '', 'name' => '- Select - ', 'continent' => 'OC'),
		array('code' => 'AF', 'name' => 'Afghanistan', 'continent' => 'AS'),
		array('code' => 'AX', 'name' => 'Aland Islands', 'continent' => 'EU'),
		array('code' => 'AL', 'name' => 'Albania', 'continent' => 'EU'),
		array('code' => 'DZ', 'name' => 'Algeria', 'continent' => 'AF'),
		array('code' => 'AS', 'name' => 'American Samoa', 'continent' => 'OC'),
		array('code' => 'AD', 'name' => 'Andorra', 'continent' => 'EU'),
		array('code' => 'AO', 'name' => 'Angola', 'continent' => 'AF'),
		array('code' => 'AI', 'name' => 'Anguilla', 'continent' => 'NA'),
		array('code' => 'AQ', 'name' => 'AN', 'continent' => 'AN'),
		array('code' => 'AG', 'name' => 'Antigua and Barbuda', 'continent' => 'NA'),
		array('code' => 'AR', 'name' => 'Argentina', 'continent' => 'SA'),
		array('code' => 'AM', 'name' => 'Armenia', 'continent' => 'AS'),
		array('code' => 'AW', 'name' => 'Aruba', 'continent' => 'NA'),
		array('code' => 'AU', 'name' => 'Australia', 'continent' => 'OC'),
		array('code' => 'AT', 'name' => 'Austria', 'continent' => 'EU'),
		array('code' => 'AZ', 'name' => 'Azerbaijan', 'continent' => 'AS'),
		array('code' => 'BS', 'name' => 'Bahamas', 'continent' => 'NA'),
		array('code' => 'BH', 'name' => 'Bahrain', 'continent' => 'AS'),
		array('code' => 'BD', 'name' => 'Bangladesh', 'continent' => 'AS'),
		array('code' => 'BB', 'name' => 'Barbados', 'continent' => 'NA'),
		array('code' => 'BY', 'name' => 'Belarus', 'continent' => 'EU'),
		array('code' => 'BE', 'name' => 'Belgium', 'continent' => 'EU'),
		array('code' => 'BZ', 'name' => 'Belize', 'continent' => 'NA'),
		array('code' => 'BJ', 'name' => 'Benin', 'continent' => 'AF'),
		array('code' => 'BM', 'name' => 'Bermuda', 'continent' => 'NA'),
		array('code' => 'BT', 'name' => 'Bhutan', 'continent' => 'AS'),
		array('code' => 'BO', 'name' => 'Bolivia', 'continent' => 'SA'),
		array('code' => 'BA', 'name' => 'Bosnia and Herzegovina', 'continent' => 'EU'),
		array('code' => 'BW', 'name' => 'Botswana', 'continent' => 'AF'),
		array('code' => 'BV', 'name' => 'Bouvet Island', 'continent' => 'AN'),
		array('code' => 'BR', 'name' => 'Brazil', 'continent' => 'SA'),
		array('code' => 'IO', 'name' => 'British Indian Ocean Territory', 'continent' => 'AS'),
		array('code' => 'BN', 'name' => 'Brunei Darussalam', 'continent' => 'AS'),
		array('code' => 'BG', 'name' => 'Bulgaria', 'continent' => 'EU'),
		array('code' => 'BF', 'name' => 'Burkina Faso', 'continent' => 'AF'),
		array('code' => 'BI', 'name' => 'Burundi', 'continent' => 'AF'),
		array('code' => 'KH', 'name' => 'Cambodia', 'continent' => 'AS'),
		array('code' => 'CM', 'name' => 'Cameroon', 'continent' => 'AF'),
		array('code' => 'CA', 'name' => 'Canada', 'continent' => 'NA'),
		array('code' => 'CV', 'name' => 'Cape Verde', 'continent' => 'AF'),
		array('code' => 'KY', 'name' => 'Cayman Islands', 'continent' => 'NA'),
		array('code' => 'CF', 'name' => 'Central African Republic', 'continent' => 'AF'),
		array('code' => 'TD', 'name' => 'Chad', 'continent' => 'AF'),
		array('code' => 'CL', 'name' => 'Chile', 'continent' => 'SA'),
		array('code' => 'CN', 'name' => 'China', 'continent' => 'AS'),
		array('code' => 'CX', 'name' => 'Christmas Island', 'continent' => 'AS'),
		array('code' => 'CC', 'name' => 'Cocos (Keeling) Islands', 'continent' => 'AS'),
		array('code' => 'CO', 'name' => 'Colombia', 'continent' => 'SA'),
		array('code' => 'KM', 'name' => 'Comoros', 'continent' => 'AF'),
		array('code' => 'CG', 'name' => 'Congo', 'continent' => 'AF'),
		array('code' => 'CD', 'name' => 'The Democratic Republic of The Congo', 'continent' => 'AF'),
		array('code' => 'CK', 'name' => 'Cook Islands', 'continent' => 'OC'),
		array('code' => 'CR', 'name' => 'Costa Rica', 'continent' => 'NA'),
		array('code' => 'CI', 'name' => 'Cote D\'ivoire', 'continent' => 'AF'),
		array('code' => 'HR', 'name' => 'Croatia', 'continent' => 'EU'),
		array('code' => 'CU', 'name' => 'Cuba', 'continent' => 'NA'),
		array('code' => 'CY', 'name' => 'Cyprus', 'continent' => 'AS'),
		array('code' => 'CZ', 'name' => 'Czech Republic', 'continent' => 'EU'),
		array('code' => 'DK', 'name' => 'Denmark', 'continent' => 'EU'),
		array('code' => 'DJ', 'name' => 'Djibouti', 'continent' => 'AF'),
		array('code' => 'DM', 'name' => 'Dominica', 'continent' => 'NA'),
		array('code' => 'DO', 'name' => 'Dominican Republic', 'continent' => 'NA'),
		array('code' => 'EC', 'name' => 'Ecuador', 'continent' => 'SA'),
		array('code' => 'EG', 'name' => 'Egypt', 'continent' => 'AF'),
		array('code' => 'SV', 'name' => 'El Salvador', 'continent' => 'NA'),
		array('code' => 'GQ', 'name' => 'Equatorial Guinea', 'continent' => 'AF'),
		array('code' => 'ER', 'name' => 'Eritrea', 'continent' => 'AF'),
		array('code' => 'EE', 'name' => 'Estonia', 'continent' => 'EU'),
		array('code' => 'ET', 'name' => 'Ethiopia', 'continent' => 'AF'),
		array('code' => 'FK', 'name' => 'Falkland Islands (Malvinas)', 'continent' => 'SA'),
		array('code' => 'FO', 'name' => 'Faroe Islands', 'continent' => 'EU'),
		array('code' => 'FJ', 'name' => 'Fiji', 'continent' => 'OC'),
		array('code' => 'FI', 'name' => 'Finland', 'continent' => 'EU'),
		array('code' => 'FR', 'name' => 'France', 'continent' => 'EU'),
		array('code' => 'GF', 'name' => 'French Guiana', 'continent' => 'SA'),
		array('code' => 'PF', 'name' => 'French Polynesia', 'continent' => 'OC'),
		array('code' => 'TF', 'name' => 'French Southern Territories', 'continent' => 'AN'),
		array('code' => 'GA', 'name' => 'Gabon', 'continent' => 'AF'),
		array('code' => 'GM', 'name' => 'Gambia', 'continent' => 'AF'),
		array('code' => 'GE', 'name' => 'Georgia', 'continent' => 'AS'),
		array('code' => 'DE', 'name' => 'Germany', 'continent' => 'EU'),
		array('code' => 'GH', 'name' => 'Ghana', 'continent' => 'AF'),
		array('code' => 'GI', 'name' => 'Gibraltar', 'continent' => 'EU'),
		array('code' => 'GR', 'name' => 'Greece', 'continent' => 'EU'),
		array('code' => 'GL', 'name' => 'Greenland', 'continent' => 'NA'),
		array('code' => 'GD', 'name' => 'Grenada', 'continent' => 'NA'),
		array('code' => 'GP', 'name' => 'Guadeloupe', 'continent' => 'NA'),
		array('code' => 'GU', 'name' => 'Guam', 'continent' => 'OC'),
		array('code' => 'GT', 'name' => 'Guatemala', 'continent' => 'NA'),
		array('code' => 'GG', 'name' => 'Guernsey', 'continent' => 'EU'),
		array('code' => 'GN', 'name' => 'Guinea', 'continent' => 'AF'),
		array('code' => 'GW', 'name' => 'Guinea-bissau', 'continent' => 'AF'),
		array('code' => 'GY', 'name' => 'Guyana', 'continent' => 'SA'),
		array('code' => 'HT', 'name' => 'Haiti', 'continent' => 'NA'),
		array('code' => 'HM', 'name' => 'Heard Island and Mcdonald Islands', 'continent' => 'AN'),
		array('code' => 'VA', 'name' => 'Holy See (Vatican City State)', 'continent' => 'EU'),
		array('code' => 'HN', 'name' => 'Honduras', 'continent' => 'NA'),
		array('code' => 'HK', 'name' => 'Hong Kong', 'continent' => 'AS'),
		array('code' => 'HU', 'name' => 'Hungary', 'continent' => 'EU'),
		array('code' => 'IS', 'name' => 'Iceland', 'continent' => 'EU'),
		array('code' => 'IN', 'name' => 'India', 'continent' => 'AS'),
		array('code' => 'ID', 'name' => 'Indonesia', 'continent' => 'AS'),
		array('code' => 'IR', 'name' => 'Iran', 'continent' => 'AS'),
		array('code' => 'IQ', 'name' => 'Iraq', 'continent' => 'AS'),
		array('code' => 'IE', 'name' => 'Ireland', 'continent' => 'EU'),
		array('code' => 'IM', 'name' => 'Isle of Man', 'continent' => 'EU'),
		array('code' => 'IL', 'name' => 'Israel', 'continent' => 'AS'),
		array('code' => 'IT', 'name' => 'Italy', 'continent' => 'EU'),
		array('code' => 'JM', 'name' => 'Jamaica', 'continent' => 'NA'),
		array('code' => 'JP', 'name' => 'Japan', 'continent' => 'AS'),
		array('code' => 'JE', 'name' => 'Jersey', 'continent' => 'EU'),
		array('code' => 'JO', 'name' => 'Jordan', 'continent' => 'AS'),
		array('code' => 'KZ', 'name' => 'Kazakhstan', 'continent' => 'AS'),
		array('code' => 'KE', 'name' => 'Kenya', 'continent' => 'AF'),
		array('code' => 'KI', 'name' => 'Kiribati', 'continent' => 'OC'),
		array('code' => 'KP', 'name' => 'Korea, Democratic People\'s Republic of', 'continent' => 'AS'),
		array('code' => 'KR', 'name' => 'Korea, Republic of', 'continent' => 'AS'),
		array('code' => 'KW', 'name' => 'Kuwait', 'continent' => 'AS'),
		array('code' => 'KG', 'name' => 'Kyrgyzstan', 'continent' => 'AS'),
		array('code' => 'LA', 'name' => 'Lao People\'s Democratic Republic', 'continent' => 'AS'),
		array('code' => 'LV', 'name' => 'Latvia', 'continent' => 'EU'),
		array('code' => 'LB', 'name' => 'Lebanon', 'continent' => 'AS'),
		array('code' => 'LS', 'name' => 'Lesotho', 'continent' => 'AF'),
		array('code' => 'LR', 'name' => 'Liberia', 'continent' => 'AF'),
		array('code' => 'LY', 'name' => 'Libya', 'continent' => 'AF'),
		array('code' => 'LI', 'name' => 'Liechtenstein', 'continent' => 'EU'),
		array('code' => 'LT', 'name' => 'Lithuania', 'continent' => 'EU'),
		array('code' => 'LU', 'name' => 'Luxembourg', 'continent' => 'EU'),
		array('code' => 'MO', 'name' => 'Macao', 'continent' => 'AS'),
		array('code' => 'MK', 'name' => 'Macedonia', 'continent' => 'EU'),
		array('code' => 'MG', 'name' => 'Madagascar', 'continent' => 'AF'),
		array('code' => 'MW', 'name' => 'Malawi', 'continent' => 'AF'),
		array('code' => 'MY', 'name' => 'Malaysia', 'continent' => 'AS'),
		array('code' => 'MV', 'name' => 'Maldives', 'continent' => 'AS'),
		array('code' => 'ML', 'name' => 'Mali', 'continent' => 'AF'),
		array('code' => 'MT', 'name' => 'Malta', 'continent' => 'EU'),
		array('code' => 'MH', 'name' => 'Marshall Islands', 'continent' => 'OC'),
		array('code' => 'MQ', 'name' => 'Martinique', 'continent' => 'NA'),
		array('code' => 'MR', 'name' => 'Mauritania', 'continent' => 'AF'),
		array('code' => 'MU', 'name' => 'Mauritius', 'continent' => 'AF'),
		array('code' => 'YT', 'name' => 'Mayotte', 'continent' => 'AF'),
		array('code' => 'MX', 'name' => 'Mexico', 'continent' => 'NA'),
		array('code' => 'FM', 'name' => 'Micronesia', 'continent' => 'OC'),
		array('code' => 'MD', 'name' => 'Moldova', 'continent' => 'EU'),
		array('code' => 'MC', 'name' => 'Monaco', 'continent' => 'EU'),
		array('code' => 'MN', 'name' => 'Mongolia', 'continent' => 'AS'),
		array('code' => 'ME', 'name' => 'Montenegro', 'continent' => 'EU'),
		array('code' => 'MS', 'name' => 'Montserrat', 'continent' => 'NA'),
		array('code' => 'MA', 'name' => 'Morocco', 'continent' => 'AF'),
		array('code' => 'MZ', 'name' => 'Mozambique', 'continent' => 'AF'),
		array('code' => 'MM', 'name' => 'Myanmar', 'continent' => 'AS'),
		array('code' => 'NA', 'name' => 'Namibia', 'continent' => 'AF'),
		array('code' => 'NR', 'name' => 'Nauru', 'continent' => 'OC'),
		array('code' => 'NP', 'name' => 'Nepal', 'continent' => 'AS'),
		array('code' => 'NL', 'name' => 'Netherlands', 'continent' => 'EU'),
		array('code' => 'AN', 'name' => 'Netherlands Antilles', 'continent' => 'NA'),
		array('code' => 'NC', 'name' => 'New Caledonia', 'continent' => 'OC'),
		array('code' => 'NZ', 'name' => 'New Zealand', 'continent' => 'OC'),
		array('code' => 'NI', 'name' => 'Nicaragua', 'continent' => 'NA'),
		array('code' => 'NE', 'name' => 'Niger', 'continent' => 'AF'),
		array('code' => 'NG', 'name' => 'Nigeria', 'continent' => 'AF'),
		array('code' => 'NU', 'name' => 'Niue', 'continent' => 'OC'),
		array('code' => 'NF', 'name' => 'Norfolk Island', 'continent' => 'OC'),
		array('code' => 'MP', 'name' => 'Northern Mariana Islands', 'continent' => 'OC'),
		array('code' => 'NO', 'name' => 'Norway', 'continent' => 'EU'),
		array('code' => 'OM', 'name' => 'Oman', 'continent' => 'AS'),
		array('code' => 'PK', 'name' => 'Pakistan', 'continent' => 'AS'),
		array('code' => 'PW', 'name' => 'Palau', 'continent' => 'OC'),
		array('code' => 'PS', 'name' => 'Palestinia', 'continent' => 'AS'),
		array('code' => 'PA', 'name' => 'Panama', 'continent' => 'NA'),
		array('code' => 'PG', 'name' => 'Papua New Guinea', 'continent' => 'OC'),
		array('code' => 'PY', 'name' => 'Paraguay', 'continent' => 'SA'),
		array('code' => 'PE', 'name' => 'Peru', 'continent' => 'SA'),
		array('code' => 'PH', 'name' => 'Philippines', 'continent' => 'AS'),
		array('code' => 'PN', 'name' => 'Pitcairn', 'continent' => 'OC'),
		array('code' => 'PL', 'name' => 'Poland', 'continent' => 'EU'),
		array('code' => 'PT', 'name' => 'Portugal', 'continent' => 'EU'),
		array('code' => 'PR', 'name' => 'Puerto Rico', 'continent' => 'NA'),
		array('code' => 'QA', 'name' => 'Qatar', 'continent' => 'AS'),
		array('code' => 'RE', 'name' => 'Reunion', 'continent' => 'AF'),
		array('code' => 'RO', 'name' => 'Romania', 'continent' => 'EU'),
		array('code' => 'RU', 'name' => 'Russian Federation', 'continent' => 'EU'),
		array('code' => 'RW', 'name' => 'Rwanda', 'continent' => 'AF'),
		array('code' => 'SH', 'name' => 'Saint Helena', 'continent' => 'AF'),
		array('code' => 'KN', 'name' => 'Saint Kitts and Nevis', 'continent' => 'NA'),
		array('code' => 'LC', 'name' => 'Saint Lucia', 'continent' => 'NA'),
		array('code' => 'PM', 'name' => 'Saint Pierre and Miquelon', 'continent' => 'NA'),
		array('code' => 'VC', 'name' => 'Saint Vincent and The Grenadines', 'continent' => 'NA'),
		array('code' => 'WS', 'name' => 'Samoa', 'continent' => 'OC'),
		array('code' => 'SM', 'name' => 'San Marino', 'continent' => 'EU'),
		array('code' => 'ST', 'name' => 'Sao Tome and Principe', 'continent' => 'AF'),
		array('code' => 'SA', 'name' => 'Saudi Arabia', 'continent' => 'AS'),
		array('code' => 'SN', 'name' => 'Senegal', 'continent' => 'AF'),
		array('code' => 'RS', 'name' => 'Serbia', 'continent' => 'EU'),
		array('code' => 'SC', 'name' => 'Seychelles', 'continent' => 'AF'),
		array('code' => 'SL', 'name' => 'Sierra Leone', 'continent' => 'AF'),
		array('code' => 'SG', 'name' => 'Singapore', 'continent' => 'AS'),
		array('code' => 'SK', 'name' => 'Slovakia', 'continent' => 'EU'),
		array('code' => 'SI', 'name' => 'Slovenia', 'continent' => 'EU'),
		array('code' => 'SB', 'name' => 'Solomon Islands', 'continent' => 'OC'),
		array('code' => 'SO', 'name' => 'Somalia', 'continent' => 'AF'),
		array('code' => 'ZA', 'name' => 'South Africa', 'continent' => 'AF'),
		array('code' => 'GS', 'name' => 'South Georgia and The South Sandwich Islands', 'continent' => 'AN'),
		array('code' => 'ES', 'name' => 'Spain', 'continent' => 'EU'),
		array('code' => 'LK', 'name' => 'Sri Lanka', 'continent' => 'AS'),
		array('code' => 'SD', 'name' => 'Sudan', 'continent' => 'AF'),
		array('code' => 'SR', 'name' => 'Suriname', 'continent' => 'SA'),
		array('code' => 'SJ', 'name' => 'Svalbard and Jan Mayen', 'continent' => 'EU'),
		array('code' => 'SZ', 'name' => 'Swaziland', 'continent' => 'AF'),
		array('code' => 'SE', 'name' => 'Sweden', 'continent' => 'EU'),
		array('code' => 'CH', 'name' => 'Switzerland', 'continent' => 'EU'),
		array('code' => 'SY', 'name' => 'Syrian Arab Republic', 'continent' => 'AS'),
		array('code' => 'TW', 'name' => 'Taiwan, Province of China', 'continent' => 'AS'),
		array('code' => 'TJ', 'name' => 'Tajikistan', 'continent' => 'AS'),
		array('code' => 'TZ', 'name' => 'Tanzania, United Republic of', 'continent' => 'AF'),
		array('code' => 'TH', 'name' => 'Thailand', 'continent' => 'AS'),
		array('code' => 'TL', 'name' => 'Timor-leste', 'continent' => 'AS'),
		array('code' => 'TG', 'name' => 'Togo', 'continent' => 'AF'),
		array('code' => 'TK', 'name' => 'Tokelau', 'continent' => 'OC'),
		array('code' => 'TO', 'name' => 'Tonga', 'continent' => 'OC'),
		array('code' => 'TT', 'name' => 'Trinidad and Tobago', 'continent' => 'NA'),
		array('code' => 'TN', 'name' => 'Tunisia', 'continent' => 'AF'),
		array('code' => 'TR', 'name' => 'Turkey', 'continent' => 'AS'),
		array('code' => 'TM', 'name' => 'Turkmenistan', 'continent' => 'AS'),
		array('code' => 'TC', 'name' => 'Turks and Caicos Islands', 'continent' => 'NA'),
		array('code' => 'TV', 'name' => 'Tuvalu', 'continent' => 'OC'),
		array('code' => 'UG', 'name' => 'Uganda', 'continent' => 'AF'),
		array('code' => 'UA', 'name' => 'Ukraine', 'continent' => 'EU'),
		array('code' => 'AE', 'name' => 'United Arab Emirates', 'continent' => 'AS'),
		array('code' => 'GB', 'name' => 'United Kingdom', 'continent' => 'EU'),
		array('code' => 'US', 'name' => 'United States', 'continent' => 'NA'),
		array('code' => 'UM', 'name' => 'United States Minor Outlying Islands', 'continent' => 'OC'),
		array('code' => 'UY', 'name' => 'Uruguay', 'continent' => 'SA'),
		array('code' => 'UZ', 'name' => 'Uzbekistan', 'continent' => 'AS'),
		array('code' => 'VU', 'name' => 'Vanuatu', 'continent' => 'OC'),
		array('code' => 'VE', 'name' => 'Venezuela', 'continent' => 'SA'),
		array('code' => 'VN', 'name' => 'Viet Nam', 'continent' => 'AS'),
		array('code' => 'VG', 'name' => 'Virgin Islands, British', 'continent' => 'NA'),
		array('code' => 'VI', 'name' => 'Virgin Islands, US', 'continent' => 'NA'),
		array('code' => 'WF', 'name' => 'Wallis and Futuna', 'continent' => 'OC'),
		array('code' => 'EH', 'name' => 'Western Sahara', 'continent' => 'AF'),
		array('code' => 'YE', 'name' => 'Yemen', 'continent' => 'AS'),
		array('code' => 'ZM', 'name' => 'Zambia', 'continent' => 'AF'),
		array('code' => 'ZW', 'name' => 'Zimbabwe', 'continent' => 'AF')
	);
?>
<script type="text/javascript">
var continentcountry = new Array;
<?php
$i = 0;
if ($countries)
{
	echo 'continentcountry[' . $i++ . "] = new Array( '','','" . JText::_('COM_TOOLS_SELECT') . "' );\n\t\t";
	foreach ($countries as $k => $items)
	{
		echo 'continentcountry[' . $i++ . "] = new Array( '" . $items['continent'] . "','" . addslashes($items['code']) . "','" . addslashes($items['name']) . "' );\n\t\t";
	}
}
?>

function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// form field validation
	submitform(pressbutton);
}
function saveAndUpdate()
{
	submitbutton('save');
	window.parent.setTimeout(function(){
		var src = window.parent.document.getElementById('locationslist').src;

		window.parent.document.getElementById('locationslist').src = src + '&';
		window.parent.$.fancybox.close();
	}, 700);
}
</script>
<?php if ($this->getError()) { ?>
	<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
<form action="index.php" method="post" name="adminForm" id="<?php echo ($this->tmpl == 'component') ? 'component-form' : 'item-form'; ?>" enctype="multipart/form-data">
<?php if ($this->tmpl == 'component') { ?>
	<fieldset>
		<div class="configuration">
			<div class="configuration-options">
				<button type="button" onclick="saveAndUpdate();"><?php echo JText::_('COM_TOOLS_SAVE'); ?></button>
				<button type="button" onclick="window.parent.$.fancybox.close();"><?php echo JText::_('COM_TOOLS_CANCEL'); ?></button>
			</div>
			<?php echo $text; ?>
		</div>
	</fieldset>
<?php } ?>
	<div class="col width-100">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('JDETAILS'); ?></span></legend>

			<input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->row->get('id')); ?>" />
			<input type="hidden" name="fields[zone_id]" value="<?php echo $this->escape($this->row->get('zone_id')); ?>" />

			<input type="hidden" name="tmpl" value="<?php echo $this->escape($this->tmpl); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->escape($this->controller); ?>">
			<input type="hidden" name="task" value="save" />

			<table class="admintable">
				<tbody>
					<tr>
						<th class="key"><label for="field-ipFROM"><?php echo JText::_('COM_TOOLS_FIELD_IP_FROM'); ?>:</label></th>
						<td><input type="text" name="fields[ipFROM]" id="field-ipFROM" value="<?php echo $this->escape(stripslashes($this->row->get('ipFROM'))); ?>" /></td>
					</tr>
					<tr>
						<th class="key"><label for="field-ipTO"><?php echo JText::_('COM_TOOLS_FIELD_IP_TO'); ?>:</label></th>
						<td><input type="text" name="fields[ipTO]" id="field-ipTO" value="<?php echo $this->escape(stripslashes($this->row->get('ipTO'))); ?>" /></td>
					</tr>
					<tr>
						<th class="key"><label for="field-continent"><?php echo JText::_('COM_TOOLS_FIELD_CONTINENT'); ?>:</label></th>
						<td>
							<select name="fields[continent]" id="field-continent" onchange="changeDynaList('field-countrySHORT', continentcountry, document.getElementById('field-continent').options[document.getElementById('field-continent').selectedIndex].value, 0, 0);">
								<option value=""<?php if ($this->row->get('continent') == '') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_TOOLS_SELECT'); ?></option>
								<option value="NA"<?php if ($this->row->get('continent') == 'NA') { echo ' selected="selected"'; } ?>>North America</option>
								<option value="SA"<?php if ($this->row->get('continent') == 'SA') { echo ' selected="selected"'; } ?>>South America</option>
								<option value="EU"<?php if ($this->row->get('continent') == 'EU') { echo ' selected="selected"'; } ?>>Europe</option>
								<option value="AF"<?php if ($this->row->get('continent') == 'AF') { echo ' selected="selected"'; } ?>>Africa</option>
								<option value="AS"<?php if ($this->row->get('continent') == 'AS') { echo ' selected="selected"'; } ?>>Asia</option>
								<option value="AN"<?php if ($this->row->get('continent') == 'AN') { echo ' selected="selected"'; } ?>>Antarctica</option>
								<option value="OC"<?php if ($this->row->get('continent') == 'OC') { echo ' selected="selected"'; } ?>>Oceania</option>
							</select>
						</td>
					</tr>
					<tr>
						<th class="key"><label for="field-countrySHORT"><?php echo JText::_('Country'); ?>:</label></th>
						<td>
							<select name="fields[countrySHORT]" id="field-countrySHORT">
								<option value=""<?php if ($this->row->get('continent') == '') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_TOOLS_SELECT'); ?></option>
								<?php
								//if ($countries = \Hubzero\Geocode\Geocode::countries())
								if ($countries)
								{
									if (!$this->row->get('continent'))
									{
										//$this->row->set('continent', 'NA');
									}
									foreach ($countries as $country)
									{
										if ($country['continent'] != $this->row->get('continent'))
										{
											continue;
										}
										echo '<option value="' . $country['code'] . '"';
										if (strtoupper($this->row->get('countrySHORT')) == $country['code'])
										{
											echo ' selected="selected"';
										}
										echo '>' . $this->escape($country['name']) . '</option>'."\n";
									}
								}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<th class="key"><label for="field-ipREGION"><?php echo JText::_('COM_TOOLS_FIELD_REGION'); ?>:</label></th>
						<td><input type="text" name="fields[ipREGION]" id="field-ipREGION" value="<?php echo $this->escape(stripslashes($this->row->get('ipREGION'))); ?>" /></td>
					</tr>
					<tr>
						<th class="key"><label for="field-ipCITY"><?php echo JText::_('COM_TOOLS_FIELD_CITY'); ?>:</label></th>
						<td><input type="text" name="fields[ipCITY]" id="field-ipCITY" value="<?php echo $this->escape(stripslashes($this->row->get('ipCITY'))); ?>" /></td>
					</tr>
					<tr>
						<th class="key"><label for="field-notes"><?php echo JText::_('COM_TOOLS_FIELD_NOTES'); ?>:</label></th>
						<td>
							<textarea name="fields[notes]" id="field-notes" rows="4" cols="35"><?php echo $this->escape(stripslashes($this->row->get('notes'))); ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>

	<?php echo JHTML::_('form.token'); ?>
</form>
