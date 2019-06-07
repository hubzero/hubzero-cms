<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
//ddie(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));

$privacy = !$this->model->isPublic() ? Lang::txt('COM_PROJECTS_PRIVATE') : Lang::txt('COM_PROJECTS_PUBLIC');

$config = $this->model->config();

?>
<div id="plg-header">
	<h3 class="inform"><?php echo Lang::txt('COM_PROJECTS_PROJECT_INFO'); ?></h3>
</div>

<?php if ($this->model->access('manager') || ($this->model->access('content') && $config->get('edit_description'))) { ?>
	<p class="editing"><a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=edit&alias=' . $this->model->get('alias') . '&active=info'); ?>"><?php echo Lang::txt('COM_PROJECTS_EDIT_PROJECT'); ?></a></p>
<?php } ?>

<div id="basic_info">
	<table id="infotbl">
		<tbody>
			<tr>
				<th class="htd"><?php echo Lang::txt('COM_PROJECTS_TITLE'); ?></th>
				<td><?php echo $this->escape($this->model->get('title')); ?></td>
				<?php if ($config->get('grantinfo', 0) && $this->model->params->get( 'grant_title')) { ?>
					<td rowspan="5" class="grantinfo">
						<h4><?php echo Lang::txt('COM_PROJECTS_INFO_GRANTINFO'); ?></h4>
						<p>
							<span class="block"><span class="faded"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_TITLE'); ?>:</span> <?php echo $this->model->params->get( 'grant_title'); ?></span>
							<span class="block"><span class="faded"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_PI'); ?>:</span> <?php echo $this->model->params->get( 'grant_PI', 'N/A'); ?></span>
							<span class="block"><span class="faded"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_AGENCY'); ?>:</span> <?php echo $this->model->params->get( 'grant_agency', 'N/A'); ?></span>
							<span class="block"><span class="faded"><?php echo Lang::txt('COM_PROJECTS_SETUP_TERMS_GRANT_BUDGET'); ?>:</span> <?php echo $this->model->params->get( 'grant_budget', 'N/A'); ?></span>
							<?php if ($this->model->access('manager')) { ?>
								<a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=edit&alias=' . $this->model->get('alias') . '&active=settings'); ?>"><?php echo Lang::txt('COM_PROJECTS_EDIT_THIS'); ?></a>
							<?php } ?>
						</p>
					</td>
				<?php } ?>
			</tr>
			<tr>
				<th class="htd"><?php echo Lang::txt('COM_PROJECTS_ALIAS'); ?></th>
				<td><?php echo $this->model->get('alias'); ?></td>
			</tr>
			<tr>
				<th class="htd"><?php echo Lang::txt('COM_PROJECTS_ACCESS'); ?></th>
				<td><?php echo $privacy; ?> <?php if ($this->model->isPublic()) { ?><span class="mini faded">[<a href="<?php echo Route::url('index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&preview=1'); ?>"><?php echo Lang::txt('COM_PROJECTS_PREVIEW_PUBLIC_PROFILE'); ?></a>]</span><?php } ?></td>
			</tr>
			<tr>
				<th class="htd"><?php echo Lang::txt('COM_PROJECTS_CREATED'); ?></th>
				<td><?php echo $this->model->created('date'); ?></td>
			</tr>
			<tr>
				<th class="htd"><?php echo Lang::txt('COM_PROJECTS_OWNER'); ?></th>
				<td><?php echo $this->model->groupOwner() ? $this->model->groupOwner('description') : $this->model->owner('name'); ?></td>
			</tr>

			<?php
				// This is for the admin-defined project information
				if ($this->info)
				{
					foreach ($this->info as $field)
					{ ?>
						<tr>
							<th class="htd"><?php echo $field->label; ?></th>
							<td><?php echo $field->value; ?></td>
						</tr>
			<?php } // end foreach
					} // end if
				?>

			<?php if ($this->model->about('parsed')) { ?>
			<tr>
				<th class="htd"><?php echo Lang::txt('COM_PROJECTS_ABOUT'); ?></th>
				<td><?php echo $this->model->about('parsed'); ?></td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div><!-- / .basic info -->
