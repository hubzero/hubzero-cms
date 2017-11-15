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

$role  = Lang::txt('COM_PROJECTS_PROJECT') . ' <span>';
if ($this->model->access('manager'))
{
	$role .= Lang::txt('COM_PROJECTS_LABEL_OWNER');
}
elseif (!$this->model->access('content'))
{
	$role .= Lang::txt('COM_PROJECTS_LABEL_REVIEWER');
}
else
{
	$role .= Lang::txt('COM_PROJECTS_LABEL_COLLABORATOR');
}
$role .= '</span>';

$counts = $this->model->get('counts');

?>
<ul id="member_options">
	<li><?php echo ucfirst($role); ?>
		<div id="options-dock">
			<div><p><?php echo Lang::txt('COM_PROJECTS_JOINED') . ' ' . $this->model->created('date'); ?></p>
				<ul>
		<?php if ($this->model->access('manager')) { ?>
					<li><a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&task=edit'); ?>"><?php echo Lang::txt('COM_PROJECTS_EDIT_PROJECT'); ?></a></li>
					<li><a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&task=edit&active=team'); ?>"><?php echo Lang::txt('COM_PROJECTS_INVITE_PEOPLE'); ?></a></li>
		<?php } ?>
		<?php if ($this->model->isPublic()) { ?>
					<li><a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&preview=1'); ?>"><?php echo Lang::txt('COM_PROJECTS_PREVIEW_PUBLIC_PROFILE'); ?></a></li>
		<?php } ?>
		<?php if (isset($counts['team']) && $counts['team'] > 1 && $this->model->member()) { ?>
					<li><a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&active=team&action=quit'); ?>"><?php echo Lang::txt('COM_PROJECTS_LEAVE_PROJECT'); ?></a></li>
		<?php } ?>
				</ul>
			</div>
		</div>
	</li>
</ul>