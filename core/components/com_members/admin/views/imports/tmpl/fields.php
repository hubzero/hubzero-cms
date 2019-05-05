<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('import')
     ->js('import');

Request::setVar('hidemainmenu', 1);

$canDo = \Components\Members\Helpers\Admin::getActions('component');

// set title
$title  = ($this->import->get('id')) ? Lang::txt('COM_MEMBERS_IMPORT_TITLE_EDIT') : Lang::txt('COM_MEMBERS_IMPORT_TITLE_ADD');

Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . $title, 'import');
if ($canDo->get('core.admin'))
{
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<?php foreach ($this->getErrors() as $error) : ?>
	<p class="error"><?php echo $error; ?></p>
<?php endforeach; ?>

<form action="<?php echo Route::url('index.php?option=com_members&controller=import&task=save'); ?>" method="post" name="adminForm" id="item-form" enctype="multipart/form-data" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span7">

			<p class="warning"><?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELDSET_MAPPING_REQUIRED'); ?></p>

			<?php
			$this->view('_fieldmap')
				->set('import', $this->import)
				->display();
			?>

		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_ID'); ?></th>
						<td><?php echo $this->import->get('id'); ?></td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_CREATEDBY'); ?></th>
						<td>
							<?php
								if ($created_by = User::getInstance($this->import->get('created_by')))
								{
									echo $created_by->get('name');
								}
							?>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_CREATEDON'); ?></th>
						<td>
							<time datetime="<?php echo $this->import->get('created_at'); ?>"><?php
								echo Date::of($this->import->get('created_at'))->toLocal('m/d/Y @ g:i a');
							?></time>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="import[id]" value="<?php echo $this->import->get('id'); ?>" />

	<?php echo Html::input('token'); ?>
</form>