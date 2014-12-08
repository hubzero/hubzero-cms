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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

// Build url
$route = $this->project->provisioned
	? 'index.php?option=com_publications' . a . 'task=submit'
	: 'index.php?option=com_projects' . a . 'alias=' . $this->project->alias;

$block   	= $this->block;
$step  	 	= $this->step;
$elementId 	= $this->element;

// Get requirements
$blocks   		= $this->publication->_curationModel->_progress->blocks;
$blockParams   	= $blocks->$step->manifest->params;
if ($blocks->$step->manifest->elements)
{
	$element  		= $blocks->$step->manifest->elements->$elementId;
	$typeParams   	= $element->params->typeParams;
}
else
{
	$typeParams = NULL;
}

$label  	= isset($typeParams->addLabel) ? $typeParams->addLabel : JText::_('PLG_PROJECTS_LINKS_SELECTOR_TYPE_URL');
$action 	= isset($typeParams->typeAction) ? $typeParams->typeAction : 'parseurl';
$btnLabel 	= JText::_('PLG_PROJECTS_LINKS_SELECTOR_SAVE_SELECTION');
$placeHolder= 'http://';

$title = $block == 'citations'
	? JText::_('PLG_PROJECTS_LINKS_SELECTOR_DOI')
	: JText::_('PLG_PROJECTS_LINKS_SELECTOR');

if ($block == 'citations')
{
	$label  	= JText::_('PLG_PROJECTS_LINKS_SELECTOR_TYPE_DOI');
	$action 	= 'parsedoi';
	$btnLabel 	= JText::_('PLG_PROJECTS_LINKS_SELECTOR_SAVE_DOI');
	$placeHolder= 'doi:';
}

//$newCiteUrl   = 'citations/add?publication=' . $this->publication->id;
$newCiteUrl   = $this->project->provisioned == 1
		? JRoute::_( $route) . '?active=links&action=newcite'
		: JRoute::_( $route . '&active=links&action=newcite') .'/?p=' . $this->props . a . 'pid='
		. $this->publication->id . a . 'vid=' . $this->publication->version_id;

// Save Selection URL
$url = $this->project->provisioned ? JRoute::_( $route) : JRoute::_( 'index.php?option=com_projects&alias=' . $this->project->alias . '&active=publications&pid=' . $this->publication->id);

?>
<div id="abox-content-wrap">
	<div id="abox-content" class="url-select">
	<script src="/plugins/projects/links/js/selector.js"></script>
		<h3><?php echo $title; ?> 	<span class="abox-controls">
				<a class="btn btn-success active" id="b-save"><?php echo $btnLabel; ?></a>
				<?php if ($this->ajax) { ?>
				<a class="btn btn-cancel" id="cancel-action"><?php echo JText::_('PLG_PROJECTS_LINKS_CANCEL'); ?></a>
				<?php } ?>
			</span>
		</h3>
		<form id="select-form" class="select-form" method="post" enctype="multipart/form-data" action="<?php echo $url; ?>">
			<fieldset >
				<input type="hidden" name="id" id="projectid" value="<?php echo $this->project->id; ?>" />
				<input type="hidden" name="version" value="<?php echo $this->publication->version_number; ?>" />
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="ajax" value="<?php echo $this->ajax; ?>" />
				<input type="hidden" name="p" id="p" value="<?php echo $this->props; ?>" />
				<input type="hidden" name="pid" value="<?php echo $this->publication->id; ?>" />
				<input type="hidden" name="vid" value="<?php echo $this->publication->version_id; ?>" />
				<input type="hidden" name="section" id="section" value="<?php echo $block; ?>" />
				<input type="hidden" name="step" value="<?php echo $step; ?>" />
				<input type="hidden" name="element" value="<?php echo $elementId; ?>" />
				<input type="hidden" name="active" value="publications" />
				<input type="hidden" name="action" value="apply" />
				<input type="hidden" name="move" value="continue" />
				<input type="hidden" name="parseaction" id="parseaction" value="<?php echo $action; ?>" />
				<input type="hidden" name="parseurl" id="parseurl" value="<?php echo JRoute::_( $route); ?>" />
				<?php if ($this->project->provisioned == 1) { ?>
					<input type="hidden" name="task" value="submit" />
					<input type="hidden" name="ajax" value="0" />
				<?php }  ?>
			</fieldset>
				<div id="import-link">
					<label>
						<?php echo $label . ':'; ?>
					<input type="text" name="<?php echo $block == 'citations' ? 'citation-doi' : 'url[]'; ?>" size="40" id="parse-url" placeholder="<?php echo $placeHolder; ?>" value="" />
					<input type="hidden" name="title[]" id="parse-title" value="" />
					<input type="hidden" name="desc[]" id="parse-description" value="" />
					</label>
					<div id="preview-wrap"></div>
				</div>
		</form>
		<?php if ($block == 'citations') {
			$config 	  = JComponentHelper::getParams( 'com_citations' );
			$allow_import = $config->get('citation_import', 1);
			if ($allow_import) { ?>
			<p class="and_or centeralign">OR</p>
			<p class="centeralign"><a href="<?php echo $newCiteUrl; ?>" class="btn" id="newcite-question"><?php echo JText::_('Enter manually'); ?></a></p>
			<?php }
		} ?>
	</div>
</div>