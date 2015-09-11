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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

// Push the module CSS to the template
$this->css();

$groups = $this->groups;
$total = count($this->groups);
?>
<div<?php echo ($this->moduleclass) ? ' class="' . $this->moduleclass . '"' : '';?>>
	<?php if ($this->params->get('button_show_all', 1) || $this->params->get('button_show_add', 1)) { ?>
	<ul class="module-nav grouped">
		<?php if ($this->params->get('button_show_all', 1)) { ?>
			<li><a class="icon-browse" href="<?php echo Route::url('index.php?option=com_groups&task=browse'); ?>"><?php echo Lang::txt('MOD_MYGROUPS_ALL_GROUPS'); ?></a></li>
		<?php } ?>
		<?php if ($this->params->get('button_show_add', 1)) { ?>
			<li><a class="icon-plus" href="<?php echo Route::url('index.php?option=com_groups&task=new'); ?>"><?php echo Lang::txt('MOD_MYGROUPS_NEW_GROUP'); ?></a></li>
		<?php } ?>
	</ul>
	<?php } ?>

	<?php if ($groups && $total > 0) { ?>
		<ul class="compactlist mygroups">
			<?php
			$i = 0;
			foreach ($groups as $group)
			{
				if ($group->published && $i < $this->limit)
				{
					$status = $this->getStatus($group);
					?>
					<li class="group">
						<a href="<?php echo Route::url('index.php?option=com_groups&cn=' . $group->cn); ?>"><?php echo $this->escape(stripslashes($group->description)); ?></a>
						<span><span class="<?php echo $status; ?> status"><?php echo Lang::txt('MOD_MYGROUPS_STATUS_' . strtoupper($status)); ?></span></span>
						<?php if (!$group->approved): ?>
							<br />
							<span class="status pending-approval"><?php echo Lang::txt('MOD_MYGROUPS_GROUP_STATUS_PENDING'); ?></span>
						<?php endif; ?>
						<?php if ($group->regconfirmed && !$group->registered) : ?>
							<span class="actions">
								<a class="action-accept" href="<?php echo Route::url('index.php?option=com_groups&cn=' . $group->cn . '&task=accept'); ?>">
									<?php echo Lang::txt('MOD_MYGROUPS_ACTION_ACCEPT'); ?>
								</a>
							</span>
						<?php endif; ?>
					</li>
					<?php
					$i++;
				}
			}
			?>
		</ul>
	<?php } else { ?>
		<p><em><?php echo Lang::txt('MOD_MYGROUPS_NO_GROUPS'); ?></em></p>
	<?php } ?>

	<?php if ($total > $this->limit) { ?>
		<p class="note"><?php echo Lang::txt('MOD_MYGROUPS_YOU_HAVE_MORE', $this->limit, $total, Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=groups')); ?></p>
	<?php } ?>
</div>

