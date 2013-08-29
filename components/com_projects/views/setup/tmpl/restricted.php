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
$html  = '';

// Do some text cleanup
$this->project->title = ProjectsHtml::cleanText($this->project->title);
$this->project->about = ProjectsHtml::cleanText($this->project->about);

$title = $this->project->title ? JText::_('COM_PROJECTS_NEW_PROJECT').': '.$this->project->title : $this->title;
?>
<div id="content-header">
	<h2><?php echo $title; ?> <?php if($this->gid && is_object($this->group)) { ?> <?php echo JText::_('COM_PROJECTS_FOR').' '.ucfirst(JText::_('COM_PROJECTS_GROUP')); ?> <a href="<?php echo JRoute::_('index.php?option=com_groups'.a.'gid='.$this->group->get('cn')); ?>"><?php echo Hubzero_View_Helper_Html::shortenText($this->group->get('description'), 50, 0); ?></a><?php } ?></h2>
</div><!-- / #content-header -->
<div class="main section" id="setup">
<div class="clear"></div>
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
	<div class="clear"></div>
	<form id="hubForm" method="post" action="index.php">
		<div class="explaination">
			<h4><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_RESTRICTED_UPFRONT_WHY'); ?></h4>
			<p><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_RESTRICTED_UPFRONT_BECAUSE'); ?></p>
		</div>
		<fieldset class="wider">
			<input type="hidden"  name="task" value="setup" />
			<input type="hidden"  name="step" value="1" />
			<input type="hidden"  name="save_stage" value="0" />
			<input type="hidden" id="option" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" id="pid" name="id" value="<?php echo $this->project->id; ?>" />
			<input type="hidden" id="gid" name="gid" value="<?php echo $this->gid; ?>" />
			<input type="hidden"  name="proceed" value="1" />
			<h2><?php echo JText::_('COM_PROJECTS_SETUP_BEFORE_WE_START'); ?></h2>
			<h4 class="setup-h"><?php echo JText::_('COM_PROJECTS_SETUP_TERMS_QUESTION_PHI'); ?></span></h4>
			<label class="terms-label dark">
				<input class="option restricted-answer" name="restricted" id="f-restricted-no" type="radio" value="no" checked="checked" />
				<?php echo JText::_('COM_PROJECTS_SETUP_TERMS_QUESTION_PHI_NO'); ?>
			</label>
			<label class="terms-label dark">
				<input class="option restricted-answer" name="restricted" id="f-restricted-yes" type="radio" value="yes"/>
				<?php echo JText::_('COM_PROJECTS_SETUP_TERMS_QUESTION_PHI_YES_NOT_SURE'); ?>
			</label>
			<div id="f-restricted-explain" class="cautionaction">
				<?php echo JText::_('COM_PROJECTS_SETUP_TERMS_RESTRICTED_EXPLAIN'); ?>
			</div>
			<p class="submitarea"><input type="submit" value="<?php echo JText::_('COM_PROJECTS_CONTINUE'); ?>" class="btn" id="btn-preform" /></p>
		</fieldset>
	</form>
</div>
<div class="clear"></div>
