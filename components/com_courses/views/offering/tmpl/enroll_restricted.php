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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//get objects
$config 	=& JFactory::getConfig();
$database 	=& JFactory::getDBO();

$base = 'index.php?option=' . $this->option . '&controller=course&gid=' . $this->course->get('alias');
?>
	<div id="content-header">
		<h2>
			<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
		</h2>
	</div>

	<div id="content-header-extra">
		<ul>
			<li>
				<a class="browse btn" href="<?php echo JRoute::_($base); ?>">
					<?php echo JText::_('Course Overview'); ?>
				</a>
			</li>
		</ul>
	</div>

	<div class="main section">
		<?php
			foreach ($this->notifications as $notification) {
				echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
			}
		?>

		<form action="<?php echo JRoute::_($base . '&task=enroll'); ?>" method="post" id="hubForm">
			<div class="explaination">
				<h3>Code not working?</h3>
				<p>It may be possible that the code has already been redeemed.</p>

				<h3>What if something?</h3>
				<p>Then something else. Duh.</p>
			</div>
			<fieldset>
				<legend><?php echo JText::_('Redeem Coupon Code'); ?></legend>
				
				<p>This course has restricted enrollment and requires a coupon code.</p>
				
				<label for="field-code">
					<?php echo JText::_('Coupon Code'); ?> <span class="required"><?php echo JText::_('COM_COURSES_REQUIRED'); ?></span>
					<input type="text" name="code" id="field-code" size="35" value="" />
				</label>
			</fieldset>
			<div class="clear"></div>
	
			<input type="hidden" name="offering" value="<?php echo $this->course->offering()->get('alias'); ?>" />
			<input type="hidden" name="gid" value="<?php echo $this->course->get('alias'); ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="enroll" />

			<p class="submit">
				<input type="submit" value="<?php echo JText::_('Redeem'); ?>" />
			</p>
		</form>
	</div><!-- /.innerwrap -->