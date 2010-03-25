<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if (!defined('n')) {
	define('t',"\t");
	define('n',"\n");
	define('br','<br />');
	define('sp','&#160;');
	define('a','&amp;');
}

class CitationsHtml 
{
	public function select($name, $arr, $value)
	{
		$html  = '<select name="'.$name.'" id="'.$name.'">';
		for ($i=0, $n=count( $arr ); $i < $n; $i++)
		{
			$html .= '<option value="'.$arr[$i].'"';
			if($value == $arr[$i]) {
				$html .= ' selected="selected"';
			}
			$html .= '>'.$arr[$i].'</option>';
		}
		$html .= '</select>';
		return $html;
	}

	//-----------
	
	public function shortenText($text, $chars=300, $p=1) 
	{
		$text = strip_tags($text);
		$text = trim($text);

		if (strlen($text) > $chars) {
			$text = $text.' ';
			$text = substr($text,0,$chars);
			$text = substr($text,0,strrpos($text,' '));
			$text = $text.' ...';
		}
		if ($text == '') {
			$text = '...';
		}
		if ($p) {
			$text = '<p>'.$text.'</p>';
		}

		return $text;
	}

	//-----------

	public function browse( &$rows, &$pageNav, $option, $filter, $mtask ) 
	{
		?>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = $('adminForm');
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			// do field validation
			submitform( pressbutton );
		}
		</script>

		<form action="index.php" method="post" name="adminForm" id="adminForm">
			<fieldset id="filter">
				<label>
					<?php echo JText::_('SEARCH'); ?>: 
					<input type="text" name="search" id="search" value="<?php echo $filter['search']; ?>" />
				</label>

				<label>
					<?php echo JText::_('SORT'); ?>: 
					<select name="sort" id="sort">
						<option value="created DESC"<?php if ($filter['sort'] == 'created DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('DATE'); ?></option>
						<option value="year"<?php if ($filter['sort'] == 'year') { echo ' selected="selected"'; } ?>><?php echo JText::_('YEAR'); ?></option>
						<option value="type"<?php if ($filter['sort'] == 'type') { echo ' selected="selected"'; } ?>><?php echo JText::_('TYPE'); ?></option>
						<option value="author ASC"<?php if ($filter['sort'] == 'author ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('AUTHORS'); ?></option>
						<option value="title ASC"<?php if ($filter['sort'] == 'title ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('TITLE'); ?></option>
					</select>
				</label>

				<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo JText::_('GO'); ?>" />
			</fieldset>

			<table class="adminlist" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
				<thead>
					<tr>
						<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $rows );?>);" /></th>
						<th><?php echo JText::_('TITLE'); ?> / <?php echo JText::_('AUTHORS'); ?></th>
						<th><?php echo JText::_('YEAR'); ?></th>
						<th><?php echo JText::_('TYPE'); ?></th>
						<th><?php echo JText::_('AFFILIATED'); ?></th>
						<th><?php echo JText::_('FUNDED_BY'); ?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<td colspan="6">
							<?php echo $pageNav->getListFooter(); ?>
						</td>
					</tr>
				</tfoot>
				<tbody>
<?php
		$k = 0;
		$filterstring = ($filter['sort']) ? '&amp;sort='.$filter['sort'] : '';

		for ($i=0, $n=count( $rows ); $i < $n; $i++) 
		{
			$row =& $rows[$i];
			
			//$row->citation = stripslashes($row->citation);
			//$row->citation = CitationsHtml::shortenText($row->citation,75,0);
?>
					<tr class="<?php echo "row$k"; ?>">
						<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked);" /></td>
						<td><a href="index.php?option=<?php echo $option ?>&amp;task=edit&amp;id[]=<?php echo $row->id; echo $filterstring; ?>"><?php echo $row->title; ?></a><br /><small><?php echo $row->author; ?></small></a></td>
						<td><?php echo $row->year; ?></td>
						<td><?php echo $row->type; ?></td>
						<td><?php if ($row->affiliated == 1) { echo '<span class="check">'.JText::_('YES').'</span>'; } ?></td>
						<td><?php if ($row->fundedby == 1) { echo '<span class="check">'.JText::_('YES').'</span>'; } ?></td>
					</tr>
<?php
			$k = 1 - $k;
		}
?>
				</tbody>
			</table>
	
			<input type="hidden" name="option" value="<?php echo $option; ?>" />
			<input type="hidden" name="task" value="<?php echo $mtask; ?>" />
			<input type="hidden" name="viewtask" value="<?php echo $mtask; ?>" />
			<input type="hidden" name="boxchecked" value="0" />
		</form>

