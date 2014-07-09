<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$canDo = CitationsHelper::getActions('type');

$text = ($this->task == 'edittype' ? JText::_('EDIT') : JText::_('NEW'));

JToolBarHelper::title(JText::_('CITATIONS') . ' ' . JText::_('CITATION_TYPES') . ': ' . $text, 'citation.png');
if ($canDo->get('core.edit'))
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('type');

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

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('CITATION_TYPES'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-type"><?php echo JText::_('CITATION_TYPES_ALIAS'); ?></label><br />
				<input type="text" name="type[type]" id="field-type" value="<?php echo $type; ?>" />
			</div>

			<div class="input-wrap">
				<label for="field-field-type_title"><?php echo JText::_('CITATION_TYPES_TITLE'); ?></label><br />
				<input type="text" name="type[type_title]" id="field-type_title" value="<?php echo $title; ?>" />
			</div>

			<div class="input-wrap">
				<label for="field-type_desc"><?php echo JText::_('CITATION_TYPES_DESC'); ?></label><br />
				<textarea name="type[type_desc]" id="field-type_desc" rows="5" cols="58"><?php echo $desc; ?></textarea>
			</div>

			<div class="input-wrap">
				<label for="field-fields"><?php echo JText::_('CITATION_TYPES_FIELDS'); ?></label><br />
				<textarea name="type[fields]" id="field-fields" rows="20" cols="58"><?php echo $fields; ?></textarea>
				<span class="hint"><?php echo JText::_('CITATION_TYPES_FIELDS_HINT'); ?></span>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th class="key"><?php echo JText::_('ID'); ?>:</th>
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
						<th><?php echo JText::_('CITATION_TYPES_PLACEHOLDER'); ?></th>
						<th><?php echo JText::_('CITATION_TYPES_FIELD'); ?></th>
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
	<div class="clr"></div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo JHTML::_('form.token'); ?>
</form>
