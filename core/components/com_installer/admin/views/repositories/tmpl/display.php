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

// No direct access.
defined('_HZEXEC_') or die();

use Hubzero\Utility\Arr;

Toolbar::title(Lang::txt('COM_INSTALLER_TITLE_REPOSITORIES'));

$canDo = \Components\Installer\Admin\Helpers\Installer::getActions();
if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_installer');
	Toolbar::divider();
}

Toolbar::addNew();

Html::behavior('tooltip');

$this->css();
$filterstring = "";
?>
<nav role="navigation" class="sub sub-navigation">
	<ul>
		<li>
			<a<?php if ($this->controller == 'packages') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=packages'); ?>"><?php echo Lang::txt('COM_INSTALLER_PACKAGES_PACKAGES'); ?></a>
		</li>
		<li>
			<a<?php if ($this->controller == 'repositories') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=repositories'); ?>"><?php echo Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORIES'); ?></a>
		</li>
	</ul>
</nav>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="updateRepositoryForm">
	<table id="tktlist" class="adminlist">
		<thead>
			<tr>
				<th scope="col">Repository</th>
				<th scope="col priority-3">Type</th>
				<th scope="col">Description</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="4">
					<?php
					echo $this->pagination($this->total, $this->filters['start'], $this->filters['limit']);
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php foreach ($this->repositories as $alias => $config) : ?>
				<?php
				?>
				<tr>
					<td> 
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&alias=' . $alias); ?>">
							<?php echo Arr::getValue($config, 'name', ''); ?>
						</a><br>
							<strong><?php echo $alias; ?></strong>
					</td>
					<td> <?php echo Arr::getValue($config, 'type', ''); ?></td>
					<td> <?php echo Arr::getValue($config, 'description', ''); ?></td>
					<td> <?php echo Arr::getValue($config, 'url', ''); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>
