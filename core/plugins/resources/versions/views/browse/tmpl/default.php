<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
				$handle = 'doi:' . (isset($v->doi_shoulder) ? $v->doi_shoulder : $this->tconfig->get('doi_shoulder')) . '/' . strtoupper($v->doi);
				$handle = '<a href="' . $this->tconfig->get('doi_resolve', 'https://doi.org/') . $handle . '">' . $handle . '</a>';
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
<?php }
