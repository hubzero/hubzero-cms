<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

$canDo = \Components\Installer\Admin\Helpers\Installer::getActions();

Toolbar::title(Lang::txt('COM_INSTALLER_HEADER_' . $this->getName()), 'install.png');

if ($canDo->get('core.admin'))
{
	Toolbar::custom('languages.install', 'upload', 'upload', 'COM_INSTALLER_TOOLBAR_INSTALL', true, false);
	Toolbar::custom('languages.find', 'refresh', 'refresh', 'COM_INSTALLER_TOOLBAR_FIND_LANGUAGES', false, false);
	Toolbar::custom('languages.purge', 'purge', 'purge', 'JTOOLBAR_PURGE_CACHE', false, false);
	Toolbar::divider();
	Toolbar::preferences('com_installer');
	Toolbar::divider();
	Toolbar::help('languages');
}

Html::behavior('multiselect');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$ver = new \JVersion;

?>
<form action="<?php echo Route::url('index.php?option=com_installer&controller=languages');?>" method="post" name="adminForm" id="adminForm">

	<?php if (count($this->items) || $this->escape($this->state->get('filter.search'))) : ?>
	<?php echo $this->loadTemplate('filter'); ?>
	<div class="width-100 fltlft">
			<table class="adminlist">
				<thead>
					<tr>
						<th>
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
						</th>
						<th class="nowrap">
							<?php echo Html::grid('sort', 'COM_INSTALLER_HEADING_NAME', 'name', $listDirn, $listOrder); ?>
						</th>
						<th class="center">
							<?php echo Lang::txt('JVERSION'); ?>
						</th>
						<th>
							<?php echo Lang::txt('COM_INSTALLER_HEADING_TYPE'); ?>
						</th>
						<th>
							<?php echo Lang::txt('COM_INSTALLER_HEADING_DETAILS_URL'); ?>
						</th>
						<th>
							<?php echo Html::grid('sort', 'COM_INSTALLER_HEADING_ID', 'update_id', $listDirn, $listOrder); ?>
						</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="6">
							<?php echo $this->pagination->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
					<?php foreach ($this->items as $i => $language) :
					?>
					<?php if (substr($language->version, 0, 3) == $ver->RELEASE) :
					?>
						<tr class="row<?php echo $i%2; ?>">
							<td>
								<?php echo Html::grid('id', $i, $language->update_id, false, 'cid'); ?>
							</td>
							<td>
								<?php echo $language->name; ?>
							</td>
							<td class="center">
								<?php echo $language->version; ?>
							</td>
							<td class="center">
								<?php echo Lang::txt('COM_INSTALLER_TYPE_' . strtoupper($language->type)); ?>
							</td>
							<td>
								<?php echo $language->detailsurl; ?>
							</td>
							<td>
								<?php echo $language->update_id; ?>
							</td>
						</tr>
					<?php endif; ?>
					<?php endforeach; ?>
				</tbody>
			</table>
	</div>
	<?php else : ?>
		<p class="nowarning"><?php echo Lang::txt('COM_INSTALLER_MSG_LANGUAGES_NOLANGUAGES'); ?></p>
	<?php endif; ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo Html::input('token'); ?>
</form>
