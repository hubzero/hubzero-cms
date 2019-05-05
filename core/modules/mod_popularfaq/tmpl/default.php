<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<div<?php echo ($this->cssId) ? ' id="' . $this->cssId . '"' : '';
	echo ($this->cssClass) ? ' class="' . $this->cssClass . '"' : ''; ?>>
	<?php if ($this->rows->count() > 0) { ?>
		<ul class="articles">
			<?php foreach ($this->rows as $row) { ?>
				<li><a href="<?php echo Route::url($row->link()); ?>"><?php echo $this->escape(stripslashes($row->get('title'))); ?></a></li>
			<?php } ?>
		</ul>
	<?php } else { ?>
		<p><?php echo Lang::txt('MOD_POPULARFAQ_NO_ARTICLES_FOUND'); ?></p>
	<?php } ?>
</div>
