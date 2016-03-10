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

use Components\Projects\Models\Orm\Project;

if (!$this->ajax)
{
	$this->css('selector');
}

// Get attachment type model
$attModel  = new \Components\Publications\Models\Attachments($this->database);
$route     = $this->model->isProvisioned() ? 'index.php?option=com_publications&task=submit&active=files' : $this->model->link('files');
$filterUrl = Route::url($route) . '?action=filter&amp;pid=' . $this->publication->get('id') . '&amp;vid=' . $this->publication->get('version_id') . '&amp;p=' . $this->props . '&amp;ajax=1&amp;no_html=1';
$elId      = $this->element;

// Get requirements
$element = $this->publication->curation('blocks', $this->step, 'elements', $this->element);
$params  = $element->params;
$max 	 = $params->max;
$min 	 = $params->min;
$required= $params->required;
$role 	 = $params->role;
$allowed = $params->typeParams->allowed_ext;
$reqext  = $params->typeParams->required_ext;
$reuse   = isset($params->typeParams->reuse) ? $params->typeParams->reuse : 1;

$minName = \Components\Projects\Helpers\Html::getNumberName($min);
$maxName = \Components\Projects\Helpers\Html::getNumberName($max);

// Spell out requirement
$req = Lang::txt('PLG_PROJECTS_FILES_SELECTOR_CHOOSE') . ' ';
if ($min && $max > $min)
{
	if ($max > 100)
	{
		// Do not say how many
		$req .= '<strong>' . $minName . ' ' . Lang::txt('PLG_PROJECTS_FILES_SELECTOR_OR_MORE') . '</strong>';
	}
	else
	{
		$req .= '<strong>' . $min . '-' . $max . ' ' . Lang::txt('PLG_PROJECTS_FILES_SELECTOR_FILES') . '</strong>';
	}
}
elseif ($min && $min == $max)
{
	$req .= ' <strong>' . $minName . ' ' . Lang::txt('PLG_PROJECTS_FILES_SELECTOR_FILE');
	$req .= $min > 1 ? 's' : '';
	$req .= '</strong>';
}
else
{
	$req .= $max == 1 ? Lang::txt('PLG_PROJECTS_FILES_SELECTOR_COUNT', $max) : Lang::txt('PLG_PROJECTS_FILES_S');
}

if (!empty($allowed))
{
	$req .= ' ' . Lang::txt('PLG_PROJECTS_FILES_SELECTOR_OF_FORMAT');
	$req .= count($allowed) > 1 ? 's - ' : ' - ';
	$x = 1;
	foreach ($allowed as $al)
	{
		$req .= '.' . strtoupper($al);
		$req .= $x == count($allowed) ? '' : ', ';
		$x++;
	}
}
else
{
	$req .= ' ' . Lang::txt('PLG_PROJECTS_FILES_SELECTOR_OF_ANY_TYPE');
}
$req .= ':';

// Get attached items
$attachments = $this->publication->attachments();
$attachments = isset($attachments['elements'][$elId]) ? $attachments['elements'][$elId] : NULL;
$attachments = $attModel->getElementAttachments($elId, $attachments, $params->type);

$used = array();
if (!$reuse && $this->publication->_attachments['elements'])
{
	foreach ($this->publication->_attachments['elements'] as $o => $elms)
	{
		if ($o != $elId)
		{
			foreach ($elms as $elm)
			{
				$used[] = $elm->path;
			}
		}
	}
}

// Get preselected items
$selected = array();
if ($attachments)
{
	foreach ($attachments as $attach)
	{
		$selected[] = $attach->path;
	}
}

if (!empty($this->directory))
{
	// Show files
	$layout = (Request::getInt('cid') && Request::getInt('cid') > 0) ? 'selector-remote' : 'selector';
	$view = new \Hubzero\Plugin\View(
		array(
			'folder'	=>'projects',
			'element'	=>'files',
			'name'		=>'selector',
			'layout'	=>$layout
		)
	);
	$view->option       = $this->option;
	$view->model        = $this->model;
	$view->items        = $this->items;
	$view->requirements = $params;
	$view->publication  = $this->publication;
	$view->selected     = $selected;
	$view->allowed      = $allowed;
	$view->used         = $used;
	$view->noUl         = true;

	echo $view->loadTemplate();

	return;
}

// Get folder array
$subdirOptions = array();
$subdirOptions[] = array('path' => '', 'label' => 'home directory');
if ($this->folders)
{
	foreach ($this->folders as $folder)
	{
		$subdirOptions[] = array('path' => $folder->get('localPath'), 'label' => $folder->get('localPath'));
	}
}

?>
<script src="<?php echo rtrim(Request::base(true), '/'); ?>/core/plugins/projects/files/assets/js/fileselector.js"></script>
<div id="abox-content">
<h3><?php echo Lang::txt('PLG_PROJECTS_FILES_SELECTOR'); ?> 	<span class="abox-controls">
		<a class="btn btn-success active" id="b-filesave"><?php echo Lang::txt('PLG_PROJECTS_FILES_SELECTOR_SAVE_SELECTION'); ?></a>
		<?php if ($this->ajax) { ?>
		<a class="btn btn-cancel" id="cancel-action"><?php echo Lang::txt('PLG_PROJECTS_FILES_CANCEL'); ?></a>
		<?php } ?>
	</span></h3>

