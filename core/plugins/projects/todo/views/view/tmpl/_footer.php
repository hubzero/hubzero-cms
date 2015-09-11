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

$url = 'index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&active=todo';

?>
<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
<input type="hidden" name="id" id="pid" value="<?php echo $this->model->get('id'); ?>" />
<input type="hidden" name="alias" value="<?php echo $this->model->get('alias'); ?>" />
<input type="hidden" name="uid" id="uid" value="<?php echo $this->uid; ?>" />
<input type="hidden" name="active" value="todo" />
<input type="hidden" name="action" value="save" />
<input type="hidden" name="todoid" id="todoid" value="0" />
<input type="hidden" name="page" id="tdpage" value="list" />
<input type="hidden" name="list" id="list" value="<?php echo $this->filters['todolist']; ?>" />
<input type="hidden" name="state" id="tdstate" value="<?php echo $this->filters['state']; ?>" />
<input type="hidden" name="mine" value="<?php echo $this->filters['mine']; ?>" />
<input type="hidden" name="sortby" value="<?php echo $this->filters['sortby']; ?>" />
<input type="hidden" name="sortdir" value="<?php echo $this->filters['sortdir']; ?>" />
<?php if ($this->filters['state'] == 0) { ?>
	<p class="tips js"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_TODO_REORDER_INSTRUCT')); ?></p>
<?php } ?>
<?php if ($this->filters['todolist']) { ?>
<div class="container-footer">
	<span class="listoptions"><a href="<?php echo Route::url($url . '&action=delete&dl=' . $this->filters['todolist']); ?>" id="del-<?php echo $this->filters['todolist']; ?>" class="dellist"><?php echo Lang::txt('PLG_PROJECTS_TODO_DELETE_TODO_LIST'); ?></a></span>
	<div class="confirmaction" id="confirm-<?php echo $this->filters['todolist']; ?>"><?php echo Lang::txt('PLG_PROJECTS_TODO_DELETE_ARE_YOU_SURE'); ?>
		<ul>
			<li><a href="<?php echo Route::url($url . '&action=delete&dl=' . $this->filters['todolist'] . '&all=1'); ?>"><?php echo Lang::txt('PLG_PROJECTS_TODO_DELETE_ALL_ITEMS'); ?></a></li>
			<li><a href="<?php echo Route::url($url . '&action=delete&dl=' . $this->filters['todolist']); ?>"><?php echo Lang::txt('PLG_PROJECTS_TODO_DELETE_LEAVE_ITEMS'); ?></a></li>
			<li><a href="<?php echo Route::url($url . '&list=' . $this->filters['todolist']); ?>" id="cnl-<?php echo $this->filters['todolist']; ?>"><?php echo Lang::txt('PLG_PROJECTS_TODO_DELETE_CANCEL'); ?></a></li>
		</ul>
	</div>
</div>
<?php } ?>