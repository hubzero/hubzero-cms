<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Get requirements
$manifest = $this->publication->curation('blocks', $this->blockId, 'manifest');
?>
<script src="<?php echo rtrim(Request::base(true), '/'); ?>/core/plugins/projects/publications/assets/js/jquery.infinitescroll.min.js"></script>
<script src="<?php echo rtrim(Request::base(true), '/'); ?>/core/plugins/projects/publications/assets/js/selector.js"></script>
<div id="abox-content">
	<h3>
		<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_SELECTOR_' . strtoupper($this->block)); ?>
		<span class="abox-controls">
			<a class="btn btn-success active" id="b-filesave"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_SELECTOR_SAVE_SELECTION'); ?></a>
			<?php if ($this->ajax) { ?>
				<a class="btn btn-cancel" id="cancel-action"><?php echo Lang::txt('JCANCEL'); ?></a>
			<?php } ?>
		</span>
	</h3>
	<form id="select-form" class="select-form" method="post" enctype="multipart/form-data" action="<?php echo Route::url($this->publication->link('edit')); ?>">
		<fieldset>
			<input type="hidden" name="version" value="<?php echo $this->publication->get('version_number'); ?>" />
			<input type="hidden" name="ajax" value="<?php echo $this->ajax; ?>" />
			<input type="hidden" id="p" name="p" value="<?php echo $this->props; ?>" />
			<input type="hidden" name="pid" value="<?php echo $this->publication->get('id'); ?>" />
			<input type="hidden" name="vid" value="<?php echo $this->publication->get('version_id'); ?>" />
			<input type="hidden" name="section" value="<?php echo $this->block; ?>" />
			<input type="hidden" name="step" value="<?php echo $this->blockId; ?>" />
			<input type="hidden" name="element" value="<?php echo $this->element; ?>" />
			<input type="hidden" name="el" value="<?php echo $this->element; ?>" />
			<input type="hidden" id="selecteditems" name="selecteditems" value="" />
			<input type="hidden" name="active" value="publications" />

			<input type="hidden" name="move" value="continue" />
			<?php if ($this->project->isProvisioned()) { ?>
				<input type="hidden" name="id" value="<?php echo $this->publication->get('id'); ?>" />
				<input type="hidden" name="task" value="submit" />
				<input type="hidden" name="option" value="com_publications" />
				<input type="hidden" name="ajax" value="0" />
			<?php } else { ?>
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="id" value="<?php echo $this->project->get('id'); ?>" />
			<?php } ?>
		</fieldset>

		<?php
		if ($this->block == 'license')
		{
			$task = 'apply';

			// Get selections
			$selections = null;
			$selected   = null;

			$objL = new \Components\Publications\Tables\License($this->database);
			$selected   = $objL->getPubLicense($this->publication->get('version_id'));
			$selections = $objL->getBlockLicenses($manifest, $selected);

			if (!$selections)
			{
				$selections = $objL->getDefaultLicense();
			}

			if (!$selections)
			{
				echo '<p class="error">' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_SELECTOR_ERROR_NO_SELECTIONS') . '</p>';
			}
			else
			{
				?>
				<p class="requirement" id="req"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_SELECTOR_REQ_' . strtoupper($this->block)); ?></p>
				<div id="content-selector" class="content-selector">
					<?php
					// Show selection
					$view = new \Hubzero\Plugin\View(
						array(
							'folder'  => 'projects',
							'element' => 'publications',
							'name'    => 'selector',
							'layout'  => $this->block
						)
					);
					$view->option      = $this->option;
					$view->project     = $this->project;
					$view->database    = $this->database;
					$view->manifest    = $manifest;
					$view->publication = $this->publication;
					$view->selected    = $selected;
					$view->selections  = $selections;
					$view->url         = Route::url($this->publication->link('edit'));
					echo $view->loadTemplate();
					?>
				</div>
				<?php
			}
		}
		else
		{
			// Get requirements
			$element = $this->publication->curation('blocks', $this->blockId, 'elements', $this->element);
			$params  = $element->params;
			$max     = $params->max;
			$min     = $params->min;
			?>
			<input type="hidden" id="maxitems" name="maxitems" value="<?php echo $max; ?>" />
			<input type="hidden" id="minitems" name="minitems" value="<?php echo $min; ?>" />
			<?php

			// Show selection
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => 'projects',
					'element' => 'publications',
					'name'    => 'selector',
					'layout'  => $this->block
				)
			);
			$view->option      = $this->option;
			$view->project     = $this->project;
			$view->database    = $this->database;
			$view->manifest    = $manifest;
			$view->publication = $this->publication;
			$view->url         = Route::url($this->publication->link('edit'));
			$view->display();

			$task = 'apply';
		}
		?>
		<input type="hidden" name="action" value="<?php echo $task; ?>" />
	</form>
</div>