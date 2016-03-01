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

// No direct access
defined('_HZEXEC_') or die();

$this->css('media.css');
?>
<script type="text/javascript">
function dirup()
{
	var urlquery = frames['filer'].location.search.substring(1);
	var curdir = urlquery.substring(urlquery.indexOf('dir=')+8);
	var listdir = curdir.substring(0,curdir.lastIndexOf('/'));
	frames['filer'].location.href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=media&gidNumber=' . $this->group->get('gidNumber') . '&task=list&tmpl=component&dir=');?>" + listdir;
}

function goUpDir()
{
	var selection = document.getElementById('dir');
	var dir = selection.options[selection.selectedIndex].value;
	frames['filer'].location.href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=media&gidNumber=' . $this->group->get('gidNumber') . '&task=list&tmpl=component&dir=', false); ?>" + dir;
}
</script>
<div id="attachments">
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=component&controller=' . $this->controller . '&gidNumber=' . $this->group->get('gidNumber') . '&task=upload'); ?>" id="adminForm" method="post" enctype="multipart/form-data">
		<fieldset>
			<div class="grid">
				<div class="col span4">
					<div class="input-wrap">
						<input type="file" name="upload" id="upload" />
					</div>
				</div>
				<div class="col span4">
					<div class="input-wrap">
						<input type="text" name="foldername" id="foldername" placeholder="<?php echo Lang::txt('COM_GROUPS_MEDIA_CREATE_DIRECTORY'); ?>" />
					</div>
				</div>
				<div class="col span4">
					<div class="input-wrap">
						<input type="submit" value="<?php echo Lang::txt('COM_GROUPS_MEDIA_ACTION_UPLOAD'); ?>" />
					</div>
				</div>
			</div>

			<input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->escape($this->controller); ?>" />
			<input type="hidden" name="task" value="upload" />
			<input type="hidden" name="gidNumber" value="<?php echo $this->escape($this->group->get('gidNumber')); ?>" />
			<input type="hidden" name="dir" value="<?php echo $this->escape(urlencode($this->dir)); ?>" />
			<input type="hidden" name="tmpl" value="component" />

			<?php echo Html::input('token'); ?>
		</fieldset>

		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } ?>

		<div id="themanager" class="manager">
			<div class="input-wrap">
				<label>
					<?php echo Lang::txt('COM_GROUPS_MEDIA_DIRECTORY'); ?>
					<?php echo $this->dirPath; ?>
				</label>
			</div>

			<iframe src="<?php echo Route::url('index.php?option=' . $this->option . '&tmpl=component&controller=' . $this->controller . '&gidNumber=' . $this->group->get('gidNumber') . '&task=list' . ($this->dir ? '&dir=' . $this->dir : '')); ?>" name="filer" id="filer" width="98%" height="400"></iframe>
		</div>
	</form>
</div>
