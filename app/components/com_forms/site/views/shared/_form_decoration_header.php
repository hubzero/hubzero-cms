<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$header = $this->decoration;
$content = htmlspecialchars($header->get('label'));
?>

<div class="header">
	<h1>
		<?php echo $content; ?>
	</h1>
</div>
