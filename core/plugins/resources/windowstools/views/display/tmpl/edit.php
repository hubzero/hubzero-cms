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

defined('_HZEXEC_') or die();

$this->css();
?>

<div class="pages-wrap">
	<div class="pages-content">

		<form action="<?php echo Route::url($this->base); ?>" method="post" id="hubForm" class="full" enctype="multipart/form-data">
			<fieldset>
				<legend><?php echo Lang::txt('PLG_RESOURCES_WINDOWSTOOLS_EDIT_PAGE'); ?></legend>

				<label for="fields_content">
					<?php echo Lang::txt('PLG_RESOURCES_WINDOWSTOOLS_FIELD_CONTENT'); ?> <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
					<?php echo $this->editor('fields[content]', $this->escape($this->page->get('content')), 35, 50, 'field_content'); ?>
				</label>

				<p class="submit">
					<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('PLG_RESOURCES_WINDOWSTOOLS_SAVE'); ?>" />
					<a class="btn btn-secondary" href="<?php echo Route::url($this->base); ?>">
						<?php echo Lang::txt('PLG_RESOURCES_WINDOWSTOOLS_CANCEL'); ?>
					</a>
				</p>
			</fieldset>

			<input type="hidden" name="fields[plugin]" value="<?php echo $this->name; ?>" />
			<input type="hidden" name="fields[title]" value="<?php echo $this->page->get('title'); ?>" />
			<input type="hidden" name="fields[alias]" value="<?php echo $this->page->get('alias'); ?>" />
			<input type="hidden" name="fields[state]" value="<?php echo $this->page->get('state'); ?>" />
			<input type="hidden" name="fields[access]" value="<?php echo $this->page->get('access'); ?>" />
			<input type="hidden" name="fields[id]" value="<?php echo $this->page->get('id'); ?>" />

			<?php echo Html::input('token'); ?>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->resource->id; ?>" />
			<input type="hidden" name="active" value="<?php echo $this->name; ?>" />
			<input type="hidden" name="action" value="save" />
		</form>

	</div>
</div>