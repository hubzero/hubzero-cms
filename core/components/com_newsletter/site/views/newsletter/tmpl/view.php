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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>

<header id="content-header">
	<h2><?php echo Lang::txt(strtoupper($this->option)); ?></h2>

	<div id="content-header-extra">
		<ul>
			<?php if (isset($this->id) && $this->id != 0) : ?>
				<li>
					<a href="<?php echo Route::url('index.php?option=com_newsletter&id=' . $this->id . '&task=output'); ?>" class="btn icon-file">
						<?php echo Lang::txt('COM_NEWSLETTER_VIEW_SAVEASPDF'); ?>
					</a>
				</li>
			<?php endif; ?>
			<li>
				<a href="<?php echo Route::url('index.php?option=com_newsletter&task=subscribe'); ?>" class="btn icon-feed">
					<?php echo Lang::txt('COM_NEWSLETTER_VIEW_SUBSCRIBE_TO_MAILINGLISTS'); ?>
				</a>
			</li>
		</ul>
	</div>
</header>

<section class="main section">
	<div class="subject newsletter">
		<?php
		if ($this->getError())
		{
			echo '<p class="error">' . $this->getError() . '</p>';
		}
		?>

		<h3><?php echo $this->escape($this->title); ?></h3>

		<?php if ($this->newsletter != '') : ?>
			<div class="container">
				<iframe id="newsletter-iframe" width="100%" height="0" src="<?php echo Route::url('index.php?option=com_newsletter&id=' . $this->id . '&no_html=1'); ?>"></iframe>
			</div>
		<?php else : ?>
			<p class="info">
				<?php echo Lang::txt('COM_NEWSLETTER_VIEW_NO_NEWSLETTERS'); ?>
			</p>
		<?php endif; ?>
	</div><!-- /.subject -->
	<aside class="aside">
		<div class="container">
			<h3><?php echo Lang::txt('COM_NEWSLETTER_VIEW_PAST_NEWSLETTERS'); ?></h3>
			<ul>
				<?php foreach ($this->newsletters as $newsletter) : ?>
					<?php if ($newsletter->published) : ?>
						<li>
							<a class="<?php if ($this->id == $newsletter->id) { echo "active"; } ?>" href="<?php echo Route::url('index.php?option=com_newsletter&id=' . $newsletter->id); ?>">
								<?php echo $newsletter->name; ?>
							</a>
						</li>
					<?php endif; ?>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="container">
			<h3><?php echo Lang::txt('COM_NEWSLETTER_VIEW_NEWSLETTER_HELP'); ?></h3>
			<ul>
				<li>
					<a class="popup" href="<?php echo Route::url('index.php?option=com_help&component=newsletter&page=index'); ?>">
						<?php echo Lang::txt('COM_NEWSLETTER_VIEW_NEWSLETTER_HELP'); ?>
					</a>
				</li>
			</ul>
		</div>
	</aside><!-- /.aside -->
</section><!-- /.main .section -->