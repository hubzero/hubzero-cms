<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

if ($this->rows) { ?>
	<div class="<?php echo $this->params->get('moduleclass_sfx'); ?>">
		<?php foreach ($this->rows as $row) { ?>
			<p><?php echo stripslashes($row->introtext); ?></p>
			<p class="more">
				<a href="<?php echo Route::url( 'index.php?option=com_content&task=view&id=' . $row->id); ?>">
					<?php echo Lang::txt('MOD_QUICKTIPS_LEARN_MORE'); ?>
				</a>
			</p>
		<?php } ?>
	</div>
<?php }