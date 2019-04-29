<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

?>
<div class="sidebox<?php if (count($this->items) == 0) { echo ' suggestions'; } ?>">
	<h4>
		<a href="<?php echo Route::url($this->model->link('publications')); ?>" class="hlink" title="<?php echo Lang::txt('COM_PROJECTS_VIEW') . ' ' . strtolower(Lang::txt('COM_PROJECTS_PROJECT')) . ' ' . strtolower(Lang::txt('COM_PROJECTS_TAB_PUBLICATIONS')); ?>"><?php echo ucfirst(Lang::txt('COM_PROJECTS_TAB_PUBLICATIONS')); ?></a>
		<?php if (count($this->items) > 0) { ?>
			<span><a href="<?php echo Route::url($this->model->link('publications')); ?>"><?php echo ucfirst(Lang::txt('COM_PROJECTS_SEE_ALL')); ?> </a></span>
		<?php } ?>
	</h4>
	<?php if (count($this->items) == 0) { ?>
		<p class="s-publications"><a href="<?php echo Route::url($this->model->link('publications') . '&action=start'); ?>"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_START_PUBLICATION'); ?></a></p>
	<?php } else { ?>
		<ul>
			<?php foreach ($this->items as $pub) {
				$status = $pub->getStatusName();
			?>
			<li>
				<span class="pub-thumb"><img src="<?php echo Route::url($pub->link('thumb')); ?>" alt=""/></span>
				<span class="pub-details">
					<a href="<?php echo Route::url($pub->link('editversion')); ?>" title="<?php echo $this->escape($pub->get('title')); ?>"><?php echo \Hubzero\Utility\Str::truncate(stripslashes($pub->get('title')), 100); ?></a>
					 <span class="block faded mini">
						<span>v. <?php echo $pub->get('version_label'); ?> (<?php echo $status; ?>)</span>
					</span>
				</span>
			</li>
			<?php } ?>
		</ul>
	<?php } ?>
</div>
