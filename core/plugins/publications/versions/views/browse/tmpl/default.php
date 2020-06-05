<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

// Build pub url
$route = $this->publication->project_provisioned == 1
	? 'index.php?option=com_publications&task=submit'
	: 'index.php?option=com_projects&alias=' . $this->publication->project_alias . '&active=publications';
$url = Route::url($route . '&pid=' . $this->publication->id);

?>
<h3 id="versions">
	<?php echo Lang::txt('PLG_PUBLICATION_VERSIONS'); ?>
</h3>
<?php if ($this->authorized && $this->contributable) { ?>
	<p class="info statusmsg"><?php echo Lang::txt('PLG_PUBLICATION_VERSIONS_ONLY_PUBLIC_SHOWN'); ?>
		<a href="<?php echo $url . '?action=versions'; ?>"><?php echo Lang::txt('PLG_PUBLICATION_VERSIONS_VIEW_ALL'); ?></a>
	</p>
<?php } ?>
<?php if ($this->versions && count($this->versions) > 0) { ?>
	<table class="resource-versions">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('PLG_PUBLICATION_VERSIONS_VERSION'); ?></th>
				<th scope="col"><?php echo Lang::txt('PLG_PUBLICATION_VERSIONS_RELEASED'); ?></th>
				<th scope="col"><?php echo Lang::txt('PLG_PUBLICATION_VERSIONS_DOI_HANDLE'); ?></th>
				<th scope="col"><?php echo Lang::txt('PLG_PUBLICATION_VERSIONS_STATUS'); ?></th>
				<th scope="col"></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$cls = 'even';

			foreach ($this->versions as $v)
			{
				$handle = ($v->doi) ? $v->doi : '';

				$cls = (($cls == 'even') ? 'odd' : 'even');
				?>
				<tr class="<?php echo $cls; ?>">
					<td <?php if ($v->version_number == $this->publication->version_number) { echo 'class="active"'; }  ?>><?php echo $v->version_label; ?></td>
					<td><?php echo ($v->published_up && $v->published_up!='0000-00-00 00:00:00') ? Date::of($v->published_up)->toLocal('M d, Y') : Lang::txt('COM_PUBLICATIONS_NA'); ?></td>
					<td><?php echo $v->doi ? $v->doi : Lang::txt('COM_PUBLICATIONS_NA'); ?></td>
					<td class="<?php echo $v->state == 1 ? 'state_published' : 'state_unpublished'; ?>"><?php echo $v->state == 1 ? Lang::txt('PLG_PUBLICATION_VERSIONS_PUBLISHED') : Lang::txt('PLG_PUBLICATION_VERSIONS_UNPUBLISHED'); ?></td>
					<td><a href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->publication->id . '&v=' . $v->version_number); ?>"><?php echo Lang::txt('PLG_PUBLICATION_VERSIONS_VIEW'); ?></a></td>
				</tr>
				<?php
			}

			if ($this->publication->master_doi)
			{
				$cls = (($cls == 'even') ? 'odd' : 'even');
				?>
				<tr class="<?php echo $cls; ?>">
					<td><?php echo Lang::txt('COM_PUBLICATION_VERSIONS_MASTER'); ?></td>
					<td><?php echo Lang::txt('COM_PUBLICATIONS_NA'); ?></td>
					<td><?php echo $this->publication->master_doi; ?></td>
					<td class="state_unpublished"><?php echo Lang::txt('PLG_PUBLICATION_VERSIONS_PUBLISHED'); ?></td>
					<td><a href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->publication->id . '&task=main'); ?>"><?php echo Lang::txt('PLG_PUBLICATION_VERSIONS_VIEW'); ?></a></td>
				</tr>
				<?php
			}
			?>
		</tbody>
	</table>
<?php } else { ?>
	<p class="nocontent"><?php echo Lang::txt('PLG_PUBLICATION_VERSIONS_NO_VERIONS_FOUND'); ?></p>
<?php }
