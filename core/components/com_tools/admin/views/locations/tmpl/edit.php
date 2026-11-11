<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;

// No direct access.
defined('_HZEXEC_') or die();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));
if (!$this->tmpl) {
    Toolbar::title(
        Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_ZONES')
        . ': ' . Lang::txt('COM_TOOLS_LOCATIONS') . ': ' . $text,
        'tools.png',
    );
    Toolbar::save();
    Toolbar::cancel();
}

Html::behavior('framework');

$this->js('locations.js');

$countries = [
    ['code' => '', 'name' => '- Select - ', 'continent' => 'AS'],
    ['code' => '', 'name' => '- Select - ', 'continent' => 'NA'],
    ['code' => '', 'name' => '- Select - ', 'continent' => 'SA'],
    ['code' => '', 'name' => '- Select - ', 'continent' => 'AF'],
    ['code' => '', 'name' => '- Select - ', 'continent' => 'EU'],
    ['code' => '', 'name' => '- Select - ', 'continent' => 'OC'],
    ['code' => 'AF', 'name' => 'Afghanistan', 'continent' => 'AS'],
    ['code' => 'AX', 'name' => 'Aland Islands', 'continent' => 'EU'],
    ['code' => 'AL', 'name' => 'Albania', 'continent' => 'EU'],
    ['code' => 'DZ', 'name' => 'Algeria', 'continent' => 'AF'],
    ['code' => 'AS', 'name' => 'American Samoa', 'continent' => 'OC'],
    ['code' => 'AD', 'name' => 'Andorra', 'continent' => 'EU'],
    ['code' => 'AO', 'name' => 'Angola', 'continent' => 'AF'],
    ['code' => 'AI', 'name' => 'Anguilla', 'continent' => 'NA'],
    ['code' => 'AQ', 'name' => 'AN', 'continent' => 'AN'],
    ['code' => 'AG', 'name' => 'Antigua and Barbuda', 'continent' => 'NA'],
    ['code' => 'AR', 'name' => 'Argentina', 'continent' => 'SA'],
    ['code' => 'AM', 'name' => 'Armenia', 'continent' => 'AS'],
    ['code' => 'AW', 'name' => 'Aruba', 'continent' => 'NA'],
    ['code' => 'AU', 'name' => 'Australia', 'continent' => 'OC'],
    ['code' => 'AT', 'name' => 'Austria', 'continent' => 'EU'],
    ['code' => 'AZ', 'name' => 'Azerbaijan', 'continent' => 'AS'],
    ['code' => 'BS', 'name' => 'Bahamas', 'continent' => 'NA'],
    ['code' => 'BH', 'name' => 'Bahrain', 'continent' => 'AS'],
    ['code' => 'BD', 'name' => 'Bangladesh', 'continent' => 'AS'],
    ['code' => 'BB', 'name' => 'Barbados', 'continent' => 'NA'],
    ['code' => 'BY', 'name' => 'Belarus', 'continent' => 'EU'],
    ['code' => 'BE', 'name' => 'Belgium', 'continent' => 'EU'],
    ['code' => 'BZ', 'name' => 'Belize', 'continent' => 'NA'],
    ['code' => 'BJ', 'name' => 'Benin', 'continent' => 'AF'],
    ['code' => 'BM', 'name' => 'Bermuda', 'continent' => 'NA'],
    ['code' => 'BT', 'name' => 'Bhutan', 'continent' => 'AS'],
    ['code' => 'BO', 'name' => 'Bolivia', 'continent' => 'SA'],
    ['code' => 'BA', 'name' => 'Bosnia and Herzegovina', 'continent' => 'EU'],
    ['code' => 'BW', 'name' => 'Botswana', 'continent' => 'AF'],
    ['code' => 'BV', 'name' => 'Bouvet Island', 'continent' => 'AN'],
    ['code' => 'BR', 'name' => 'Brazil', 'continent' => 'SA'],
    ['code' => 'IO', 'name' => 'British Indian Ocean Territory', 'continent' => 'AS'],
    ['code' => 'BN', 'name' => 'Brunei Darussalam', 'continent' => 'AS'],
    ['code' => 'BG', 'name' => 'Bulgaria', 'continent' => 'EU'],
    ['code' => 'BF', 'name' => 'Burkina Faso', 'continent' => 'AF'],
    ['code' => 'BI', 'name' => 'Burundi', 'continent' => 'AF'],
    ['code' => 'KH', 'name' => 'Cambodia', 'continent' => 'AS'],
    ['code' => 'CM', 'name' => 'Cameroon', 'continent' => 'AF'],
    ['code' => 'CA', 'name' => 'Canada', 'continent' => 'NA'],
    ['code' => 'CV', 'name' => 'Cape Verde', 'continent' => 'AF'],
    ['code' => 'KY', 'name' => 'Cayman Islands', 'continent' => 'NA'],
    ['code' => 'CF', 'name' => 'Central African Republic', 'continent' => 'AF'],
    ['code' => 'TD', 'name' => 'Chad', 'continent' => 'AF'],
    ['code' => 'CL', 'name' => 'Chile', 'continent' => 'SA'],
    ['code' => 'CN', 'name' => 'China', 'continent' => 'AS'],
    ['code' => 'CX', 'name' => 'Christmas Island', 'continent' => 'AS'],
    ['code' => 'CC', 'name' => 'Cocos (Keeling) Islands', 'continent' => 'AS'],
    ['code' => 'CO', 'name' => 'Colombia', 'continent' => 'SA'],
    ['code' => 'KM', 'name' => 'Comoros', 'continent' => 'AF'],
    ['code' => 'CG', 'name' => 'Congo', 'continent' => 'AF'],
    ['code' => 'CD', 'name' => 'The Democratic Republic of The Congo', 'continent' => 'AF'],
    ['code' => 'CK', 'name' => 'Cook Islands', 'continent' => 'OC'],
    ['code' => 'CR', 'name' => 'Costa Rica', 'continent' => 'NA'],
    ['code' => 'CI', 'name' => 'Cote D\'ivoire', 'continent' => 'AF'],
    ['code' => 'HR', 'name' => 'Croatia', 'continent' => 'EU'],
    ['code' => 'CU', 'name' => 'Cuba', 'continent' => 'NA'],
    ['code' => 'CY', 'name' => 'Cyprus', 'continent' => 'AS'],
    ['code' => 'CZ', 'name' => 'Czech Republic', 'continent' => 'EU'],
    ['code' => 'DK', 'name' => 'Denmark', 'continent' => 'EU'],
    ['code' => 'DJ', 'name' => 'Djibouti', 'continent' => 'AF'],
    ['code' => 'DM', 'name' => 'Dominica', 'continent' => 'NA'],
    ['code' => 'DO', 'name' => 'Dominican Republic', 'continent' => 'NA'],
    ['code' => 'EC', 'name' => 'Ecuador', 'continent' => 'SA'],
    ['code' => 'EG', 'name' => 'Egypt', 'continent' => 'AF'],
    ['code' => 'SV', 'name' => 'El Salvador', 'continent' => 'NA'],
    ['code' => 'GQ', 'name' => 'Equatorial Guinea', 'continent' => 'AF'],
    ['code' => 'ER', 'name' => 'Eritrea', 'continent' => 'AF'],
    ['code' => 'EE', 'name' => 'Estonia', 'continent' => 'EU'],
    ['code' => 'ET', 'name' => 'Ethiopia', 'continent' => 'AF'],
    ['code' => 'FK', 'name' => 'Falkland Islands (Malvinas)', 'continent' => 'SA'],
    ['code' => 'FO', 'name' => 'Faroe Islands', 'continent' => 'EU'],
    ['code' => 'FJ', 'name' => 'Fiji', 'continent' => 'OC'],
    ['code' => 'FI', 'name' => 'Finland', 'continent' => 'EU'],
    ['code' => 'FR', 'name' => 'France', 'continent' => 'EU'],
    ['code' => 'GF', 'name' => 'French Guiana', 'continent' => 'SA'],
    ['code' => 'PF', 'name' => 'French Polynesia', 'continent' => 'OC'],
    ['code' => 'TF', 'name' => 'French Southern Territories', 'continent' => 'AN'],
    ['code' => 'GA', 'name' => 'Gabon', 'continent' => 'AF'],
    ['code' => 'GM', 'name' => 'Gambia', 'continent' => 'AF'],
    ['code' => 'GE', 'name' => 'Georgia', 'continent' => 'AS'],
    ['code' => 'DE', 'name' => 'Germany', 'continent' => 'EU'],
    ['code' => 'GH', 'name' => 'Ghana', 'continent' => 'AF'],
    ['code' => 'GI', 'name' => 'Gibraltar', 'continent' => 'EU'],
    ['code' => 'GR', 'name' => 'Greece', 'continent' => 'EU'],
    ['code' => 'GL', 'name' => 'Greenland', 'continent' => 'NA'],
    ['code' => 'GD', 'name' => 'Grenada', 'continent' => 'NA'],
    ['code' => 'GP', 'name' => 'Guadeloupe', 'continent' => 'NA'],
    ['code' => 'GU', 'name' => 'Guam', 'continent' => 'OC'],
    ['code' => 'GT', 'name' => 'Guatemala', 'continent' => 'NA'],
    ['code' => 'GG', 'name' => 'Guernsey', 'continent' => 'EU'],
    ['code' => 'GN', 'name' => 'Guinea', 'continent' => 'AF'],
    ['code' => 'GW', 'name' => 'Guinea-bissau', 'continent' => 'AF'],
    ['code' => 'GY', 'name' => 'Guyana', 'continent' => 'SA'],
    ['code' => 'HT', 'name' => 'Haiti', 'continent' => 'NA'],
    ['code' => 'HM', 'name' => 'Heard Island and Mcdonald Islands', 'continent' => 'AN'],
    ['code' => 'VA', 'name' => 'Holy See (Vatican City State)', 'continent' => 'EU'],
    ['code' => 'HN', 'name' => 'Honduras', 'continent' => 'NA'],
    ['code' => 'HK', 'name' => 'Hong Kong', 'continent' => 'AS'],
    ['code' => 'HU', 'name' => 'Hungary', 'continent' => 'EU'],
    ['code' => 'IS', 'name' => 'Iceland', 'continent' => 'EU'],
    ['code' => 'IN', 'name' => 'India', 'continent' => 'AS'],
    ['code' => 'ID', 'name' => 'Indonesia', 'continent' => 'AS'],
    ['code' => 'IR', 'name' => 'Iran', 'continent' => 'AS'],
    ['code' => 'IQ', 'name' => 'Iraq', 'continent' => 'AS'],
    ['code' => 'IE', 'name' => 'Ireland', 'continent' => 'EU'],
    ['code' => 'IM', 'name' => 'Isle of Man', 'continent' => 'EU'],
    ['code' => 'IL', 'name' => 'Israel', 'continent' => 'AS'],
    ['code' => 'IT', 'name' => 'Italy', 'continent' => 'EU'],
    ['code' => 'JM', 'name' => 'Jamaica', 'continent' => 'NA'],
    ['code' => 'JP', 'name' => 'Japan', 'continent' => 'AS'],
    ['code' => 'JE', 'name' => 'Jersey', 'continent' => 'EU'],
    ['code' => 'JO', 'name' => 'Jordan', 'continent' => 'AS'],
    ['code' => 'KZ', 'name' => 'Kazakhstan', 'continent' => 'AS'],
    ['code' => 'KE', 'name' => 'Kenya', 'continent' => 'AF'],
    ['code' => 'KI', 'name' => 'Kiribati', 'continent' => 'OC'],
    ['code' => 'KP', 'name' => 'Korea, Democratic People\'s Republic of', 'continent' => 'AS'],
    ['code' => 'KR', 'name' => 'Korea, Republic of', 'continent' => 'AS'],
    ['code' => 'KW', 'name' => 'Kuwait', 'continent' => 'AS'],
    ['code' => 'KG', 'name' => 'Kyrgyzstan', 'continent' => 'AS'],
    ['code' => 'LA', 'name' => 'Lao People\'s Democratic Republic', 'continent' => 'AS'],
    ['code' => 'LV', 'name' => 'Latvia', 'continent' => 'EU'],
    ['code' => 'LB', 'name' => 'Lebanon', 'continent' => 'AS'],
    ['code' => 'LS', 'name' => 'Lesotho', 'continent' => 'AF'],
    ['code' => 'LR', 'name' => 'Liberia', 'continent' => 'AF'],
    ['code' => 'LY', 'name' => 'Libya', 'continent' => 'AF'],
    ['code' => 'LI', 'name' => 'Liechtenstein', 'continent' => 'EU'],
    ['code' => 'LT', 'name' => 'Lithuania', 'continent' => 'EU'],
    ['code' => 'LU', 'name' => 'Luxembourg', 'continent' => 'EU'],
    ['code' => 'MO', 'name' => 'Macao', 'continent' => 'AS'],
    ['code' => 'MK', 'name' => 'Macedonia', 'continent' => 'EU'],
    ['code' => 'MG', 'name' => 'Madagascar', 'continent' => 'AF'],
    ['code' => 'MW', 'name' => 'Malawi', 'continent' => 'AF'],
    ['code' => 'MY', 'name' => 'Malaysia', 'continent' => 'AS'],
    ['code' => 'MV', 'name' => 'Maldives', 'continent' => 'AS'],
    ['code' => 'ML', 'name' => 'Mali', 'continent' => 'AF'],
    ['code' => 'MT', 'name' => 'Malta', 'continent' => 'EU'],
    ['code' => 'MH', 'name' => 'Marshall Islands', 'continent' => 'OC'],
    ['code' => 'MQ', 'name' => 'Martinique', 'continent' => 'NA'],
    ['code' => 'MR', 'name' => 'Mauritania', 'continent' => 'AF'],
    ['code' => 'MU', 'name' => 'Mauritius', 'continent' => 'AF'],
    ['code' => 'YT', 'name' => 'Mayotte', 'continent' => 'AF'],
    ['code' => 'MX', 'name' => 'Mexico', 'continent' => 'NA'],
    ['code' => 'FM', 'name' => 'Micronesia', 'continent' => 'OC'],
    ['code' => 'MD', 'name' => 'Moldova', 'continent' => 'EU'],
    ['code' => 'MC', 'name' => 'Monaco', 'continent' => 'EU'],
    ['code' => 'MN', 'name' => 'Mongolia', 'continent' => 'AS'],
    ['code' => 'ME', 'name' => 'Montenegro', 'continent' => 'EU'],
    ['code' => 'MS', 'name' => 'Montserrat', 'continent' => 'NA'],
    ['code' => 'MA', 'name' => 'Morocco', 'continent' => 'AF'],
    ['code' => 'MZ', 'name' => 'Mozambique', 'continent' => 'AF'],
    ['code' => 'MM', 'name' => 'Myanmar', 'continent' => 'AS'],
    ['code' => 'NA', 'name' => 'Namibia', 'continent' => 'AF'],
    ['code' => 'NR', 'name' => 'Nauru', 'continent' => 'OC'],
    ['code' => 'NP', 'name' => 'Nepal', 'continent' => 'AS'],
    ['code' => 'NL', 'name' => 'Netherlands', 'continent' => 'EU'],
    ['code' => 'AN', 'name' => 'Netherlands Antilles', 'continent' => 'NA'],
    ['code' => 'NC', 'name' => 'New Caledonia', 'continent' => 'OC'],
    ['code' => 'NZ', 'name' => 'New Zealand', 'continent' => 'OC'],
    ['code' => 'NI', 'name' => 'Nicaragua', 'continent' => 'NA'],
    ['code' => 'NE', 'name' => 'Niger', 'continent' => 'AF'],
    ['code' => 'NG', 'name' => 'Nigeria', 'continent' => 'AF'],
    ['code' => 'NU', 'name' => 'Niue', 'continent' => 'OC'],
    ['code' => 'NF', 'name' => 'Norfolk Island', 'continent' => 'OC'],
    ['code' => 'MP', 'name' => 'Northern Mariana Islands', 'continent' => 'OC'],
    ['code' => 'NO', 'name' => 'Norway', 'continent' => 'EU'],
    ['code' => 'OM', 'name' => 'Oman', 'continent' => 'AS'],
    ['code' => 'PK', 'name' => 'Pakistan', 'continent' => 'AS'],
    ['code' => 'PW', 'name' => 'Palau', 'continent' => 'OC'],
    ['code' => 'PS', 'name' => 'Palestinia', 'continent' => 'AS'],
    ['code' => 'PA', 'name' => 'Panama', 'continent' => 'NA'],
    ['code' => 'PG', 'name' => 'Papua New Guinea', 'continent' => 'OC'],
    ['code' => 'PY', 'name' => 'Paraguay', 'continent' => 'SA'],
    ['code' => 'PE', 'name' => 'Peru', 'continent' => 'SA'],
    ['code' => 'PH', 'name' => 'Philippines', 'continent' => 'AS'],
    ['code' => 'PN', 'name' => 'Pitcairn', 'continent' => 'OC'],
    ['code' => 'PL', 'name' => 'Poland', 'continent' => 'EU'],
    ['code' => 'PT', 'name' => 'Portugal', 'continent' => 'EU'],
    ['code' => 'PR', 'name' => 'Puerto Rico', 'continent' => 'NA'],
    ['code' => 'QA', 'name' => 'Qatar', 'continent' => 'AS'],
    ['code' => 'RE', 'name' => 'Reunion', 'continent' => 'AF'],
    ['code' => 'RO', 'name' => 'Romania', 'continent' => 'EU'],
    ['code' => 'RU', 'name' => 'Russian Federation', 'continent' => 'EU'],
    ['code' => 'RW', 'name' => 'Rwanda', 'continent' => 'AF'],
    ['code' => 'SH', 'name' => 'Saint Helena', 'continent' => 'AF'],
    ['code' => 'KN', 'name' => 'Saint Kitts and Nevis', 'continent' => 'NA'],
    ['code' => 'LC', 'name' => 'Saint Lucia', 'continent' => 'NA'],
    ['code' => 'PM', 'name' => 'Saint Pierre and Miquelon', 'continent' => 'NA'],
    ['code' => 'VC', 'name' => 'Saint Vincent and The Grenadines', 'continent' => 'NA'],
    ['code' => 'WS', 'name' => 'Samoa', 'continent' => 'OC'],
    ['code' => 'SM', 'name' => 'San Marino', 'continent' => 'EU'],
    ['code' => 'ST', 'name' => 'Sao Tome and Principe', 'continent' => 'AF'],
    ['code' => 'SA', 'name' => 'Saudi Arabia', 'continent' => 'AS'],
    ['code' => 'SN', 'name' => 'Senegal', 'continent' => 'AF'],
    ['code' => 'RS', 'name' => 'Serbia', 'continent' => 'EU'],
    ['code' => 'SC', 'name' => 'Seychelles', 'continent' => 'AF'],
    ['code' => 'SL', 'name' => 'Sierra Leone', 'continent' => 'AF'],
    ['code' => 'SG', 'name' => 'Singapore', 'continent' => 'AS'],
    ['code' => 'SK', 'name' => 'Slovakia', 'continent' => 'EU'],
    ['code' => 'SI', 'name' => 'Slovenia', 'continent' => 'EU'],
    ['code' => 'SB', 'name' => 'Solomon Islands', 'continent' => 'OC'],
    ['code' => 'SO', 'name' => 'Somalia', 'continent' => 'AF'],
    ['code' => 'ZA', 'name' => 'South Africa', 'continent' => 'AF'],
    ['code' => 'GS', 'name' => 'South Georgia and The South Sandwich Islands', 'continent' => 'AN'],
    ['code' => 'ES', 'name' => 'Spain', 'continent' => 'EU'],
    ['code' => 'LK', 'name' => 'Sri Lanka', 'continent' => 'AS'],
    ['code' => 'SD', 'name' => 'Sudan', 'continent' => 'AF'],
    ['code' => 'SR', 'name' => 'Suriname', 'continent' => 'SA'],
    ['code' => 'SJ', 'name' => 'Svalbard and Jan Mayen', 'continent' => 'EU'],
    ['code' => 'SZ', 'name' => 'Swaziland', 'continent' => 'AF'],
    ['code' => 'SE', 'name' => 'Sweden', 'continent' => 'EU'],
    ['code' => 'CH', 'name' => 'Switzerland', 'continent' => 'EU'],
    ['code' => 'SY', 'name' => 'Syrian Arab Republic', 'continent' => 'AS'],
    ['code' => 'TW', 'name' => 'Taiwan', 'continent' => 'AS'],
    ['code' => 'TJ', 'name' => 'Tajikistan', 'continent' => 'AS'],
    ['code' => 'TZ', 'name' => 'Tanzania, United Republic of', 'continent' => 'AF'],
    ['code' => 'TH', 'name' => 'Thailand', 'continent' => 'AS'],
    ['code' => 'TL', 'name' => 'Timor-leste', 'continent' => 'AS'],
    ['code' => 'TG', 'name' => 'Togo', 'continent' => 'AF'],
    ['code' => 'TK', 'name' => 'Tokelau', 'continent' => 'OC'],
    ['code' => 'TO', 'name' => 'Tonga', 'continent' => 'OC'],
    ['code' => 'TT', 'name' => 'Trinidad and Tobago', 'continent' => 'NA'],
    ['code' => 'TN', 'name' => 'Tunisia', 'continent' => 'AF'],
    ['code' => 'TR', 'name' => 'Turkey', 'continent' => 'AS'],
    ['code' => 'TM', 'name' => 'Turkmenistan', 'continent' => 'AS'],
    ['code' => 'TC', 'name' => 'Turks and Caicos Islands', 'continent' => 'NA'],
    ['code' => 'TV', 'name' => 'Tuvalu', 'continent' => 'OC'],
    ['code' => 'UG', 'name' => 'Uganda', 'continent' => 'AF'],
    ['code' => 'UA', 'name' => 'Ukraine', 'continent' => 'EU'],
    ['code' => 'AE', 'name' => 'United Arab Emirates', 'continent' => 'AS'],
    ['code' => 'GB', 'name' => 'United Kingdom', 'continent' => 'EU'],
    ['code' => 'US', 'name' => 'United States', 'continent' => 'NA'],
    ['code' => 'UM', 'name' => 'United States Minor Outlying Islands', 'continent' => 'OC'],
    ['code' => 'UY', 'name' => 'Uruguay', 'continent' => 'SA'],
    ['code' => 'UZ', 'name' => 'Uzbekistan', 'continent' => 'AS'],
    ['code' => 'VU', 'name' => 'Vanuatu', 'continent' => 'OC'],
    ['code' => 'VE', 'name' => 'Venezuela', 'continent' => 'SA'],
    ['code' => 'VN', 'name' => 'Viet Nam', 'continent' => 'AS'],
    ['code' => 'VG', 'name' => 'Virgin Islands, British', 'continent' => 'NA'],
    ['code' => 'VI', 'name' => 'Virgin Islands, US', 'continent' => 'NA'],
    ['code' => 'WF', 'name' => 'Wallis and Futuna', 'continent' => 'OC'],
    ['code' => 'EH', 'name' => 'Western Sahara', 'continent' => 'AF'],
    ['code' => 'YE', 'name' => 'Yemen', 'continent' => 'AS'],
    ['code' => 'ZM', 'name' => 'Zambia', 'continent' => 'AF'],
    ['code' => 'ZW', 'name' => 'Zimbabwe', 'continent' => 'AF'],
];
?>
<script type="application/json" id="country-data" data-select="<?php echo Lang::txt('COM_TOOLS_SELECT'); ?>">
    {
        "data": <?php echo json_encode($countries); ?>
    }
