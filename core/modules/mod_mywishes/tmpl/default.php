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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

?>
<div<?php echo ($this->params->get('moduleclass')) ? ' class="' . $this->params->get('moduleclass') . '"' : ''; ?>>
	<?php if ($this->params->get('button_show_add', 1)) { ?>
	<ul class="module-nav">
		<li>
			<a class="icon-plus" href="<?php echo Route::url('index.php?option=com_wishlist&task=add&category=general&rid=1'); ?>">
				<?php echo Lang::txt('MOD_MYWISHES_NEW_WISH'); ?>
			</a>
		</li>
	</ul>
	<?php } ?>

	<h4 class="wish-category"><?php echo Lang::txt('MOD_MYWISHES_SUBMITTED'); ?></h4>
	<?php if (count($this->rows1) <= 0) { ?>
		<p><em><?php echo Lang::txt('MOD_MYWISHES_NO_WISHES'); ?></em></p>
	<?php } else { ?>
		<ul class="expandedlist">
			<?php
			foreach ($this->rows1 as $row)
			{
				$when = Date::of($row->proposed)->relative();
				$title = strip_tags($row->about) ? $this->escape(stripslashes($row->subject)) . ' :: ' . \Hubzero\Utility\String::truncate($this->escape(strip_tags($row->about)), 160) : NULL;
			?>
			<li class="wishlist">
				<a href="<?php echo Route::url('index.php?option=com_wishlist&task=wish&id=' . $row->wishlist . '&wishid=' . $row->id); ?>" class="tooltips" title="<?php echo $title; ?>">
					#<?php echo $row->id; ?>: <?php echo \Hubzero\Utility\String::truncate(stripslashes($row->subject), 35); ?>
				</a>
				<span>
					<span class="<?php
					echo ($row->status==3) ? 'rejected' : '';
					if ($row->status==0) {
						echo ($row->accepted==1) ? 'accepted' : 'pending';
					}
					?>">
						<?php
						echo ($row->status==3) ? Lang::txt('MOD_MYWISHES_REJECTED') : '';
						if ($row->status==0) {
							echo ($row->accepted==1) ? Lang::txt('MOD_MYWISHES_ACCEPTED') : Lang::txt('MOD_MYWISHES_PENDING');
						}
						?>
					</span>
					<span>
						<?php echo Lang::txt('MOD_MYWISHES_WISHLIST') . ': ' . $this->escape(stripslashes($row->listtitle)); ?>
					</span>
				</span>
			</li>
			<?php
			}
			?>
		</ul>
	<?php } ?>

	<h4 class="wish-category"><?php echo Lang::txt('MOD_MYWISHES_ASSIGNED'); ?></h4>
	<?php if (count($this->rows2) <= 0) { ?>
		<p><?php echo Lang::txt('MOD_MYWISHES_NO_WISHES'); ?></p>
	<?php } else { ?>
		<ul class="expandedlist">
			<?php
			foreach ($this->rows2 as $row)
			{
				$when = Date::of($row->proposed)->relative();
				$title = strip_tags($row->about) ? $this->escape(stripslashes($row->subject)) . ' :: ' . \Hubzero\Utility\String::truncate($this->escape(strip_tags($row->about)), 160) : NULL;
			?>
			<li class="wishlist">
				<a href="<?php echo Route::url('index.php?option=com_wishlist&task=wish&id=' . $row->wishlist . '&wishid=' . $row->id); ?>" class="tooltips" title="<?php echo $title; ?>">
					#<?php echo $row->id; ?>: <?php echo \Hubzero\Utility\String::truncate(stripslashes($row->subject), 35); ?>
				</a>
				<span>
					<span class="<?php
					echo ($row->status==3) ? 'rejected' : '';
					if ($row->status==0) {
						echo ($row->accepted==1) ? 'accepted' : 'pending';
					}
					?>">
						<?php
						echo ($row->status==3) ? Lang::txt('MOD_MYWISHES_REJECTED') : '';
						if ($row->status==0) {
							echo ($row->accepted==1) ? Lang::txt('MOD_MYWISHES_ACCEPTED') : Lang::txt('MOD_MYWISHES_PENDING');
						}
						?>
					</span>
					<span>
						<?php echo Lang::txt('MOD_MYWISHES_WISHLIST') . ': ' . $this->escape(stripslashes($row->listtitle)); ?>
					</span>
				</span>
			</li>
			<?php
			}
			?>
		</ul>
	<?php } ?>
</div>