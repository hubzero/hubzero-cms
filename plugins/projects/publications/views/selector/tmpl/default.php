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

$title = JText::_('PLG_PROJECTS_PUBLICATIONS_SELECTOR_' . strtoupper($this->block));
$req   = JText::_('PLG_PROJECTS_PUBLICATIONS_SELECTOR_REQ_' . strtoupper($this->block));

$block   = $this->block;
$step  	 = $this->step;
$elId 	 = $this->element;

// Get requirements
$blocks   = $this->publication->_curationModel->_progress->blocks;
$manifest = $blocks->$step->manifest;

$selections = NULL;
$selected 	= NULL;

// Get selections
if ($block == 'license')
{
	$objL 			= new PublicationLicense( $this->database);
	$selected 		= $objL->getPubLicense( $this->publication->version_id );
	$selections 	= $objL->getBlockLicenses( $manifest, $selected );

	if (!$selections)
	{
		$selections = $objL->getDefaultLicense();
	}
}

?>
<script src="/plugins/projects/publications/js/selector.js"></script>
<div id="abox-content">
<h3><?php echo $title; ?> 	<span class="abox-controls">
				<a class="btn btn-success active" id="b-filesave"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_SELECTOR_SAVE_SELECTION'); ?></a>
				<?php if ($this->ajax) { ?>
				<a class="btn btn-cancel" id="cancel-action"><?php echo JText::_('PLG_PROJECTS_PUBLICATIONS_CANCEL'); ?></a>
				<?php } ?>
			</span></h3>
<form id="select-form" class="select-form" method="post" enctype="multipart/form-data" action="<?php echo $url; ?>">
	<fieldset >
		<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
		<input type="hidden" name="version" value="<?php echo $this->publication->version_number; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="ajax" value="<?php echo $this->ajax; ?>" />
		<input type="hidden" id="p" name="p" value="<?php echo $this->props; ?>" />
		<input type="hidden" name="pid" value="<?php echo $this->publication->id; ?>" />
		<input type="hidden" name="vid" value="<?php echo $this->publication->version_id; ?>" />
		<input type="hidden" name="section" value="<?php echo $block; ?>" />
		<input type="hidden" name="step" value="<?php echo $step; ?>" />
		<input type="hidden" name="element" value="<?php echo $elId; ?>" />
		<input type="hidden" name="el" value="<?php echo $elId; ?>" />
		<input type="hidden" id="selecteditems" name="selecteditems" value="" />
		<input type="hidden" name="active" value="publications" />
		<input type="hidden" name="action" value="apply" />
		<input type="hidden" name="move" value="continue" />
		<?php if ($this->project->provisioned == 1) { ?>
			<input type="hidden" name="task" value="submit" />
			<input type="hidden" name="ajax" value="0" />
		<?php }  ?>
	</fieldset>

	<?php if (!$selections)
		{
			echo '<p class="error">' . JText::_('PLG_PROJECTS_PUBLICATIONS_SELECTOR_ERROR_NO_SELECTIONS') . '</p>';
		}
		else
		{
	?>
	<p class="requirement" id="req"><?php echo $req; ?></p>
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
			$view->pubconfig	= $this->pubconfig;
			$view->url			= $url;
			echo $view->loadTemplate();
		?>
	</div>
	<?php } ?>
	</form>
</div>