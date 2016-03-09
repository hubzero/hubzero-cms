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

// load js
$this->js('feeds');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section">
	<?php if ($this->getErrors()) { ?>
		<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
	<?php } ?>

	<form method="post" id="hubForm" action="<?php echo Route::url('index.php?option=' . $this->option . ' &task=save'); ?>">
		<div class="explaination">
			<p>
				<?php echo Lang::txt('COM_FEEDAGGREGATOR_FEED_INFO_ASIDE'); ?>
			</p>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('COM_FEEDAGGREGATOR_FEED_INFORMATION'); ?></legend>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
			<input type="hidden" name="id" value="<?php echo $this->feed->get('id', 0); ?>">
			<input type="hidden" name="enabled" value="<?php echo $this->feed->get('enabled', 1); ?>">
			<input type="hidden" name="task" value="save" />

			<label for="feedTitle">
				<?php echo Lang::txt('COM_FEEDAGGREGATOR_LABEL_FEEDNAME'); ?> <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
				<input type="text" class="required-field" name="name" id="feedTitle" size="25" value="<?php echo $this->escape($this->feed->get('name')); ?>"/>
			</label>

			<label for="feedURL">
				<?php echo Lang::txt('COM_FEEDAGGREGATOR_LABEL_FEEDURL'); ?> <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
				<input type="text" class="required-field" name="url" id="feedURL" size="50" value="<?php echo $this->escape($this->feed->get('url')); ?>" />
			</label>

			<label for="feedDescription">
				<?php echo Lang::txt('COM_FEEDAGGREGATOR_LABEL_DESCRIPTION'); ?>
				<input type="text" name="description" id="feedDescription" size="50" value="<?php echo $this->escape($this->feed->get('description')); ?>" />
			</label>
		</fieldset>
		<p class="submit">
			<input type="submit" id="submitBtn" class="btn btn-success" name="formsubmitBtn" value="<?php echo Lang::txt('COM_FEEDAGGREGATOR_SUBMIT'); ?>" />
		</p>

		<?php echo Html::input('token'); ?>
	</form>
</section><!-- / .main section -->
