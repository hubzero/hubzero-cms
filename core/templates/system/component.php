<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" class="<?php echo $this->direction; ?>">
	<head>
		<jdoc:include type="head" />
	</head>
	<body class="contentpane">
		<jdoc:include type="message" />
		<jdoc:include type="component" />
	</body>
</html>