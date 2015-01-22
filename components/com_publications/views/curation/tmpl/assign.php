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

if (!$this->ajax)
{
	$this->css('curation.css')
		 ->js('curation.js');
}
JPluginHelper::importPlugin( 'hubzero' );
$dispatcher = JDispatcher::getInstance();

?>
<div id="abox-content" class="curation-wrap">
	<h3><?php echo JText::_('COM_PUBLICATIONS_CURATION_ASSIGN_VIEW'); ?></h3>
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . a . 'controller=curation'); ?>" method="post" id="hubForm-ajax" name="curation-form" class="curation-history">
		<fieldset>
			<input type="hidden" name="id" value="<?php echo $this->row->publication_id; ?>" />
			<input type="hidden" name="vid" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="task" value="assign" />
			<input type="hidden" name="confirm" value="1" />
		</fieldset>
		<p class="info"><?php echo JText::_('COM_PUBLICATIONS_CURATION_ASSIGN_INSTRUCT'); ?></p>
		<label><?php echo JText::_('COM_PUBLICATIONS_CURATION_ASSIGN_CHOOSE'); ?>
		<?php
			$ownerProfile  = $this->row->curator ? \Hubzero\User\Profile::getInstance($this->row->curator) : NULL;
			$selected = $ownerProfile ? $ownerProfile->get('name') : '';
			$mc = $dispatcher->trigger( 'onGetSingleEntryWithSelect', array(array('members', 'owner', 'owner', '', $selected,'','owner')) );
			if (count($mc) > 0) {
				echo $mc[0];
			} else { ?>
				<input type="text" name="owner" id="owner" value="" size="35" maxlength="200" />
			<?php } ?>
			<?php if ($selected) { ?>
				<input type="hidden" name="selected" value="<?php echo $this->row->curator; ?>" />
			<?php } ?>
		</label>
		<p class="submitarea">
			<input type="submit" class="btn" value="<?php echo JText::_('COM_PUBLICATIONS_SAVE'); ?>" />
			<?php if ($this->ajax) { ?>
			<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo JText::_('COM_PUBLICATIONS_CANCEL'); ?>" />
			<?php } else { ?>
			<a href="<?php echo JRoute::_('index.php?option=' . $this->option . a . 'controller=curation'); ?>" class="btn btn-cancel"><?php echo JText::_('COM_PUBLICATIONS_CANCEL'); ?></a>
			<?php } ?>
		</p>
	</form>
</div>
