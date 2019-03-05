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

// no direct access
defined('_HZEXEC_') or die();

// Push the module CSS to the template
$this->css()
     ->js();
?>
<div<?php echo ($this->moduleclass) ? ' class="' . $this->moduleclass . '"' : '';?>>
	<ul class="tab_titles">
		<li rel="recentgroups<?php echo $this->module->id; ?>" class="tab_title<?php if (count($this->recentgroups) > 0) { echo ' active'; } ?>"><?php echo Lang::txt('MOD_MYGROUPS_RECENT'); ?></li>
		<li rel="allgroups<?php echo $this->module->id; ?>" class="tab_title<?php if (!count($this->recentgroups)) { echo ' active'; } ?>"><?php echo Lang::txt('MOD_MYGROUPS_ALL'); ?></li>
	</ul>

	<div id="recentgroups<?php echo $this->module->id; ?>" class="tab_panel<?php if (count($this->recentgroups) > 0) { echo ' active'; } ?>">
		<?php if (count($this->recentgroups) > 0) { ?>
			<ul class="compactlist mygroups">
				<?php
				foreach ($this->recentgroups as $group)
				{
					if ($group->published)
					{
						$status = $this->getStatus($group);

						require $this->getLayoutPath('_item');
					}
				}
				?>
			</ul>
		<?php } else { ?>
			<p><em><?php echo Lang::txt('MOD_MYGROUPS_NO_RECENT_GROUPS'); ?></em></p>
		<?php } ?>
	</div>

	<div id="allgroups<?php echo $this->module->id; ?>" class="tab_panel<?php if (!count($this->recentgroups)) { echo ' active'; } ?>">
		<?php
		$total = count($this->allgroups);
		if ($total > 0) { ?>
			<ul class="compactlist mygroups">
				<?php
				$i = 0;
				foreach ($this->allgroups as $group)
				{
					if ($group->published && $i < $this->limit)
					{
						$status = $this->getStatus($group);

						require $this->getLayoutPath('_item');

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

	<?php if ($this->params->get('button_show_add', 1)) { ?>
		<ul class="module-nav grouped">
			<li>
				<a class="icon-plus" href="<?php echo Route::url('index.php?option=com_groups&task=new'); ?>"><?php echo Lang::txt('MOD_MYGROUPS_NEW_GROUP'); ?></a></li>
			</li>
			<?php /*if ($this->params->get('button_show_all', 1)) { ?>
				<p><a class="icon-browse" href="<?php echo Route::url('index.php?option=com_groups&task=browse'); ?>"><?php echo Lang::txt('MOD_MYGROUPS_ALL_GROUPS'); ?></a></p>
			<?php }*/ ?>
		</ul>
	<?php } ?>
</div>
