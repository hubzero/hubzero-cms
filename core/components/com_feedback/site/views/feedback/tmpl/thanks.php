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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="main-page btn" href="<?php echo Route::url('index.php?option=' . $this->option); ?>">
				<?php echo Lang::txt('COM_FEEDBACK_MAIN'); ?>
			</a>
		</p>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<section class="main section">
	<p class="passed"><?php echo Lang::txt('COM_FEEDBACK_STORY_THANKS'); ?></p>

	<div class="quote">
		<?php if (count($this->addedPictures)) { ?>
			<?php foreach ($this->addedPictures as $img) { ?>
				<img src="<?php echo $this->path . '/' . $img; ?>" alt="" />
			<?php } ?>
		<?php } ?>

		<blockquote cite="<?php echo $this->escape($this->row->get('fullname')); ?>">
			<?php echo $this->escape(stripslashes($this->row->get('quote'))); ?>
		</blockquote>

		<p class="cite">
			<?php
			if ($this->row->get('user_id'))
			{
				echo '<img src="' . $this->row->user->picture() . '" alt="' . $this->escape($this->row->get('fullname')) . '" width="30" height="30" />';
			}
			?>
			<cite><?php echo $this->escape($this->row->get('fullname')); ?></cite><br />
			<?php echo $this->escape($this->row->get('org')); ?>
		</p>
	</div>
</section><!-- / .main section -->
