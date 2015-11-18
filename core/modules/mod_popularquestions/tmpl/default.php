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
?>
<div<?php echo ($this->cssId) ? ' id="' . $this->cssId . '"' : ''; echo ($this->cssClass) ? ' class="' . $this->cssClass . '"' : ''; ?>>
	<?php if (count($this->rows) > 0) { ?>
		<ul class="questions">
		<?php
		foreach ($this->rows as $row)
		{
			$name = Lang::txt('MOD_POPULARQUESTIONS_ANONYMOUS');
			if (!$row->get('anonymous'))
			{
				$name = $row->creator()->get('name');
			}
			$rcount = $row->responses()->where('state', '<', 2)->count();
			?>
			<li>
				<?php if ($this->style == 'compact') { ?>
					<a href="<?php echo Route::url($row->link()); ?>"><?php echo $this->escape(strip_tags($row->subject)); ?></a>
				<?php } else { ?>
					<h4><a href="<?php echo Route::url($row->link()); ?>"><?php echo $this->escape(strip_tags($row->subject)); ?></a></h4>
					<p class="entry-details">
						<?php echo Lang::txt('MOD_POPULARQUESTIONS_ASKED_BY', $this->escape($name)); ?> @
						<span class="entry-time"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('time'); ?></time></span> on
						<span class="entry-date"><time datetime="<?php echo $row->created(); ?>"><?php echo $row->created('date'); ?></time></span>
						<span class="entry-details-divider">&bull;</span>
						<span class="entry-comments">
							<a href="<?php echo Route::url($row->link() . '#answers'); ?>" title="<?php echo Lang::txt('MOD_RECENTQUESTIONS_RESPONSES', $rcount); ?>">
								<?php echo $rcount; ?>
							</a>
						</span>
					</p>
					<p class="entry-tags"><?php echo Lang::txt('MOD_POPULARQUESTIONS_TAGS'); ?>:</p>
					<?php echo $row->tags('cloud'); ?>
				<?php } ?>
			</li>
			<?php
		}
		?>
		</ul>
	<?php } else { ?>
		<p><?php echo Lang::txt('MOD_POPULARQUESTIONS_NO_RESULTS'); ?></p>
	<?php } ?>
</div>