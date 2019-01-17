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

// no direct access
defined('_HZEXEC_') or die();

Html::addIncludePath(PATH_COMPONENT . '/helpers');
?>
<?php if ($this->params->get('show_page_heading')) : ?>
	<header id="content-header">
		<h2>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h2>
	</header>
<?php endif; ?>

<section class="main section">
	<div class="archive<?php echo $this->pageclass_sfx; ?>">
		<form id="adminForm" action="<?php echo Route::url('index.php?option=com_content'); ?>" method="post">
			<fieldset class="filters">
				<legend class="hidelabeltxt"><?php echo Lang::txt('JGLOBAL_FILTER_LABEL'); ?></legend>

				<div class="filter-search">
					<?php if ($this->params->get('filter_field') != 'hide') : ?>
						<label class="filter-search-lbl" for="filter-search"><?php echo Lang::txt('COM_CONTENT_' . $this->params->get('filter_field') . '_FILTER_LABEL') . '&#160;'; ?></label>
						<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->filter); ?>" class="inputbox" onchange="document.getElementById('adminForm').submit();" />
					<?php endif; ?>

					<?php echo $this->form->monthField; ?>
					<?php echo $this->form->yearField; ?>
					<?php echo $this->form->limitField; ?>
					<button type="submit" class="button"><?php echo Lang::txt('JGLOBAL_FILTER_BUTTON'); ?></button>
				</div>

				<input type="hidden" name="view" value="archive" />
				<input type="hidden" name="option" value="com_content" />
				<input type="hidden" name="limitstart" value="0" />
			</fieldset>

			<?php echo $this->loadTemplate('items'); ?>
		</form>
	</div>
</section>
