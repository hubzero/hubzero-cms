<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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