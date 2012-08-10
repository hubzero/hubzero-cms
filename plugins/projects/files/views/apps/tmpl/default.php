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

// Use alias or id in urls?
$use_alias = $this->config->get('use_alias', 0);
$goto  = $use_alias ? 'alias='.$this->project->alias : 'id='.$this->project->id;
?>
<div id="preview-window"></div>
	<h3 class="apps"><?php echo JText::_('COM_PROJECTS_PANEL_APPS'); ?></h3>
	<ul id="app-tabs">
		<li class="active"><?php echo JText::_('COM_PROJECTS_APPS_TAB_DEVELOPMENT'); ?></li>
		<li><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active='.$this->case).'?action=browse'; ?>"><?php echo JText::_('COM_PROJECTS_APPS_TAB_BROWSE'); ?></a></li>
	</ul>
	<div id="app-dev">
		<div class="list-editing files"><p><span class="prominent"> <?php echo count($this->apps); ?></span> <?php echo JText::_('COM_PROJECTS_COUNT_APPS'); ?></p></div>
		<div class="three columns first second">
			<?php if(count($this->apps) > 0) { ?>
			<?php } else { ?>
				<p class="noresults"><?php echo JText::_('COM_PROJECTS_APPS_NONE_STARTED'); ?></p>
			<?php } ?>
			<?php
				// New tool registration form
				$view = new Hubzero_Plugin_View(
					array(
						'folder'=>'projects',
						'element'=>'files',
						'name'=>'apps',
						'layout'=>'register'
					)
				);
				$view->option = $this->option;
				$view->project = $this->project;
				$view->active = 'apps';
				if ($this->getError()) {
					$view->setError( $this->getError() );
				}
				$view->display();			
			?>
			<div class="clear"></div>
		</div><!-- / .two columns first -->
		<div class="three columns third">
			<div>
				<h4><?php echo JText::_('COM_PROJECTS_APPS_ABOUT_DEVELOPMENT'); ?></h4>
				<p><span class="num">1</span> <?php echo JText::_('COM_PROJECTS_APPS_ABOUT_DEVELOPMENT_FIRST_NEED').' <span class="prominent">'.JText::_('COM_PROJECTS_APPS_ABOUT_DEVELOPMENT_FIRST_REGISTER').'</span> '.JText::_('COM_PROJECTS_APPS_ABOUT_DEVELOPMENT_FIRST_USING'); ?></p>
				<p><span class="num">2</span> <?php echo JText::_('COM_PROJECTS_APPS_ABOUT_DEVELOPMENT_SECOND'); ?> <?php echo JText::_('COM_PROJECTS_APPS_GET'); ?> <a href=""><?php echo JText::_('COM_PROJECTS_APPS_UPLOAD_INSTRUCTIONS'); ?></a>. <?php echo JText::_('COM_PROJECTS_APPS_LEARN_ABOUT'); ?> <a href=""><?php echo JText::_('COM_PROJECTS_APPS_GIT'); ?></a>.</p>
				<p><span class="num">3</span> <?php echo JText::_('COM_PROJECTS_APPS_ABOUT_DEVELOPMENT_THIRD'); ?></p>
			</div>
		</div><!-- / .two columns second-->
		<div class="clear"></div>
	</div>