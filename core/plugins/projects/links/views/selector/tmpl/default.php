<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
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

// Get block properties
$complete = $this->publication->curation('blocks', $this->step, 'complete');
$manifest = $this->publication->curation('blocks', $this->step, 'manifest');

$elementId = $this->element;
if ($manifest->elements)
{
	$element  		= $manifest->elements->$elementId;
	$typeParams   	= $element->params->typeParams;
}
else
{
	$typeParams = NULL;
}

$label  	= isset($typeParams->addLabel) ? $typeParams->addLabel : Lang::txt('PLG_PROJECTS_LINKS_SELECTOR_TYPE_URL');
$action 	= isset($typeParams->typeAction) ? $typeParams->typeAction : 'parseurl';
$btnLabel 	= Lang::txt('PLG_PROJECTS_LINKS_SELECTOR_SAVE_SELECTION');
$placeHolder= 'http://';

$title = $this->block == 'citations'
	? Lang::txt('PLG_PROJECTS_LINKS_SELECTOR_DOI')
	: Lang::txt('PLG_PROJECTS_LINKS_SELECTOR');

if ($this->block == 'citations')
{
	$label  	= Lang::txt('PLG_PROJECTS_LINKS_SELECTOR_TYPE_DOI');
	$action 	= 'parsedoi';
	$btnLabel 	= Lang::txt('PLG_PROJECTS_LINKS_SELECTOR_SAVE_DOI');
	$placeHolder= 'doi:';
}

$newCiteUrl = Route::url( $this->publication->link('editversionid') . '&active=links&action=newcite&p=' . $this->props);

?>
<div id="abox-content-wrap">
	<div id="abox-content" class="url-select">
	<script src="<?php echo rtrim(Request::base(true), '/'); ?>/core/plugins/projects/links/assets/js/selector.js"></script>
		<h3><?php echo $title; ?> 	<span class="abox-controls">
				<a class="btn btn-success active" id="b-save"><?php echo $btnLabel; ?></a>
				<?php if ($this->ajax) { ?>
				<a class="btn btn-cancel" id="cancel-action"><?php echo Lang::txt('PLG_PROJECTS_LINKS_CANCEL'); ?></a>
				<?php } ?>
			</span>
		</h3>
		<form id="select-form" class="select-form" method="post" enctype="multipart/form-data" action="<?php echo $this->publication->link('editversionid'); ?>">
			<fieldset >
				<input type="hidden" name="id" id="projectid" value="<?php echo $this->model->get('id'); ?>" />
				<input type="hidden" name="version" value="<?php echo $this->publication->get('version_number'); ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="ajax" value="<?php echo $this->ajax; ?>" />
				<input type="hidden" name="p" id="p" value="<?php echo $this->props; ?>" />
				<input type="hidden" name="pid" value="<?php echo $this->publication->get('id'); ?>" />
				<input type="hidden" name="vid" value="<?php echo $this->publication->get('version_id'); ?>" />
				<input type="hidden" name="section" id="section" value="<?php echo $this->block; ?>" />
				<input type="hidden" name="step" value="<?php echo $this->step; ?>" />
				<input type="hidden" name="element" value="<?php echo $this->element; ?>" />
				<input type="hidden" name="active" value="publications" />
				<input type="hidden" name="action" value="apply" />
				<input type="hidden" name="move" value="continue" />
				<input type="hidden" name="parseaction" id="parseaction" value="<?php echo $action; ?>" />
				<input type="hidden" name="parseurl" id="parseurl" value="<?php echo Route::url($this->publication->link('editbase')); ?>" />
				<?php if ($this->model->isProvisioned()) { ?>
					<input type="hidden" name="task" value="submit" />
					<input type="hidden" name="ajax" value="0" />
				<?php }  ?>
			</fieldset>
				<div id="import-link">
					<label>
						<?php echo $label . ':'; ?>
					<input type="text" name="<?php echo $this->block == 'citations' ? 'citation-doi' : 'url[]'; ?>" size="40" id="parse-url" placeholder="<?php echo $placeHolder; ?>" value="" />
					<input type="hidden" name="title[]" id="parse-title" value="" />
					<input type="hidden" name="desc[]" id="parse-description" value="" />
					</label>
					<div id="preview-wrap"></div>
				</div>
		</form>
		<?php if ($this->block == 'citations') {
			$config       = Component::params( 'com_citations' );
			$allow_import = $config->get('citation_import', 1);
			if ($allow_import) { ?>
			<p class="and_or centeralign">OR</p>
			<p class="centeralign"><a href="<?php echo $newCiteUrl; ?>" class="btn" id="newcite-question"><?php echo Lang::txt('Enter manually'); ?></a></p>
			<?php }
		} ?>
	</div>
</div>