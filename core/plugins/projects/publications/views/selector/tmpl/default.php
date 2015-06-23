<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// No direct access
defined('_HZEXEC_') or die();

// Get requirements
$manifest = $this->publication->curation('blocks', $this->blockId, 'manifest');

// Get selections
$selections = NULL;
$selected   = NULL;
if ($this->block == 'license')
{
	$objL 			= new \Components\Publications\Tables\License( $this->database);
	$selected 		= $objL->getPubLicense( $this->publication->get('version_id') );
	$selections 	= $objL->getBlockLicenses( $manifest, $selected );

	if (!$selections)
	{
		$selections = $objL->getDefaultLicense();
	}
}

?>
<script src="<?php echo rtrim(Request::base(true), '/'); ?>/core/plugins/projects/publications/assets/js/selector.js"></script>
<div id="abox-content">
<h3><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_SELECTOR_' . strtoupper($this->block)); ?> 	<span class="abox-controls">
				<a class="btn btn-success active" id="b-filesave"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_SELECTOR_SAVE_SELECTION'); ?></a>
				<?php if ($this->ajax) { ?>
				<a class="btn btn-cancel" id="cancel-action"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CANCEL'); ?></a>
				<?php } ?>
			</span></h3>
<form id="select-form" class="select-form" method="post" enctype="multipart/form-data" action="<?php echo Route::url($this->publication->link('edit')); ?>">
	<fieldset >
		<input type="hidden" name="id" value="<?php echo $this->project->get('id'); ?>" />
		<input type="hidden" name="version" value="<?php echo $this->publication->get('version_number'); ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
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
		<input type="hidden" name="action" value="apply" />
		<input type="hidden" name="move" value="continue" />
		<?php if ($this->project->isProvisioned()) { ?>
			<input type="hidden" name="task" value="submit" />
			<input type="hidden" name="ajax" value="0" />
		<?php }  ?>
	</fieldset>

	<?php if (!$selections)
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
					'folder'	=>'projects',
					'element'	=>'publications',
					'name'		=>'selector',
					'layout'	=> $this->block
				)
			);
			$view->option 		= $this->option;
			$view->project 		= $this->project;
			$view->database		= $this->database;
			$view->manifest 	= $manifest;
			$view->publication  = $this->publication;
			$view->selected		= $selected;
			$view->selections	= $selections;
			$view->url			= Route::url($this->publication->link('edit'));
			echo $view->loadTemplate();
		?>
	</div>
	<?php } ?>
	</form>
</div>