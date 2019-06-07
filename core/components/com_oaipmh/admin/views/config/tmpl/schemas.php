<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$canDo = \Components\Oaipmh\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('COM_OAIPMH_SETTINGS'), 'oaipmh');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_oaipmh', 500);
	Toolbar::spacer();
}
Toolbar::help('oaipmh');

$this->css();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('COM_OAIPMH_SCHEMA_NAME'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_OAIPMH_SCHEMA_PREFIX'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_OAIPMH_SCHEMA_FORMAT'); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($this->service->getSchemas() as $name) { ?>
			<tr>
				<?php
				$this->service->setSchema($name);
				$schema = $this->service->getSchema();
				?>
				<th scope="row"><?php echo $schema->name(); ?></th>
				<td><?php echo $schema->prefix(); ?></td>
				<td><code>&amp;metadataPrefix=<?php echo $schema->prefix(); ?></code></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>