<form id="select-form" class="select-form" method="post" enctype="multipart/form-data" action="<?php echo Route::url( $this->publication->link('edit')); ?>">
	<fieldset >
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
		<input type="hidden" name="version" value="<?php echo $this->publication->get('version_number'); ?>" />
		<input type="hidden" name="ajax" value="<?php echo $this->ajax; ?>" />
		<input type="hidden" id="selecteditems" name="selecteditems" value="" />
		<input type="hidden" id="maxitems" name="maxitems" value="<?php echo $max; ?>" />
		<input type="hidden" id="minitems" name="minitems" value="<?php echo $min; ?>" />
		<input type="hidden" id="p" name="p" value="<?php echo $this->props; ?>" />
		<input type="hidden" id="filterUrl" name="filterUrl" value="<?php echo $filterUrl; ?>" />
		<input type="hidden" name="pid" value="<?php echo $this->publication->get('id'); ?>" />
		<input type="hidden" name="vid" value="<?php echo $this->publication->get('version_id'); ?>" />
		<input type="hidden" name="section" value="<?php echo $this->block; ?>" />
		<input type="hidden" name="element" value="<?php echo $elId; ?>" />
		<input type="hidden" name="el" value="<?php echo $elId; ?>" />
		<input type="hidden" name="step" value="<?php echo $this->step; ?>" />
		<input type="hidden" name="active" value="publications" />
		<input type="hidden" name="action" value="apply" />
		<input type="hidden" name="move" value="continue" />
		<?php if ($this->model->isProvisioned()) { ?>
			<input type="hidden" name="task" value="submit" />
			<input type="hidden" name="ajax" value="0" />
		<?php }  ?>
	</fieldset>

	<p class="requirement" id="req"><?php echo $req; ?></p>

	<div id="content-selector" class="content-selector">
		<?php
			if ($this->showCons && empty($this->directory) && !Request::getInt('cid'))
			{
				// Show files
				$view = new \Hubzero\Plugin\View(
					array(
						'folder'  => 'projects',
						'element' => 'files',
						'name'    => 'selector',
						'layout'  => 'connections'
					)
				);
				$view->model       = $this->model;
				$view->connections = Project::oneOrFail($this->model->get('id'))->connections()->thatICanView();

				echo $view->loadTemplate();
			}
			else
			{
				// Show files
				$view = new \Hubzero\Plugin\View(
					array(
						'folder'  => 'projects',
						'element' => 'files',
						'name'    => 'selector',
						'layout'  => 'selector'
					)
				);
				$view->option       = $this->option;
				$view->model        = $this->model;
				$view->items        = $this->items;
				$view->requirements = $params;
				$view->publication  = $this->publication;
				$view->selected     = $selected;
				$view->allowed      = $allowed;
				$view->used         = $used;

				echo $view->loadTemplate();
			}
		?>
	</div>
	</form>
	<form id="upload-form" class="upload-form" method="post" enctype="multipart/form-data" action="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias')); ?>">

	<fieldset >
		<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="ajax" value="<?php echo $this->ajax; ?>" />
		<input type="hidden" name="pid" value="<?php echo $this->publication->id; ?>" />
		<input type="hidden" name="vid" value="<?php echo $this->publication->version_id; ?>" />
		<input type="hidden" name="alias" value="<?php echo $this->model->get('alias'); ?>" />
		<input type="hidden" name="active" value="files" />
		<input type="hidden" name="action" value="save" />
		<input type="hidden" name="json" value="1" />
		<input type="hidden" name="ajax" value="1" />
		<input type="hidden" name="no_html" value="1" />
	</fieldset>
	<div id="status-box"></div>

	<div id="quick-upload" class="quick-uploader">
		<?php if ($this->model->isProvisioned()) { ?>
			<input type="hidden" name="provisioned" id="provisioned" value="1" />
			<input type="hidden" name="task" value="submit" />
		<?php } ?>
		<p><?php echo Lang::txt('PLG_PROJECTS_FILES_SELECTOR_NEED_ADD_FILES'); ?> <?php echo Lang::txt('PLG_PROJECTS_FILES_SELECTOR_QUICK_UPLOAD'); ?>:</p>

		<label>
			<input name="upload[]" type="file" id="uploader" multiple="multiple" />
		</label>

		<?php if (count($subdirOptions) > 1) { ?>
		<label><?php echo Lang::txt('PLG_PROJECTS_FILES_UPLOAD_INTO_SUBDIR'); ?>
			<select name="subdir">
				<?php foreach ($subdirOptions as $sd) { ?>
					<option value="<?php echo $sd['path']; ?>"><?php echo $sd['label']; ?></options>
				<?php } ?>
			</select>
		</label>
		<?php } ?>
		<input type="submit" value="<?php echo Lang::txt('PLG_PROJECTS_FILES_UPLOAD'); ?>" class="upload-file" id="upload-file" />
	</div>

	</form>
</div>
