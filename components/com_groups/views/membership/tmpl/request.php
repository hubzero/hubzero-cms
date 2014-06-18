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

?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last">
				<a class="group btn" href="<?php echo JRoute::_('index.php?option='.$this->option); ?>">
					<?php echo JText::_('COM_GROUPS_ACTION_BACK_TO_ALL_GROUPS'); ?>
				</a>
			</li>
		</ul>
	</div><!-- / #content-header-extra -->
</header>

<section class="main section">
	<?php
		foreach($this->notifications as $notification)
		{
			echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
		}
	?>
	<form action="index.php" method="post" id="hubForm">
		<div class="explaination">
			<p class="info"><?php echo JText::_('COM_GROUPS_JOIN_HELP'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_GROUPS_JOIN_SECTION_TITLE'); ?></legend>

			<?php if ($this->group->get('restrict_msg')) { ?>
				<p class="warning"><?php echo JText::_('NOTE') . ': ' . $this->escape(stripslashes($this->group->get('restrict_msg'))); ?></p>
			<?php } ?>

			<label for="reason">
				<?php echo JText::_('COM_GROUPS_JOIN_REASON'); ?>
				<textarea name="reason" id="reason" rows="10" cols="50"></textarea>
			</label>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="membership" />
			<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
			<input type="hidden" name="task" value="dorequest" />
		</fieldset>
		<div class="clear"></div>

		<p class="submit">
			<input class="btn btn-success" type="submit" value="<?php echo JText::_('COM_GROUPS_JOIN_BTN_TEXT'); ?>" />
		</p>
	</form>
</section><!-- / .main section -->
