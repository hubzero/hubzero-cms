<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo Lang::txt('Email this resource'); ?></title>

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<link rel="stylesheet" type="text/css" media="screen" href="<?php echo Request::base(true); ?>/templates/<?php echo App::get('template')->template; ?>/css/main.css" />

		<script type="text/javascript" src="<?php echo Request::base(true); ?>/core/assets/js/jquery.js"></script>
	</head>
	<body id="small-page">
	</body>
</html>
