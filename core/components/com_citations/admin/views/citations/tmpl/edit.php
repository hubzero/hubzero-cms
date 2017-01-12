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

$canDo = Components\Citations\Helpers\Permissions::getActions('citation');

$text = ($this->task == 'edit' ? Lang::txt('EDIT') : Lang::txt('NEW'));

Toolbar::title(Lang::txt('CITATION') . ': ' . $text, 'citation.png');
if ($canDo->get('core.edit'))
{
	Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('citation');

//set the escape callback
$this->setEscape("htmlentities");

//need to fix these fields
$author = html_entity_decode($this->row->author);
$author = (!preg_match('!\S!u', $author)) ? utf8_encode($author) : $author;

$ceditor = html_entity_decode($this->row->editor);
$ceditor = (!preg_match('!\S!u', $ceditor)) ? utf8_encode($ceditor) : $ceditor;

$title = html_entity_decode($this->row->title);
$title = (!preg_match('!\S!u', $title)) ? utf8_encode($title) : $title;

$booktitle = html_entity_decode($this->row->booktitle);
$booktitle = (!preg_match('!\S!u', $booktitle)) ? utf8_encode($booktitle) : $booktitle;

$short_title = html_entity_decode($this->row->short_title);
$short_title = (!preg_match('!\S!u', $short_title)) ? utf8_encode($short_title) : $short_title;

$journal = html_entity_decode($this->row->journal);
$journal = (!preg_match('!\S!u', $journal)) ? utf8_encode($journal) : $journal;
?>

<script type="text/javascript" src="<?php echo Request::root(); ?>core/components/com_citations/site/assets/js/citations.js"></script>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.getElementById('adminForm');

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	// form field validation
	//if (form.title.value == '') {
	//	alert('<?php echo Lang::txt('CITATION_MUST_HAVE_TITLE'); ?>');
	//} else {
		submitform(pressbutton);
	//}
}
</script>
<?php
	if ($this->getError())
	{
		echo '<p class="error">' . $this->getError() . '</p>';
	}
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('DETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="type"><?php echo Lang::txt('TYPE'); ?>:</label><br />
					<select name="citation[type]" id="type">
						<?php foreach ($this->types as $t) : ?>
							<?php $sel = ($t['id'] == $this->row->type) ? 'selected="selected"' : ''; ?>
							<option <?php echo $sel; ?> value="<?php echo $t['id']; ?>"><?php echo $this->escape(stripslashes($t['type_title'])); ?> (<?php echo $this->escape($t['type']); ?>)</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="grid">
					<div class="col span6">
						<div class="input-wrap">
							<label for="cite"><?php echo Lang::txt('CITE_KEY'); ?>:</label><br />
							<input type="text" name="citation[cite]" id="cite" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->cite)); ?>" />
							<span class="hint"><?php echo Lang::txt('CITE_KEY_EXPLANATION'); ?></span>
						</div>
					</div>
					<div class="col span6">
						<div class="input-wrap">
							<label for="ref_type"><?php echo Lang::txt('REF_TYPE'); ?>:</label><br />
							<input type="text" name="citation[ref_type]" id="ref_type" size="11" maxlength="50" value="<?php echo $this->escape(stripslashes($this->row->ref_type)); ?>" />
						</div>
					</div>
				</div>

				<div class="grid">
					<div class="col span6">
						<div class="input-wrap">
							<label for="date_submit"><?php echo Lang::txt('DATE_SUBMITTED'); ?>:</label><br />
							<input type="text" name="citation[date_submit]" id="date_submit" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->date_submit)); ?>" />
						</div>
					</div>
					<div class="col span6">
						<div class="input-wrap">
							<label for="date_accept"><?php echo Lang::txt('DATE_ACCEPTED'); ?>:</label><br />
							<input type="text" name="citation[date_accept]" id="date_accept" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->date_accept)); ?>" />
						</div>
					</div>
				</div>

				<div class="grid">
					<div class="col span6">
						<div class="input-wrap">
							<label for="date_publish"><?php echo Lang::txt('DATE_PUBLISHED'); ?>:</label><br />
							<input type="text" name="citation[date_publish]" id="date_publish" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->date_publish)); ?>" />
						</div>
					</div>
					<div class="col span6">
						<div class="grid">
							<div class="col span6">
								<div class="input-wrap">
									<label for="year"><?php echo Lang::txt('YEAR'); ?>:</label><br />
									<input type="text" name="citation[year]" id="year" size="4" maxlength="4" value="<?php echo $this->escape(stripslashes($this->row->year)); ?>" />
								</div>
							</div>
							<div class="col span6">
								<div class="input-wrap">
									<label for="month"><?php echo Lang::txt('MONTH'); ?>:</label><br />
									<input type="text" name="citation[month]" id="month" size="11" maxlength="50" value="<?php echo $this->escape(stripslashes($this->row->month)); ?>" />
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="input-wrap">
					<label for="author"><?php echo Lang::txt('AUTHORS'); ?>:</label><br />
					<input type="text" name="citation[author]" id="author" value="<?php echo $this->escape($author); ?>" />
				</div>
				<div class="input-wrap">
					<label for="author_address"><?php echo Lang::txt('COM_CITATIONS_FIELD_AUTHOR_ADDRESS'); ?>:</label><br />
					<input type="text" name="citation[author_address]" id="author_address" value="<?php echo $this->escape(stripslashes($this->row->author_address)); ?>" />
				</div>
				<div class="input-wrap">
					<label for="editor"><?php echo Lang::txt('EDITORS'); ?>:</label><br />
					<input type="text" name="citation[editor]" id="editor" maxlength="250" value="<?php echo $this->escape($ceditor); ?>" />
				</div>
				<div class="input-wrap">
					<label for="title"><?php echo Lang::txt('TITLE_CHAPTER'); ?>:</label><br />
					<input type="text" name="citation[title]" id="title" maxlength="250" value="<?php echo $this->escape($title); ?>" />
				</div>
				<div class="input-wrap">
					<label for="booktitle"><?php echo Lang::txt('BOOK_TITLE'); ?>:</label><br />
					<input type="text" name="citation[booktitle]" id="booktitle" maxlength="250" value="<?php echo $this->escape($booktitle); ?>" />
				</div>
				<div class="input-wrap">
					<label for="shorttitle"><?php echo Lang::txt('COM_CITATIONS_FIELD_SHORT_TITLE'); ?>:</label><br />
					<input type="text" name="citation[short_title]" id="shorttitle" maxlength="250" value="<?php echo $this->escape($short_title); ?>" />
				</div>
				<div class="input-wrap">
					<label for="journal"><?php echo Lang::txt('JOURNAL'); ?>:</label><br />
					<input type="text" name="citation[journal]" id="journal" maxlength="250" value="<?php echo $this->escape($journal); ?>" />
				</div>

				<div class="grid">
					<div class="col span6">
						<div class="input-wrap">
							<label for="volume"><?php echo Lang::txt('VOLUME'); ?>:</label><br />
							<input type="text" name="citation[volume]" id="volume" maxlength="11" value="<?php echo $this->escape(stripslashes($this->row->volume)); ?>" />
						</div>
					</div>
					<div class="col span6">
						<div class="input-wrap">
							<label for="number"><?php echo Lang::txt('ISSUE'); ?>:</label><br />
							<input type="text" name="citation[number]" id="number" maxlength="50" value="<?php echo $this->escape(stripslashes($this->row->number)); ?>" />
						</div>
					</div>
				</div>

				<div class="grid">
					<div class="col span6">
						<div class="input-wrap">
							<label for="pages"><?php echo Lang::txt('PAGES'); ?>:</label><br />
							<input type="text" name="citation[pages]" id="pages" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->pages)); ?>" />
						</div>
					</div>
					<div class="col span6">
						<div class="input-wrap">
							<label for="isbn"><?php echo Lang::txt('ISBN'); ?>:</label><br />
							<input type="text" name="citation[isbn]" id="isbn" maxlength="50" value="<?php echo $this->escape(stripslashes($this->row->isbn)); ?>" />
						</div>
					</div>
				</div>

				<div class="input-wrap">
					<label for="doi"><?php echo Lang::txt('DOI'); ?>:</label><br />
					<input type="text" name="citation[doi]" id="doi" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->doi)); ?>" />
				</div>
				<div class="input-wrap">
					<label for="callnumber"><?php echo Lang::txt('COM_CITATIONS_FIELD_CALL_NUMBER'); ?>:</label><br />
					<input type="text" name="citation[call_number]" id="callnumber" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->call_number)); ?>" />
				</div>
				<div class="input-wrap">
					<label for="accessionnumber"><?php echo Lang::txt('COM_CITATIONS_FIELD_ACCESSION_NUMBER'); ?>:</label><br />
					<input type="text" name="citation[accession_number]" id="accessionnumber" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->accession_number)); ?>" />
				</div>
				<div class="input-wrap">
					<label for="series"><?php echo Lang::txt('SERIES'); ?>:</label><br />
					<input type="text" name="citation[series]" id="series" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->series)); ?>" />
				</div>
				<div class="input-wrap">
					<label for="edition"><?php echo Lang::txt('EDITION'); ?>:</label><br />
					<input type="text" name="citation[edition]" id="edition" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->edition)); ?>" />
					<br /><span style="font-size: 90%;color:#aaa;"><?php echo Lang::txt('EDITION_EXPLANATION'); ?></span>
				</div>
				<div class="input-wrap">
					<label for="school"><?php echo Lang::txt('SCHOOL'); ?>:</label><br />
					<input type="text" name="citation[school]" id="school" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->school)); ?>" />
				</div>
				<div class="input-wrap">
					<label for="publisher"><?php echo Lang::txt('PUBLISHER'); ?>:</label><br />
					<input type="text" name="citation[publisher]" id="publisher" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->publisher)); ?>" />
				</div>
				<div class="input-wrap">
					<label for="institution"><?php echo Lang::txt('INSTITUTION'); ?>:</label><br />
					<input type="text" name="citation[institution]" id="institution" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->institution)); ?>" />
					<br /><span style="font-size: 90%;color:#aaa;"><?php echo Lang::txt('INSTITUTION_EXPLANATION'); ?></span>
				</div>
				<div class="input-wrap">
					<label for="address"><?php echo Lang::txt('ADDRESS'); ?>:</label><br />
					<input type="text" name="citation[address]" id="address" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->address)); ?>" />
				</div>
				<div class="input-wrap">
					<label for="location"><?php echo Lang::txt('LOCATION'); ?>:</label><br />
					<input type="text" name="citation[location]" id="location" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->location)); ?>" />
					<span style="font-size: 90%;color:#aaa;"><?php echo Lang::txt('LOCATION_EXPLANATION'); ?></span>
				</div>
				<div class="input-wrap">
					<label for="howpublished"><?php echo Lang::txt('PUBLISH_METHOD'); ?>:</label><br />
					<input type="text" name="citation[howpublished]" id="howpublished" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->howpublished)); ?>" />
					<br /><span style="font-size: 90%;color:#aaa;"><?php echo Lang::txt('PUBLISH_METHOD_EXPLANATION'); ?></span>
				</div>
				<div class="input-wrap">
					<label for="url"><?php echo Lang::txt('URL'); ?>:</label><br />
					<input type="text" name="citation[url]" id="url" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->url)); ?>" />
				</div>
				<div class="input-wrap">
					<label for="eprint"><?php echo Lang::txt('EPRINT'); ?>:</label><br />
					<input type="text" name="citation[eprint]" id="eprint" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->eprint)); ?>" />
					<br /><span class="hint"><?php echo Lang::txt('EPRINT_EXPLANATION'); ?></span>
				</div>
				<div class="input-wrap">
					<label for="abstract"><?php echo Lang::txt('COM_CITATIONS_FIELD_ABSTRACT'); ?>:</label><br />
					<?php echo $this->editor('citation[abstract]', stripslashes($this->row->abstract), 50, 10, 'abstract'); ?>
				</div>
				<div class="input-wrap">
					<label for="note"><?php echo Lang::txt('NOTES'); ?>:</label><br />
					<?php echo $this->editor('citation[note]', stripslashes($this->row->note), 50, 10, 'note'); ?>
				</div>
				<div class="input-wrap">
					<label for="keywords"><?php echo Lang::txt('COM_CITATIONS_FIELD_KEYWORDS'); ?>:</label><br />
					<?php echo $this->editor('citation[keywords]', stripslashes($this->row->keywords), 50, 10, 'keywords'); ?>
				</div>
				<div class="input-wrap">
					<label for="research_notes"><?php echo Lang::txt('COM_CITATIONS_FIELD_RESEARCH_NOTES'); ?>:</label><br />
					<?php echo $this->editor('citation[research_notes]', stripslashes($this->row->research_notes), 50, 10, 'research_notes'); ?>
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('MANUAL_FORMAT'); ?></span></legend>
				<div class="input-wrap">
					<label for="format_type"><?php echo Lang::txt('MANUAL_FORMAT_FORMAT'); ?>:</label>
					<select id="format_type" name="citation[format]">
						<option value="apa" <?php echo ($this->row->format == 'apa') ? 'selected="selected"' : ''; ?>><?php echo Lang::txt('MANUAL_FORMAT_FORMAT_APA'); ?></option>
						<option value="ieee" <?php echo ($this->row->format == 'ieee') ? 'selected="selected"' : ''; ?>><?php echo Lang::txt('MANUAL_FORMAT_FORMAT_IEEE'); ?></option>
					</select>
				</div>
				<div class="input-wrap">
					<label for="citation-formatted"><?php echo Lang::txt('MANUAL_FORMAT_CITATION'); ?>:</label>
					<?php echo $this->editor('citation[formatted]', stripslashes($this->row->formatted), 50, 10, 'formatted'); ?>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('CITATION_FOR'); ?></span></legend>

				<table class="admintable" id="assocs">
					<thead>
						<tr>
							<th><?php echo Lang::txt('TYPE'); ?></th>
							<th><?php echo Lang::txt('ID'); ?></th>
							<!--<th><?php //echo Lang::txt('TABLE'); ?></th>-->
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="2"><a href="#" onclick="HUB.Citations.addRow('assocs');return false;"><?php echo Lang::txt('ADD_A_ROW'); ?></a></td>
						</tr>
					</tfoot>
					<tbody>
						<?php
						$assocs = $this->assocs;
						$r = count($assocs);
						if ($r > 5) {
							$n = $r;
						} else {
							$n = 5;
						}
						for ($i=0; $i < $n; $i++)
						{
							if ($r == 0 || !isset($assocs[$i])) {
								$assocs[$i] = new stdClass;
								$assocs[$i]->id = NULL;
								$assocs[$i]->cid = NULL;
								$assocs[$i]->oid = NULL;
								$assocs[$i]->type = NULL;
								$assocs[$i]->tbl = NULL;
							}
							echo '  <tr>'."\n";
							//echo '   <td><input type="text" name="assocs['.$i.'][type]" value="'.$assocs[$i]->type.'" size="5" /></td>'."\n";
							echo '   <td><select name="assocs['.$i.'][tbl]">'."\n";
							echo ' <option value=""';
							echo ($assocs[$i]->tbl == '') ? ' selected="selected"': '';
							echo '>'.Lang::txt('SELECT').'</option>'."\n";
							//echo ' <option value="content"';
							//echo ($assocs[$i]->tbl == 'content') ? ' selected="selected"': '';
							//echo '>'.Lang::txt('CONTENT').'</option>'."\n";
							echo ' <option value="resource"';
							echo ($assocs[$i]->tbl == 'resource') ? ' selected="selected"': '';
							echo '>'.Lang::txt('RESOURCE').'</option>'."\n";
							echo ' <option value="publication"';
							echo ($assocs[$i]->tbl == 'publication') ? ' selected="selected"': '';
							echo '>'.Lang::txt('Publication').'</option>'."\n";
							//echo ' <option value="topic"';
							//echo ($assocs[$i]->tbl == 'topic') ? ' selected="selected"': '';
							//echo '>'.Lang::txt('TOPIC').'</option>'."\n";
							echo '</select></td>'."\n";
							echo '   <td><input type="text" name="assocs['.$i.'][oid]" value="'.$assocs[$i]->oid.'" size="5" />'."\n";
							echo '<input type="hidden" name="assocs['.$i.'][id]" value="'.$assocs[$i]->id.'" />'."\n";
							echo '<input type="hidden" name="assocs['.$i.'][cid]" value="'.$assocs[$i]->cid.'" /></td>'."\n";
							echo '  </tr>'."\n";
						}
						?>
					</tbody>
				</table>
			</fieldset>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('AFFILIATION'); ?></span></legend>

				<div class="input-wrap">
					<input type="checkbox" name="citation[affiliated]" id="affiliated" value="1"<?php if ($this->row->affiliated) { echo ' checked="checked"'; } ?> />
					<label for="affiliated"><?php echo Lang::txt('AFFILIATED_WITH_YOUR_ORG'); ?></label>
				</div>
				<div class="input-wrap">
					<input type="checkbox" name="citation[fundedby]" id="fundedby" value="1"<?php if ($this->row->fundedby) { echo ' checked="checked"'; } ?> />
					<label for="fundedby"><?php echo Lang::txt('FUNDED_BY_YOUR_ORG'); ?></label>
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('SCOPE'); ?></span></legend>
				<div class="input-wrap">
					<label for="scope"><?php echo Lang::txt('SCOPE'); ?></label>
					<select name="citation[scope]" id="scope">
						<option <?php echo ($this->row->scope == "hub") ? 'selected="selected"': ''; ?> value="hub">Hub</option>
						<option <?php echo ($this->row->scope == "group") ? 'selected="selected"': ''; ?> value="group">Group</option>
						<option <?php echo ($this->row->scope == "member") ? 'selected="selected"': ''; ?> value="member">Member</option>
					</select>
				</div>
				<div class="input-wrap">
					<label for="scope_id"><?php echo Lang::txt('SCOPE_ID'); ?></label>
					<input type="text" name="citation[scope_id]" id="scope_id" maxlength="10" value="<?php echo $this->escape(stripslashes($this->row->scope_id)); ?>" />
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_CITATIONS_OPTIONS'); ?></span></legend>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_CITATIONS_FIELD_SPONSORS_HINT'); ?>">
					<label for="field-sponsors"><?php echo Lang::txt('COM_CITATIONS_FIELD_SPONSORS'); ?></label>
					<select name="sponsors[]" id="field-sponsors" multiple="multiple">
						<!--<option value="sponsors">- Select Citation Sponsor &mdash;</option>-->
						<?php foreach ($this->sponsors as $s) : ?>
							<?php $sel = (in_array($s['id'], $this->row_sponsors)) ? 'selected="selected"': ''; ?>
							<option <?php echo $sel; ?> value="<?php echo $s['id']; ?>"><?php echo $this->escape(stripslashes($s['sponsor'])); ?></option>
						<?php endforeach; ?>
					</select>
					<span class="hint"><?php echo Lang::txt('COM_CITATIONS_FIELD_SPONSORS_HINT'); ?></span>
				</div>

				<?php if ($this->config->get('citation_allow_tags', 'no') == 'yes') : ?>
					<div class="input-wrap">
						<?php
							$t = array();
							foreach ($this->tags as $tag)
							{
								$t[] = stripslashes($tag['raw_tag']);
							}
						?>
						<label for="field-tags"><?php echo Lang::txt('COM_CITATIONS_FIELD_TAGS'); ?></label>
						<textarea name="tags" id="field-tags" rows="10"><?php echo implode(',', $t); ?></textarea>
					</div>
				<?php endif; ?>

				<?php if ($this->config->get('citation_allow_badges', 'no') == 'yes') : ?>
					<div class="input-wrap">
						<?php
							$b = array();
							foreach ($this->badges as $badge)
							{
								$b[] = stripslashes($badge['raw_tag']);
							}
						?>
						<label for="field-badges"><?php echo Lang::txt('COM_CITATIONS_FIELD_BADGES'); ?></label>
						<textarea name="badges" id="field-badges" rows="10"><?php echo implode(',', $b); ?></textarea>
					</div>
				<?php endif; ?>


				<div class="input-wrap">
					<label for="field-exclude"><?php echo Lang::txt('COM_CITATIONS_FIELD_EXCLUDE_FROM_EXPORT'); ?></label>
					<textarea name="exclude" id="field-exclude" rows="10"><?php echo $this->params->get('exclude'); ?></textarea>
				</div>

				<?php
					$rollovers = $this->config->get("citation_rollover", "no");
					$rollover  = $this->params->get("rollover");

					//check the the global setting
					if ($rollovers == 'yes')
					{
						$ckd = 'checked="checked"';
					}
					else
					{
						$ckd = '';
					}

					//check this citations setting
					if ($rollover == 1)
					{
						$ckd = 'checked="checked"';
					}
					elseif ($rollover == 0 && is_numeric($rollover))
					{
						$ckd = '';
					}
					else
					{
						$ckd = $ckd;
					}
				?>
				<div class="input-wrap">
					<input type="checkbox" name="rollover" id="rollover" value="1" <?php echo $ckd; ?> />
					<label for="rollover"><?php echo Lang::txt('COM_CITATIONS_FIELD_ABSTRACT_ROLLOVER'); ?></label>
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="citation[uid]" value="<?php echo $this->row->uid; ?>" />
	<input type="hidden" name="citation[created]" value="<?php echo $this->row->created; ?>" />
	<input type="hidden" name="citation[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="citation[published]" value="<?php echo $this->row->published; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
