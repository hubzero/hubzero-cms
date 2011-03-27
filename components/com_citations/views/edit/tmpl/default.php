<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div>
<div class="main section">

	<form action="index.php" method="post" id="hubForm">
		<div class="explaination">
			<p><?php echo JText::_('Please enter the information for the work that references content on this site. <strong>Not all fields may apply to the citation</strong> - fill in only those that do.'); ?></p>
		</div>
		<fieldset>
			<h3><?php echo JText::_('DETAILS'); ?></h3>

			<div class="group twoup">
			<label for="type">
				<?php echo JText::_('TYPE'); ?>:
				<?php echo CitationsHtml::select('type', $this->types, $this->row->type); ?>
			</label>

			<label for="cite">
				<?php echo JText::_('CITE_KEY'); ?>:
				<input type="text" name="cite" id="cite" size="30" maxlength="250" value="<?php echo $this->row->cite; ?>" />
				<span class="hint"><?php echo JText::_('CITE_KEY_EXPLANATION'); ?></span>
			</label>
			</div>

			<label for="ref_type">
				<?php echo JText::_('REF_TYPE'); ?>:
				<input type="text" name="ref_type" id="ref_type" size="11" maxlength="50" value="<?php echo $this->row->ref_type; ?>" />
			</label>
			
			<div class="group threeup">
			<label for="date_submit">
				<?php echo JText::_('DATE_SUBMITTED'); ?>:
				<input type="text" name="date_submit" id="date_submit" size="30" maxlength="250" value="<?php echo $this->row->date_submit; ?>" />
				<span class="hint">YYYY-MM-DD HH:MM:SS</span>
			</label>

			<label for="date_accept">
				<?php echo JText::_('DATE_ACCEPTED'); ?>:
				<input type="text" name="date_accept" id="date_accept" size="30" maxlength="250" value="<?php echo $this->row->date_accept; ?>" />
				<span class="hint">YYYY-MM-DD HH:MM:SS</span>
			</label>

			<label for="date_publish">
				<?php echo JText::_('DATE_PUBLISHED'); ?>:
				<input type="text" name="date_publish" id="date_publish" size="30" maxlength="250" value="<?php echo $this->row->date_publish; ?>" />
				<span class="hint">YYYY-MM-DD HH:MM:SS</span>
			</label>
			</div>

			<!--
			<div class="group twoup">
			<label for="year">
				<?php echo JText::_('YEAR'); ?>:
				<input type="text" name="year" id="year" size="4" maxlength="4" value="<?php echo $this->row->year; ?>" />
			</label>

			<label for="month">
				<?php echo JText::_('MONTH'); ?>:
				<input type="text" name="month" id="month" size="11" maxlength="50" value="<?php echo $this->row->month; ?>" />
			</label>
			</div> -->

			<label for="author">
				<?php echo JText::_('AUTHORS'); ?>:
				<input type="text" name="author" id="author" size="30" value="<?php echo $this->row->author; ?>" />
				<span class="hint"><?php echo JText::_('Lastname, Firstname; Lastname, Firstname; Lastname ...'); ?></span>
			</label>

			<label for="editor">
				<?php echo JText::_('EDITORS'); ?>:
				<input type="text" name="editor" id="editor" size="30" maxlength="250" value="<?php echo $this->row->editor; ?>" />
				<span class="hint"><?php echo JText::_('Lastname, Firstname; Lastname, Firstname; Lastname ...'); ?></span>
			</label>

			<label for="title">
				<?php echo JText::_('TITLE_CHAPTER'); ?>:
				<input type="text" name="title" id="title" size="30" maxlength="250" value="<?php echo $this->row->title; ?>" />
			</label>

			<label for="booktitle">
				<?php echo JText::_('BOOK_TITLE'); ?>:
				<input type="text" name="booktitle" id="booktitle" size="30" maxlength="250" value="<?php echo $this->row->booktitle; ?>" />
			</label>

			<label for="journal">
				<?php echo JText::_('JOURNAL'); ?>:
				<input type="text" name="journal" id="journal" size="30" maxlength="250" value="<?php echo $this->row->journal; ?>" />
			</label>
			
			<div class="group threeup">
			<label for="volume">
				<?php echo JText::_('VOLUME'); ?>:
				<input type="text" name="volume" id="volume" size="11" maxlength="11" value="<?php echo $this->row->volume; ?>" />
			</label>

			<label for="number">
				<?php echo JText::_('ISSUE'); ?>:
				<input type="text" name="number" id="number" size="11" maxlength="50" value="<?php echo $this->row->number; ?>" />
			</label>

			<label for="pages">
				<?php echo JText::_('PAGES'); ?>:
				<input type="text" name="pages" id="pages" size="11" maxlength="250" value="<?php echo $this->row->pages; ?>" />
			</label>
			</div>
			
			<div class="group twoup">
			<label for="isbn">
				<?php echo JText::_('ISBN'); ?>:
				<input type="text" name="isbn" id="isbn" size="11" maxlength="50" value="<?php echo $this->row->isbn; ?>" />
			</label>

			<label for="doi">
				<abbr title="<?php echo JText::_('Digital Object Identifier'); ?>"><?php echo JText::_('DOI'); ?></abbr>:
				<input type="text" name="doi" id="doi" size="30" maxlength="250" value="<?php echo $this->row->doi; ?>" />
			</label>
			</div>

			<label for="series">
				<?php echo JText::_('SERIES'); ?>:
				<input type="text" name="series" id="series" size="30" maxlength="250" value="<?php echo $this->row->series; ?>" />
			</label>

			<label for="edition">
				<?php echo JText::_('EDITION'); ?>:
				<input type="text" name="edition" id="edition" size="30" maxlength="250" value="<?php echo $this->row->edition; ?>" /> 
				<span class="hint"><?php echo JText::_('EDITION_EXPLANATION'); ?></span>
			</label>

			<label for="school">
				<?php echo JText::_('SCHOOL'); ?>:
				<input type="text" name="school" id="school" size="30" maxlength="250" value="<?php echo $this->row->school; ?>" />
			</label>

			<label for="publisher">
				<?php echo JText::_('PUBLISHER'); ?>:
				<input type="text" name="publisher" id="publisher" size="30" maxlength="250" value="<?php echo $this->row->publisher; ?>" />
			</label>

			<label for="institution">
				<?php echo JText::_('INSTITUTION'); ?>:
				<input type="text" name="institution" id="institution" size="30" maxlength="250" value="<?php echo $this->row->institution; ?>" /> 
				<span class="hint"><?php echo JText::_('INSTITUTION_EXPLANATION'); ?></span>
			</label>

			<label for="address">
				<?php echo JText::_('ADDRESS'); ?>:
				<input type="text" name="address" id="address" size="30" maxlength="250" value="<?php echo $this->row->address; ?>" />
			</label>

			<label for="location">
				<?php echo JText::_('LOCATION'); ?>:
				<input type="text" name="location" id="location" size="30" maxlength="250" value="<?php echo $this->row->location; ?>" /> 
				<span class="hint"><?php echo JText::_('LOCATION_EXPLANATION'); ?></span>
			</label>

			<label for="howpublished">
				<?php echo JText::_('PUBLISH_METHOD'); ?>:
				<input type="text" name="howpublished" id="howpublished" size="30" maxlength="250" value="<?php echo $this->row->howpublished; ?>" /> 
				<span class="hint"><?php echo JText::_('PUBLISH_METHOD_EXPLANATION'); ?></span>
			</label>

			<label for="uri">
				<?php echo JText::_('URL'); ?>:
				<input type="text" name="uri" id="uri" size="30" maxlength="250" value="<?php echo $this->row->url; ?>" />
			</label>

			<label for="eprint">
				<?php echo JText::_('EPRINT'); ?>:
				<input type="text" name="eprint" id="eprint" size="30" maxlength="250" value="<?php echo $this->row->eprint; ?>" />
				<span class="hint"><?php echo JText::_('EPRINT_EXPLANATION'); ?></span>
			</label>

			<label>
				<?php echo JText::_('NOTES'); ?>:
				<textarea name="note" rows="15" cols="10"><?php echo stripslashes($this->row->note); ?></textarea>
			</label>
		</fieldset><div class="clear"></div>
		<div class="explaination">
			<p><?php echo JText::_('Please enter all the resources, articles, or topic pages the work references.'); ?></p>
		</div>
		<fieldset>
			<h3><?php echo JText::_('CITATION_FOR'); ?></h3>
			
			<table id="assocs">
				<thead>
					<tr>
						<th><?php echo JText::_('ID'); ?></th>
						<th><?php echo JText::_('TYPE'); ?></th>
						<th><?php echo JText::_('TABLE'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="3"><a href="#" onclick="HUB.Citations.addRow('assocs');return false;"><?php echo JText::_('ADD_A_ROW'); ?></a></td>
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
								$this->assocs[$i]->table = NULL;
							}
							echo "\t\t\t".'  <tr>'."\n";
							echo "\t\t\t".'   <td><input type="text" name="assocs['.$i.'][oid]" value="'.$this->assocs[$i]->oid.'" /></td>'."\n";
							echo "\t\t\t".'   <td><input type="text" name="assocs['.$i.'][type]" value="'.$this->assocs[$i]->type.'" /></td>'."\n";
							echo "\t\t\t".'   <td><select name="assocs['.$i.'][table]">'."\n";
							echo ' <option value=""';
							echo ($this->assocs[$i]->table == '') ? ' selected="selected"': '';
							echo '>'.JText::_('SELECT').'</option>'."\n";
							echo ' <option value="content"';
							echo ($this->assocs[$i]->table == 'content') ? ' selected="selected"': '';
							echo '>'.JText::_('CONTENT').'</option>'."\n";
							echo ' <option value="resource"';
							echo ($this->assocs[$i]->table == 'resource') ? ' selected="selected"': '';
							echo '>'.JText::_('RESOURCE').'</option>'."\n";
							echo ' <option value="topic"';
							echo ($this->assocs[$i]->table == 'topic') ? ' selected="selected"': '';
							echo '>'.JText::_('TOPIC').'</option>'."\n";
							echo '</select>'."\n";
							echo "\t\t\t\t".'<input type="hidden" name="assocs['.$i.'][id]" value="'.$this->assocs[$i]->id.'" />'."\n";
							echo "\t\t\t\t".'<input type="hidden" name="assocs['.$i.'][cid]" value="'.$this->assocs[$i]->cid.'" /></td>'."\n";
							echo "\t\t\t".'  </tr>'."\n";
						}
				?>
				</tbody>
			</table>
		</fieldset><div class="clear"></div>
		<fieldset>
			<h3><?php echo JText::_('AFFILIATION'); ?></h3>
			
			<label>
				<input type="checkbox" class="option" name="affiliated" id="affiliated" value="1"<?php if ($this->row->affiliated) { echo ' checked="checked"'; } ?> />
				<?php echo JText::_('AFFILIATED_WITH_YOUR_ORG'); ?>
			</label>

			<label>
				<input type="checkbox" class="option" name="fundedby" id="fundedby" value="1"<?php if ($this->row->fundedby) { echo ' checked="checked"'; } ?> />
				<?php echo JText::_('FUNDED_BY_YOUR_ORG'); ?>
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
