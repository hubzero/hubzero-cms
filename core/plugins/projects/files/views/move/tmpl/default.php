<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

// No direct access
defined('_HZEXEC_') or die();

$f = 1;
$i = 1;
$skipped = 0;
$maxlevel = 100;

$subdirlink = $this->subdir ? '&amp;subdir=' . urlencode($this->subdir) : '';

// Get all parents
$dirs = array();
if ($this->list)
{
	foreach ($this->list as $item)
	{
		if ($item->type == 'folder')
		{
			$dirs[] = $item->localPath;
		}
	}
}

?>
<div id="abox-content">
<h3><?php echo Lang::txt('PLG_PROJECTS_FILES_MOVE_PROJECT_FILES'); ?></h3>
<?php
// Display error or success message
if ($this->getError()) {
	echo '<p class="witherror">' . $this->getError() . '</p>';
}
else {
?>
<form id="hubForm-ajax" method="post" action="<?php echo $this->url; ?>">
	<fieldset >
		<input type="hidden" name="id" value="<?php echo $this->model->get('id'); ?>" />
		<input type="hidden" name="action" value="moveit" />
		<input type="hidden" name="task" value="view" />
		<input type="hidden" name="active" value="files" />
		<input type="hidden" name="repo" value="<?php echo $this->repo->get('name'); ?>" />
		<input type="hidden" name="subdir" value="<?php echo $this->subdir; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<p><?php echo Lang::txt('PLG_PROJECTS_FILES_MOVE_FILES_CONFIRM'); ?></p>

		<ul class="sample">
		<?php foreach ($this->items as $file)
		{
			// Display list item with file data
			$this->view('default', 'selected')
			     ->set('skip', false)
			     ->set('file', $file)
			     ->set('action', 'move')
			     ->set('multi', 'multi')
			     ->display();
		} ?>
		</ul>

		<div id="dirs" class="dirs">
			<h4><?php echo Lang::txt('PLG_PROJECTS_FILES_MOVE_WHERE'); ?></h4>
			<?php if (count($dirs) > 0) {  echo '<ul class="dirtree">';
			?>
				<li>
					<input type="radio" name="newpath" value="" <?php if (!$this->subdir) { echo 'disabled="disabled" '; } ?> checked="checked" /> <span><?php echo Lang::txt('PLG_PROJECTS_FILES_HOME_DIRECTORY'); ?></span>
				</li>
			<?php
			for ($i= 0; $i < count($dirs); $i++)
			{
					$dir = $dirs[$i];

					// Remove full path
					$dir 			= trim(str_replace($this->path, "", $dir), DS);
					$desect_path 	= explode(DS, $dir);
					$level 			= count($desect_path);
					$dirname 		= end($desect_path);
					$maxlevel 		= $level > $maxlevel ? $level : $maxlevel;

					$leftMargin = ($level * 15) . 'px';
				 ?>
				<li style="margin-left:<?php echo $leftMargin; ?>">
					<input type="radio" name="newpath" value="<?php echo urlencode($dir); ?>" <?php if ($this->subdir == $dir) { echo 'disabled="disabled" '; } ?> /> <span><span class="folder <?php if ($this->subdir == $dir) { echo 'prominent '; } ?>"><?php echo $dirname; ?></span></span>
				</li>
			<?php }
			echo '</ul>'; }
			if ($maxlevel <= 100) { ?>
			<?php if (count($dirs) > 0) { ?>
				<div class="or"><?php echo Lang::txt('COM_PROJECTS_OR'); ?></div>
			<?php }  ?>
			<label><span class="block"><?php echo Lang::txt('PLG_PROJECTS_FILES_MOVE_TO_NEW_DIRECTORY'); ?></span>
				<span class="mini prominent"><?php echo $this->subdir ? $this->subdir . DS : ''; ?></span>
				<input type="text" name="newdir" maxlength="50" value="" />
			</label>
			<?php }  ?>
		</div>
		<p class="submitarea">
			<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_PROJECTS_FILES_MOVE'); ?>" />
			<?php if ($this->ajax) { ?>
				<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo Lang::txt('JCANCEL'); ?>" />
			<?php } else {  ?>
				<span>
					<a id="cancel-action"  class="btn btn-cancel"  href="<?php echo $this->url . '?a=1' . $subdirlink; ?>"><?php echo Lang::txt('JCANCEL'); ?></a>
				</span>
			<?php } ?>
		</p>
	</fieldset>
</form>
<?php } ?>
</div>