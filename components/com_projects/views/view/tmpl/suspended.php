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

// Do some text cleanup
$this->project->title = ProjectsHtml::cleanText($this->project->title);
?>
<div id="project-wrap">
<div class="main section">
	<form method="post" action="<?php echo JRoute::_('index.php?option=' . $this->option . a . 'alias='.$this->project->alias); ?>">
		<fieldset >
			<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
			<input type="hidden" name="task" value="reinstate" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<?php echo ProjectsHtml::writeProjectHeader($this, '', 1); ?>	
		<p class="warning"><?php echo $this->suspended == 2 ? JText::_('COM_PROJECTS_CANCEL_SUSPENDED_PROJECT') : JText::_('COM_PROJECTS_CANCEL_SUSPENDED_PROJECT_ADMIN'); ?> <?php if($this->project->role != 1 && $this->suspended == 2) { ?><?php echo JText::_('COM_PROJECTS_CANCEL_SUSPENDED_PROJECT_NO_MANAGER'); ?><?php } ?></p>
		<?php if($this->project->role == 1 && $this->suspended == 2) { ?>	
			<h4><?php echo JText::_('COM_PROJECTS_CANCEL_WANT_TO_REINSTATE'); ?></h4>
			<p>
				<span><input type="submit" class="confirm" value="<?php echo JText::_('COM_PROJECTS_CANCEL_YES_REINSTATE'); ?>" /></span>
			</p>
			<p><?php echo ucfirst(JText::_('COM_PROJECTS_CANCEL_PERMANENTLY')); ?>, <?php echo JText::_('COM_PROJECTS_CANCEL_YOU_CAN_ALSO'); ?> <a href="<?php echo JRoute::_('index.php?option=com_feedback'.a.'task=report_problems'); ?>"><?php echo JText::_('COM_PROJECTS_CANCEL_CONTACT_ADMIN'); ?></a> </p>
		<?php } ?>
		</fieldset>
	</form>
 </div><!-- / .main section -->
</div>