</script>
<?php if ($this->getError()) { ?>
    <p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
<?php
$formAction = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller);
$formId = ($this->tmpl == 'component') ? 'component-form' : 'item-form';
?>
<form action="<?php echo $formAction; ?>" method="post" name="adminForm"
    id="<?php echo $formId; ?>" enctype="multipart/form-data">
<?php if ($this->tmpl == 'component') { ?>
    <fieldset>
        <div class="configuration">
            <div class="configuration-options">
                <button type="button" id="btn-save"><?php echo Lang::txt('COM_TOOLS_SAVE'); ?></button>
                <button type="button" id="btn-close"><?php echo Lang::txt('JCANCEL'); ?></button>
            </div>
            <?php echo $text; ?>
        </div>
    </fieldset>
<?php } ?>
    <div class="col span12">
        <fieldset class="adminform">
            <legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

            <input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->row->get('id')); ?>" />
            <input type="hidden" name="fields[zone_id]"
                value="<?php echo $this->escape($this->row->get('zone_id')); ?>" />

            <input type="hidden" name="tmpl" value="<?php echo $this->escape($this->tmpl); ?>" />
            <input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
            <input type="hidden" name="controller" value="<?php echo $this->escape($this->controller); ?>">
            <input type="hidden" name="task" value="save" />

            <table class="admintable">
                <tbody>
                    <tr>
                        <th class="key">
                            <label for="field-ipFROM"><?php echo Lang::txt('COM_TOOLS_FIELD_IP_FROM'); ?>:</label></th>
                        <td>
                            <?php $ipFromVal = $this->escape(\Components\Tools\Helpers\Iprange::toText($this->row->get('ipFROM'))); ?>
                            <input type="text" name="fields[ipFROM]" id="field-ipFROM"
                                value="<?php echo $ipFromVal; ?>" /></td>
                    </tr>
                    <tr class="ipTOrow">
                        <th class="key">
                            <label for="field-ipTO"><?php echo Lang::txt('COM_TOOLS_FIELD_IP_TO'); ?>:</label></th>
                        <td>
                            <?php $ipToVal = $this->escape(\Components\Tools\Helpers\Iprange::toText($this->row->get('ipTO'))); ?>
                            <input type="text" name="fields[ipTO]" id="field-ipTO"
                                value="<?php echo $ipToVal; ?>" /></td>
                    </tr>
                    <tr>
                        <th class="key">
                            <label for="field-continent">
                                <?php echo Lang::txt('COM_TOOLS_FIELD_CONTINENT'); ?>:</label></th>
                        <td>
                            <select name="fields[continent]" id="field-continent">
                                <option value=""<?= $this->row->get('continent') == '' ? ' selected="selected"' : '' ?>><?= Lang::txt('COM_TOOLS_SELECT') ?></option>
                                <option value="NA"<?= $this->row->get('continent') == 'NA' ? ' selected="selected"' : '' ?>>North America</option>
                                <option value="SA"<?= $this->row->get('continent') == 'SA' ? ' selected="selected"' : '' ?>>South America</option>
                                <option value="EU"<?= $this->row->get('continent') == 'EU' ? ' selected="selected"' : '' ?>>Europe</option>
                                <option value="AF"<?= $this->row->get('continent') == 'AF' ? ' selected="selected"' : '' ?>>Africa</option>
                                <option value="AS"<?= $this->row->get('continent') == 'AS' ? ' selected="selected"' : '' ?>>Asia</option>
                                <option value="AN"<?= $this->row->get('continent') == 'AN' ? ' selected="selected"' : '' ?>>Antarctica</option>
                                <option value="OC"<?= $this->row->get('continent') == 'OC' ? ' selected="selected"' : '' ?>>Oceania</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th class="key">
                            <label for="field-countrySHORT"><?php echo Lang::txt('Country'); ?>:</label></th>
                        <td>
                            <select name="fields[countrySHORT]" id="field-countrySHORT">
                                <option value=""<?= $this->row->get('continent') == '' ? ' selected="selected"' : '' ?>><?= Lang::txt('COM_TOOLS_SELECT') ?></option>
                                <?php
                                //if ($countries = \Hubzero\Geocode\Geocode::countries())
                                if ($countries) {
                                    if (!$this->row->get('continent')) {
                                        //$this->row->set('continent', 'NA');
                                    }
                                    foreach ($countries as $country) {
                                        if ($country['continent'] != $this->row->get('continent')) {
                                            continue;
                                        }
                                        echo '<option value="' . $country['code'] . '"';
                                        if (strtoupper($this->row->get('countrySHORT')) == $country['code']) {
                                            echo ' selected="selected"';
                                        }
                                        echo '>' . $this->escape($country['name']) . '</option>' . "\n";
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th class="key">
                            <label for="field-ipREGION"><?php echo Lang::txt('COM_TOOLS_FIELD_REGION'); ?>:</label></th>
                        <td>
                            <?php $ipRegionVal = $this->escape(stripslashes($this->row->get('ipREGION'))); ?>
                            <input type="text" name="fields[ipREGION]" id="field-ipREGION"
                                value="<?php echo $ipRegionVal; ?>" /></td>
                    </tr>
                    <tr>
                        <th class="key">
                            <label for="field-ipCITY"><?php echo Lang::txt('COM_TOOLS_FIELD_CITY'); ?>:</label></th>
                        <td>
                            <?php $ipCityVal = $this->escape(stripslashes($this->row->get('ipCITY'))); ?>
                            <input type="text" name="fields[ipCITY]" id="field-ipCITY"
                                value="<?php echo $ipCityVal; ?>" /></td>
                    </tr>
                    <tr>
                        <th class="key">
                            <label for="field-notes"><?php echo Lang::txt('COM_TOOLS_FIELD_NOTES'); ?>:</label></th>
                        <td>
                            <?php $notesVal = $this->escape(stripslashes($this->row->get('notes'))); ?>
                            <textarea name="fields[notes]" id="field-notes"
                                rows="4" cols="35"><?php echo $notesVal; ?></textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    </div>
    <div class="clr"></div>

    <?php echo Html::input('token'); ?>
</form>
