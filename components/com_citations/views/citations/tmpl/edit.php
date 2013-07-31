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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$allow_tags = $this->config->get("citation_allow_tags","no");
$allow_badges = $this->config->get("citation_allow_badges","no");

$fieldset_label = ($allow_tags == "yes") ? "Tags" : "";
$fieldset_label = ($allow_badges == "yes") ? "Badges" : $fieldset_label;
$fieldset_label = ($allow_tags == "yes" && $allow_badges == "yes") ? "Tags and Badges" : $fieldset_label;

$t = array();
$b = array();

foreach($this->tags as $tag) 
{
	$t[] = $tag['raw_tag'];
}

foreach($this->badges as $badge) 
{
	$b[] = $badge['raw_tag'];
}

JPluginHelper::importPlugin('hubzero');
$dispatcher =& JDispatcher::getInstance();

$tags_list = $dispatcher->trigger('onGetMultiEntry', array(array('tags', 'tags', 'actags','', implode(",",$t))));
$badges_list = $dispatcher->trigger('onGetMultiEntry', array(array('tags', 'badges', 'actags1','', implode(",",$b))));

//get the referrer
$backLink = JRoute::_('index.php?option=' . $this->option);
if (isset($_SERVER['HTTP_REFERER']) && filter_var($_SERVER['HTTP_REFERER'], FILTER_VALIDATE_URL))
{
	$backLink = $_SERVER['HTTP_REFERER'];
}
?>
<div id="content-header">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
	<ul>
		<li class="last">
			<a class="browse btn" href="<?php echo $backLink ?>">Back</a>
		</li>
	</ul>
</div><!-- / #content-header-extra -->

