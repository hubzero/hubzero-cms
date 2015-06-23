<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$cls = 'even';

$this->css();
?>
<h3 class="section-header">
	<?php echo Lang::txt('PLG_RESOURCES_VERSIONS'); ?>
</h3>
<?php if ($this->rows) { ?>
	<table class="resource-versions">
		<thead>
			<tr>
				<th><?php echo Lang::txt('PLG_RESOURCES_VERSIONS_VERSION'); ?></th>
				<th><?php echo Lang::txt('PLG_RESOURCES_VERSIONS_RELEASED'); ?></th>
				<th><?php echo Lang::txt('PLG_RESOURCES_VERSIONS_DOI_HANDLE'); ?></th>
				<th><?php echo Lang::txt('PLG_RESOURCES_VERSIONS_PUBLISHED'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach ($this->rows as $v)
		{
			$handle = '';

			if (isset($v->doi) && $v->doi && $this->tconfig->get('doi_shoulder'))
			{
				$handle = 'doi:' . $this->tconfig->get('doi_shoulder') . '/' . strtoupper($v->doi);
				$handle = '<a href="' . $this->tconfig->get('doi_resolve', 'http://dx.doi.org/') . $handle . '">' . $handle . '</a>';
			}
			else if (isset($v->doi_label) && $v->doi_label)
			{
				$handle = 'doi:10254/' . $this->tconfig->get('doi_prefix') . $this->resource->id . '.' . $v->doi_label;
				$handle = '<a href="http://hdl.handle.net/' . $handle . '">' . $handle . '</a>';
			}

			$cls = (($cls == 'even') ? 'odd' : 'even');
		?>
			<tr class="<?php echo $cls; ?>">
				<td>
					<?php echo ($v->version) ? '<a href="' . Route::url('index.php?option=' . $this->option . '&id=' . $this->resource->id . '&rev=' . $v->revision) . '">' . $v->version . '</a>' : 'N/A'; ?>
				</td>
				<td>
					<?php echo ($v->released && $v->released != '0000-00-00 00:00:00') ? Date::of($v->released)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) : 'N/A'; ?>
				</td>
				<td>
					<?php echo ($handle) ? $handle : 'N/A'; ?>
				</td>
				<td>
					<span class="version-state <?php echo ($v->state=='1') ? 'toolpublished' : 'toolunpublished'; ?>">
						<?php echo ($v->state=='1') ? Lang::txt('PLG_RESOURCES_VERSIONS_YES') : Lang::txt('PLG_RESOURCES_VERSIONS_NO'); ?>
					</span>
				</td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
<?php } else { ?>
	<p><?php echo Lang::txt('PLG_RESOURCES_VERSIONS_NO_VERIONS_FOUND'); ?></p>
<?php } ?>