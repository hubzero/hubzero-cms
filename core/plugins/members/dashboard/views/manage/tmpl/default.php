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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<div class="admin-header">
	<a class="icon-add button push-module" href="<?php echo Route::url('index.php?option=com_members&controller=plugins&task=manage&plugin=dashboard&action=push'); ?>">
		<?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_PUSH_TITLE'); ?>
	</a>
	<a class="icon-add button add-module" href="<?php echo Route::url('index.php?option=com_members&controller=plugins&task=manage&plugin=dashboard&action=add'); ?>">
		<?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_ADD_MODULES'); ?>
	</a>
	<h3>
		<?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_MANAGE'); ?>
	</h3>
</div>

<div class="member_dashboard">

	<div class="modules customizable">
		<?php
			foreach ($this->modules as $module)
			{
				// create view object
				$this->view('module', 'display')
				     ->set('admin', $this->admin)
				     ->set('module', $module)
				     ->display();
			}
		?>
	</div>

	<div class="modules-empty">
		<h3><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_ADMIN_EMPTY_TITLE'); ?></h3>
		<p><?php echo Lang::txt('PLG_MEMBERS_DASHBOARD_ADMIN_EMPTY_DESC'); ?></p>
	</div>
</div>