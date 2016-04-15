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
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" enctype="multipart/form-data" name="filelist" id="filelist" onsubmit="return validate();">
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>

	<table>
		<tbody>
			<?php
			$k = 0;

			if ($this->file && file_exists($this->file_path . DS . $this->file))
			{
				$this_size = filesize($this->file_path . DS . $this->file);
				list($ow, $oh, $type, $attr) = getimagesize($this->file_path . DS . $this->file);

				// scale if image is bigger than 120w x120h
				$num = max($ow/120, $oh/120);
				if ($num > 1)
				{
					$mw = round($ow/$num);
					$mh = round($oh/$num);
				}
				else
				{
					$mw = $ow;
					$mh = $oh;
				}
				?>
				<tr>
					<td>
						<img src="<?php echo $this->webpath . DS . $this->path . DS . $this->file; ?>" alt="" id="conimage" height="<?php echo $mh; ?>" width="<?php echo $mw; ?>" />
					</td>
					<td width="100%">
						<input type="hidden" name="conimg" value="<?php echo $this->webpath . DS . $this->path . DS . $this->file; ?>" />
						<input type="hidden" name="task" value="delete" />
						<input type="hidden" name="file" id="file" value="<?php echo $this->file; ?>" />
						<input type="submit" name="submit" value="<?php echo Lang::txt('DELETE'); ?>" />
					</td>
				</tr>
			<?php } else { ?>
				<tr>
					<td>
						<img src="<?php echo $this->default_picture; ?>" alt="" id="oimage" name="oimage" />
					</td>
					<td>
						<p><?php echo Lang::txt('COM_FEEDBACK_STORY_ADD_PICTURE'); ?><br /><small>(gif/jpg/jpeg/png - 200K max)</small></p>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="hidden" name="conimg" value="" />
						<input type="hidden" name="task" value="upload" />
						<input type="hidden" name="currentfile" value="<?php $this->file; ?>" />
						<input type="file" name="upload" id="upload" size="10" /> <input type="submit" value="<?php echo Lang::txt('COM_FEEDBACK_UPLOAD'); ?>" />
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
</form>
<script type="text/javascript">
	<!--
	function validate()
	{
		var apuf = document.getElementById('file');
		return apuf.value ? true : false;
	}

	function passparam()
	{
		parent.document.getElementById('picture').value = this.document.forms[0].conimg.value;
	}

	window.onload = passparam;
	//-->
</script>