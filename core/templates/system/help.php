<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

// include frameworks
Html::behavior('framework', true);
Html::behavior('modal');
?>
<!DOCTYPE html>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
		<jdoc:include type="head" />
		<link rel="stylesheet" media="screen" href="<?php echo \Hubzero\Document\Assets::getSystemStylesheet(); ?>" type="text/css" />
		<link rel="stylesheet" media="screen" href="<?php echo $this->baseurl; ?>/templates/system/css/help.css" type="text/css" />
	</head>
	<body>
		<jdoc:include type="message" />
		<jdoc:include type="component" />
	</body>
</html>