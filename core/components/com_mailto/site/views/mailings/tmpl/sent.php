<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<div>
	<div class="align-right">
		<a href="javascript: void window.close()">
			<?php echo Lang::txt('COM_MAILTO_CLOSE_WINDOW'); ?>
			<img src="<?php echo $this->img('close-x.png'); ?>" alt="" />
		</a>
	</div>

	<h2>
		<?php echo Lang::txt('COM_MAILTO_EMAIL_SENT'); ?>
	</h2>
</div>
