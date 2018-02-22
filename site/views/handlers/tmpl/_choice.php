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
// no direct access
defined('_HZEXEC_') or die();

$class = $this->item->assigned && $this->item->active ? ' assigned' : ' unassigned';
?>

<div class="handlertype-<?php echo $this->handler->get('_name') . $class; ?>">
	<h3><?php echo $this->configs->label; ?></h3>
	<p class="manage-handler">
		<a href="<?php echo Route::url('index.php?option=com_projects&alias='
				. $this->publication->project_alias . '&active=publications&pid=' . $this->publication->id) . '?vid=' . $this->publication->version_id . '&amp;action=handler&amp;h=' . $this->handler->get('_name') . '&amp;p=' . $this->props; ?>" class="showinbox box-expanded"><?php echo ($this->item->assigned && $this->item->active) ? Lang::txt('COM_PUBLICATIONS_HANDLER_VIEW_MANAGE') : Lang::txt('COM_PUBLICATIONS_HANDLER_ACTIVATE'); ?></a>
	</p>
</div>