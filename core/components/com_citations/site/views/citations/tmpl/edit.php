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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();

$allow_tags = $this->config->get("citation_allow_tags","no");
$allow_badges = $this->config->get("citation_allow_badges","no");

$fieldset_label = ($allow_tags == "yes") ? "Tags" : "";
$fieldset_label = ($allow_badges == "yes") ? "Badges" : $fieldset_label;
$fieldset_label = ($allow_tags == "yes" && $allow_badges == "yes") ? "Tags and Badges" : $fieldset_label;

$t = array();
$b = array();

foreach ($this->tags as $tag)
{
	$t[] = $tag['raw_tag'];
}

foreach ($this->badges as $badge)
{
	$b[] = $badge['raw_tag'];
}

$tags_list   = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags','', implode(",",$t))));
$badges_list = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'badges', 'actags1','', implode(",",$b))));

//get the referrer
$backLink = Route::url('index.php?option=' . $this->option);
if (isset($_SERVER['HTTP_REFERER']) && filter_var($_SERVER['HTTP_REFERER'], FILTER_VALIDATE_URL))
{
	$backLink = $_SERVER['HTTP_REFERER'];
}

$pid = Request::getInt('publication', 0);

?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-browse browse btn" href="<?php echo $backLink ?>"><?php echo Lang::txt('COM_CITATIONS_BACK'); ?></a>
		</p>
	</div>
</header><!-- / #content-header -->

