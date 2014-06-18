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
defined('_JEXEC') or die('Restricted access');

if ($this->getError()) { ?>
	<p class="error"><?php echo JText::_('MOD_FEATUREDQUESTION_MISSING_CLASS'); ?></p>
<?php } else {
	if ($this->row) {
		$name = JText::_('MOD_FEATUREDQUESTION_ANONYMOUS');
		if ($this->row->anonymous == 0)
		{
			$juser = JUser::getInstance($this->row->created_by);
			if (is_object($juser))
			{
				$name = $juser->get('name');
			}
		}

		$when = JHTML::_('date.relative', $this->row->created);
?>
	<div class="<?php echo $this->cls; ?>">
		<h3><?php echo JText::_('MOD_FEATUREDQUESTION'); ?></h3>
	<?php if (is_file(JPATH_ROOT . $this->thumb)) { ?>
		<p class="featured-img">
			<a href="<?php echo JRoute::_('index.php?option=com_answers&task=question&id=' . $this->row->id); ?>">
				<img width="50" height="50" src="<?php echo $this->thumb; ?>" alt="" />
			</a>
		</p>
	<?php } ?>
		<p>
			<a href="<?php echo JRoute::_('index.php?option=com_answers&task=question&id=' . $this->row->id); ?>">
				<?php echo $this->escape(strip_tags($this->row->subject)); ?>
			</a>
		<?php if ($this->row->question) { ?>
			: <?php echo \Hubzero\Utility\String::truncate($this->escape(strip_tags($this->row->question)), $this->txt_length); ?>
		<?php } ?>
			<br />
			<span><?php echo JText::sprintf('MOD_FEATUREDQUESTION_ASKED_BY', $name); ?></span> -
			<span><?php echo JText::sprintf('MOD_FEATUREDQUESTION_AGO', $when); ?></span> -
			<span><?php echo ($this->row->rcount == 1) ? JText::sprintf('MOD_FEATUREDQUESTION_RESPONSE', $this->row->rcount) : JText::sprintf('MOD_FEATUREDQUESTION_RESPONSES', $this->row->rcount); ?></span>
		</p>
	</div>
<?php
	}
}
?>