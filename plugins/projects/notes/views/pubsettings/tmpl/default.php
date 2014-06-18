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

$juri = JURI::getInstance();
?>

<div id="abox-content">
<h3><?php echo JText::_('COM_PROJECTS_NOTES_SHARING'); ?></h3>
<?php
// Display error or success message
if ($this->getError()) {
	echo ('<p class="witherror">'.$this->getError().'</p>');
}
?>
<?php
if (!$this->getError()) {
?>
<form id="hubForm-ajax" method="post" action="<?php echo $this->url; ?>">
	<fieldset >
		<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
		<input type="hidden" name="active" value="notes" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="p" value="<?php echo $this->page->id; ?>" />

		<h4><?php echo JText::_('COM_PROJECTS_NOTES_PUB_LINK_TO_NOTE') . ' &ldquo;' . $this->page->title . '&rdquo;:'; ?></h4>
		<p class="publink"><?php echo trim($juri->base(), DS) . JRoute::_('index.php?option=' . $this->option . a . 'action=get') . '?s=' . $this->pubStamp; ?></p>
		<p class="about"><?php echo JText::_('COM_PROJECTS_NOTES_PUB_LINK_ABOUT'); ?></p>

		<?php if ($this->project->private == 0) { ?>
		<h4><?php echo JText::_('COM_PROJECTS_NOTES_PUB_LINK_LIST_SHOW_ON_PUBLIC'); ?></h4>
		<label>
		<input type="radio" name="action" value="<?php echo 'publist'; ?>" <?php if ($this->listed) { echo 'checked="checked"'; } ?> /><?php echo JText::_('COM_PROJECTS_NOTES_PUB_LIST'); ?>
		</label>
		<label class="block">
		<input type="radio" name="action" value="<?php echo 'unlist'; ?>" <?php if (!$this->listed) { echo 'checked="checked"'; } ?>  /><?php echo JText::_('COM_PROJECTS_NOTES_PUB_UNLIST'); ?>
		</label>
		<p class="submitarea">
			<input type="submit" value="<?php echo JText::_('COM_PROJECTS_SAVE_MY_CHOICE'); ?>" />
			<?php if ($this->ajax) { ?>
				<input type="reset" id="cancel-action" value="<?php echo JText::_('COM_PROJECTS_CANCEL'); ?>" />
			<?php } else {  ?>
				<span class="btn btncancel">
					<a id="cancel-action" href="<?php echo $this->url . DS . $this->page->pagename; ?>"><?php echo JText::_('COM_PROJECTS_CANCEL'); ?></a>
				</span>
			<?php } ?>
		</p>
		<?php } elseif ($this->ajax) { ?>
			<p class="submitarea">
			<input type="reset" id="cancel-action" value="<?php echo JText::_('COM_PROJECTS_CLOSE_THIS'); ?>" />
			</p>
		<?php } ?>
	</fieldset>
</form>
<?php } ?>
</div>
