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
$i = 1;
?>
<div id="abox-content">
<h3><?php echo JText::_('COM_PROJECTS_DELETE_TEAM_MEMBERS'); ?></h3>
<?php
// Display error or success message
if ($this->getError()) {
	echo ('<p class="witherror">'.$this->getError().'</p>');
}
$self = in_array($this->aid, $this->checked) ? 1 : 0;
?>
<?php
if (!$this->getError()) {
?>
<form id="hubForm-ajax" method="post" action="<?php echo JRoute::_('index.php?option=' . $this->option . a . 'alias=' . $this->project->alias); ?>">
	<fieldset >
		<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
		<input type="hidden" name="action" value="removeowner" />
		<input type="hidden" name="ajax" value="1" />
		<input type="hidden" name="no_html" value="1" />
		<?php if($this->setup) { ?>
		<input type="hidden" name="task" value="setup" />
		<input type="hidden" name="step" value="1" />
		<?php } else { ?>
		<input type="hidden" name="task" value="edit" />
		<input type="hidden" name="edit" value="team" />
		<?php } ?>
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<p class="anote"><?php echo JText::_('COM_PROJECTS_DELETE_TEAM_MEMBERS_NOTE'); ?></p>
		<p><?php echo JText::_('COM_PROJECTS_DELETE_TEAM_MEMBERS_CONFIRM'); ?></p>
		<p class="prominent"><?php foreach ($this->selected as $owner) {
			echo trim($owner->fullname) ? $owner->fullname : $owner->invited_email;
			echo ($i < count($this->selected)) ? ', ': '';
			$i++;
			echo '<input type="hidden" name="owner[]" value="'.$owner->id.'" />';
		} ?></p>
		<?php if($self) { ?><p class="warning"><?php echo JText::_('COM_PROJECTS_TEAM_WARNING_SELF_DELETE'); ?></p><?php } ?>
		<p class="submitarea">
			<input type="submit" value="<?php echo JText::_('COM_PROJECTS_DELETE'); ?>" class="btn" />
			<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo JText::_('COM_PROJECTS_CANCEL'); ?>" />
		</p>
	</fieldset>
</form>
<?php } ?>
</div>