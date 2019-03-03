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

$this->css('
hr {
	margin: 0;
}
.align-center {
	text-align: center;
}
.img-wrap {
	display: block;
}
.img-wrap img {
	border: 1px solid #ccc;
}
.img-preview {
	position: relative;
}
.img-preview .delete {
	position: absolute;
	top: 1em;
	right: 1em;
	display: block;
	width: 1em;
	height: 1em;
	font-size: 1.2em;
	line-height: 1;
	overflow: hidden;
	border: none;
	color: #bbb;
	font-style: normal;
}
.img-preview .delete:hover {
	color: red;
}
.icon-trash:before {
	content: "\f014";
	font-family: Fontcons;
	margin-right: 0.5em;
}
');
?>
<div id="media">
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" enctype="multipart/form-data" name="filelist" id="filelist">
		<fieldset>
			<p>
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
				<input type="hidden" name="tmpl" value="<?php echo Request::getCmd('tmpl'); ?>" />
				<input type="hidden" name="id" value="<?php echo $this->profile->get('id'); ?>" />
				<input type="hidden" name="task" value="upload" />
				<?php echo Html::input('token'); ?>

				<label for="image"><?php echo Lang::txt('COM_MEMBERS_MEDIA_UPLOAD'); ?> <?php echo Lang::txt('COM_MEMBERS_MEDIA_WILL_REPLACE_EXISTING_IMAGE'); ?></label>
				<input type="file" name="upload" id="upload" size="17" />&nbsp;&nbsp;&nbsp;
				<input type="submit" value="<?php echo Lang::txt('COM_MEMBERS_MEDIA_UPLOAD'); ?>" />
			</p>

			<?php
			if ($this->getError())
			{
				echo '<p class="error">' . $this->getError() . '</p>';
			}
			?>
			<hr />

			<div class="img-preview">
				<a class="icon-trash delete" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=remove&id=' . $this->profile->get('id') . '&file=profile.png&' . Session::getFormToken() . '=1&tmpl=' . Request::getCmd('tmpl')); ?>"><?php echo Lang::txt('JACTION_DELETE'); ?></a>

				<p class="input-wrap align-center">
					<span class="img-dimensions">50 x 50</span><br />
					<span class="img-wrap"><img src="<?php echo $this->profile->picture(0, true); ?>" alt="<?php echo Lang::txt('COM_MEMBERS_MEDIA_PICTURE'); ?>" width="50" height="50" id="memberthumb" /></span>
				</p>
				<p class="input-wrap align-center">
					<span class="img-dimensions">200 x 200</span><br />
					<span class="img-wrap"><img src="<?php echo $this->profile->picture(0, false); ?>" alt="<?php echo Lang::txt('COM_MEMBERS_MEDIA_PICTURE'); ?>" width="200" height="200" id="conimage" /></span>
				</p>
			</div>
		</fieldset>
	</form>
</div>
