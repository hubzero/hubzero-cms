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

$route = $this->project->provisioned == 1
		? 'index.php?option=com_publications&task=submit&pid=' . $this->publication->id
		: 'index.php?option=com_projects&alias=' . $this->project->alias;

// Save Selection URL
$url = $this->project->provisioned ? JRoute::_( $route) : JRoute::_( 'index.php?option=com_projects&alias='
	. $this->project->alias . '&active=publications&pid=' . $this->publication->id);

$i = 0;

$block   = $this->block;
$step  	 = $this->step;

// Get requirements
$blocks   = $this->publication->_curationModel->_progress->blocks;
$params   = $blocks->$step->manifest->params;

$selected = array();

if (count($this->authors) > 0)
{
	foreach ($this->authors as $sel)
	{
		$selected[] = $sel->project_owner_id;
	}
}

$newauthorUrl   = $this->project->provisioned == 1
		? JRoute::_( $route) . '?active=team&action=newauthor'
		: JRoute::_( $route . '&active=team&action=newauthor') .'/?p=' . $this->props . a . 'pid='
		. $this->publication->id . a . 'vid=' . $this->publication->version_id;

?>
<div id="abox-content-wrap">
<div id="abox-content">
<script src="/plugins/projects/team/js/selector.js"></script>
<h3><?php echo JText::_('PLG_PROJECTS_TEAM_SELECTOR'); ?> 	<span class="abox-controls">
		<a class="btn btn-success active" id="b-save"><?php echo JText::_('PLG_PROJECTS_TEAM_SELECTOR_SAVE_SELECTION'); ?></a>
		<?php if ($this->ajax) { ?>
		<a class="btn btn-cancel" id="cancel-action"><?php echo JText::_('PLG_PROJECTS_TEAM_CANCEL'); ?></a>
		<?php } ?>
	</span></h3>
<form id="select-form" class="select-form" method="post" enctype="multipart/form-data" action="<?php echo $url; ?>">
	<fieldset >
		<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
		<input type="hidden" name="version" value="<?php echo $this->publication->version_number; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="ajax" value="<?php echo $this->ajax; ?>" />
		<input type="hidden" name="selecteditems" id="selecteditems" value="" />
		<input type="hidden" name="p" id="p" value="<?php echo $this->props; ?>" />
		<input type="hidden" name="pid" value="<?php echo $this->publication->id; ?>" />
		<input type="hidden" name="vid" value="<?php echo $this->publication->version_id; ?>" />
		<input type="hidden" name="section" value="<?php echo $block; ?>" />
		<input type="hidden" name="step" value="<?php echo $step; ?>" />
		<input type="hidden" name="active" value="publications" />
		<input type="hidden" name="action" value="apply" />
		<input type="hidden" name="move" value="continue" />
	</fieldset>
	<p class="requirement"><?php echo JText::_('PLG_PROJECTS_TEAM_SELECTOR_SELECT_FROM_TEAM'); ?></p>
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
			$view->project 		= $this->project;
			$view->selected		= $selected;
			$view->params 		= $params;
			$view->publication  = $this->publication;
			$view->team			= $this->team;
			echo $view->loadTemplate();
		?>
	</div>
	</form>
	<p class="newauthor-question"><span><?php echo JText::_('PLG_PROJECTS_TEAM_SELECTOR_AUTHOR_NOT_PART_OF_TEAM'); ?> <a href="<?php echo $newauthorUrl; ?>" class="add" id="newauthor-question"><?php echo JText::_('PLG_PROJECTS_TEAM_SELECTOR_ADD_AUTHOR'); ?></a></span></p>
</div>
</div>