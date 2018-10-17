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

$title 		 = Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTENT_RELABEL');
$placeholder = Lang::txt('PLG_PROJECTS_PUBLICATIONS_NO_DESCRIPTION');

if ($this->row->type == 'file')
{
	$dirpath = dirname($this->row->path) == '.' ? '' : dirname($this->row->path) . DS;
	$gone 	 = is_file($this->path . DS . $this->row->path) ? false : true;
	$title   = $gone ? Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTENT_RELABEL') : Lang::txt('PLG_PROJECTS_PUBLICATIONS_CONTENT_RENAME');
	$placeholder = basename($this->row->path);
}

?>
<div id="abox-content">
<h3><?php echo $title; ?></h3>
	<form id="hubForm-ajax" method="post" action="<?php  ?>">
			<fieldset>
				<input type="hidden" name="id" value="<?php echo $this->project->get('id'); ?>" />
				<input type="hidden" name="aid" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="props" value="<?php echo $this->props; ?>" />
				<input type="hidden" name="action" value="saveitem" />
				<input type="hidden" name="active" value="publications" />
				<input type="hidden" name="option" value="<?php echo $this->project->isProvisioned() ? 'com_publications' : $this->option; ?>" />
				<input type="hidden" name="backUrl" value="<?php echo $this->backUrl; ?>" />
				<?php if ($this->project->isProvisioned()) { ?>
				<input type="hidden" name="task" value="submit" />
				<?php } ?>
			</fieldset>
			<div class="content-wrap">
				<div class="content-edit">
					<label for="title">
						<span class="leftshift faded"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_LABEL')); ?>:</span>
						<input type="text" name="title" maxlength="250" class="long" value="<?php echo $this->row && $this->row->title ? $this->row->title : ''; ?>" placeholder="<?php echo $placeholder; ?>"  />
						<span class="optional"><?php echo Lang::txt('OPTIONAL'); ?></span>
					</label>

					<?php if ($this->row->type == 'file') { ?>
						<?php if ($gone) { ?>
						<p>
							<span class="leftshift faded"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_FILE_PATH')); ?>*:</span>
							<span><?php echo $this->row->path; ?></span>
						</p>
						<p class="content-hint"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_FILE_MISSING_NOTICE'); ?></p>
						<?php } else { ?>
						<p>
							<span class="leftshift faded"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_FILE_PATH')); ?>*:</span>
							<span><?php echo $dirpath; ?> <input type="text" name="filename" maxlength="100" value="<?php echo basename($this->row->path); ?>" /></span>
						</p>
						<p class="content-hint"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_RENAME_NOTICE'); ?></p>
						<?php } ?>
					<?php } ?>

					<p class="submitarea">
						<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_SAVE'); ?>" />
						<?php if ($this->ajax) { ?>
						<input type="reset" id="cancel-action" class="btn btn-cancel" value="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CANCEL'); ?>" />
						<?php } else { ?>
						<a href="<?php echo $this->backUrl; ?>" class="btn btn-cancel"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_CANCEL'); ?></a>
						<?php } ?>
					</p>
				</div>
			</div>
	</form>
	<div class="clear"></div>
</div>