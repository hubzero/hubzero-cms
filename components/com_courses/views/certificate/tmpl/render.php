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

$path = '/components/com_courses/views/certificate/tmpl/';
$juri =& JURI::getInstance();
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

			html {
				margin: 0;
				padding: 0;
				background: #fff;
			}
			body {
				text-align: center;
				margin: 0;
				padding: 0;
				background: #fff;
			}

			p {
				font-family: Alegreya-Regular, sans-serif;
				font-size: 1em;
			}

			#wrap {
				margin: 0 auto;
				padding: 0;
				width: 1100px;
				height: 850px;
				background: url(<?php echo $path; ?>img/border.jpg) center top no-repeat;
			}

			#content {
				margin: 0;
				padding: 100px 0;
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

			#date_day, 
			#date_month, 
			#date_year {
				font-family: Alegreya-Bold, sans-serif;
				font-weight: bold;
			}

			#location {
				font-family: Alegreya-Bold, sans-serif;
			}
		</style>
	</head>
	<body>
		<div id="wrap">
			<div id="content">
				<p id="title">Certification of completion</p>

				<p>This is to certify that</p>
				<p id="name"><?php echo $this->juser->get('name'); ?></p>
				<p>has successfully completed the course requirements for</p>

				<p id="certification"><?php echo $this->course->get('title'); ?></p>

				<p>On the <span id="date_day"><?php echo date("d"); ?></span> Day of <span id="date_month"><?php echo date("M"); ?></span> In the Year <span id="date_year"><?php echo date("Y"); ?></span></p>
				<p>At: <span id="location"><?php echo $juri->base(); ?></span>.</p>

				<p class="signed">Signed,</p>
				<p class="signature"><span>&nbsp;</span><img src="<?php echo $path; ?>img/signature.png" height="100" width="208" /></p>
			</div>
		</div>
	</body>
</html>