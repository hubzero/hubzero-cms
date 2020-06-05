<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

// Push the module CSS to the template
$this->css()
	->js();
?>
<div class="<?php echo $this->module->module . ($this->moduleclass ? ' ' . $this->moduleclass : ''); ?>" id="<?php echo $this->module->module . $this->module->id; ?>">
	<?php if ($this->params->get('button_show_all', 1) || $this->params->get('button_show_add', 1)): ?>
		<ul class="module-nav">
			<?php if ($this->params->get('button_show_all', 1)): ?>
				<li>
					<a class="icon-browse" href="<?php echo Route::url('index.php?option=com_publications&task=browse'); ?>">
						<?php echo Lang::txt('MOD_MYPUBLICATIONS_ALL_PUBLICATIONS'); ?>
					</a>
				</li>
			<?php endif; ?>
			<?php if ($this->params->get('button_show_add', 1)): ?>
				<li>
					<a class="icon-plus" href="<?php echo Route::url('index.php?option=com_publications&task=submit'); ?>">
						<?php echo Lang::txt('MOD_MYPUBLICATIONS_NEW_PUBLICATION'); ?>
					</a>
				</li>
			<?php endif; ?>
		</ul>
	<?php endif; ?>

	<ul class="tab_titles">
		<li rel="draftpublications<?php echo $this->module->id; ?>" class="tab_title active">
			<?php echo Lang::txt('MOD_MYPUBLICATIONS_DRAFTS'); ?>
		</li>
		<li rel="publishedpublications<?php echo $this->module->id; ?>" class="tab_title">
			<?php echo Lang::txt('MOD_MYPUBLICATIONS_PUBLISHED'); ?>
		</li>
	</ul>

	<div id="draftpublications<?php echo $this->module->id; ?>" class="tab_panel active">
		<?php if (count($this->drafts) > 0): ?>
			<ul class="expandedlist mypublications">
				<?php
				foreach ($this->drafts as $item):
					require $this->getLayoutPath('_item');
				endforeach;
				?>
			</ul>
		<?php else: ?>
			<p><em><?php echo Lang::txt('MOD_MYGROUPS_NO_RECENT_GROUPS'); ?></em></p>
		<?php endif; ?>
	</div>

	<div id="publishedpublications<?php echo $this->module->id; ?>" class="tab_panel">
		<?php if (count($this->published) > 0): ?>
			<ul class="expandedlist mypublications">
				<?php
				foreach ($this->published as $item):
					require $this->getLayoutPath('_item');
				endforeach;
				?>
			</ul>
		<?php else: ?>
			<p><em><?php echo Lang::txt('MOD_MYGROUPS_NO_RECENT_GROUPS'); ?></em></p>
		<?php endif; ?>
	</div>
</div>