		<?php
	}

	//-----------
	
	public function edit( $row, $assocs, $option ) 
	{
		$types = array('article','book','booklet','conference','inbook','incollection','inproceedings','magazine','manual','mastersthesis','misc','phdthesis','proceedings','techreport','unpublished','patent appl','chapter','notes','letter','manuscript');

		jimport('joomla.html.editor');
		$editor =& JEditor::getInstance();
		
		?>
		<script type="text/javascript" src="../includes/js/mootools.js"></script>
		<script type="text/javascript" src="components/com_citations/citations.js"></script>
		<script type="text/javascript">
		function submitbutton(pressbutton) 
		{
			var form = document.getElementById('adminForm');
			
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}
			
			// form field validation
			if (form.title.value == '') {
				alert( '<?php echo JText::_('CITATION_MUST_HAVE_TITLE'); ?>' );
			} else {
				submitform( pressbutton );
			}
		}
		</script>
		
		<form action="index.php" method="post" name="adminForm" id="adminForm">
			<div class="col width-70">
				<fieldset class="adminform">
					<legend><?php echo JText::_('DETAILS'); ?></legend>
					<table class="admintable">
						<tbody>
							<tr>
								<td class="key"><label for="type"><?php echo JText::_('TYPE'); ?>:</label></td>
								<td colspan="3"><?php echo CitationsHtml::select('type', $types, $row->type); ?></td>
							</tr>
							<tr>
								<td class="key"><label for="cite"><?php echo JText::_('CITE_KEY'); ?>:</label></td>
								<td>
									<input type="text" name="cite" id="cite" size="30" maxlength="250" value="<?php echo $row->cite; ?>" />
									<br /><span style="font-size: 90%;color:#aaa;"><?php echo JText::_('CITE_KEY_EXPLANATION'); ?></span>
								</td>
								<td class="key"><label for="ref_type"><?php echo JText::_('REF_TYPE'); ?>:</label></td>
								<td><input type="text" name="ref_type" id="ref_type" size="11" maxlength="50" value="<?php echo $row->ref_type; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><label for="date_submit"><?php echo JText::_('DATE_SUBMITTED'); ?>:</label></td>
								<td colspan="3"><input type="text" name="date_submit" id="date_submit" size="30" maxlength="250" value="<?php echo $row->date_submit; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><label for="date_accept"><?php echo JText::_('DATE_ACCEPTED'); ?>:</label></td>
								<td colspan="3"><input type="text" name="date_accept" id="date_accept" size="30" maxlength="250" value="<?php echo $row->date_accept; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><label for="date_publish"><?php echo JText::_('DATE_PUBLISHED'); ?>:</label></td>
								<td colspan="3"><input type="text" name="date_publish" id="date_publish" size="30" maxlength="250" value="<?php echo $row->date_publish; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><label for="year"><?php echo JText::_('YEAR'); ?>:</label></td>
								<td><input type="text" name="year" id="year" size="4" maxlength="4" value="<?php echo $row->year; ?>" /></td>
								<td class="key"><label for="month"><?php echo JText::_('MONTH'); ?>:</label></td>
								<td><input type="text" name="month" id="month" size="11" maxlength="50" value="<?php echo $row->month; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><label for="author"><?php echo JText::_('AUTHORS'); ?>:</label></td>
								<td colspan="3"><input type="text" name="author" id="author" size="30" value="<?php echo $row->author; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><label for="editor"><?php echo JText::_('EDITORS'); ?>:</label></td>
								<td colspan="3"><input type="text" name="editor" id="editor" size="30" maxlength="250" value="<?php echo $row->editor; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><label for="title"><?php echo JText::_('TITLE_CHAPTER'); ?>:</label></td>
								<td colspan="3"><input type="text" name="title" id="title" size="30" maxlength="250" value="<?php echo $row->title; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><label for="booktitle"><?php echo JText::_('BOOK_TITLE'); ?>:</label></td>
								<td colspan="3"><input type="text" name="booktitle" id="booktitle" size="30" maxlength="250" value="<?php echo $row->booktitle; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><label for="journal"><?php echo JText::_('JOURNAL'); ?>:</label></td>
								<td colspan="3"><input type="text" name="journal" id="journal" size="30" maxlength="250" value="<?php echo $row->journal; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><label for="volume"><?php echo JText::_('VOLUME'); ?>:</label></td>
								<td><input type="text" name="volume" id="volume" size="11" maxlength="11" value="<?php echo $row->volume; ?>" /></td>
								<td class="key"><label for="number"><?php echo JText::_('ISSUE'); ?>:</label></td>
								<td><input type="text" name="number" id="number" size="11" maxlength="50" value="<?php echo $row->number; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><label for="pages"><?php echo JText::_('PAGES'); ?>:</label></td>
								<td><input type="text" name="pages" id="pages" size="11" maxlength="250" value="<?php echo $row->pages; ?>" /></td>
								<td class="key"><label for="isbn"><?php echo JText::_('ISBN'); ?>:</label></td>
								<td><input type="text" name="isbn" id="isbn" size="11" maxlength="50" value="<?php echo $row->isbn; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><label for="doi"><?php echo JText::_('DOI'); ?>:</label></td>
								<td colspan="3"><input type="text" name="doi" id="doi" size="30" maxlength="250" value="<?php echo $row->doi; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><label for="series"><?php echo JText::_('SERIES'); ?>:</label></td>
								<td colspan="3"><input type="text" name="series" id="series" size="30" maxlength="250" value="<?php echo $row->series; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><label for="edition"><?php echo JText::_('EDITION'); ?>:</label></td>
								<td colspan="3">
									<input type="text" name="edition" id="edition" size="30" maxlength="250" value="<?php echo $row->edition; ?>" /> 
									<br /><span style="font-size: 90%;color:#aaa;"><?php echo JText::_('EDITION_EXPLANATION'); ?></span>
								</td>
							</tr>
							<tr>
								<td class="key"><label for="school"><?php echo JText::_('SCHOOL'); ?>:</label></td>
								<td colspan="3"><input type="text" name="school" id="school" size="30" maxlength="250" value="<?php echo $row->school; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><label for="publisher"><?php echo JText::_('PUBLISHER'); ?>:</label></td>
								<td colspan="3"><input type="text" name="publisher" id="publisher" size="30" maxlength="250" value="<?php echo $row->publisher; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><label for="institution"><?php echo JText::_('INSTITUTION'); ?>:</label></td>
								<td colspan="3">
									<input type="text" name="institution" id="institution" size="30" maxlength="250" value="<?php echo $row->institution; ?>" /> 
									<br /><span style="font-size: 90%;color:#aaa;"><?php echo JText::_('INSTITUTION_EXPLANATION'); ?></span>
								</td>
							</tr>
							<tr>
								<td class="key"><label for="address"><?php echo JText::_('ADDRESS'); ?>:</label></td>
								<td colspan="3"><input type="text" name="address" id="address" size="30" maxlength="250" value="<?php echo $row->address; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><label for="location"><?php echo JText::_('LOCATION'); ?>:</label></td>
								<td colspan="3">
									<input type="text" name="location" id="location" size="30" maxlength="250" value="<?php echo $row->location; ?>" /> 
					   				<span style="font-size: 90%;color:#aaa;"><?php echo JText::_('LOCATION_EXPLANATION'); ?></span>
								</td>
							</tr>
							<tr>
								<td class="key"><label for="howpublished"><?php echo JText::_('PUBLISH_METHOD'); ?>:</label></td>
								<td colspan="3">
									<input type="text" name="howpublished" id="howpublished" size="30" maxlength="250" value="<?php echo $row->howpublished; ?>" /> 
									<br /><span style="font-size: 90%;color:#aaa;"><?php echo JText::_('PUBLISH_METHOD_EXPLANATION'); ?></span>
								</td>
							</tr>
							<tr>
								<td class="key"><label for="uri"><?php echo JText::_('URL'); ?>:</label></td>
								<td colspan="3"><input type="text" name="uri" id="uri" size="30" maxlength="250" value="<?php echo $row->url; ?>" /></td>
							</tr>
							<tr>
								<td class="key"><label for="eprint"><?php echo JText::_('EPRINT'); ?>:</label></td>
								<td colspan="3">
									<input type="text" name="eprint" id="eprint" size="30" maxlength="250" value="<?php echo $row->eprint; ?>" />
									<br /><span style="font-size: 90%;color:#aaa;"><?php echo JText::_('EPRINT_EXPLANATION'); ?></span>
								</td>
							</tr>
							<tr>
								<td class="key"><?php echo JText::_('NOTES'); ?>:</td>
								<td colspan="3">
									<?php
									echo $editor->display('note', stripslashes($row->note), '360px', '200px', '50', '10');
									?>
								</td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div>
			<div class="col width-30">
				<fieldset class="adminform">
					<legend><?php echo JText::_('CITATION_FOR'); ?></legend>
					
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
								$r = count($assocs);
								if($r > 5) {
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
									echo t.t.t.'  <tr>'.n;
									echo t.t.t.'   <td><input type="text" name="assocs['.$i.'][oid]" value="'.$assocs[$i]->oid.'" size="5" /></td>'.n;
									echo t.t.t.'   <td><input type="text" name="assocs['.$i.'][type]" value="'.$assocs[$i]->type.'" size="5" /></td>'.n;
									echo t.t.t.'   <td><select name="assocs['.$i.'][table]">'.n;
									echo ' <option value=""';
									echo ($assocs[$i]->table == '') ? ' selected="selected"': '';
									echo '>'.JText::_('SELECT').'</option>'.n;
									echo ' <option value="content"';
									echo ($assocs[$i]->table == 'content') ? ' selected="selected"': '';
									echo '>'.JText::_('CONTENT').'</option>'.n;
									echo ' <option value="resource"';
									echo ($assocs[$i]->table == 'resource') ? ' selected="selected"': '';
									echo '>'.JText::_('RESOURCE').'</option>'.n;
									echo ' <option value="topic"';
									echo ($assocs[$i]->table == 'topic') ? ' selected="selected"': '';
									echo '>'.JText::_('TOPIC').'</option>'.n;
									echo '</select>'.n;
									echo t.t.t.t.'<input type="hidden" name="assocs['.$i.'][id]" value="'.$assocs[$i]->id.'" />'.n;
									echo t.t.t.t.'<input type="hidden" name="assocs['.$i.'][cid]" value="'.$assocs[$i]->cid.'" /></td>'.n;
									echo t.t.t.'  </tr>'.n;
								}
						?>
						</tbody>
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend><?php echo JText::_('AFFILIATION'); ?></legend>
					
					<table class="adminform">
						<tbody>
							<tr>
								<td>
									<label>
										<input type="checkbox" name="affiliated" id="affiliated" value="1"<?php if ($row->affiliated) { echo ' checked="checked"'; } ?> />
										<?php echo JText::_('AFFILIATED_WITH_YOUR_ORG'); ?>
									</label>
								</td>
							</tr>
							<tr>
								<td>
									<label>
										<input type="checkbox" name="fundedby" id="fundedby" value="1"<?php if ($row->fundedby) { echo ' checked="checked"'; } ?> />
										<?php echo JText::_('FUNDED_BY_YOUR_ORG'); ?>
									</label>
								</td>
							</tr>
						</tbody>
					</table>
					
					<input type="hidden" name="uid" value="<?php echo $row->uid; ?>" />
					<input type="hidden" name="created" value="<?php echo $row->created; ?>" />
					<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
					<input type="hidden" name="option" value="<?php echo $option; ?>" />
					<input type="hidden" name="task" value="save" />
				</fieldset>
			</div>
			<div class="clr"></div>
		</form>
		<?php
	}
	
	//-----------
	
	public function stats( $stats, $option ) 
	{
		$html  = '<table class="admintable">'.n;
		$html .= t.'<thead>'.n;
		$html .= t.t.'<tr>'.n;
		$html .= t.t.t.'<th>'.JText::_('YEAR').'</th>'.n;
		$html .= t.t.t.'<th>'.JText::_('AFFILIATED').'</th>'.n;
		$html .= t.t.t.'<th>'.JText::_('NONAFFILIATED').'</th>'.n;
		$html .= t.t.t.'<th>'.JText::_('TOTAL').'</th>'.n;
		$html .= t.t.'<tr>'.n;
		$html .= t.'</thead>'.n;
		$html .= t.'<tbody>'.n;
		foreach ($stats as $year=>$amt) 
		{
			$html .= t.t.'<tr>'.n;
			$html .= t.t.t.'<th>'.$year.'</th>'.n;
			$html .= t.t.t.'<td>'.$amt['affiliate'].'</td>'.n;
			$html .= t.t.t.'<td>'.$amt['non-affiliate'].'</td>'.n;
			$html .= t.t.t.'<td><span style="color:#c00;">'.(intval($amt['affiliate']) + intval($amt['non-affiliate'])).'</span></td>'.n;
			$html .= t.t.'</tr>'.n;
		}
		$html .= t.'</tbody>'.n;
		$html .= '</table>'.n;
		echo $html;
	}
}
?>
