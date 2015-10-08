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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Citations\Helpers\Permissions::getActions('type');

$text = ($this->task == 'edittype' ? Lang::txt('EDIT') : Lang::txt('NEW'));

Toolbar::title(Lang::txt('CITATIONS') . ' ' . Lang::txt('CITATION_TYPES') . ': ' . $text, 'citation.png');
if ($canDo->get('core.edit'))
{
	Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('type');

$id     = NULL;
$type   = NULL;
$title  = NULL;
$desc   = NULL;
$fields = NULL;
if ($this->type)
{
	$id     = $this->type->id;
	$type   = $this->escape(stripslashes($this->type->type));
	$title  = $this->escape(stripslashes($this->type->type_title));
	$desc   = $this->escape(stripslashes($this->type->type_desc));
	$fields = $this->escape(stripslashes($this->type->fields));
}

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

<script type="text/javascript">
function submitbutton(pressbutton)
{
	return submitform(pressbutton);
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('CITATION_TYPES'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-type"><?php echo Lang::txt('CITATION_TYPES_ALIAS'); ?></label><br />
					<input type="text" name="type[type]" id="field-type" value="<?php echo $type; ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-type_title"><?php echo Lang::txt('CITATION_TYPES_TITLE'); ?></label><br />
					<input type="text" name="type[type_title]" id="field-type_title" value="<?php echo $title; ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-type_desc"><?php echo Lang::txt('CITATION_TYPES_DESC'); ?></label><br />
					<textarea name="type[type_desc]" id="field-type_desc" rows="5" cols="58"><?php echo $desc; ?></textarea>
				</div>

				<div class="input-wrap">
					<label for="field-fields"><?php echo Lang::txt('CITATION_TYPES_FIELDS'); ?></label><br />
					<textarea name="type[fields]" id="field-fields" rows="20" cols="58"><?php echo $fields; ?></textarea>
					<span class="hint"><?php echo Lang::txt('CITATION_TYPES_FIELDS_HINT'); ?></span>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th class="key"><?php echo Lang::txt('ID'); ?>:</th>
						<td>
							<?php echo ($id ? $id : 0); ?>
							<input type="hidden" name="type[id]" value="<?php echo $id; ?>" />
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
