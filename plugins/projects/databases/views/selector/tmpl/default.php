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
$blocks   = $this->publication->_curationModel->_progress->blocks;
$elements = $blocks->$step->manifest->elements;
$element = isset($elements->$elementId) ? $elements->$elementId: $elements->$default;
$params  = $element->params;

// Save Selection URL
$url = $this->project->provisioned ? JRoute::_( $route) : JRoute::_( 'index.php?option=com_projects&alias=' . $this->project->alias . '&active=publications&pid=' . $this->publication->id);

// Get attachment type model
$attModel = new PublicationsModelAttachments($this->database);

// Get attached items
$attachments = $this->publication->_attachments;
$attachments = isset($attachments['elements'][$elementId]) ? $attachments['elements'][$elementId] : NULL;
$attachments = $attModel->getElementAttachments($elementId, $attachments, $params->type);

// Get preselected items
$selected = array();
if ($attachments)
{
	foreach ($attachments as $attach)
	{
		$selected[] = $attach->object_name;
	}
}
?>
<div id="abox-content-wrap">
	<div id="abox-content">
	<script src="/plugins/projects/publications/js/selector.js"></script>
		<h3><?php echo JText::_('PLG_PROJECTS_DATABASES_SELECTOR'); ?> 	<span class="abox-controls">
				<a class="btn btn-success active" id="b-filesave"><?php echo JText::_('PLG_PROJECTS_DATABASES_SELECTOR_SAVE_SELECTION'); ?></a>
				<?php if ($this->ajax) { ?>
				<a class="btn btn-cancel" id="cancel-action"><?php echo JText::_('PLG_PROJECTS_DATABASES_CANCEL'); ?></a>
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
				<input type="hidden" id="selecteditems" name="selecteditems" value="" />
			</fieldset>
			<?php if ($this->items) { ?>
			<ul class="pub-selector database-selector" id="pub-selector">
				<?php foreach ($this->items as $item) {
					$liId = 'choice-' . $item->database_name;
					$sel = in_array($item->database_name, $selected) ? true : false;
					?>
					<li class="c-click databases allowed <?php if ($sel) { echo ' selectedfilter'; } ?>" id="<?php echo $liId; ?>">
						<span class="item-info"></span>
						<span class="item-wrap"><?php echo $item->title; ?></span>
						<span class="item-fullinfo">
							<?php echo $item->description; ?>
						</span>
					</li>
				<?php } ?>
			</ul>
			<?php } else { ?>
			<p class="warning"><?php echo JText::_('PLG_PROJECTS_DATABASES_SELECTOR_NONE'); ?> <span class="block">Go to <a href="<?php echo JRoute::_($route . '&active=databases'); ?>">Databases</a> to create a new database</span></p>
		<?php } ?>
		</form>
	</div>
</div>