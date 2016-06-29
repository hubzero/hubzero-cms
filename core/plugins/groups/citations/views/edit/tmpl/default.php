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
 * @author    Shawn Rice <zooley@purdue.edu>, Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
//defined('_HZEXEC_') or die();

$this->css()->js();

$base = 'index.php?option=com_groups&cn=' . $this->group->get('cn') . '&active=citations';

//$allow_tags = $this->config->get("citation_allow_tags", "no");
$allow_tags = "no";
$allow_badges = "no";
//$allow_badges = $this->config->get("citation_allow_badges", "no");

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

$tags_list = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags', '', implode(",", $t))));
$badges_list = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'badges', 'actags1', '', implode(",", $b))));

//get the referrer
$backLink = Route::url('index.php?option=' . $this->_name);
if (isset($_SERVER['HTTP_REFERER']) && filter_var($_SERVER['HTTP_REFERER'], FILTER_VALIDATE_URL))
{
	$backLink = $_SERVER['HTTP_REFERER'];
}

?>

<div id="browsebox" class="frm">
	<!--  <section class="main section">  -->
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>

	<form action="<?php echo Route::url($base . '?action=save'); ?>" method="post" id="hubForm" class="add-citation">
		<div class="explaination">
			<p id="applicableFields"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_DETAILS_DESC'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('PLG_GROUPS_CITATIONS_DETAILS'); ?></legend>

			<div class="grid">
				<div class="col span6">
					<label for="type">
						<?php echo Lang::txt('PLG_GROUPS_CITATIONS_TYPE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
						<select name="type" id="type">
							<option value=""> <?php echo Lang::txt('PLG_GROUPS_CITATIONS_TYPE_SELECT'); ?></option>
							<?php
							foreach ($this->types as $t)
							{
								$sel = ($this->row->type == $t->id) ? "selected=\"selected\"" : "";
								echo "<option {$sel} value=\"{$t->id}\">{$t->type_title}</option>";
							}
							?>
						</select>
					</label>
				</div>
				<div class="col span6 omega">
					<label for="cite">
						<?php echo Lang::txt('PLG_GROUPS_CITATIONS_CITE_KEY'); ?>:
						<input type="text" name="cite" id="cite" size="30" maxlength="250" value="<?php echo $this->escape($this->row->cite); ?>" />
						<span class="hint"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_CITE_KEY_EXPLANATION'); ?></span>
					</label>
				</div>
			</div>

			<label for="ref_type">
				<?php echo Lang::txt('PLG_GROUPS_CITATIONS_REF_TYPE'); ?>:
				<input type="text" name="ref_type" id="ref_type" size="11" maxlength="50" value="<?php echo $this->escape($this->row->ref_type); ?>" />
			</label>

			<div class="grid">
				<div class="col span4">
					<label for="date_submit">
						<?php echo Lang::txt('PLG_GROUPS_CITATIONS_DATE_SUBMITTED'); ?>:
						<input type="text" name="date_submit" id="date_submit" size="30" maxlength="250" value="<?php echo $this->escape($this->row->date_submit); ?>" />
						<span class="hint"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_DATE_HINT'); ?></span>
					</label>
				</div>
				<div class="col span4">
					<label for="date_accept">
						<?php echo Lang::txt('PLG_GROUPS_CITATIONS_DATE_ACCEPTED'); ?>:
						<input type="text" name="date_accept" id="date_accept" size="30" maxlength="250" value="<?php echo $this->escape($this->row->date_accept); ?>" />
						<span class="hint"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_DATE_HINT'); ?></span>
					</label>
				</div>
				<div class="col span4 omega">
					<label for="date_publish">
						<?php echo Lang::txt('PLG_GROUPS_CITATIONS_DATE_PUBLISHED'); ?>:
						<input type="text" name="date_publish" id="date_publish" size="30" maxlength="250" value="<?php echo $this->escape($this->row->date_publish); ?>" />
						<span class="hint"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_DATE_HINT'); ?></span>
					</label>
				</div>
			</div>

			<div class="grid">
				<div class="col span6">
					<label for="year">
						<?php echo Lang::txt('PLG_GROUPS_CITATIONS_YEAR'); ?>:
						<input type="text" name="year" id="year" size="4" maxlength="4" value="<?php echo $this->escape($this->row->year); ?>" />
					</label>
				</div>
				<div class="col span6 omega">
					<label for="month">
						<?php echo Lang::txt('PLG_GROUPS_CITATIONS_MONTH'); ?>:
						<input type="text" name="month" id="month" size="11" maxlength="50" value="<?php echo $this->escape($this->row->month); ?>" />
					</label>
				</div>
			</div>

			<fieldset class="author-manager" data-add="<?php echo Route::url('index.php?option=com_citations&controller=authors&citation=' . $this->row->id . '&task=add&' . JUtility::getToken() . '=1'); ?>" data-update="<?php echo Route::url('index.php?option=com_citations&controller=authors&citation=' . $this->row->id . '&task=update&' . JUtility::getToken() . '=1'); ?>" data-list="<?php echo Route::url('index.php?option=com_citations&controller=authors&citation=' . $this->row->id . '&task=display&' . JUtility::getToken() . '=1'); ?>">
					<div class="grid">
						<div class="col span10">
							<label for="field-author">
								<?php echo Lang::txt('PLG_GROUPS_CITATIONS_AUTHORS'); ?>
								<?php	

								$mc = Event::trigger('hubzero.onGetMultiEntry', array(array('members', 'author', 'field-author', '', (isset($this->authorString) ? $this->authorString : ''))));
								if (count($mc) > 0) {
									echo $mc[0];
								} else { ?>
									<input type="text" name="author" id="field-author" value="" />
								<?php } ?>
							</label>
						</div>
						<div class="col span2 omega">
							<button class="btn btn-success add-author"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_ADD'); ?></button>
						</div>
					</div>

					<div class="field-wrap author-list">
						<?php if (isset($this->authors) && count($this->authors)) { ?>
							<?php foreach ($this->authors as $i => $this->author) { ?>
								<p class="citation-author" id="author_<?php echo $this->escape($this->author->id); ?>">
									<span class="author-handle">
									</span>
									<span class="author-name">
										<?php echo $this->escape($this->author->author); ?>
									</span>
									<span class="author-description">
										<input type="hidden" name="author[<?php echo $i; ?>][id]" value="<?php echo $this->escape($this->author->id); ?>" />
										<a class="delete" data-id="<?php echo $this->escape($this->author->id); ?>" href="<?php echo Route::url('index.php?option=com_citations&controller=authors&task=remove&citation=' . $this->row->id . '&author=' . $this->author->id . '&' . JUtility::getToken() . '=1'); ?>">
											<?php echo Lang::txt('JDELETE'); ?>
										</a>
									</span>
								</p>
							<?php } ?>
						<?php } else { ?>
							<p class="author-instructions"><?php //echo Lang::txt('PLG_GROUPS_CITATIONS_AUTHORS_HINT'); ?></p>
						<?php } ?>
					</div>
					</fieldset>
<?php /*
			<label for="author">
				<?php echo Lang::txt('PLG_GROUPS_CITATIONS_AUTHORS'); ?>:
				<input type="text" name="author" id="author" size="30" value="<?php echo $this->escape($this->row->author); ?>" />
				<span class="hint"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_AUTHORS_HINT'); ?></span>
			</label>
			*/ ?>
			<label for="authoraddress">
				<?php echo Lang::txt('PLG_GROUPS_CITATIONS_AUTHOR_ADDRESS'); ?>:
				<input type="text" name="author_address" id="authoraddress" size="30" value="<?php echo $this->escape($this->row->author_address); ?>" />
			</label>
			<label for="editor">
				<?php echo Lang::txt('PLG_GROUPS_CITATIONS_EDITORS'); ?>:
				<input type="text" name="editor" id="editor" size="30" maxlength="250" value="<?php echo $this->escape($this->row->editor); ?>" />
				<span class="hint"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_AUTHORS_HINT'); ?></span>
			</label>
			<label for="title">
				<?php echo Lang::txt('PLG_GROUPS_CITATIONS_TITLE_CHAPTER'); ?>:  <span class="required"><?php echo Lang::txt('JREQUIRED'); ?></span>
				<input type="text" name="title" id="title" size="30" maxlength="250" value="<?php echo $this->escape($this->row->title); ?>" />
			</label>
			<label for="booktitle">
				<?php echo Lang::txt('PLG_GROUPS_CITATIONS_BOOK_TITLE'); ?>:
				<input type="text" name="booktitle" id="booktitle" size="30" maxlength="250" value="<?php echo $this->escape($this->row->booktitle); ?>" />
			</label>

			<label for="shorttitle">
				<?php echo Lang::txt('PLG_GROUPS_CITATIONS_SHORT_TITLE'); ?>:
				<input type="text" name="short_title" id="shorttitle" size="30" maxlength="250" value="<?php echo $this->escape($this->row->short_title); ?>" />
			</label>
			<label for="journal">
				<?php echo Lang::txt('PLG_GROUPS_CITATIONS_JOURNAL'); ?>:
				<input type="text" name="journal" id="journal" size="30" maxlength="250" value="<?php echo $this->escape($this->row->journal); ?>" />
			</label>

			<div class="grid">
				<div class="col span4">
					<label for="volume">
						<?php echo Lang::txt('PLG_GROUPS_CITATIONS_VOLUME'); ?>:
						<input type="text" name="volume" id="volume" size="11" maxlength="11" value="<?php echo $this->escape($this->row->volume); ?>" />
					</label>
				</div>
				<div class="col span4">
					<label for="number">
						<?php echo Lang::txt('PLG_GROUPS_CITATIONS_ISSUE'); ?>:
						<input type="text" name="number" id="number" size="11" maxlength="50" value="<?php echo $this->escape($this->row->number); ?>" />
					</label>
				</div>
				<div class="col span4 omega">
					<label for="pages">
						<?php echo Lang::txt('PLG_GROUPS_CITATIONS_PAGES'); ?>:
						<input type="text" name="pages" id="pages" size="11" maxlength="250" value="<?php echo $this->escape($this->row->pages); ?>" />
					</label>
				</div>
			</div>
			<div class="grid">
				<div class="col span6">
					<label for="isbn">
						<?php echo Lang::txt('PLG_GROUPS_CITATIONS_ISBN'); ?>:
						<input type="text" name="isbn" id="isbn" size="11" maxlength="50" value="<?php echo $this->escape($this->row->isbn); ?>" />
					</label>
				</div>
				<div class="col span6 omega">
					<label for="doi">
						<abbr title="<?php echo Lang::txt('PLG_GROUPS_CITATIONS_DOI_FULL'); ?>">
						<?php echo Lang::txt('PLG_GROUPS_CITATIONS_DOI'); ?></abbr>:
						<input type="text" name="doi" id="doi" size="30" maxlength="250" value="<?php echo $this->escape($this->row->doi); ?>" />
					</label>
				</div>
			</div>

			<div class="grid">
				<div class="col span6">
					<label for="callnumber">
						<?php echo Lang::txt('PLG_GROUPS_CITATIONS_CALL_NUMBER'); ?>:
						<input type="text" name="call_number" id="callnumber" value="<?php echo $this->escape($this->row->call_number); ?>" />
					</label>
				</div>
				<div class="col span6 omega">
					<label for="accessionnumber">
						<?php echo Lang::txt('PLG_GROUPS_CITATIONS_ACCESSION_NUMBER'); ?>:
						<input type="text" name="accession_number" id="accessionnumber"  value="<?php echo $this->escape($this->row->accession_number); ?>" />
					</label>
				</div>
			</div>

			<label for="series">
				<?php echo Lang::txt('PLG_GROUPS_CITATIONS_SERIES'); ?>:
				<input type="text" name="series" id="series" size="30" maxlength="250" value="<?php echo $this->escape($this->row->series); ?>" />
			</label>

			<label for="edition">
				<?php echo Lang::txt('PLG_GROUPS_CITATIONS_EDITION'); ?>:
				<input type="text" name="edition" id="edition" size="30" maxlength="250" value="<?php echo $this->escape($this->row->edition); ?>" />
				<span class="hint"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_EDITION_EXPLANATION'); ?></span>
			</label>

			<label for="school">
				<?php echo Lang::txt('PLG_GROUPS_CITATIONS_SCHOOL'); ?>:
				<input type="text" name="school" id="school" size="30" maxlength="250" value="<?php echo $this->escape($this->row->school); ?>" />
			</label>

			<label for="publisher">
				<?php echo Lang::txt('PLG_GROUPS_CITATIONS_PUBLISHER'); ?>:
				<input type="text" name="publisher" id="publisher" size="30" maxlength="250" value="<?php echo $this->escape($this->row->publisher); ?>" />
			</label>

			<label for="institution">
				<?php echo Lang::txt('PLG_GROUPS_CITATIONS_INSTITUTION'); ?>:
				<input type="text" name="institution" id="institution" size="30" maxlength="250" value="<?php echo $this->escape($this->row->institution); ?>" />
				<span class="hint"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_INSTITUTION_EXPLANATION'); ?></span>
			</label>

			<label for="address">
				<?php echo Lang::txt('PLG_GROUPS_CITATIONS_ADDRESS'); ?>:
				<input type="text" name="address" id="address" size="30" maxlength="250" value="<?php echo $this->escape($this->row->address); ?>" />
			</label>

			<label for="location">
				<?php echo Lang::txt('PLG_GROUPS_CITATIONS_LOCATION'); ?>:
				<input type="text" name="location" id="location" size="30" maxlength="250" value="<?php echo $this->escape($this->row->location); ?>" />
				<span class="hint"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_LOCATION_EXPLANATION'); ?></span>
			</label>

			<label for="howpublished">
				<?php echo Lang::txt('PLG_GROUPS_CITATIONS_PUBLISH_METHOD'); ?>:
				<input type="text" name="howpublished" id="howpublished" size="30" maxlength="250" value="<?php echo $this->escape($this->row->howpublished); ?>" />
				<span class="hint"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_PUBLISH_METHOD_EXPLANATION'); ?></span>
			</label>

			<label for="uri">
				<?php echo Lang::txt('PLG_GROUPS_CITATIONS_URL'); ?>:
				<input type="text" name="uri" id="uri" size="30" maxlength="250" value="<?php echo $this->escape($this->row->url); ?>" />
			</label>

			<label for="eprint">
				<?php echo Lang::txt('PLG_GROUPS_CITATIONS_EPRINT'); ?>:
				<input type="text" name="eprint" id="eprint" size="30" maxlength="250" value="<?php echo $this->escape($this->row->eprint); ?>" />
				<span class="hint"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_EPRINT_EXPLANATION'); ?></span>
			</label>

			<label for="abstract">
				<?php echo Lang::txt('PLG_GROUPS_CITATIONS_ABSTRACT'); ?>:
				<textarea name="abstract" id="abstract" rows="8" cols="10"><?php echo $this->escape(stripslashes($this->row->abstract)); ?></textarea>
			</label>

			<label for="note">
				<?php echo Lang::txt('PLG_GROUPS_CITATIONS_NOTES'); ?>:
				<textarea name="note" id="note" rows="8" cols="10"><?php echo $this->escape(stripslashes($this->row->note)); ?></textarea>
			</label>

			<label for="keywords">
				<?php echo Lang::txt('PLG_GROUPS_CITATIONS_KEYWORDS'); ?>:
				<textarea name="keywords" id="keywords" rows="8" cols="10"><?php echo $this->escape(stripslashes($this->row->keywords)); ?></textarea>
			</label>

			<label for="research_notes">
				<?php echo Lang::txt('PLG_GROUPS_CITATIONS_RESEARCH_NOTES'); ?>:
				<textarea name="research_notes" id="research_notes" rows="8" cols="10"><?php echo $this->escape(stripslashes($this->row->research_notes)); ?></textarea>
			</label>

			<div class="group twoup">
				<label for="language">
					<?php echo Lang::txt('PLG_GROUPS_CITATIONS_LANGUAGE'); ?>:
					<input type="text" name="language" id="language" size="11" maxlength="50" value="<?php echo $this->escape($this->row->language); ?>" />
				</label>

				<label for="label">
					<?php echo Lang::txt('PLG_GROUPS_CITATIONS_LABEL'); ?>:
					<input type="text" name="label" id="label" size="30" maxlength="250" value="<?php echo $this->escape($this->row->label); ?>" />
				</label>
			</div>
		</fieldset>
		<div class="clear"></div>

		<div class="explaination">
			<p><?php echo Lang::txt('PLG_GROUPS_CITATIONS_TAGS_EXPLAINATION'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('PLG_GROUPS_CITATIONS_TAGS'); ?></legend>
				<label>
					<?php echo Lang::txt('PLG_GROUPS_CITATIONS_TAGS'); ?>: <span class="optional"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_OPTIONAL');?></span>
					<?php
						if (count($tags_list) > 0) {
							echo $tags_list[0];
						} else {
							echo '<input type="text" name="tags" value="' . $this->escape($tags) . '" />';
						}
					?>
					<span class="hint"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_TAGS_HINT'); ?></span>
				</label>

				<label class="badges">
					<?php echo Lang::txt('PLG_GROUPS_CITATIONS_BADGES'); ?>: <span class="optional"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_OPTIONAL');?></span>
					<?php
						if (count($badges_list) > 0) {
							echo $badges_list[0];
						} else {
							echo '<input type="text" name="badges" value="' . $this->escape($badges) . '" />';
						}
					?>
					<span class="hint"><?php echo Lang::txt('PLG_GROUPS_CITATIONS_BADGES_HINT'); ?></span>
				</label>
		</fieldset><div class="clear"></div>

		<div class="explaination">
			<p><?php echo Lang::txt('PLG_GROUPS_CITATIONS_LINKS_EXPLAINATION'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo Lang::txt('PLG_GROUPS_CITATIONS_LINKS'); ?></legend>

			<div class="link-manager">
				<?php
				$i = 0;
				$links = $this->row->links()->rows();
				foreach ($links as $link)
				{
					?>
					<div class="link grid">
						<div class="col span6">
							<label for="links-<?php echo $i; ?>-title">
								<?php echo Lang::txt('PLG_GROUPS_CITATIONS_LINK_TITLE'); ?>:
								<input type="text" name="links[<?php echo $i; ?>][title]" id="links-<?php echo $i; ?>-title" value="<?php echo $this->escape($link->title); ?>" placeholder="<?php echo Lang::txt('PLG_GROUPS_CITATIONS_LINK_TITLE_PLACEHOLDER'); ?>" />
							</label>
						</div>
						<div class="col span6 omega">

							<label for="links-<?php echo $i; ?>-url">
								<?php echo Lang::txt('PLG_GROUPS_CITATIONS_LINK_URL'); ?>:
								<input type="text" name="links[<?php echo $i; ?>][url]" id="links-<?php echo $i; ?>-url" value="<?php echo $this->escape($link->url); ?>" placeholder="http://" />
							</label>

							<input type="hidden" name="links[<?php echo $i; ?>][id]" value="<?php echo $link->id; ?>" />
							<input type="hidden" name="links[<?php echo $i; ?>][citation_id]" value="<?php echo $link->citation_id; ?>" />
						</div>
					</div>
					<?php
					$i++;
				}
				?>

				<div class="link grid">
					<div class="col span6">
						<label for="links-<?php echo $i; ?>-title">
							<?php echo Lang::txt('PLG_GROUPS_CITATIONS_LINK_TITLE'); ?>:
							<input type="text" name="links[<?php echo $i; ?>][title]" id="links-<?php echo $i; ?>-title" value="" placeholder="<?php echo Lang::txt('PLG_GROUPS_CITATIONS_LINK_TITLE_PLACEHOLDER'); ?>" />
						</label>
					</div>
					<div class="col span6 omega">
						<label for="links-<?php echo $i; ?>-url">
							<?php echo Lang::txt('PLG_GROUPS_CITATIONS_LINK_URL'); ?>:
							<input type="text" name="links[<?php echo $i; ?>][url]" id="links-<?php echo $i; ?>-url" value="" placeholder="http://" />
						</label>

						<input type="hidden" name="links[<?php echo $i; ?>][id]" value="" />
						<input type="hidden" name="links[<?php echo $i; ?>][citation_id]" value="<?php echo $this->row->id; ?>" />
					</div>
				</div>
			</div>
		</fieldset><div class="clear"></div>

		<input type="hidden" name="scope" value="<?php echo $this->escape($this->row->scope); ?>" />
		<input type="hidden" name="scope_id" value="<?php echo $this->escape($this->row->scope_id); ?>" />
		<input type="hidden" name="published" value="<?php echo ($this->row->id ? $this->escape($this->row->published) : 1); ?>" />

		<input type="hidden" name="uid" value="<?php echo $this->row->uid; ?>" />
		<input type="hidden" name="created" value="<?php echo $this->row->created; ?>" />
		<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
		<input type="hidden" name="option" value="com_groups" />
		<input type="hidden" name="active" value="citations" />
		<input type="hidden" name="action" value="save" />

		<p class="submit">
			<input class="btn btn-success" type="submit" name="create" value="<?php echo Lang::txt('PLG_GROUPS_CITATIONS_SAVE'); ?>" />
		</p>
		<div class="clear"></div>
	</form>
	<!-- </section> -->
</div>
