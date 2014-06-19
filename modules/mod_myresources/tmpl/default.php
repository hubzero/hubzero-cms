<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$juser = JFactory::getUser();

if (!$this->no_html) {
?>
	<ul class="module-nav">
		<li>
			<a class="icon-browse" href="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=contributions&area=resources'); ?>">
				<?php echo JText::_('MOD_MYRESOURCES_ALL_PUBLICATIONS'); ?>
			</a>
		</li>
	</ul>
	<form method="get" action="<?php echo JRoute::_('index.php?option=com_members&id=' . $juser->get('id') . '&active=dashboard'); ?>" data-module="<?php echo $this->module->id; ?>" id="myresources-form" enctype="multipart/form-data">
<?php } ?>
		<div id="myresources-content">
		<?php if (!$this->contributions) { ?>
			<p><?php echo JText::_('MOD_MYRESOURCES_NONE_FOUND'); ?></p>
		<?php } else { ?>
			<ul class="expandedlist">
			<?php
				for ($i=0; $i < count($this->contributions); $i++)
				{
					// Determine css class
					switch ($this->contributions[$i]->published)
					{
						case 1:  $class = 'published';  break;  // published
						case 2:  $class = 'draft';      break;  // draft
						case 3:  $class = 'pending';    break;  // pending
						case 0:  $class = 'deleted';    break;  // pending
					}

					$thedate = JHTML::_('date', $this->contributions[$i]->publish_up, JText::_('DATE_FORMAT_HZ1'));
			?>
				<li class="<?php echo $class; ?>">
					<a href="<?php echo JRoute::_('index.php?option=com_resources&id=' . $this->contributions[$i]->id); ?>">
						<?php echo \Hubzero\Utility\String::truncate(stripslashes($this->contributions[$i]->title), 40); ?>
					</a>
					<span class="under">
						<?php echo $thedate . ' &nbsp; ' . $this->escape(stripslashes($this->contributions[$i]->typetitle)); ?>
					</span>
				</li>
			<?php
				}
			?>
			</ul>
		<?php } ?>
		</div>
<?php if (!$this->no_html) { ?>
	</form>
<?php } ?>
