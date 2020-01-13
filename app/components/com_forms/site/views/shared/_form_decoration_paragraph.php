<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$paragraph = $this->decoration;
$content = htmlspecialchars($paragraph->get('label'));
?>

<div class="paragraph">
	<p>
		<?php echo $content; ?>
	</p>
</div>
