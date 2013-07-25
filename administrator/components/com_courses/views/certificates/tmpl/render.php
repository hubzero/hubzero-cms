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
$path = '/administrator/components/com_courses/views/certificates/tmpl/';
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

		<style>
		@font-face {
	font-family: Alegreya-Regular;
	src: url('<?php echo $path; ?>fonts/Alegreya/Alegreya-Regular.ttf');
}

@font-face {
    font-family: Alegreya-Bold;
    src: url('<?php echo $path; ?>fonts/Alegreya/Alegreya-Bold.ttf');
}

@font-face {
    font-family: Asset;
    src: url('<?php echo $path; ?>fonts/Asset/Asset.ttf');
}

@font-face {
    font-family: PinyonScript-Regular;
    src: url('<?php echo $path; ?>fonts/Pinyon_Script/PinyonScript-Regular.ttf');
}

body {
	text-align: center;
	margin: 0;
	padding: 0;
	padding-top: 4em;
	background: url(<?php echo $path; ?>texture.jpg) repeat center center fixed;
}

p {
	font-family: Alegreya-Regular, sans-serif;
	font-size: 1em;
}

#border-top, #border-bottom {
    background: url(<?php echo $path; ?>border.png) repeat-x left top;
    height: 61px;
    margin: 0;
    padding: 0;
}

#border-bottom {
	 -webkit-transform:scaleY(-1);
	 margin-bottom: 5.25em;
}

p.signed, p.signature {
	text-align:right;
	padding-right: 25%;
}

p.signature {
	margin-top: -2em;
}

#title {
	font-family: PinyonScript-Regular, sans-serif;
	font-size: 4em;
	margin-bottom: -0.25em;
}

#name {
	font-family: Alegreya-Bold, sans-serif;
	font-size: 1.5em;
}

#certification {
	font-family: Asset, sans-serif;
	font-size: 1.5em;
}

#date_day, #date_month, #date_year {
	   font-family: Alegreya-Bold, sans-serif;
}

#location {
	font-family: Alegreya-Bold, sans-serif;
}
		</style>
	</head>
	<body>
		<div id="border-top"><span>&nbsp;</span></div>
		
		<p id="title">Certification of completion</p>
		
		<p>This is to certify that</p>
		<p id="name"><?php echo $this->student->get('name'); ?></p>
		<p>has successfully completed the course requirements for</p>

		<p id="certification"><?php echo $this->course->get('title'); ?></p>

		<p>On the <span id="date_day">[[date_day]]</span> Day of <span id="date_month">[[date_month]]</span> In the Year <span id="date_year">[[date_year]]</span></p>
		<p>At: <span id="location">[[location]]</span>.</p>
		<p class="signed">Signed,</p>
		<p class="signature"><span>&nbsp;</span><img src="<?php echo $path; ?>signature.png" height="100" width="208" /></p>

		<div id="border-bottom"><span>&nbsp;</span></div>
	</body>
</html>