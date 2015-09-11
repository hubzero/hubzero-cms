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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

// Push the module CSS to the template
$this->css();

$projects = $this->rows;

$setup_complete = $this->pconfig->get('confirm_step', 0) ? 3 : 2;
?>
<div<?php echo ($this->moduleclass) ? ' class="' . $this->moduleclass . '"' : ''; ?> id="myprojects">
	<?php if ($this->params->get('button_show_all', 1) || $this->params->get('button_show_add', 1)) { ?>
	<ul class="module-nav">
		<?php if ($this->params->get('button_show_all', 1)) { ?>
		<li>
			<a class="icon-browse" href="<?php echo Route::url('index.php?option=com_projects&task=browse'); ?>">
				<?php echo Lang::txt('MOD_MYPROJECTS_ALL_PROJECTS'); ?>
			</a>
		</li>
		<?php } ?>
		<?php if ($this->params->get('button_show_add', 1)) { ?>
		<li>
			<a class="icon-plus" href="<?php echo Route::url('index.php?option=com_projects&task=start'); ?>">
				<?php echo Lang::txt('MOD_MYPROJECTS_NEW_PROJECT'); ?>
			</a>
		</li>
		<?php } ?>
	</ul>
	<?php } ?>

	<?php if ($projects && $this->total > 0) { ?>
		<ul class="compactlist">
			<?php
			$i = 0;
			foreach ($projects as $row)
			{
				if ($i >= $this->limit)
				{
					break;
				}
				$goto  = 'alias=' . $row->alias;
				$owned_by = Lang::txt('MOD_MYPROJECTS_BY') . ' ';
				if ($row->owned_by_group)
				{
					$owned_by .= '<strong>' . \Hubzero\Utility\String::truncate($row->groupname, 20) . '</strong>';
				}
				else if ($row->created_by_user == User::get('id'))
				{
					$owned_by .= Lang::txt('MOD_MYPROJECTS_ME');
				}
				else
				{
					$owned_by .= '<strong>' . $row->authorname . '</strong>';
				}
				$role = $row->role == 1 ? Lang::txt('MOD_MYPROJECTS_STATUS_MANAGER') : Lang::txt('MOD_MYPROJECTS_STATUS_COLLABORATOR');
				$setup = ($row->setup_stage < $setup_complete) ? Lang::txt('MOD_MYPROJECTS_STATUS_SETUP') : '';

				$class = '';
				if ($row->state == 1 && $row->setup_stage >= $setup_complete)
				{
					$class = "pr-active";
				}
				else if ($row->setup_stage < $setup_complete)
				{
					$class = "pr-setup";
				}
				else if ($row->state == 0)
				{
					$class = "pr-inactive";
				}
				$class = $class ? ' class="' . $class . '"' : '';

				$i++;
				?>
					<li <?php echo $class; ?>>
						<a href="<?php echo Route::url('index.php?option=com_projects&task=view&' . $goto); ?>" title="<?php echo $this->escape($row->title) . ' (' . $row->alias . ')'; ?>"><img src="<?php echo Route::url('index.php?option=com_projects&alias=' . $row->alias . '&controller=media&media=thumb'); ?>" alt="<?php echo $this->escape($row->title); ?>" class="project-image" /></a>
						<a href="<?php echo Route::url('index.php?option=com_projects&task=view&' . $goto); ?>" title="<?php echo $this->escape($row->title) . ' (' . $row->alias . ')'; ?>"><?php echo \Hubzero\Utility\String::truncate($this->escape($row->title), 30); ?></a>
						<span class="sub">
							<?php echo $owned_by; ?> | <?php echo $role; ?> <?php
							if ($setup)
							{
								echo ' | ' . $setup;
							}
							else if ($row->state == 0)
							{
								echo ' | ' . Lang::txt('MOD_MYPROJECTS_STATUS_SUSPENDED');
							}
							?>
							<?php if ($row->newactivity && $row->state == 1 && !$setup) { ?>
								<span class="s-new"><?php echo $row->newactivity; ?></span>
							<?php } ?>
						</span>
					</li>
				<?php
			}
			?>
		</ul>
	<?php } else { ?>
		<p><em><?php echo Lang::txt('MOD_MYPROJECTS_NO_PROJECTS'); ?></em></p>
	<?php } ?>

	<?php if ($this->total > $this->limit) { ?>
		<p class="note">
			<?php echo Lang::txt('MOD_MYPROJECTS_YOU_HAVE_MORE', $this->limit, $this->total, Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=projects')); ?>
		</p>
	<?php } ?>
</div>