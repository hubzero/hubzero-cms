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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('CITATION') . ': ' . Lang::txt('CITATION_STATS'), 'citation.png');
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('YEAR'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('AFFILIATED'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('NONAFFILIATED'); ?></th>
				<th scope="col"><?php echo Lang::txt('TOTAL'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($this->stats as $year => $amt) { ?>
			<tr>
				<th><?php echo $year; ?></th>
				<td class="priority-2"><?php echo $amt['affiliate']; ?></td>
				<td class="priority-2"><?php echo $amt['non-affiliate']; ?></td>
				<td><?php echo (intval($amt['affiliate']) + intval($amt['non-affiliate'])); ?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
</form>