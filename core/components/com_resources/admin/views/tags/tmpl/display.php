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

Toolbar::title(Lang::txt('COM_RESOURCES') . ': ' . Lang::txt('COM_RESOURCES_TAGS') . ' #' . $this->row->id, 'addedit.png');
Toolbar::save();
Toolbar::cancel();

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	submitform(pressbutton);
}
function addtag(tag)
{
	var input = document.getElementById('tags-men');
	if (input.value == '') {
		input.value = tag;
	} else {
		input.value += ', '+tag;
	}
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm">
	<fieldset class="adminform">
		<legend><span><?php echo Lang::txt('COM_RESOURCES_TAGS_CREATE'); ?></span></legend>

		<p><?php echo Lang::txt('COM_RESOURCES_TAGS_CREATE_HELP'); ?></p>

		<div class="input-wrap">
			<label for="tags-men"><?php echo Lang::txt('COM_RESOURCES_TAGS_FIELD_NEW_TAGS'); ?>:</label>
			<input type="text" name="tags" id="tags-men" size="65" value="" />
		</div>
	</fieldset>

	<fieldset class="adminform">
		<legend><span><?php echo Lang::txt('COM_RESOURCES_TAGS_EXISTING'); ?></span></legend>

		<p><?php echo Lang::txt('COM_RESOURCES_TAGS_EXISTING_HELP'); ?></p>

		<table class="adminlist">
			<thead>
				<tr>
					<th></th>
					<th><?php echo Lang::txt('COM_RESOURCES_TAGS_RAW_TAG'); ?></th>
					<th><?php echo Lang::txt('COM_RESOURCES_TAGS_TAG'); ?></th>
					<th><?php echo Lang::txt('COM_RESOURCES_TAGS_ADMIN'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
			$k = 0;
			$i = 0;
			foreach ($this->tags as $tag)
			{
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td><input type="checkbox" name="tgs[]" id="cb<?php echo $i; ?>" <?php if (in_array($tag->tag, $this->mytagarray)) { echo 'checked="checked"'; } ?> value="<?php echo $this->escape($tag->tag); ?>" /></td>
					<td><a href="#" onclick="addtag('<?php echo stripslashes($tag->tag); ?>');"><?php echo $this->escape($tag->raw_tag); ?></a></td>
					<td><a href="#" onclick="addtag('<?php echo stripslashes($tag->tag); ?>');"><?php echo $this->escape($tag->tag); ?></a></td>
					<td><?php if ($tag->admin == 1) { echo '<span class="check">admin</span>'; } ?></td>
				</tr>
				<?php
				$k = 1 - $k;
				$i++;
			}
			?>
			</tbody>
		</table>
	</fieldset>

	<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
