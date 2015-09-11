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
<div id="plg-header">
	<h3 class="todo"><?php if ($this->listName or $this->filters['assignedto'] or $this->filters['state'] == 1) { ?> <a href="<?php echo Route::url($url); ?>"> <?php } ?><?php echo $this->title; ?><?php if ($this->listName or $this->filters['assignedto'] or $this->filters['state'] == 1) { ?></a><?php } ?>
	<?php if ($this->listName) { ?> &raquo; <a href="<?php echo Route::url($url).'/?list='.$this->filters['todolist']; ?>"><span class="indlist <?php echo 'pin_'.$this->filters['todolist'] ?>"><?php echo $this->listName; ?></span></a> <?php } ?>
	<?php if ($this->filters['assignedto']) { ?> &raquo; <span class="indlist mytodo"><a href="<?php echo Route::url($url).'/?mine=1'; ?>"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_TODO_MY_TODOS')); ?></a></span> <?php } ?>
	<?php if ($this->filters['state']) { ?> &raquo; <span class="indlist completedtd"><?php echo ucfirst(Lang::txt('PLG_PROJECTS_TODO_COMPLETED')); ?></span> <?php } ?>
	</h3>
</div>