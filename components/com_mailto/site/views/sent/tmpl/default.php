<?php
/**
 * @package		Joomla.Site
 * @subpackage	com_mailto
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<div style="padding: 10px;">
	<div style="text-align:right">
		<a href="javascript: void window.close()">
			<?php echo Lang::txt('COM_MAILTO_CLOSE_WINDOW'); ?>
			<img src="<?php echo $this->img('close-x.png'); ?>" alt="" />
		</a>
	</div>

	<h2>
		<?php echo Lang::txt('COM_MAILTO_EMAIL_SENT'); ?>
	</h2>
</div>
