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
?>
<ol id="steps" class="steps">
	<li class="setup-step<?php if ($this->step == 0) { echo ' active'; } else if ($this->model->get('setup_stage') >= 1) { echo ' completed'; } ?>"><?php if ($this->model->get('setup_stage') > 0 && $this->step != 0) { ?><a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&task=setup&section=describe'); ?>"><?php } ?><?php echo Lang::txt('COM_PROJECTS_DESCRIBE_PROJECT'); ?><?php if ($this->model->get('setup_stage') > 0 && $this->step != 0) { ?></a><?php } ?></li>
	<li <?php if ($this->step == 1) { echo 'class="active"'; } elseif ($this->model->get('setup_stage') >= 2) { echo 'class="completed"'; } else { echo 'class="coming"'; } ?>><?php if ($this->model->get('setup_stage') >= 1 && $this->step != 1) { ?><a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&task=setup&section=team'); ?>"><?php } ?><?php echo Lang::txt('COM_PROJECTS_ADD_TEAM'); ?><?php if ($this->model->get('setup_stage') >= 1 && $this->step != 1) { ?></a><?php } ?></li>
	<?php if ($this->step == 2) { ?>
	<li class="active"><?php echo Lang::txt('COM_PROJECTS_SETUP_ONE_LAST_THING'); ?></li>
	<?php } ?>
	<li class="coming"><?php echo Lang::txt('COM_PROJECTS_READY_TO_GO'); ?></li>
</ol>
