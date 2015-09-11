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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

use Components\Time\Models\Permissions;

if (!isset($this->permissions))
{
	$this->permissions = new Permissions($this->option);
}

?>

<div class="com_time_navigation">
	<ul class="com_time_menu">
		<?php
			foreach (array('overview', 'records', 'tasks', 'hubs', 'reports') as $tab)
			{
				if (!$this->permissions->can('view.' . $tab))
				{
					continue;
				}
				$cls  = ($this->controller == $tab) ? ' active' : '';
				$link = Route::url('index.php?option=' . $this->option . '&controller=' . $tab);

				echo "<li class=\"{$tab}{$cls}\"><a data-title=\"" . ucfirst($tab) . "\" href=\"{$link}\">" . ucfirst($tab) . "</a></li>";
			}
		?>
	</ul>
	<div class="com_time_quick_links">
		<ul>
			<?php if ($this->permissions->can('new.records')) : ?>
				<li>
					<a class="new-record" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=records&task=new'); ?>">
						<?php echo Lang::txt('COM_TIME_NEW_RECORD'); ?>
					</a>
				</li>
			<?php endif; ?>
			<?php if ($this->permissions->can('new.tasks')) : ?>
				<li>
					<a class="new-task" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=tasks&task=new'); ?>">
						<?php echo Lang::txt('COM_TIME_NEW_TASK'); ?>
					</a>
				</li>
			<?php endif; ?>
			<?php if ($this->permissions->can('new.hubs')) : ?>
				<li>
					<a class="new-hub" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=hubs&task=new'); ?>">
						<?php echo Lang::txt('COM_TIME_NEW_HUB'); ?>
					</a>
				</li>
			<?php endif; ?>
		</ul>
	</div>
</div>