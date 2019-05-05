<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if (count($this->items) > 0) { ?>
<div class="public-list-header">
	<h3><?php echo Lang::txt('COM_PROJECTS_PUBLICATIONS'); ?></h3>
</div>
<div class="public-list-wrap">
	<ul class="public-list">
		<?php foreach ($this->items as $pub) { ?>
			<li>
				<span class="pub-thumb"><img src="<?php echo Route::url($pub->link('thumb')); ?>" alt="" /></span>
				<span class="pub-details">
					<a href="<?php echo Route::url($pub->link('version')); ?>" title="<?php echo $this->escape($pub->get('title')); ?>"><?php echo stripslashes($pub->get('title')) . ' v.' . $pub->get('version_label'); ?></a>
					<span class="public-list-info">
						- <?php echo Lang::txt('COM_PROJECTS_PUBLISHED') . ' ' . $pub->published('date') . ' ' . Lang::txt('COM_PROJECTS_IN') . ' <a href="' . Route::url($pub->link('category')) . '">' . $pub->category()->name . '</a>'; ?>
					</span>
				</span>
			</li>
		<?php } ?>
	</ul>
</div>
<?php }
