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

$html  = '';

$this->css()
     ->js();

// Do some text cleanup
$this->project->title = $this->escape($this->project->title);
$this->project->about = $this->escape($this->project->about);

$title = $this->project->title ? Lang::txt('COM_PROJECTS_NEW_PROJECT') . ': ' . $this->project->title : $this->title;
?>
<header id="content-header">
	<h2><?php echo $title; ?> <?php if ($this->gid && is_object($this->group)) { ?> <?php echo Lang::txt('COM_PROJECTS_FOR').' '.ucfirst(Lang::txt('COM_PROJECTS_GROUP')); ?> <a href="<?php echo Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn')); ?>"><?php echo \Hubzero\Utility\Str::truncate($this->group->get('description'), 50); ?></a><?php } ?></h2>
</header><!-- / #content-header -->

<section class="main section" id="setup">
	<div class="section-inner">
		<div class="status-msg">
			<?php
			// Display error or success message
			if ($this->getError())
			{
				echo ('<p class="witherror">' . $this->getError().'</p>');
			}
			else if ($this->msg)
			{
				echo ('<p>' . $this->msg . '</p>');
			}
			?>
		</div>

		<div class="clear"></div>

		<form id="hubForm" method="post" action="<?php echo Route::url('index.php?option=' . $this->option); ?>">
			<div class="explaination">
				<h4><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_UPFRONT_WHY'); ?></h4>
				<p><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_UPFRONT_BECAUSE'); ?></p>
			</div>
			<fieldset class="wider">
				<input type="hidden"  name="task" value="setup" />
				<input type="hidden"  name="step" value="1" />
				<input type="hidden"  name="save_stage" value="0" />
				<input type="hidden" id="option" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" id="pid" name="id" value="<?php echo $this->project->id; ?>" />
				<input type="hidden" id="gid" name="gid" value="<?php echo $this->gid; ?>" />
				<input type="hidden"  name="proceed" value="1" />
				<h2><?php echo Lang::txt('COM_PROJECTS_SETUP_BEFORE_WE_START'); ?></h2>
				<h4 class="setup-h"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_PHI'); ?></span></h4>
				<label class="terms-label dark">
					<input class="option restricted-answer" name="restricted" id="f-restricted-no" type="radio" value="no" checked="checked" />
					<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_PHI_NO'); ?>
				</label>
				<label class="terms-label dark">
					<input class="option restricted-answer" name="restricted" id="f-restricted-yes" type="radio" value="yes"/>
					<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_QUESTION_PHI_YES_NOT_SURE'); ?>
				</label>
				<div id="f-restricted-explain" class="cautionaction">
					<?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_RESTRICTED_EXPLAIN'); ?>
				</div>
				<p class="submitarea"><input type="submit" value="<?php echo Lang::txt('COM_PROJECTS_CONTINUE'); ?>" class="btn" id="btn-preform" /></p>
			</fieldset>
		</form>
	</div>
</section>