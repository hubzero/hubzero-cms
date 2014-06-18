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
$p_url = JRoute::_($route . a . 'active=team');

?>
<div id="abox-content">
<h3><?php echo JText::_('COM_PROJECTS_INVITE_BY_EMAIL_TO_JOIN'); ?></h3>
<form id="hubForm-ajax" method="post" action="<?php echo $p->url; ?>">
	<fieldset >
		<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
		<input type="hidden" name="action" value="sendinvite" />
		<input type="hidden" name="task" value="view" />
		<input type="hidden" name="active" value="team" />
		<input type="hidden" name="ajax" value="<?php echo $this->ajax; ?>" />
		<input type="hidden" name="no_html" value="<?php echo $this->no_html; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="email" value="<?php echo $this->email; ?>" />
		<?php if($this->project->provisioned == 1 ) { ?>
		<input type="hidden" name="task" value="submit" />
		<?php } ?>
		<label for="from"><span class="leftshift"><?php echo ucfirst(JText::_('COM_PROJECTS_FROM')); ?>:</span>
		<input type="text" name="from" value="<?php echo $this->name; ?>" maxlength="100" /></label>
		<label for="to"><span class="leftshift"><?php echo ucfirst(JText::_('COM_PROJECTS_TO')); ?>:
			<span class="hint block mini"><?php echo JText::_('COM_PROJECTS_COMMA_SEPARATED_LIST'); ?></span></span>
			<textarea name="to" cols="4" rows="4" class="emailstring" ></textarea>
		</label>
		<label for="message"><span class="leftshift"><?php echo ucfirst(JText::_('COM_PROJECTS_MESSAGE')); ?>:
			<span class="hint block mini">(<?php echo JText::_('COM_PROJECTS_OPTIONAL'); ?>)</span></span>
			<textarea name="message" cols="4" rows="4" class="msgstring" ></textarea>
		</label>
		<p class="submitarea">
			<input type="submit" value="<?php echo JText::_('COM_PROJECTS_INVITE'); ?>" />
			<input type="reset" id="cancel-action" value="<?php echo JText::_('COM_PROJECTS_CANCEL'); ?>" />
		</p>
	</fieldset>
</form>
</div>