<div class="main section">
	<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
	<?php } ?>
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" method="post" id="hubForm" class="add-citation">
		<div class="explaination">
			<p><?php echo JText::_('Please enter the information for the work that references content on this site. <strong>Not all fields may apply to the citation</strong> - fill in only those that do.'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('DETAILS'); ?></legend>

			<div class="group twoup">
				<label for="type">
					<?php echo JText::_('TYPE'); ?>: <span class="required">Required</span>
					<select name="type" id="type">
						<option value=""> - Select a Citation Type &mdash;</option>
						<?php
							foreach($this->types as $t) {
								$sel = ($this->row->type == $t['id']) ? "selected=\"selected\"" : "";
 								echo "<option {$sel} value=\"{$t['id']}\">{$t['type_title']}</option>";
							}
						?>
					</select>
				</label>

				<label for="cite">
					<?php echo JText::_('COM_CITATIONS_CITE_KEY'); ?>:
					<input type="text" name="cite" id="cite" size="30" maxlength="250" value="<?php echo $this->row->cite; ?>" />
					<span class="hint"><?php echo JText::_('COM_CITATIONS_CITE_KEY_EXPLANATION'); ?></span>
				</label>
			</div>

			<label for="ref_type">
				<?php echo JText::_('COM_CITATIONS_REF_TYPE'); ?>:
				<input type="text" name="ref_type" id="ref_type" size="11" maxlength="50" value="<?php echo $this->row->ref_type; ?>" />
			</label>
			
			<div class="group threeup">
				<label for="date_submit">
					<?php echo JText::_('COM_CITATIONS_DATE_SUBMITTED'); ?>:
					<input type="text" name="date_submit" id="date_submit" size="30" maxlength="250" value="<?php echo $this->row->date_submit; ?>" />
					<span class="hint">YYYY-MM-DD HH:MM:SS</span>
				</label>

				<label for="date_accept">
					<?php echo JText::_('COM_CITATIONS_DATE_ACCEPTED'); ?>:
					<input type="text" name="date_accept" id="date_accept" size="30" maxlength="250" value="<?php echo $this->row->date_accept; ?>" />
					<span class="hint">YYYY-MM-DD HH:MM:SS</span>
				</label>

				<label for="date_publish">
					<?php echo JText::_('COM_CITATIONS_DATE_PUBLISHED'); ?>:
					<input type="text" name="date_publish" id="date_publish" size="30" maxlength="250" value="<?php echo $this->row->date_publish; ?>" />
					<span class="hint">YYYY-MM-DD HH:MM:SS</span>
				</label>
			</div>

			<div class="group twoup">
				<label for="year">
					<?php echo JText::_('COM_CITATIONS_YEAR'); ?>:
					<input type="text" name="year" id="year" size="4" maxlength="4" value="<?php echo $this->row->year; ?>" />
				</label>

				<label for="month">
					<?php echo JText::_('COM_CITATIONS_MONTH'); ?>:
					<input type="text" name="month" id="month" size="11" maxlength="50" value="<?php echo $this->row->month; ?>" />
				</label>
			</div> 

			<label for="author">
				<?php echo JText::_('COM_CITATIONS_AUTHORS'); ?>:
				<input type="text" name="author" id="author" size="30" value="<?php echo $this->row->author; ?>" />
				<span class="hint"><?php echo JText::_('Lastname, Firstname; Lastname, Firstname; Lastname ...'); ?></span>
			</label>
			
			<label for="authoraddress">
				<?php echo JText::_('Author Address'); ?>:
				<input type="text" name="author_address" id="authoraddress" size="30" value="<?php echo $this->row->author_address; ?>" />
			</label>

			<label for="editor">
				<?php echo JText::_('COM_CITATIONS_EDITORS'); ?>:
				<input type="text" name="editor" id="editor" size="30" maxlength="250" value="<?php echo $this->row->editor; ?>" />
				<span class="hint"><?php echo JText::_('Lastname, Firstname; Lastname, Firstname; Lastname ...'); ?></span>
			</label>

			<label for="title">
				<?php echo JText::_('COM_CITATIONS_TITLE_CHAPTER'); ?>:  <span class="required">Required</span>
				<input type="text" name="title" id="title" size="30" maxlength="250" value="<?php echo $this->row->title; ?>" />
			</label>

			<label for="booktitle">
				<?php echo JText::_('COM_CITATIONS_BOOK_TITLE'); ?>:
				<input type="text" name="booktitle" id="booktitle" size="30" maxlength="250" value="<?php echo $this->row->booktitle; ?>" />
			</label>
			
			<label for="shorttitle">
				<?php echo JText::_('Short Title'); ?>:
				<input type="text" name="short_title" id="shorttitle" size="30" maxlength="250" value="<?php echo $this->row->short_title; ?>" />
			</label>

			<label for="journal">
				<?php echo JText::_('COM_CITATIONS_JOURNAL'); ?>:
				<input type="text" name="journal" id="journal" size="30" maxlength="250" value="<?php echo $this->row->journal; ?>" />
			</label>
			
			<div class="group threeup">
				<label for="volume">
					<?php echo JText::_('COM_CITATIONS_VOLUME'); ?>:
					<input type="text" name="volume" id="volume" size="11" maxlength="11" value="<?php echo $this->row->volume; ?>" />
				</label>

				<label for="number">
					<?php echo JText::_('COM_CITATIONS_ISSUE'); ?>:
					<input type="text" name="number" id="number" size="11" maxlength="50" value="<?php echo $this->row->number; ?>" />
				</label>

				<label for="pages">
					<?php echo JText::_('COM_CITATIONS_PAGES'); ?>:
					<input type="text" name="pages" id="pages" size="11" maxlength="250" value="<?php echo $this->row->pages; ?>" />
				</label>
			</div>
			
			<div class="group twoup">
				<label for="isbn">
					<?php echo JText::_('COM_CITATIONS_ISBN'); ?>:
					<input type="text" name="isbn" id="isbn" size="11" maxlength="50" value="<?php echo $this->row->isbn; ?>" />
				</label>

				<label for="doi">
					<abbr title="<?php echo JText::_('Digital Object Identifier'); ?>"><?php echo JText::_('COM_CITATIONS_DOI'); ?></abbr>:
					<input type="text" name="doi" id="doi" size="30" maxlength="250" value="<?php echo $this->row->doi; ?>" />
				</label>
			</div>
			
			<div class="group twoup">
				<label for="callnumber">
					<?php echo JText::_('Call Number'); ?>:
					<input type="text" name="call_number" id="callnumber" value="<?php echo $this->row->call_number; ?>" />
				</label>

				<label for="accessionnumber">
					<?php echo JText::_('Accession Number'); ?>:
					<input type="text" name="accession_number" id="accessionnumber"  value="<?php echo $this->row->accession_number; ?>" />
				</label>
			</div>

			<label for="series">
				<?php echo JText::_('COM_CITATIONS_SERIES'); ?>:
				<input type="text" name="series" id="series" size="30" maxlength="250" value="<?php echo $this->row->series; ?>" />
			</label>

			<label for="edition">
				<?php echo JText::_('COM_CITATIONS_EDITION'); ?>:
				<input type="text" name="edition" id="edition" size="30" maxlength="250" value="<?php echo $this->row->edition; ?>" /> 
				<span class="hint"><?php echo JText::_('COM_CITATIONS_EDITION_EXPLANATION'); ?></span>
			</label>

			<label for="school">
				<?php echo JText::_('COM_CITATIONS_SCHOOL'); ?>:
				<input type="text" name="school" id="school" size="30" maxlength="250" value="<?php echo $this->row->school; ?>" />
			</label>

			<label for="publisher">
				<?php echo JText::_('COM_CITATIONS_PUBLISHER'); ?>:
				<input type="text" name="publisher" id="publisher" size="30" maxlength="250" value="<?php echo $this->row->publisher; ?>" />
			</label>

			<label for="institution">
				<?php echo JText::_('COM_CITATIONS_INSTITUTION'); ?>:
				<input type="text" name="institution" id="institution" size="30" maxlength="250" value="<?php echo $this->row->institution; ?>" /> 
				<span class="hint"><?php echo JText::_('COM_CITATIONS_INSTITUTION_EXPLANATION'); ?></span>
			</label>

			<label for="address">
				<?php echo JText::_('COM_CITATIONS_ADDRESS'); ?>:
				<input type="text" name="address" id="address" size="30" maxlength="250" value="<?php echo $this->row->address; ?>" />
			</label>

			<label for="location">
				<?php echo JText::_('COM_CITATIONS_LOCATION'); ?>:
				<input type="text" name="location" id="location" size="30" maxlength="250" value="<?php echo $this->row->location; ?>" /> 
				<span class="hint"><?php echo JText::_('COM_CITATIONS_LOCATION_EXPLANATION'); ?></span>
			</label>

			<label for="howpublished">
				<?php echo JText::_('COM_CITATIONS_PUBLISH_METHOD'); ?>:
				<input type="text" name="howpublished" id="howpublished" size="30" maxlength="250" value="<?php echo $this->row->howpublished; ?>" /> 
				<span class="hint"><?php echo JText::_('COM_CITATIONS_PUBLISH_METHOD_EXPLANATION'); ?></span>
			</label>

			<label for="uri">
				<?php echo JText::_('COM_CITATIONS_URL'); ?>:
				<input type="text" name="uri" id="uri" size="30" maxlength="250" value="<?php echo $this->row->url; ?>" />
			</label>

			<label for="eprint">
				<?php echo JText::_('COM_CITATIONS_EPRINT'); ?>:
				<input type="text" name="eprint" id="eprint" size="30" maxlength="250" value="<?php echo $this->row->eprint; ?>" />
				<span class="hint"><?php echo JText::_('COM_CITATIONS_EPRINT_EXPLANATION'); ?></span>
			</label>

			<label for="abstract">
				<?php echo JText::_('Abstract'); ?>:
				<textarea name="abstract" id="abstract" rows="8" cols="10"><?php echo stripslashes($this->row->abstract); ?></textarea>
			</label>
			
			<label for="note">
				<?php echo JText::_('Notes'); ?>:
				<textarea name="note" id="note" rows="8" cols="10"><?php echo stripslashes($this->row->note); ?></textarea>
			</label>
			
			<label for="keywords">
				<?php echo JText::_('Keywords'); ?>:
				<textarea name="keywords" id="keywords" rows="8" cols="10"><?php echo stripslashes($this->row->keywords); ?></textarea>
			</label>
				
			<label for="research_notes">
				<?php echo JText::_('Research Notes'); ?>:
				<textarea name="research_notes" id="research_notes" rows="8" cols="10"><?php echo stripslashes($this->row->research_notes); ?></textarea>
			</label>
			
			<div class="group twoup">
				<label for="language">
					<?php echo JText::_('Language'); ?>:
					<input type="text" name="language" id="language" size="11" maxlength="50" value="<?php echo $this->row->language; ?>" />
				</label>

				<label for="label">
					<?php echo JText::_('Label'); ?>:
					<input type="text" name="label" id="label" size="30" maxlength="250" value="<?php echo $this->row->label; ?>" />
				</label>
			</div>
		</fieldset><div class="clear"></div>
		
		<?php if ($allow_tags == "yes" || $allow_badges == "yes") : ?>
			<div class="explaination">
				<p><?php echo JText::_(''); ?></p>
			</div>
			<fieldset>
				<legend><?php echo $fieldset_label; ?></legend>
				<?php if ($allow_tags == "yes") : ?>
					<label>
						Tags: <span class="optional">Optional</span>
						<?php 
							if (count($tags_list) > 0) {
								echo $tags_list[0];
							} else {
								echo "<input type=\"text\" name=\"tags\" value=\"{$tags}\" />";
							}
						?>
						<span class="hint"><?php echo JText::_('Enter tags separated by commas (e.g. negf theory, ion transport).'); ?></span>
					</label>
				<?php endif; ?>
				
				<?php if ($allow_badges == "yes") : ?>
					<label class="badges">
						Badges: <span class="optional">Optional</span>
						<?php 
							if (count($badges_list) > 0) {
								echo $badges_list[0];
							} else {
								echo "<input type=\"text\" name=\"badges\" value=\"{$badges}\" />";
							}
						?>
						<span class="hint"><?php echo JText::_('Enter badges separated by commas (e.g.evidence-based, peer-reviewed).'); ?></span>
					</label>
				<?php endif; ?>
			</fieldset><div class="clear"></div>
		<?php endif; ?>
		
		
		<div class="explaination">
			<p><?php echo JText::_('Please enter all the resources, articles, or topic pages the work references.'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo JText::_('COM_CITATIONS_CITATION_FOR'); ?></legend>
			
			<div class="field-wrap">
			<table id="assocs">
				<thead>
					<tr>
						<th><?php echo JText::_('COM_CITATIONS_TYPE'); ?></th>
						<th><?php echo JText::_('COM_CITATIONS_ID'); ?></th>
						<!--<th><?php //echo JText::_('TABLE'); ?></th>-->
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="3"><a href="#" onclick="HUB.Citations.addRow('assocs');return false;"><?php echo JText::_('COM_CITATIONS_ADD_A_ROW'); ?></a></td>
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
							if ($r == 0 || !isset($this->assocs[$i])) {
								$this->assocs[$i] = new stdClass;
								$this->assocs[$i]->id = NULL;
								$this->assocs[$i]->cid = NULL;
								$this->assocs[$i]->oid = NULL;
								$this->assocs[$i]->type = NULL;
								$this->assocs[$i]->tbl = NULL;
							}
							echo "\t\t\t".'  <tr>'."\n";
							//echo "\t\t\t".'   <td><input type="text" name="assocs['.$i.'][type]" value="'.$this->assocs[$i]->type.'" /></td>'."\n";
							echo "\t\t\t".'   <td><select name="assocs['.$i.'][tbl]">'."\n";
							echo ' <option value=""';
							echo ($this->assocs[$i]->tbl == '') ? ' selected="selected"': '';
							echo '>'.JText::_('COM_CITATIONS_SELECT').'</option>'."\n";
							//echo ' <option value="content"';
							//echo ($this->assocs[$i]->tbl == 'content') ? ' selected="selected"': '';
							//echo '>'.JText::_('CONTENT').'</option>'."\n";
							echo ' <option value="resource"';
							echo ($this->assocs[$i]->tbl == 'resource') ? ' selected="selected"': '';
							echo '>'.JText::_('COM_CITATIONS_RESOURCE').'</option>'."\n";
							//echo ' <option value="topic"';
							//echo ($this->assocs[$i]->tbl == 'topic') ? ' selected="selected"': '';
							//echo '>'.JText::_('TOPIC').'</option>'."\n";
							echo '</select></td>'."\n";
							echo "\t\t\t".'   <td><input type="text" name="assocs['.$i.'][oid]" value="'.$this->assocs[$i]->oid.'" /></td>'."\n";
							echo "\t\t\t\t".'<input type="hidden" name="assocs['.$i.'][id]" value="'.$this->assocs[$i]->id.'" />'."\n";
							echo "\t\t\t\t".'<input type="hidden" name="assocs['.$i.'][cid]" value="'.$this->assocs[$i]->cid.'" /></td>'."\n";
							echo "\t\t\t".'  </tr>'."\n";
						}
				?>
				</tbody>
			</table>
			</div>
		</fieldset><div class="clear"></div>
		<fieldset>
			<legend><?php echo JText::_('COM_CITATIONS_AFFILIATION'); ?></legend>
			
			<label>
				<input type="checkbox" class="option" name="affiliated" id="affiliated" value="1"<?php if ($this->row->affiliated) { echo ' checked="checked"'; } ?> />
				<?php echo JText::_('COM_CITATIONS_AFFILIATED_WITH_YOUR_ORG'); ?>
			</label>

			<label>
				<input type="checkbox" class="option" name="fundedby" id="fundedby" value="1"<?php if ($this->row->fundedby) { echo ' checked="checked"'; } ?> />
				<?php echo JText::_('COM_CITATIONS_FUNDED_BY_YOUR_ORG'); ?>
			</label>
			
			<input type="hidden" name="uid" value="<?php echo $this->row->uid; ?>" />
			<input type="hidden" name="created" value="<?php echo $this->row->created; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="task" value="save" />
		</fieldset>
		<div class="clear"></div>
		<p class="submit"><input type="submit" name="create" value="<?php echo JText::_('Save'); ?>" /></p>
	</form>
</div>