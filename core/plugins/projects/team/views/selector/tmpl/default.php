<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

// No direct access
defined('_HZEXEC_') or die();

$selected = array();
if (count($this->authors) > 0)
{
	foreach ($this->authors as $sel)
	{
		$selected[] = $sel->project_owner_id;
	}
}

$newauthorUrl = Route::url($this->publication->link('editversionid') . '&active=team&action=newauthor&p=' . $this->props);

?>
<div id="abox-content-wrap">
<div id="abox-content">
<script src="<?php echo rtrim(Request::base(true), '/'); ?>/core/plugins/projects/team/assets/js/selector.js"></script>
<h3><?php echo Lang::txt('PLG_PROJECTS_TEAM_SELECTOR'); ?> 	<span class="abox-controls">
		<a class="btn btn-success active" id="b-save"><?php echo Lang::txt('PLG_PROJECTS_TEAM_SELECTOR_SAVE_SELECTION'); ?></a>
		<?php if ($this->ajax) { ?>
		<a class="btn btn-cancel" id="cancel-action"><?php echo Lang::txt('PLG_PROJECTS_TEAM_CANCEL'); ?></a>
		<?php } ?>
	</span></h3>
<form id="select-form" class="select-form" method="post" enctype="multipart/form-data" action="<?php echo Route::url(Route::url($this->publication->link('edit'))); ?>">
	<fieldset >
		<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
		<input type="hidden" name="version" value="<?php echo $this->publication->get('version_number'); ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="ajax" value="<?php echo $this->ajax; ?>" />
		<input type="hidden" name="selecteditems" id="selecteditems" value="" />
		<input type="hidden" name="p" id="p" value="<?php echo $this->props; ?>" />
		<input type="hidden" name="pid" value="<?php echo $this->publication->get('id'); ?>" />
		<input type="hidden" name="vid" value="<?php echo $this->publication->get('version_id'); ?>" />
		<input type="hidden" name="section" value="<?php echo $this->block; ?>" />
		<input type="hidden" name="step" value="<?php echo $this->step; ?>" />
		<input type="hidden" name="active" value="publications" />
		<input type="hidden" name="action" value="apply" />
		<input type="hidden" name="move" value="continue" />
	</fieldset>
	<p class="requirement"><?php echo Lang::txt('PLG_PROJECTS_TEAM_SELECTOR_SELECT_FROM_TEAM'); ?></p>
	<div id="content-selector" class="content-selector">
		<?php
			// Show files
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'	=>'projects',
					'element'	=>'team',
					'name'		=>'selector',
					'layout'	=>'selector'
				)
			);
			$view->option 		= $this->option;
			$view->model 		= $this->model;
			$view->selected		= $selected;
			$view->publication  = $this->publication;
			$view->team			= $this->team;
			echo $view->loadTemplate();
		?>
	</div>
	</form>
	<p class="newauthor-question"><span><?php echo Lang::txt('PLG_PROJECTS_TEAM_SELECTOR_AUTHOR_NOT_PART_OF_TEAM'); ?> <a href="<?php echo $newauthorUrl; ?>" class="add" id="newauthor-question"><?php echo Lang::txt('PLG_PROJECTS_TEAM_SELECTOR_ADD_AUTHOR'); ?></a></span></p>
</div>
</div>