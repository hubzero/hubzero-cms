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