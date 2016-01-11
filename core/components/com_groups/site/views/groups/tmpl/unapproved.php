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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<section class="main section">
	<div class="group-unapproved">
		<span class="name">
			<?php echo $this->group->get('description'); ?>
		</span>
		<p class="warning"><?php echo Lang::txt('COM_GROUPS_PENDING_APPROVAL_WARNING'); ?></p>

		<?php if (in_array(User::get('id'), $this->group->get('invitees'))) : ?>
			<hr />
			<a href="<?php echo Route::url('index.php?option=com_groups&controller=groups&cn='.$this->group->get('cn').'&task=accept'); ?>" class="group-invited">
				<?php echo Lang::txt('COM_GROUPS_ACCEPT_INVITE'); ?>
			</a>
			<hr />
		<?php endif; ?>

		<p><a class="all-groups" href="<?php echo Route::url('index.php?option=com_groups'); ?>"><?php echo Lang::txt('COM_GROUPS_ALL_GROUPS'); ?></a> | <a class="my-groups" href="<?php echo Route::url('index.php?option=com_members&task=myaccount&active=groups'); ?>"><?php echo Lang::txt('COM_GROUPS_MY_GROUPS'); ?></a></p>
	</div>
</section>