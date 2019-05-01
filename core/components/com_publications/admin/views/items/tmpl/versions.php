<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

Toolbar::title(Lang::txt('COM_PUBLICATIONS_PUBLICATION_MANAGER') . ' - ' . Lang::txt('COM_PUBLICATIONS_PUBLICATION') . ': #' . $this->pub->id . ' - ' . Lang::txt('COM_PUBLICATIONS_VERSIONS'), 'publications');
Toolbar::spacer();
Toolbar::cancel();

?>

<?php if ($this->config->get('enabled') == 0) { ?>
	<p class="warning"><?php echo Lang::txt('COM_PUBLICATIONS_COMPONENT_DISABLED'); ?></p>
<?php } ?>
<p class="crumbs"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>"><?php echo Lang::txt('COM_PUBLICATIONS_PUBLICATION_MANAGER'); ?></a> &raquo; <a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id[]=' . $this->pub->id); ?>"><?php echo Lang::txt('COM_PUBLICATIONS_PUBLICATION') . ' #' . $this->pub->id; ?></a> &raquo; <?php echo Lang::txt('COM_PUBLICATIONS_VERSIONS'); ?></p>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th class="tdmini"></th>
				<th class="tdmini"><?php echo Lang::txt('COM_PUBLICATIONS_VERSION'); ?></th>
				<th><?php echo Lang::txt('COM_PUBLICATIONS_TITLE'); ?></th>
				<th><?php echo Lang::txt('COM_PUBLICATIONS_STATUS'); ?></th>
				<th><?php echo Lang::txt('COM_PUBLICATIONS_DOI'); ?></th>
				<th><?php echo ucfirst(Lang::txt('COM_PUBLICATIONS_OPTIONS')); ?></th>
			</tr>
		 </thead>
		<tbody>
		<?php
		$k = 0;
			foreach ($this->versions as $v) {
			// Get DOI
			$doi = $v->doi ? 'doi:' . $v->doi : '';
			$doi_notice = $doi ? $doi : Lang::txt('COM_PUBLICATIONS_NA');

			// Version status
			$status = $this->pub->getStatusName($v->state);
			$class  = $this->pub->getStatusCss($v->state);
			$date   = $this->pub->getStatusDate($v);

			?>
			<tr class="mini <?php if ($v->main == 1) { echo ' vprime'; } ?>">
				<td class="centeralign"><?php echo $v->version_number ? $v->version_number : ''; ?></td>
				<td><?php echo $v->version_label; ?></td>
				<td><?php echo $v->title; ?></td>
				<td class="v-status">
					<span class="<?php echo $class; ?>"><?php echo $status; ?></span>
					<?php if ($date) { echo '<span class="block faded">'.$date.'</span>';  } ?>
				</td>
				<td><?php echo $doi_notice; ?></td>
				<td><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id[]=' . $this->pub->id . '&version=' . $v->version_number); ?>"><?php echo Lang::txt('COM_PUBLICATIONS_MANAGE_VERSION'); ?></a></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>
