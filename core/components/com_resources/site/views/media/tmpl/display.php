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

// No direct access.
defined('_HZEXEC_') or die();

$this->css('component.css');
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" name="adminForm" id="adminForm" method="post" enctype="multipart/form-data">
	<fieldset>
		<label for="upload">
			<input type="file" class="option" name="upload" id="upload" />
			<input type="submit" class="option" value="<?php echo strtolower(Lang::txt('COM_CONTRIBUTE_UPLOAD')); ?>" />
		</label>

		<input type="hidden" name="tmpl" value="component" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="resource" value="<?php echo $this->resource; ?>" />
		<input type="hidden" name="task" value="upload" />
	</fieldset>

	<?php if ($this->getError()) { ?>
		<p class="error">
			<?php echo implode('<br />', $this->getErrors()); ?>
		</p>
	<?php } ?>

	<?php if (count($this->folders) > 0 || count($this->docs) > 0) { ?>
		<table>
			<tbody>
			<?php
			$docs = $this->docs;
			for ($i=0; $i<count($docs); $i++)
			{
				$docName = key($docs);

				$subdird = ($this->subdir && $this->subdir != DS) ? $this->subdir . DS : DS;
			?>
				<tr>
					<td width="100%">
						<?php echo Route::url('index.php?option=com_resources&id=' . ($this->row->alias ? $this->row->alias : $this->resource) . '&task=download&file=' . $docs[$docName]); ?>
					</td>
					<td>
						<a class="icon-delete delete" href="<?php echo Request::base(true); ?>/index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=delete&amp;file=<?php echo $docs[$docName]; ?>&amp;resource=<?php echo $this->resource; ?>&amp;tmpl=component&amp;subdir=<?php echo $this->subdir; ?>&amp;<?php echo Session::getFormToken(); ?>=1" target="filer" onclick="return deleteFile('<?php echo $docs[$docName]; ?>');" title="<?php echo Lang::txt('JACTION_DELETE'); ?>">
							<span><?php echo Lang::txt('JACTION_DELETE'); ?></span>
						</a>
					</td>
				</tr>
			<?php
				next($docs);
			}
			?>
			</tbody>
		</table>
	<?php } ?>

	<?php echo Html::input('token'); ?>
</form>
