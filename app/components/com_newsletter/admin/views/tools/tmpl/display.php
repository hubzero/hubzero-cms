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

//set title
Toolbar::title(Lang::txt( 'COM_NEWSLETTER_NEWSLETTER_TOOLS' ), 'tools');

// add jquery
Html::behavior('framework');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form" enctype="multipart/form-data">
	<div class="grid">
		<div class="col span6">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_NEWSLETTER_TOOLS_MOZIFY'); ?></span></legend>

				<table class="admintable">
					<tbody>
						<tr>
							<td colspan="2">
								<span class="hint"><?php echo Lang::txt('COM_NEWSLETTER_TOOLS_MOZIFY_DESC'); ?></span>
							</td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_NEWSLETTER_TOOLS_MOZIFY_IMAGE_FILE'); ?></th>
							<td>
								<input type="file" name="image-file" />
							</td>
						</tr>
						<tr>
							<td colspan="2" style="text-align:center;font-weight:bold;font-size:16px">&mdash;&mdash;&mdash; or &mdash;&mdash;&mdash;</td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_NEWSLETTER_TOOLS_MOZIFY_IMAGE_URL'); ?></th>
							<td>
								<input type="text" name="image-url" />
							</td>
						</tr>
						<tr>
							<th><?php echo Lang::txt('COM_NEWSLETTER_TOOLS_MOZIFY_MOSAIC_SIZE'); ?></th>
							<td>
								<select name="mosaic-size">
									<option value="1">1</option>
									<option value="3">3</option>
									<option selected="selected" value="5">5</option>
									<option value="10">10</option>
									<option value="15">15</option>
									<option value="20">20</option>
									<option value="25">25</option>
									<option value="30">30</option>
									<option value="35">35</option>
									<option value="40">40</option>
									<option value="45">45</option>
									<option value="50">50</option>
								</select>
							</td>
						</tr>
						<tr>
							<td></td>
							<td>
								<input type="submit" value="<?php echo Lang::txt('Submit'); ?>" />
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</div>
		<div class="col span6">
			<?php if ($this->code != '') : ?>
				<h3><?php echo Lang::txt('COM_NEWSLETTER_TOOLS_MOZIFY_ORIGINAL'); ?></h3>
				<img src="<?php echo str_replace(PATH_APP, '', $this->original); ?>" alt="" />

				<h3><?php echo Lang::txt('COM_NEWSLETTER_TOOLS_MOZIFY_MOZIFIED'); ?></h3>
				<iframe id="preview-iframe" style="border:1px solid transparent"></iframe>
				<div id="preview-code" style="display:none"><?php echo $this->preview; ?></div>

				<h3><?php echo Lang::txt('COM_NEWSLETTER_TOOLS_MOZIFY_CODE'); ?></h3>
				<textarea id="code"><?php echo str_replace("\n", "", $this->code); ?></textarea>

				<script>
					jQuery(document).ready(function($){
						//get iframe and mozified code
						var previewIframe = $('#preview-iframe'),
							previewCode = $('#preview-code').find('table').first();

						//set iframe height and width
						//add preview code to iframe
						previewIframe
							.css({
								width: previewCode.attr('width') + 'px',
								height: previewCode.attr('height') + 'px'
							})
							.contents().find('html').html( previewCode );
					});
				</script>
			<?php endif; ?>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="mozify" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>