<section class="main section">
	<?php if ($pid) { ?>
		<h3><?php echo Lang::txt('COM_CITATIONS_CITATION_FOR'); ?> <?php echo Lang::txt('COM_CITATIONS_PUBLICATION') . ' #' . $pid; ?></h3>
	<?php } ?>
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>
	<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" id="hubForm" class="add-citation">
		<div class="explaination">
			<p><?php echo Lang::txt('COM_CITATIONS_DETAILS_DESC'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('COM_CITATIONS_DETAILS'); ?></legend>

			<div class="grid">
				<div class="col span6">
					<label for="type">
						<?php echo Lang::txt('COM_CITATIONS_TYPE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
						<select name="fields[type]" id="type">
							<option value=""> <?php echo Lang::txt('COM_CITATIONS_TYPE_SELECT'); ?></option>
							<?php
								foreach ($this->types as $t)
								{
									$sel = ($this->row->type == $t['id']) ? "selected=\"selected\"" : "";
									echo "<option {$sel} value=\"{$t['id']}\">{$t['type_title']}</option>";
								}
							?>
						</select>
					</label>
				</div>
				<div class="col span6 omega">
					<label for="cite">
						<?php echo Lang::txt('COM_CITATIONS_CITE_KEY'); ?>:
						<input type="text" name="fields[cite]" id="cite" size="30" maxlength="250" value="<?php echo $this->escape($this->row->cite); ?>" />
						<span class="hint"><?php echo Lang::txt('COM_CITATIONS_CITE_KEY_EXPLANATION'); ?></span>
					</label>
				</div>
			</div>

			<label for="ref_type">
				<?php echo Lang::txt('COM_CITATIONS_REF_TYPE'); ?>:
				<input type="text" name="fields[ref_type]" id="ref_type" size="11" maxlength="50" value="<?php echo $this->escape($this->row->ref_type); ?>" />
			</label>

			<div class="grid">
				<div class="col span4">
					<label for="date_submit">
						<?php echo Lang::txt('COM_CITATIONS_DATE_SUBMITTED'); ?>:
						<input type="text" name="fields[date_submit]" id="date_submit" size="30" maxlength="250" value="<?php echo $this->escape($this->row->date_submit); ?>" />
						<span class="hint"><?php echo Lang::txt('COM_CITATIONS_DATE_HINT'); ?></span>
					</label>
				</div>
				<div class="col span4">
					<label for="date_accept">
						<?php echo Lang::txt('COM_CITATIONS_DATE_ACCEPTED'); ?>:
						<input type="text" name="fields[date_accept]" id="date_accept" size="30" maxlength="250" value="<?php echo $this->escape($this->row->date_accept); ?>" />
						<span class="hint"><?php echo Lang::txt('COM_CITATIONS_DATE_HINT'); ?></span>
					</label>
				</div>
				<div class="col span4 omega">
					<label for="date_publish">
						<?php echo Lang::txt('COM_CITATIONS_DATE_PUBLISHED'); ?>:
						<input type="text" name="fields[date_publish]" id="date_publish" size="30" maxlength="250" value="<?php echo $this->escape($this->row->date_publish); ?>" />
						<span class="hint"><?php echo Lang::txt('COM_CITATIONS_DATE_HINT'); ?></span>
					</label>
				</div>
			</div>

			<div class="grid">
				<div class="col span6">
					<label for="year">
						<?php echo Lang::txt('COM_CITATIONS_YEAR'); ?>:
						<input type="text" name="fields[year]" id="year" size="4" maxlength="4" value="<?php echo $this->escape($this->row->year); ?>" />
					</label>
				</div>
				<div class="col span6 omega">
					<label for="month">
						<?php echo Lang::txt('COM_CITATIONS_MONTH'); ?>:
						<input type="text" name="fields[month]" id="month" size="11" maxlength="50" value="<?php echo $this->escape($this->row->month); ?>" />
					</label>
				</div>
			</div>

			<label for="author">
				<?php echo Lang::txt('COM_CITATIONS_AUTHORS'); ?>:
				<input type="text" name="fields[author]" id="author" size="30" value="<?php echo $this->escape($this->row->author); ?>" />
				<span class="hint"><?php echo Lang::txt('COM_CITATIONS_AUTHORS_HINT'); ?></span>
			</label>

			<label for="authoraddress">
				<?php echo Lang::txt('COM_CITATIONS_AUTHOR_ADDRESS'); ?>:
				<input type="text" name="fields[author_address]" id="authoraddress" size="30" value="<?php echo $this->escape($this->row->author_address); ?>" />
			</label>

			<label for="editor">
				<?php echo Lang::txt('COM_CITATIONS_EDITORS'); ?>:
				<input type="text" name="fields[editor]" id="editor" size="30" maxlength="250" value="<?php echo $this->escape($this->row->editor); ?>" />
				<span class="hint"><?php echo Lang::txt('COM_CITATIONS_AUTHORS_HINT'); ?></span>
			</label>

			<label for="title">
				<?php echo Lang::txt('COM_CITATIONS_TITLE_CHAPTER'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
				<input type="text" name="fields[title]" id="title" size="30" maxlength="250" value="<?php echo $this->escape($this->row->title); ?>" />
			</label>

			<label for="booktitle">
				<?php echo Lang::txt('COM_CITATIONS_BOOK_TITLE'); ?>:
				<input type="text" name="fields[booktitle]" id="booktitle" size="30" maxlength="250" value="<?php echo $this->escape($this->row->booktitle); ?>" />
			</label>

			<label for="shorttitle">
				<?php echo Lang::txt('COM_CITATIONS_SHORT_TITLE'); ?>:
				<input type="text" name="fields[short_title]" id="shorttitle" size="30" maxlength="250" value="<?php echo $this->escape($this->row->short_title); ?>" />
			</label>

			<label for="journal">
				<?php echo Lang::txt('COM_CITATIONS_JOURNAL'); ?>:
				<input type="text" name="fields[journal]" id="journal" size="30" maxlength="250" value="<?php echo $this->escape($this->row->journal); ?>" />
			</label>

			<div class="grid">
				<div class="col span4">
					<label for="volume">
						<?php echo Lang::txt('COM_CITATIONS_VOLUME'); ?>:
						<input type="text" name="fields[volume]" id="volume" size="11" maxlength="11" value="<?php echo $this->escape($this->row->volume); ?>" />
					</label>
				</div>
				<div class="col span4">
					<label for="number">
						<?php echo Lang::txt('COM_CITATIONS_ISSUE'); ?>:
						<input type="text" name="fields[number]" id="number" size="11" maxlength="50" value="<?php echo $this->escape($this->row->number); ?>" />
					</label>
				</div>
				<div class="col span4 omega">
					<label for="pages">
						<?php echo Lang::txt('COM_CITATIONS_PAGES'); ?>:
						<input type="text" name="fields[pages]" id="pages" size="11" maxlength="250" value="<?php echo $this->escape($this->row->pages); ?>" />
					</label>
				</div>
			</div>

			<div class="grid">
				<div class="col span6">
					<label for="isbn">
						<?php echo Lang::txt('COM_CITATIONS_ISBN'); ?>:
						<input type="text" name="fields[isbn]" id="isbn" size="11" maxlength="50" value="<?php echo $this->escape($this->row->isbn); ?>" />
					</label>
				</div>
				<div class="col span6 omega">
					<label for="doi">
						<abbr title="<?php echo Lang::txt('COM_CITATIONS_DOI_FULL'); ?>"><?php echo Lang::txt('COM_CITATIONS_DOI'); ?></abbr>:
						<input type="text" name="fields[doi]" id="doi" size="30" maxlength="250" value="<?php echo $this->escape($this->row->doi); ?>" />
					</label>
				</div>
			</div>

			<div class="grid">
				<div class="col span6">
					<label for="callnumber">
						<?php echo Lang::txt('COM_CITATIONS_CALL_NUMBER'); ?>:
						<input type="text" name="fields[call_number]" id="callnumber" value="<?php echo $this->escape($this->row->call_number); ?>" />
					</label>
				</div>
				<div class="col span6 omega">
					<label for="accessionnumber">
						<?php echo Lang::txt('COM_CITATIONS_ACCESSION_NUMBER'); ?>:
						<input type="text" name="fields[accession_number]" id="accessionnumber"  value="<?php echo $this->escape($this->row->accession_number); ?>" />
					</label>
				</div>
			</div>

			<label for="series">
				<?php echo Lang::txt('COM_CITATIONS_SERIES'); ?>:
				<input type="text" name="fields[series]" id="series" size="30" maxlength="250" value="<?php echo $this->escape($this->row->series); ?>" />
			</label>

			<label for="edition">
				<?php echo Lang::txt('COM_CITATIONS_EDITION'); ?>:
				<input type="text" name="fields[edition]" id="edition" size="30" maxlength="250" value="<?php echo $this->escape($this->row->edition); ?>" />
				<span class="hint"><?php echo Lang::txt('COM_CITATIONS_EDITION_EXPLANATION'); ?></span>
			</label>

			<label for="school">
				<?php echo Lang::txt('COM_CITATIONS_SCHOOL'); ?>:
				<input type="text" name="fields[school]" id="school" size="30" maxlength="250" value="<?php echo $this->escape($this->row->school); ?>" />
			</label>

			<label for="publisher">
				<?php echo Lang::txt('COM_CITATIONS_PUBLISHER'); ?>:
				<input type="text" name="fields[publisher]" id="publisher" size="30" maxlength="250" value="<?php echo $this->escape($this->row->publisher); ?>" />
			</label>

			<label for="institution">
				<?php echo Lang::txt('COM_CITATIONS_INSTITUTION'); ?>:
				<input type="text" name="fields[institution]" id="institution" size="30" maxlength="250" value="<?php echo $this->escape($this->row->institution); ?>" />
				<span class="hint"><?php echo Lang::txt('COM_CITATIONS_INSTITUTION_EXPLANATION'); ?></span>
			</label>

			<label for="address">
				<?php echo Lang::txt('COM_CITATIONS_ADDRESS'); ?>:
				<input type="text" name="fields[address]" id="address" size="30" maxlength="250" value="<?php echo $this->escape($this->row->address); ?>" />
			</label>

			<label for="location">
				<?php echo Lang::txt('COM_CITATIONS_LOCATION'); ?>:
				<input type="text" name="fields[location]" id="location" size="30" maxlength="250" value="<?php echo $this->escape($this->row->location); ?>" />
				<span class="hint"><?php echo Lang::txt('COM_CITATIONS_LOCATION_EXPLANATION'); ?></span>
			</label>

			<label for="howpublished">
				<?php echo Lang::txt('COM_CITATIONS_PUBLISH_METHOD'); ?>:
				<input type="text" name="fields[howpublished]" id="howpublished" size="30" maxlength="250" value="<?php echo $this->escape($this->row->howpublished); ?>" />
				<span class="hint"><?php echo Lang::txt('COM_CITATIONS_PUBLISH_METHOD_EXPLANATION'); ?></span>
			</label>

			<label for="url">
				<?php echo Lang::txt('COM_CITATIONS_URL'); ?>:
				<input type="text" name="fields[url]" id="url" size="30" maxlength="250" value="<?php echo $this->escape($this->row->url); ?>" />
			</label>

			<label for="eprint">
				<?php echo Lang::txt('COM_CITATIONS_EPRINT'); ?>:
				<input type="text" name="fields[eprint]" id="eprint" size="30" maxlength="250" value="<?php echo $this->escape($this->row->eprint); ?>" />
				<span class="hint"><?php echo Lang::txt('COM_CITATIONS_EPRINT_EXPLANATION'); ?></span>
			</label>

			<label for="abstract">
				<?php echo Lang::txt('COM_CITATIONS_ABSTRACT'); ?>:
				<textarea name="fields[abstract]" id="abstract" rows="8" cols="10"><?php echo $this->escape(stripslashes($this->row->abstract)); ?></textarea>
			</label>

			<label for="note">
				<?php echo Lang::txt('COM_CITATIONS_NOTES'); ?>:
				<textarea name="fields[note]" id="note" rows="8" cols="10"><?php echo $this->escape(stripslashes($this->row->note)); ?></textarea>
			</label>

			<label for="keywords">
				<?php echo Lang::txt('COM_CITATIONS_KEYWORDS'); ?>:
				<textarea name="fields[keywords]" id="keywords" rows="8" cols="10"><?php echo $this->escape(stripslashes($this->row->keywords)); ?></textarea>
			</label>

			<label for="research_notes">
				<?php echo Lang::txt('COM_CITATIONS_RESEARCH_NOTES'); ?>:
				<textarea name="fields[research_notes]" id="research_notes" rows="8" cols="10"><?php echo $this->escape(stripslashes($this->row->research_notes)); ?></textarea>
			</label>

			<div class="group twoup">
				<label for="language">
					<?php echo Lang::txt('COM_CITATIONS_LANGUAGE'); ?>:
					<input type="text" name="fields[language]" id="language" size="11" maxlength="50" value="<?php echo $this->escape($this->row->language); ?>" />
				</label>

				<label for="label">
					<?php echo Lang::txt('COM_CITATIONS_LABEL'); ?>:
					<input type="text" name="fields[label]" id="label" size="30" maxlength="250" value="<?php echo $this->escape($this->row->label); ?>" />
				</label>
			</div>
		</fieldset><div class="clear"></div>

		<fieldset>
			<legend><?php echo Lang::txt('COM_CITATIONS_MANUALLY_FORMAT'); ?>:</legend>
			<p class="warning"><?php echo Lang::txt('COM_CITATIONS_MANUALLY_FORMAT_HINT'); ?></p>
			<label for="format">
				<?php echo Lang::txt('COM_CITATIONS_MANUALLY_FORMAT_FORMAT'); ?>:
				<select id="format" name="fields[format]">
					<option value="apa" <?php echo ($this->row->format == 'apa') ? 'selected="selected"' : ''; ?>><?php echo Lang::txt('COM_CITATIONS_MANUALLY_FORMAT_APA'); ?></option>
					<option value="ieee" <?php echo ($this->row->format == 'ieee') ? 'selected="selected"' : ''; ?>><?php echo Lang::txt('COM_CITATIONS_MANUALLY_FORMAT_IEEE'); ?></option>
				</select>
			</label>
			<label for="formatted">
				<?php echo Lang::txt('COM_CITATIONS_MANUALLY_FORMAT_CITATION'); ?>:
				<textarea name="fields[formatted]" id="formatted" rows="8" cols="10"><?php echo $this->escape(stripslashes($this->row->formatted)); ?></textarea>
			</label>
		</fieldset><div class="clear"></div>

		<?php if ($allow_tags == "yes" || $allow_badges == "yes") : ?>
			<fieldset>
				<legend><?php echo $fieldset_label; ?></legend>
				<?php if ($allow_tags == "yes") : ?>
					<label>
						<?php echo Lang::txt('COM_CITATIONS_TAGS'); ?>:
						<?php
							if (count($tags_list) > 0) {
								echo $tags_list[0];
							} else {
								echo '<input type="text" name="tags" value="' . $tags . '" />';
							}
						?>
						<span class="hint"><?php echo Lang::txt('COM_CITATIONS_TAGS_HINT'); ?></span>
					</label>
				<?php endif; ?>

				<?php if ($allow_badges == "yes") : ?>
					<label class="badges">
						<?php echo Lang::txt('COM_CITATIONS_BADGES'); ?>:
						<?php
							if (count($badges_list) > 0) {
								echo $badges_list[0];
							} else {
								echo '<input type="text" name="badges" value="' . $badges . '" />';
							}
						?>
						<span class="hint"><?php echo Lang::txt('COM_CITATIONS_BADGES_HINT'); ?></span>
					</label>
				<?php endif; ?>
			</fieldset><div class="clear"></div>
		<?php endif; ?>

		<?php if ($pid) { ?>
			<input type="hidden" name="assocs[0][oid]" value="<?php echo $pid; ?>" />
			<input type="hidden" name="assocs[0][tbl]" value="publication" />
			<input type="hidden" name="assocs[0][id]" value="0" />
		<?php } else { ?>
			<div class="explaination">
				<p><?php echo Lang::txt('COM_CITATIONS_ASSOCIATION_DESC'); ?></p>
			</div>
			<fieldset>
				<legend><?php echo Lang::txt('COM_CITATIONS_CITATION_FOR'); ?></legend>

				<div class="field-wrap">
					<table id="assocs">
						<thead>
							<tr>
								<th><?php echo Lang::txt('COM_CITATIONS_TYPE'); ?></th>
								<th><?php echo Lang::txt('COM_CITATIONS_ID'); ?></th>
								<th><?php echo Lang::txt('COM_CITATIONS_CONTEXT'); ?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="3"><a href="#" class="btn icon-add" onclick="HUB.Citations.addRow('assocs');return false;"><?php echo Lang::txt('COM_CITATIONS_ADD_A_ROW'); ?></a></td>
							</tr>
						</tfoot>
						<tbody>
						<?php
								$r = count($this->assocs);
								if ($r > 5) {
									$n = $r;
								} else {
									$n = 5;
								}
								for ($i=0; $i < $n; $i++)
								{
									if ($r == 0 || !isset($this->assocs[$i]))
									{
										$this->assocs[$i] = new stdClass;
										$this->assocs[$i]->id   = null;
										$this->assocs[$i]->cid  = $this->row->id;
										$this->assocs[$i]->oid  = null;
										$this->assocs[$i]->type = null;
										$this->assocs[$i]->tbl  = null;
									}

									echo "\t\t\t".'  <tr>'."\n";
									echo "\t\t\t".'   <td><select name="assocs['.$i.'][tbl]">'."\n";
									echo ' <option value=""';
									echo ($this->assocs[$i]->tbl == '') ? ' selected="selected"': '';
									echo '>'.Lang::txt('COM_CITATIONS_SELECT').'</option>'."\n";
									echo ' <option value="resource"';
									echo ($this->assocs[$i]->tbl == 'resource') ? ' selected="selected"': '';
									echo '>'.Lang::txt('COM_CITATIONS_RESOURCE').'</option>'."\n";
									echo ' <option value="publication"';
									echo ($this->assocs[$i]->tbl == 'publication') ? ' selected="selected"': '';
									echo '>'.Lang::txt('COM_CITATIONS_PUBLICATION').'</option>'."\n";
									echo '</select></td>'."\n";
									echo "\t\t\t".'<td><input type="text" name="assocs['.$i.'][oid]" value="'.$this->escape($this->assocs[$i]->oid).'" />'."\n";
									echo "\t\t\t\t".'<input type="hidden" name="assocs['.$i.'][id]" value="'.$this->assocs[$i]->id.'" />'."\n";
									echo "\t\t\t\t".'<input type="hidden" name="assocs['.$i.'][cid]" value="'.$this->assocs[$i]->cid.'" /></td>'."\n";
									echo "\t\t\t".'<td><select name="assocs['.$i.'][type]">'."\n";
									echo ' <option value=""';
									echo ($this->assocs[$i]->type == '') ? ' selected="selected"': '';
									echo '>'.Lang::txt('COM_CITATIONS_SELECT').'</option>'."\n";
									echo ' <option value="references"';
									echo ($this->assocs[$i]->type == 'references') ? ' selected="selected"': '';
									echo '>'.Lang::txt('COM_CITATIONS_CONTEXT_REFERENCES').'</option>'."\n";
									echo ' <option value="referencedby"';
									echo ($this->assocs[$i]->type == 'referencedby') ? ' selected="selected"': '';
									echo '>'.Lang::txt('COM_CITATIONS_CONTEXT_REFERENCEDBY').'</option>'."\n";
									echo '</select></td>'."\n";
									echo "\t\t\t".'</tr>'."\n";
								}
						?>
						</tbody>
					</table>
				</div>
			</fieldset><div class="clear"></div>
		<?php } ?>

		<fieldset>
			<legend><?php echo Lang::txt('COM_CITATIONS_AFFILIATION'); ?></legend>

			<label for="affiliated">
				<input type="checkbox" class="option" name="fields[affiliated]" id="affiliated" value="1"<?php if ($this->row->affiliated) { echo ' checked="checked"'; } ?> />
				<?php echo Lang::txt('COM_CITATIONS_AFFILIATED_WITH_YOUR_ORG'); ?>
			</label>

			<label for="fundedby">
				<input type="checkbox" class="option" name="fields[fundedby]" id="fundedby" value="1"<?php if ($this->row->fundedby) { echo ' checked="checked"'; } ?> />
				<?php echo Lang::txt('COM_CITATIONS_FUNDED_BY_YOUR_ORG'); ?>
			</label>

			<input type="hidden" name="fields[uid]" value="<?php echo $this->row->uid; ?>" />
			<input type="hidden" name="fields[created]" value="<?php echo $this->escape($this->row->created); ?>" />
			<input type="hidden" name="fields[scope]" value="<?php echo $this->escape($this->row->scope); ?>" />
			<input type="hidden" name="fields[scope_id]" value="<?php echo $this->escape($this->row->scope_id); ?>" />
			<input type="hidden" name="fields[published]" value="<?php echo ($this->row->id ? $this->escape($this->row->published) : 1); ?>" />
			<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="save" />

			<?php echo Html::input('token'); ?>
		</fieldset>
		<div class="clear"></div>

		<p class="submit">
			<input type="submit" class="btn btn-success" name="create" value="<?php echo Lang::txt('COM_CITATIONS_SAVE'); ?>" />

			<a class="btn btn-secondary" href="<?php echo Route::url('index.php?option=' . $this->option); ?>">
				<?php echo Lang::txt('COM_CITATIONS_CANCEL'); ?>
			</a>
		</p>
	</form>
</section>