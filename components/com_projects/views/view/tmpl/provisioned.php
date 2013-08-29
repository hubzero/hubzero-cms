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

$route = 'index.php?option=com_publications' . a . 'task=submit';
$url = JRoute::_($route . a . 'pid=' . $this->pub->id);
	
?>
<div id="project-wrap">
 <div class="main section">
	<div id="content-header">
		<h2><?php echo $this->title; ?></h2>
	</div>
	<h3 class="prov-header"><a href="<?php echo $route; ?>"><?php echo ucfirst(JText::_('COM_PROJECTS_PUBLICATIONS_MY_SUBMISSIONS')); ?></a> &raquo; <a href="<?php echo $url; ?>"> "<?php echo Hubzero_View_Helper_Html::shortenText($this->pub->title, 65, 0); ?>"</a> &raquo; <?php echo JText::_('COM_PROJECTS_PROVISIONED_PROJECT'); ?></h3>
	<div class="status-msg">
	<?php 
		// Display error or success message
		if ($this->getError()) { 
			echo ('<p class="witherror">' . $this->getError().'</p>');
		}
		else if($this->msg) {
			echo ('<p>' . $this->msg . '</p>');
		} ?>
	</div>
	<div id="activate-intro">	
		<div>
			<div class="two columns first">
				<h3><?php echo JText::_('COM_PROJECTS_ACTIVATE_WHAT_YOU_GET'); ?></h3>
				<ul id="activate-features">
					<li id="feature-files">
						<span class="ima">&nbsp;</span> 
						<span class="desc"><?php echo JText::_('COM_PROJECTS_ACTIVATE_GET_REPOSITORY'); ?></span></li>
					<li id="feature-todo">
						<span class="ima">&nbsp;</span> 
						<span class="desc"><?php echo JText::_('COM_PROJECTS_ACTIVATE_GET_TODO'); ?></span></li>
					<li id="feature-wiki">
						<span class="ima">&nbsp;</span> 
						<span class="desc"><?php echo JText::_('COM_PROJECTS_ACTIVATE_GET_WIKI'); ?></span></li>
					<li id="andmore"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=features'); ?>"><?php echo JText::_('COM_PROJECTS_ACTIVATE_AND_MORE'); ?></a></li>
				</ul>
			</div>
			<div class="two columns second">
			  <div id="activate-body">
				<h3><?php echo JText::_('COM_PROJECTS_ACTIVATE_YOUR_NEW_PROJECT'); ?></h3>
				<form action="<?php echo JRoute::_('index.php?option=com_projects' . a . 'alias=' . $this->project->alias . a. 'task=activate'); ?>" method="post" id="activate-form" enctype="multipart/form-data">	
					<fieldset>	
						<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" id="projectid" />					
						<input type="hidden" name="task" value="activate" />
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="verified" id="verified" value="<?php echo $this->verified; ?>" />
						<input type="hidden" name="pubid" value="<?php echo $this->pub->id; ?>" />
					</fieldset>
					<div id="activate-summary">
						<p><span class="activate-label">Publication:</span> 
							<span class="prominent"><?php echo $this->pub->title; ?></span></p>
						<p><span class="activate-label"><?php echo JText::_('COM_PROJECTS_TEAM'); ?>:</span> <?php echo $this->team; ?></p>
					</div>
					<fieldset>	
						<label><span class="pub-info-pop tooltips" title="<?php echo JText::_('COM_PROJECTS_PROJECT_TITLE').' :: ' . JText::_('COM_PROJECTS_HINTS_TITLE'); ?>">&nbsp;</span>
						<?php echo JText::_('COM_PROJECTS_PROJECT_TITLE'); ?>
						<input name="title" maxlength="250" type="text" value="<?php echo $this->pub->title; ?>" class="long" /></label>					
						<label>
							<span class="pub-info-pop tooltips" title="<?php echo JText::_('COM_PROJECTS_CHOOSE_ALIAS').'::'.JText::_('COM_PROJECTS_HINTS_NAME'); ?>">&nbsp;</span>
							<?php echo JText::_('COM_PROJECTS_ALIAS_NAME'); ?>
						<input name="new-alias" id="new-alias" maxlength="30" type="text" value="<?php echo $this->suggested; ?>" class="long" /></label>
						<div id="verify-alias"></div>
					
						<p class="submitarea">
							<input type="submit" id="b-continue" value="<?php echo JText::_('COM_PROJECTS_ACTIVATE_CREATE_A_PROJECT'); ?>"  />
							<span class="btn btncancel"><a href="<?php echo $url; ?>"><?php echo JText::_('COM_PROJECTS_CANCEL'); ?></a></span>
						</p>
					</fieldset>	
				</form>	
				</div>		
			</div>
		<div class="clear"></div>
		</div>
	</div><!-- / #introduction.section -->		
	<div class="clear"></div>
 </div><!-- / .main section -->
</div>