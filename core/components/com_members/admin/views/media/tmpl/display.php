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
?>
<div id="media">
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" enctype="multipart/form-data" name="filelist" id="filelist">
		<table class="formed">
			<thead>
				<tr>
					<th><label for="image"><?php echo Lang::txt('COM_MEMBERS_MEDIA_UPLOAD'); ?> <?php echo Lang::txt('COM_MEMBERS_MEDIA_WILL_REPLACE_EXISTING_IMAGE'); ?></label></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
						<input type="hidden" name="tmpl" value="component" />
						<input type="hidden" name="id" value="<?php echo $this->profile->get('id'); ?>" />
						<input type="hidden" name="task" value="upload" />

						<input type="file" name="upload" id="upload" size="17" />&nbsp;&nbsp;&nbsp;
						<input type="submit" value="<?php echo Lang::txt('COM_MEMBERS_MEDIA_UPLOAD'); ?>" />
					</td>
				</tr>
			</tbody>
		</table>
		<?php
		if ($this->getError())
		{
			echo '<p class="error">' . $this->getError() . '</p>';
		}
		?>
		<div class="input-wrap" style="text-align: center; max-width: 300px;">
			<img style="width: 100%;" src="<?php echo $this->profile->picture(); ?>" alt="<?php echo Lang::txt('COM_MEMBERS_MEDIA_PICTURE'); ?>" id="conimage" />
		</div>
		<?php echo Html::input('token'); ?>
	</form>
</div>
