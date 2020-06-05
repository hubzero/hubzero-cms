<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel="stylesheet" type="text/css" href="styles.css" />
		<style>
		@font-face {
	font-family: Alegreya-Regular;
	src: url('<?php echo PATH_CORE . DS . 'components/com_courses/admin/views/certificates/tmpl/'; ?>fonts/Alegreya/Alegreya-Regular.ttf');
}

@font-face {
    font-family: Alegreya-Bold;
    src: url('<?php echo PATH_CORE . DS . 'components/com_courses/admin/views/certificates/tmpl/'; ?>fonts/Alegreya/Alegreya-Bold.ttf');
}

@font-face {
    font-family: Asset;
    src: url('<?php echo PATH_CORE . DS . 'components/com_courses/admin/views/certificates/tmpl/'; ?>fonts/Asset/Asset.ttf');
}

@font-face {
    font-family: PinyonScript-Regular;
    src: url('<?php echo PATH_CORE . DS . 'components/com_courses/admin/views/certificates/tmpl/'; ?>fonts/Pinyon_Script/PinyonScript-Regular.ttf');
}

body {
	text-align: center;
	margin: 0;
	padding: 0;
	padding-top: 4em;
	background: #c00 url(<?php echo PATH_CORE . DS . 'components/com_courses/admin/views/certificates/tmpl/'; ?>texture.jpg) repeat center center fixed;
}

p {
	font-family: Alegreya-Regular, sans-serif;
	font-size: 1em;
}

#border-top, #border-bottom {
    background: url(<?php echo PATH_CORE . DS . 'components/com_courses/admin/views/certificates/tmpl/'; ?>border.png) repeat-x left top;
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
		<p class="signature"><span>&nbsp;</span><img src="<?php echo PATH_CORE . DS . 'components/com_courses/admin/views/certificates/tmpl/'; ?>signature.png" height="100" width="208" /></p>

		<div id="border-bottom"><span>&nbsp;</span></div>
	</body>
</html>