<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

defined( '_JEXEC' ) or die('Restricted access');

$this->css();

$rows = array();
foreach ($this->unapprovedPages as $unapprovedPage)
{
	$gidNumber = $unapprovedPage->get('gidNumber');
	if (!isset($rows[$gidNumber]['pages']))
	{
		$rows[$gidNumber]['pages'] = 1;
		continue;
	}
	$rows[$gidNumber]['pages']++;
}
foreach ($this->unapprovedModules as $unapprovedModule)
{
	$gidNumber = $unapprovedModule->get('gidNumber');
	if (!isset($rows[$gidNumber]['modules']))
	{
		$rows[$gidNumber]['modules'] = 1;
		continue;
	}
	$rows[$gidNumber]['modules']++;
}
?>
<div class="<?php echo $this->module->module; ?>">
	<table class="adminlist grouppages-list">
		<thead>
			<tr>
				<td class="title" width="60%">
					<strong><?php echo JText::_('MOD_GROUPPAGES_COL_GROUP'); ?></strong>
				</td>
				<td class="title">
					<strong><?php echo JText::_('MOD_GROUPPAGES_COL_PAGES'); ?></strong>
				</td>
				<td class="title">
					<strong><?php echo JText::_('MOD_GROUPPAGES_COL_MODULES'); ?></strong>
				</td>
			</tr>
		</thead>
		<tbody>
			<?php if (count($rows) > 0) : ?>
				<?php foreach ($rows as $gidNumber => $row) : ?>
					<tr>
						<td>
							<?php
								$group = \Hubzero\User\Group::getInstance($gidNumber);
								echo $group->get('description');
							?>
						</td>
						<td>
							<a class="page" href="index.php?option=com_groups&amp;gid=<?php echo $group->get('cn'); ?>&amp;controller=pages">
								<?php echo (isset($row['pages'])) ? $row['pages'] : 0; ?>
							</a>
						</td>
						<td>
							<a class="module" href="index.php?option=com_groups&amp;gid=<?php echo $group->get('cn'); ?>&amp;controller=modules">
								<?php echo (isset($row['modules'])) ? $row['modules'] : 0; ?>
							</a>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr>
					<td colspan="3">
						<em><?php echo JText::_('MOD_GROUPPAGES_NO_RESULTS'); ?></em>
					</td>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>