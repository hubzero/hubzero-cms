<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$this->css()
     ->js();
?>

<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last">
				<a class="group btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn')); ?>">
					<?php echo JText::_('COM_GROUPS_ACTION_BACK_TO_GROUP'); ?>
				</a>
			</li>
		</ul>
	</div><!-- / #content-header-extra -->
</header>

<section class="main section">
	<?php foreach ($this->notifications as $notification) : ?>
		<p class="<?php echo $notification['type']; ?>">
			<?php echo $notification['message']; ?>
		</p>
	<?php endforeach; ?>
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=delete'); ?>" method="post" id="hubForm">
		<div class="explaination">
			<p><strong><?php echo JText::_('COM_GROUPS_DELETE_ARE_YOU_SURE_TITLE'); ?></strong></p>
			<p><?php echo JText::_('COM_GROUPS_DELETE_ARE_YOU_SURE_DESC'); ?></p>

			<p><strong><?php echo JText::_('COM_GROUPS_DELETE_ALTERNATIVE_TITLE'); ?></strong></p>
			<p><?php echo JText::_('COM_GROUPS_DELETE_ALTERNATIVE_DESC'); ?></p>
			<p>
				<a class="config btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&cn='.$this->group->get('cn').'&task=edit'); ?>">
					<?php echo JText::_('COM_GROUPS_DELETE_ALTERNATIVE_BTN_TEXT'); ?>
				</a>
			</p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_GROUPS_DELETE_CONFIRM_BOX_HEADING'); ?></legend>

	 		<p class="warning"><?php echo JText::sprintf('COM_GROUPS_DELETE_CONFIRM_BOX_WARNING', $this->group->get('description')) . '<br /><br />' . $this->log; ?></p>

			<label for="msg">
				<?php echo JText::_('COM_GROUPS_DELETE_CONFIRM_BOX_MESSAGE_LABEL'); ?>
				<textarea name="msg" id="msg" rows="12" cols="50"><?php echo htmlentities($this->msg); ?></textarea>
			</label>

			<label for="confirmdel">
				<input type="checkbox" class="option" name="confirmdel" id="confirmdel" value="1" />
				<?php echo JText::_('COM_GROUPS_DELETE_CONFIRM_CONFIRM'); ?>
			</label>
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
		<input type="hidden" name="task" value="dodelete" />

		<p class="submit">
			<input class="btn btn-danger" type="submit" value="<?php echo JText::_('DELETE'); ?>" />
		</p>
	</form>
</section><!-- / .main section -->
