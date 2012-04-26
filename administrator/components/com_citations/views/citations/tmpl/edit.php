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
defined('_JEXEC') or die( 'Restricted access' );
$text = ( $this->task == 'edit' ? JText::_( 'EDIT' ) : JText::_( 'NEW' ) );

JToolBarHelper::title( JText::_( 'CITATION' ).': <small><small>[ '. $text.' ]</small></small>', 'citation.png' );
JToolBarHelper::save();
JToolBarHelper::cancel();

jimport('joomla.html.editor');
$editor =& JEditor::getInstance();

?>
<!--
<script type="text/javascript" src="../includes/js/mootools.js"></script>
<script type="text/javascript" src="components/com_citations/citations.js"></script>
-->
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.getElementById('adminForm');
	
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	
	// form field validation
	//if (form.title.value == '') {
	//	alert( '<?php echo JText::_('CITATION_MUST_HAVE_TITLE'); ?>' );
	//} else {
		submitform( pressbutton );
	//}
}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-70 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('DETAILS'); ?></span></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="type"><?php echo JText::_('TYPE'); ?>:</label></td>
						<td colspan="3">
							<select name="citation[type]" id="type">
								<?php foreach($this->types as $t) : ?>
									<?php $sel = ($t['id'] == $this->row->type) ? "selected=\"selected\"" : "" ?>
									<option <?php echo $sel; ?> value="<?php echo $t['id']; ?>"><?php echo $t['type_title']; ?> (<?php echo $t['type']; ?>)</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="cite"><?php echo JText::_('CITE_KEY'); ?>:</label></td>
						<td>
							<input type="text" name="citation[cite]" id="cite" size="30" maxlength="250" value="<?php echo $this->row->cite; ?>" />
							<br /><span style="font-size: 90%;color:#aaa;"><?php echo JText::_('CITE_KEY_EXPLANATION'); ?></span>
						</td>
						<td class="key"><label for="ref_type"><?php echo JText::_('REF_TYPE'); ?>:</label></td>
						<td><input type="text" name="citation[ref_type]" id="ref_type" size="11" maxlength="50" value="<?php echo $this->row->ref_type; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="date_submit"><?php echo JText::_('DATE_SUBMITTED'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[date_submit]" id="date_submit" size="30" maxlength="250" value="<?php echo $this->row->date_submit; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="date_accept"><?php echo JText::_('DATE_ACCEPTED'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[date_accept]" id="date_accept" size="30" maxlength="250" value="<?php echo $this->row->date_accept; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="date_publish"><?php echo JText::_('DATE_PUBLISHED'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[date_publish]" id="date_publish" size="30" maxlength="250" value="<?php echo $this->row->date_publish; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="year"><?php echo JText::_('YEAR'); ?>:</label></td>
						<td><input type="text" name="citation[year]" id="year" size="4" maxlength="4" value="<?php echo $this->row->year; ?>" /></td>
						<td class="key"><label for="month"><?php echo JText::_('MONTH'); ?>:</label></td>
						<td><input type="text" name="citation[month]" id="month" size="11" maxlength="50" value="<?php echo $this->row->month; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="author"><?php echo JText::_('AUTHORS'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[author]" id="author" size="100" value="<?php echo htmlentities($this->row->author,ENT_COMPAT,'UTF-8'); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="author_address"><?php echo JText::_('Author Address'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[author_address]" id="author_address" size="100" value="<?php echo htmlentities($this->row->author_address,ENT_COMPAT,'UTF-8'); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="editor"><?php echo JText::_('EDITORS'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[editor]" id="editor" size="100" maxlength="250" value="<?php echo htmlentities($this->row->editor,ENT_COMPAT,'UTF-8'); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="title"><?php echo JText::_('TITLE_CHAPTER'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[title]" id="title" size="100" maxlength="250" value="<?php echo htmlentities($this->row->title,ENT_COMPAT,'UTF-8'); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="booktitle"><?php echo JText::_('BOOK_TITLE'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[booktitle]" id="booktitle" size="100" maxlength="250" value="<?php echo htmlentities($this->row->booktitle,ENT_COMPAT,'UTF-8'); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="shorttitle"><?php echo JText::_('Short Title'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[short_title]" id="shorttitle" size="100" maxlength="250" value="<?php echo htmlentities($this->row->short_title,ENT_COMPAT,'UTF-8'); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="journal"><?php echo JText::_('JOURNAL'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[journal]" id="journal" size="100" maxlength="250" value="<?php echo htmlentities($this->row->journal,ENT_COMPAT,'UTF-8'); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="volume"><?php echo JText::_('VOLUME'); ?>:</label></td>
						<td><input type="text" name="citation[volume]" id="volume" size="11" maxlength="11" value="<?php echo $this->row->volume; ?>" /></td>
						<td class="key"><label for="number"><?php echo JText::_('ISSUE'); ?>:</label></td>
						<td><input type="text" name="citation[number]" id="number" size="11" maxlength="50" value="<?php echo $this->row->number; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="pages"><?php echo JText::_('PAGES'); ?>:</label></td>
						<td><input type="text" name="citation[pages]" id="pages" size="11" maxlength="250" value="<?php echo $this->row->pages; ?>" /></td>
						<td class="key"><label for="isbn"><?php echo JText::_('ISBN'); ?>:</label></td>
						<td><input type="text" name="citation[isbn]" id="isbn" size="11" maxlength="50" value="<?php echo $this->row->isbn; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="doi"><?php echo JText::_('DOI'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[doi]" id="doi" size="30" maxlength="250" value="<?php echo $this->row->doi; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="callnumber"><?php echo JText::_('Call Number'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[call_number]" id="callnumber" size="30" maxlength="250" value="<?php echo $this->row->call_number; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="accessionnumber"><?php echo JText::_('Accession Number'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[accession_number]" id="accessionnumber" size="30" maxlength="250" value="<?php echo $this->row->accession_number; ?>" /></td>
					</tr>
					
					<tr>
						<td class="key"><label for="series"><?php echo JText::_('SERIES'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[series]" id="series" size="30" maxlength="250" value="<?php echo $this->row->series; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="edition"><?php echo JText::_('EDITION'); ?>:</label></td>
						<td colspan="3">
							<input type="text" name="citation[edition]" id="edition" size="30" maxlength="250" value="<?php echo $this->row->edition; ?>" /> 
							<br /><span style="font-size: 90%;color:#aaa;"><?php echo JText::_('EDITION_EXPLANATION'); ?></span>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="school"><?php echo JText::_('SCHOOL'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[school]" id="school" size="30" maxlength="250" value="<?php echo $this->row->school; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="publisher"><?php echo JText::_('PUBLISHER'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[publisher]" id="publisher" size="30" maxlength="250" value="<?php echo $this->row->publisher; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="institution"><?php echo JText::_('INSTITUTION'); ?>:</label></td>
						<td colspan="3">
							<input type="text" name="citation[institution]" id="institution" size="30" maxlength="250" value="<?php echo $this->row->institution; ?>" /> 
							<br /><span style="font-size: 90%;color:#aaa;"><?php echo JText::_('INSTITUTION_EXPLANATION'); ?></span>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="address"><?php echo JText::_('ADDRESS'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[address]" id="address" size="30" maxlength="250" value="<?php echo $this->row->address; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="location"><?php echo JText::_('LOCATION'); ?>:</label></td>
						<td colspan="3">
							<input type="text" name="citation[location]" id="location" size="30" maxlength="250" value="<?php echo $this->row->location; ?>" /> 
			   				<span style="font-size: 90%;color:#aaa;"><?php echo JText::_('LOCATION_EXPLANATION'); ?></span>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="howpublished"><?php echo JText::_('PUBLISH_METHOD'); ?>:</label></td>
						<td colspan="3">
							<input type="text" name="citation[howpublished]" id="howpublished" size="30" maxlength="250" value="<?php echo $this->row->howpublished; ?>" /> 
							<br /><span style="font-size: 90%;color:#aaa;"><?php echo JText::_('PUBLISH_METHOD_EXPLANATION'); ?></span>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="url"><?php echo JText::_('URL'); ?>:</label></td>
						<td colspan="3"><input type="text" name="citation[url]" id="url" size="30" maxlength="250" value="<?php echo $this->row->url; ?>" /></td>
					</tr>
					<tr>
						<td class="key"><label for="eprint"><?php echo JText::_('EPRINT'); ?>:</label></td>
						<td colspan="3">
							<input type="text" name="citation[eprint]" id="eprint" size="30" maxlength="250" value="<?php echo $this->row->eprint; ?>" />
							<br /><span style="font-size: 90%;color:#aaa;"><?php echo JText::_('EPRINT_EXPLANATION'); ?></span>
						</td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('Abstract'); ?>:</td>
						<td colspan="3">
							<?php echo $editor->display('citation[abstract]', stripslashes($this->row->abstract), '500px', '100px', '50', '10'); ?>
						</td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('NOTES'); ?>:</td>
						<td colspan="3">
							<?php echo $editor->display('citation[note]', stripslashes($this->row->note), '500px', '100px', '50', '10'); ?>
						</td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('Keywords'); ?>:</td>
						<td colspan="3">
							<?php echo $editor->display('citation[keywords]', stripslashes($this->row->keywords), '500px', '100px', '50', '10'); ?>
						</td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('Research Notes'); ?>:</td>
						<td colspan="3">
							<?php echo $editor->display('citation[research_notes]', stripslashes($this->row->research_notes), '500px', '100px', '50', '10'); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-30 fltrt">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('CITATION_FOR'); ?></span></legend>
			
			<table class="admintable" id="assocs">
				<thead>
					<tr>
						<th><?php echo JText::_('ID'); ?></th>
						<th><?php echo JText::_('TYPE'); ?></th>
						<th><?php echo JText::_('TABLE'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="3"><a href="#" onclick="Citations.addRow('assocs');return false;"><?php echo JText::_('ADD_A_ROW'); ?></a></td>
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
								$assocs[$i]->table = NULL;
							}
							echo '  <tr>'."\n";
							echo '   <td><input type="text" name="assocs['.$i.'][oid]" value="'.$assocs[$i]->oid.'" size="5" /></td>'."\n";
							echo '   <td><input type="text" name="assocs['.$i.'][type]" value="'.$assocs[$i]->type.'" size="5" /></td>'."\n";
							echo '   <td><select name="assocs['.$i.'][table]">'."\n";
							echo ' <option value=""';
							echo ($assocs[$i]->table == '') ? ' selected="selected"': '';
							echo '>'.JText::_('SELECT').'</option>'."\n";
							echo ' <option value="content"';
							echo ($assocs[$i]->table == 'content') ? ' selected="selected"': '';
							echo '>'.JText::_('CONTENT').'</option>'."\n";
							echo ' <option value="resource"';
							echo ($assocs[$i]->table == 'resource') ? ' selected="selected"': '';
							echo '>'.JText::_('RESOURCE').'</option>'."\n";
							echo ' <option value="topic"';
							echo ($assocs[$i]->table == 'topic') ? ' selected="selected"': '';
							echo '>'.JText::_('TOPIC').'</option>'."\n";
							echo '</select>'."\n";
							echo '<input type="hidden" name="assocs['.$i.'][id]" value="'.$assocs[$i]->id.'" />'."\n";
							echo '<input type="hidden" name="assocs['.$i.'][cid]" value="'.$assocs[$i]->cid.'" /></td>'."\n";
							echo '  </tr>'."\n";
						}
				?>
				</tbody>
			</table>
		</fieldset>
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('AFFILIATION'); ?></span></legend>
			
			<table class="adminform">
				<tbody>
					<tr>
						<td>
							<label>
								<input type="checkbox" name="citation[affiliated]" id="affiliated" value="1"<?php if ($this->row->affiliated) { echo ' checked="checked"'; } ?> />
								<?php echo JText::_('AFFILIATED_WITH_YOUR_ORG'); ?>
							</label>
						</td>
					</tr>
					<tr>
						<td>
							<label>
								<input type="checkbox" name="citation[fundedby]" id="fundedby" value="1"<?php if ($this->row->fundedby) { echo ' checked="checked"'; } ?> />
								<?php echo JText::_('FUNDED_BY_YOUR_ORG'); ?>
							</label>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Citation Sponsors'); ?></span></legend>
			<table class="adminform">
				<tbody>
					<tr>
						<td>
							<select name="sponsors[]" multiple="multiple" style="max-width:inherit;width:100%;">
								<!--<option value="sponsors">- Select Citation Sponsor &mdash;</option>-->
								<?php foreach($this->sponsors as $s) : ?>
									<?php $sel = (in_array($s['id'], $this->row_sponsors)) ? 'selected="selected"': ''; ?>
									<option <?php echo $sel; ?> value="<?php echo $s['id']; ?>"><?php echo $s['sponsor']; ?></option>
								<?php endforeach; ?>
							</select>
							<span style="font-size: 90%;color:#aaa;">Select multiple sponsors by Ctrl + click.</span>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		
		<?php if($this->config->get("citation_allow_tags", "no") == "yes") : ?>
			<fieldset class="adminform">
				<legend><span><?php echo JText::_('Tags'); ?></span></legend>
				<table class="adminform">
					<tbody>
						<tr>
							<td>
								<?php
									$t = array();
									foreach($this->tags as $tag) {
										$t[] = $tag['raw_tag'];
									}
								?>
								<textarea name="tags" rows="10" style="width:98%;"><?php echo implode(",", $t); ?></textarea>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		<?php endif; ?>
		
		<?php if($this->config->get("citation_allow_badges", "no") == "yes") : ?>
			<fieldset class="adminform">
				<legend><span><?php echo JText::_('Badges'); ?></span></legend>
				<table class="adminform">
					<tbody>
						<tr>
							<td>
								<?php
									$b = array();
									foreach($this->badges as $badge) {
										$b[] = $badge['raw_tag'];
									}
								?>
								<textarea name="badges" rows="10" style="width:98%;"><?php echo implode(",", $b); ?></textarea>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		<?php endif; ?>
		
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Exclude from Download'); ?></span></legend>
			<table class="adminform">
				<tbody>
					<tr>
						<td>
							<textarea name="exclude" rows="10" style="width:98%;"><?php echo $this->params->get("exclude"); ?></textarea>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>
	
	<input type="hidden" name="citation[uid]" value="<?php echo $this->row->uid; ?>" />
	<input type="hidden" name="citation[created]" value="<?php echo $this->row->created; ?>" />
	<input type="hidden" name="citation[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
