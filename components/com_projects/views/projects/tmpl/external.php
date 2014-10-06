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

$this->css()
     ->js()
	 ->css('external');

$html  = '';

// Do some text cleanup
$this->project->title = ProjectsHtml::cleanText($this->project->title);

$project = new ProjectsModelProject($this->project);
$this->project->about = $project->about('parsed');

// Get project params
$params = new JParameter( $this->project->params );
$theme = $params->get('theme', $this->config->get('theme', 'light'));

// Include extended CSS
$this->css('extended.css');

// Include theme CSS
$this->css('theme' . $theme . '.css');

?>
<div id="project-wrap" class="theme publicview">
	<div id="content-header-extra">
		<ul id="useroptions">
			<li><a class="btn icon-browse" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse'); ?>"><?php echo JText::_('COM_PROJECTS_ALL_PROJECTS'); ?></a></li>
			<li><a class="btn icon-add" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=start'); ?>"><?php echo JText::_('COM_PROJECTS_START_NEW'); ?></a></li>
		</ul>
	</div><!-- / #content-header-extra -->

	<?php if (($this->authorized or $this->project->owner) && !$this->reviewer) { // Public preview for authorized users ?>
		<div id="project-preview">
			<p><?php echo JText::_('COM_PROJECTS_THIS_IS_PROJECT_PREVIEW'); ?> <span><?php echo JText::_('COM_PROJECTS_RETURN_TO'); ?> <a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&alias=' . $this->project->alias); ?>"><?php echo JText::_('COM_PROJECTS_PROJECT_PAGE'); ?></a></span></p>
		</div>
	<?php } else if ($this->reviewer) { ?>
		<div id="project-preview">
			<p><?php echo JText::_('COM_PROJECTS_REVIEWER_PROJECT_PREVIEW'); ?> <span><?php echo JText::_('COM_PROJECTS_RETURN_TO'); ?> <a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=browse') . '?reviewer=' . $this->reviewer; ?>"><?php echo JText::_('COM_PROJECTS_PROJECT_LIST'); ?></a></span></p>
		</div>
	<?php } ?>
	<?php echo ProjectsHtml::drawProjectHeader($this, true); ?>
	
<div class="project-inner-wrap">
	<section class="main section">

			<?php if ($this->project->about) { ?>
			<div class="public-list-header">
				<h3><?php echo JText::_('COM_PROJECTS_ABOUT'); ?></h3>
			</div>
			<div class="public-list-wrap">
				<?php echo $this->project->about; ?>
			</div>
			<?php } ?>

			<?php if ($this->params->get('publications_public', 0))
			{
				// Show team
				$view = new \Hubzero\Plugin\View(
					array(
						'folder'=>'projects',
						'element'=>'publications',
						'name'=>'publist'
					)
				);
				$view->option 	= $this->option;
				$view->project 	= $this->project;
				$view->pubconfig = $this->config;
				echo $view->loadTemplate();
			 } ?>

			<?php if ($this->params->get('files_public', 1))
			{
				// Show files
				$view = new \Hubzero\Plugin\View(
					array(
						'folder'=>'projects',
						'element'=>'files',
						'name'=>'publist'
					)
				);
				$view->option 	= $this->option;
				$view->project 	= $this->project;
				echo $view->loadTemplate();
			 } ?>

			<?php if ($this->params->get('notes_public', 1))
			{
				// Show team
				$view = new \Hubzero\Plugin\View(
					array(
						'folder'=>'projects',
						'element'=>'notes',
						'name'=>'publist'
					)
				);
				$view->option 	= $this->option;
				$view->project 	= $this->project;
				echo $view->loadTemplate();
			 } ?>

			<?php if ($this->params->get('team_public', 0))
			{
				// Show team
				$view = new \Hubzero\Plugin\View(
					array(
						'folder'=>'projects',
						'element'=>'team',
						'name'=>'view',
						'layout'=>'horizontal'
					)
				);
				$view->option 	= $this->option;
				$view->project 	= $this->project;
				$view->goto 	= 'alias='.$this->project->alias;
				$view->team 	= $this->team;
				echo $view->loadTemplate();
			 } ?>
	</section><!-- / .main section -->
	</div>
</div>
