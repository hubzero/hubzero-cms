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

<h4><?php echo ucwords(Lang::txt('COM_PROJECTS_EDIT_TEAM')); ?></h4>
<div id="cbody">
	<?php echo $this->content; ?>
</div>
<h5 class="terms-question">
	<?php echo Lang::txt('COM_PROJECTS_PROJECT') . ' ' . Lang::txt('COM_PROJECTS_OWNER'); ?>
	<?php if ($this->model->access('manager')): ?>
		<span class="mini">
			<a href="<?php echo Route::url($this->model->link('team') . '&action=changeowner'); ?>" class="showinbox">
				<?php echo ucfirst(Lang::txt('COM_PROJECTS_EDIT')); ?>
			</a>
		</span>
	<?php endif; ?>
</h5>
<?php if ($this->model->groupOwner() && $cn = $this->model->groupOwner('cn'))
{
	$ownedby = ucfirst(Lang::txt('COM_PROJECTS_GROUP')) . ' <a href="' . Route::url('index.php?option=com_groups&cn=' . $cn) . '">' . ' ' . $this->model->groupOwner('description') . ' (' . $cn . ')</a>';
}
else
{
	$ownedby = '<a href="' . Route::url('index.php?option=com_members&id=' . $this->model->owner('id')) . '">' . $this->model->owner('name') . '</a>';
}

echo '<span class="mini">' . $ownedby . '</span>';

?>
