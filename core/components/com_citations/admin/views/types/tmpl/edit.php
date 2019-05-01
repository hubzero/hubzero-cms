<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Citations\Helpers\Permissions::getActions('type');

$text = ($this->task == 'edittype' ? Lang::txt('EDIT') : Lang::txt('NEW'));

Toolbar::title(Lang::txt('CITATIONS') . ' ' . Lang::txt('CITATION_TYPES') . ': ' . $text, 'citation');
if ($canDo->get('core.edit'))
{
	Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('type');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();

$f = array(
	"cite"           => 'Cite Key',
	"ref_type"       => 'Ref Type',
	"date_submit"    => 'Date Submitted',
	"date_accept"    => 'Date Accepted',
	"date_publish"   => 'Date Published',
	"year"           => 'Year',
	"author"         => 'Authors',
	"author_address" => 'Author Address',
	"editor"         => 'Editors',
	"booktitle"      => 'Book Title',
	"shorttitle"     => 'Short Title',
	"journal"        => 'Journal',
	"volume"         => 'Volume',
	"issue"          => 'Issue/Number',
	"pages"          => 'Pages',
	"isbn"           => 'ISBN/ISSN',
	"doi"            => 'DOI',
	"callnumber"     => 'Call Number',
	"accessionnumber" => 'Accession Number',
	"series"         => 'Series',
	"edition"        => 'Edition',
	"school"         => 'School',
	"publisher"      => 'Publisher',
	"institution"    => 'Institution',
	"address"        => 'Address',
	"location"       => 'Location',
	"howpublished"   => 'How Published',
	"uri"            => 'URL',
	"eprint"         => 'E-print',
	"abstract"       => 'Abstract',
	"note"           => 'Text Snippet/ Notes',
	"keywords"       => 'Keywords',
	"research_notes" => 'Research Notes',
	"language"       => 'Language',
	"label"          => 'Label'
);
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form" class="form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('CITATION_TYPES'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-type"><?php echo Lang::txt('CITATION_TYPES_ALIAS'); ?></label><br />
					<input type="text" name="type[type]" id="field-type" value="<?php echo $this->escape(stripslashes($this->type->type)); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-type_title"><?php echo Lang::txt('CITATION_TYPES_TITLE'); ?></label><br />
					<input type="text" name="type[type_title]" id="field-type_title" value="<?php echo $this->escape(stripslashes($this->type->type_title)); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-type_desc"><?php echo Lang::txt('CITATION_TYPES_DESC'); ?></label><br />
					<textarea name="type[type_desc]" id="field-type_desc" rows="5" cols="58"><?php echo $this->escape(stripslashes($this->type->type_desc)); ?></textarea>
				</div>

				<div class="input-wrap">
					<label for="field-fields"><?php echo Lang::txt('CITATION_TYPES_FIELDS'); ?></label><br />
					<textarea name="type[fields]" id="field-fields" rows="20" cols="58"><?php echo $this->escape(stripslashes($this->type->fields)); ?></textarea>
					<span class="hint"><?php echo Lang::txt('CITATION_TYPES_FIELDS_HINT'); ?></span>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th scope="row"><?php echo Lang::txt('ID'); ?>:</th>
						<td>
							<?php echo ($this->type->id) ? $this->type->id : 0; ?>
							<input type="hidden" name="type[id]" value="<?php echo $this->type->id; ?>" />
						</td>
					</tr>
				</tbody>
			</table>

			<div class="data-wrap">
				<table class="admintable">
					<thead>
						<tr>
							<th scope="col"><?php echo Lang::txt('CITATION_TYPES_PLACEHOLDER'); ?></th>
							<th scope="col"><?php echo Lang::txt('CITATION_TYPES_FIELD'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($f as $k => $v) : ?>
							<tr>
								<td><?php echo $k; ?></td>
								<td><?php echo $v; ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
