<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_HZEXEC_') or die();

// Push the module CSS to the template
$this->css();
?>
<div<?php echo ($this->moduleclass) ? ' class="' . $this->moduleclass . '"' : ''; ?>>
	<ul class="module-nav">
		<li><a class="icon-browse" href="<?php echo Route::url('index.php?option=com_support&task=tickets'); ?>"><?php echo Lang::txt('MOD_MYTICKETS_ALL_TICKETS'); ?></a></li>
		<li><a class="icon-plus" href="<?php echo Route::url('index.php?option=com_support&task=new'); ?>"><?php echo Lang::txt('MOD_MYTICKETS_NEW_TICKET'); ?></a></li>
	</ul>

	<h4>
		<?php echo Lang::txt('MOD_MYTICKETS_SUBMITTED'); ?>
	</h4>
	<?php if (count($this->rows1) <= 0) { ?>
		<p><em><?php echo Lang::txt('MOD_MYTICKETS_NO_TICKETS'); ?></em></p>
	<?php } else { ?>
		<ul class="expandedlist">
		<?php
		foreach ($this->rows1 as $row)
		{
			?>
			<li class="support-ticket <?php echo $this->escape($row->severity); ?>">
				<a href="<?php echo Route::url('index.php?option=com_support&task=ticket&id=' . $row->id); ?>" class="tooltips" title="#<?php echo $row->id . ' :: ' . $this->escape($this->escape(stripslashes($row->summary))); ?>">#<?php echo $row->id . ': ' . \Hubzero\Utility\String::truncate($this->escape(stripslashes($row->summary)), 35); ?></a>
				<span><span><?php echo Date::of($row->created)->relative(); ?></span>, <span><?php echo Lang::txt('MOD_MYTICKETS_COMMENTS', $row->comments); ?></span></span>
			</li>
			<?php
		}
		?>
		</ul>
	<?php } ?>

	<h4>
		<?php echo Lang::txt('MOD_MYTICKETS_ASSIGNED'); ?>
	</h4>
	<?php if (count($this->rows2) <= 0) { ?>
		<p><em><?php echo Lang::txt('MOD_MYTICKETS_NO_TICKETS'); ?></em></p>
	<?php } else { ?>
		<ul class="expandedlist">
		<?php
		foreach ($this->rows2 as $row)
		{
			?>
			<li class="support-ticket <?php echo $this->escape($row->severity); ?>">
				<a href="<?php echo Route::url('index.php?option=com_support&task=ticket&id=' . $row->id); ?>" class="tooltips" title="#<?php echo $row->id . ' :: ' . $this->escape($this->escape(stripslashes($row->summary))); ?>">#<?php echo $row->id . ': ' . \Hubzero\Utility\String::truncate($this->escape(stripslashes($row->summary)), 35); ?></a>
				<span><span><?php echo Date::of($row->created)->relative(); ?></span>, <span><?php echo Lang::txt('MOD_MYTICKETS_COMMENTS', $row->comments); ?></span></span>
			</li>
			<?php
		}
		?>
		</ul>
	<?php } ?>

	<h4>
		<?php echo Lang::txt('MOD_MYTICKETS_CONTRIBUTIONS'); ?>
	</h4>
	<?php if (count($this->rows3) <= 0) { ?>
		<p><em><?php echo Lang::txt('MOD_MYTICKETS_NO_TICKETS'); ?></em></p>
	<?php } else { ?>
		<ul class="expandedlist">
		<?php
		foreach ($this->rows3 as $row)
		{
			?>
			<li class="support-ticket <?php echo $this->escape($row->severity); ?>">
				<a href="<?php echo Route::url('index.php?option=com_support&task=ticket&id=' . $row->id); ?>" class="tooltips" title="#<?php echo $row->id . ' :: ' . $this->escape($this->escape(stripslashes($row->summary))); ?>">#<?php echo $row->id . ': ' . \Hubzero\Utility\String::truncate($this->escape(stripslashes($row->summary)), 35); ?></a>
				<span><span><?php echo Date::of($row->created)->relative(); ?></span>, <span><?php echo Lang::txt('MOD_MYTICKETS_COMMENTS', $row->comments); ?></span></span>
			</li>
			<?php
		}
		?>
		</ul>
	<?php } ?>
</div>