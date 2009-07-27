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
	public function error( $msg, $tag='p' )
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------
	
	public function warning( $msg, $tag='p' )
	{
		return '<'.$tag.' class="warning">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------
	
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}

	//-----------
	
	public function hed($level, $txt)
	{
		return '<h'.$level.'>'.$txt.'</h'.$level.'>';
	}

	//-----------

	public function div($txt, $cls='', $id='')
	{
		$html  = '<div';
		$html .= ($cls) ? ' class="'.$cls.'"' : '';
		$html .= ($id) ? ' id="'.$id.'"' : '';
		$html .= '>';
		$html .= ($txt != '') ? n.$txt.n : '';
		$html .= '</div><!-- / ';
		if ($id) {
			$html .= '#'.$id;
		}
		if ($cls) {
			$html .= '.'.$cls;
		}
		$html .= ' -->'.n;
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

	public function select($name, $array, $value, $class='')
	{
		$out  = '<select name="'.$name.'" id="'.$name.'"';
		$out .= ($class) ? ' class="'.$class.'">'.n : '>'.n;
		foreach ($array as $avalue => $alabel) 
		{
			$selected = ($avalue == $value || $alabel == $value)
					  ? ' selected="selected"'
					  : '';
			$out .= ' <option value="'.$avalue.'"'.$selected.'>'.$alabel.'</option>'.n;
		}
		$out .= '</select>'.n;
		return $out;
	}
	
	//-----------
	
	public function cleanUrl( $url ) 
	{
		$url = stripslashes($url);
		$url = str_replace('&amp;', '&', $url);
		$url = str_replace('&', '&amp;', $url);
		
		return $url;
	}
	
	//-----------

	public function browse( $database, $citations, $pageNav, $option, $task, $filters ) 
	{
		$types = array('all'=>JText::_('ALL'),
						'article'=>JText::_('ARTICLE'),
						'book'=>JText::_('BOOK'),
						'booklet'=>JText::_('BOOKLET'),
						'conference'=>JText::_('CONFERENCE'),
						'inbook'=>JText::_('INBOOK'),
						'incollection'=>JText::_('INCOLLECTION'),
						'inproceedings'=>JText::_('INPROCEEDINGS'),
						'journal'=>JText::_('JOURNAL'),
						'magazine'=>JText::_('MAGAZINE'),
						'manual'=>JText::_('MANUAL'),
						'mastersthesis'=>JText::_('MASTERSTHESIS'),
						'misc'=>JText::_('MISC'),
						'phdthesis'=>JText::_('PHDTHESIS'),
						'proceedings'=>JText::_('PROCEEDINGS'),
						'techreport'=>JText::_('TECHREPORT'),
						'unpublished'=>JText::_('UNPUBLISHED')
						);
		
		$filter = array('all'=>JText::_('ALL'),
						'aff'=>JText::_('AFFILIATE'),
						'nonaff'=>JText::_('NONAFFILIATE')
						);
						
		$sorts = array('year DESC'=>JText::_('YEAR'),
						'created DESC'=>JText::_('NEWEST'),
						'title ASC'=>JText::_('TITLE'),
						'author ASC'=>JText::_('AUTHORS'),
						'journal ASC'=>JText::_('JOURNAL')
						);
			
		$html  = CitationsHtml::div( CitationsHtml::hed(2,JText::_('CITATIONS').': '.JText::_(strtoupper($task))), '', 'content-header').n;
		$html .= CitationsHtml::div( '<p><a class="add" href="'.JRoute::_('index.php?option='.$option.a.'task=add').'">'.JText::_('Add a citation').'</a></p>', '', 'content-header-extra').n;
		$html .= '<div class="main section">'.n;
		$html .= '<form action="'. JRoute::_('index.php?option='.$option.a.'task='.$task) .'" id="citeform" method="post">'.n;
		$html .= '<div class="aside">'.n;
		$html .= t.'<fieldset>'.n;
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.'Type:'.n;
		$html .= CitationsHtml::select('type',$types,$filters['type']);
		$html .= t.t.'</label>'.n;
			
		$html .= t.t.'<label>'.JText::_('FILTER').':'.n;
		$html .= CitationsHtml::select('filter',$filter,$filters['filter']);
		$html .= t.t.'</label>'.n;

		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('FOR_YEAR').':'.n;
		$html .= t.t.t.'<select name="year">'.n;
		$html .= t.t.t.t.'<option value=""';
		if ($filters['year'] == 0) { 
			$html .= ' selected="selected"';
		}
		$html .= '>'.JText::_('All').'</option>'.n;
		
		$y = date("Y");
		$y++;
		for ($i=1995, $n=$y; $i < $n; $i++) 
		{
			$html .= t.t.t.t.'<option value="'. $i .'"';
			if ($filters['year'] == $i) { 
				$html .= ' selected="selected"';
			}
			$html .= '>'. $i .'</option>'.n;
		}
		$html .= t.t.t.'</select>'.n;
		$html .= t.t.'</label>'.n;
	
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('SORT_BY').':'.n;
		$html .= CitationsHtml::select('sortby',$sorts,$filters['sort']);
		$html .= t.t.'</label>'.n;
				
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('SEARCH_TITLE').':'.n;
		$html .= t.t.t.'<input type="text" name="search" value="'. $filters['search'] .'" />'.n;
		$html .= t.t.'</label>'.n;
				
		$html .= t.t.'<input type="submit" name="go" value="'.JText::_('GO').'" />'.n;
		$html .= t.t.'<input type="hidden" name="option" value="'. $option .'" />'.n;
		$html .= t.'</fieldset>'.n;
		
		$html .= '</div><!-- / .aside -->'.n;
		$html .= '<div class="subject">'.n;
		
		if (count($citations) > 0) {
			$html .= '<ul class="citations results">'.n;
			foreach ($citations as $cite) 
			{
				// Get the associations
				$assoc = new CitationsAssociation( $database );
				$assocs = $assoc->getRecords( array('cid'=>$cite->id) );
				
				$html .= ' <li>'.n;
				$html .= CitationsFormatter::formatReference( $cite, $cite->url );
				$html .= t.'<p class="details">'.n;
				$html .= t.t.'<a href="'.JRoute::_('index.php?option='.$option.a.'task=download'.a.'id='.$cite->id.a.'format=bibtex'.a.'no_html=1').'" title="'.JText::_('DOWNLOAD_BIBTEX').'">BibTex</a> <span>|</span> '.n;
				$html .= t.t.'<a href="'.JRoute::_('index.php?option='.$option.a.'task=download'.a.'id='.$cite->id.a.'format=endnote'.a.'no_html=1').'" title="'.JText::_('DOWNLOAD_ENDNOTE').'">EndNote</a>'.n;
				if (count($assocs) > 0 || $cite->eprint) {
					if (count($assocs) > 0) {
						if (count($assocs) > 1) {
							$html .= t.t.' <span>|</span> '.JText::_('RESOURCES_CITED').': '.n;
							$k = 0;
							$rrs = array();
							foreach ($assocs as $rid) 
							{
								if ($rid->table == 'resource') {
									$database->setQuery( "SELECT published FROM #__resources WHERE id=".$rid->oid );
									$state = $database->loadResult();
									if ($state == 1) {
										$k++;
										//$html .= t.t.' <a href="'.JRoute::_('index.php?option=com_resources&id='.$rid->oid).'">['.$k.']</a>,'.n;
										$rrs[] = '<a href="'.JRoute::_('index.php?option=com_resources&id='.$rid->oid).'">['.$k.']</a>';
									}
								}
							}
							$html .= t.t.implode(', ',$rrs).n;
						} else {
							if ($assocs[0]->table == 'resource') {
								$database->setQuery( "SELECT published FROM #__resources WHERE id=".$assocs[0]->oid );
								$state = $database->loadResult();
								if ($state == 1) {
									$html .= t.t.' <span>|</span> <a href="'.JRoute::_('index.php?option=com_resources&id='.$assocs[0]->oid).'">'.JText::_('RESOURCE_CITED').'</a>'.n;
								}
							}
						}
					}
					if ($cite->eprint) {
						$html .= t.t.' <span>|</span> <a href="'.CitationsHtml::cleanUrl($cite->eprint).'">'.JText::_('ELECTRONIC_PAPER').'</a>'.n;
					}
				}
				$html .= t.'</p>'.n;
				$html .= ' </li>'.n;
			}
			$html .= '</ul>'.n;
			$html .= $pageNav->getListFooter();
		} else {
			$html .= CitationsHtml::warning( JText::_('NO_CITATIONS_FOUND') ).n;
		}
		$html .= '</div><!-- / .subject -->'.n;
		$html .= '<div class="clear"></div>'.n;
		$html .= '</form>'.n;
		$html .= '</div><!-- / .main section -->'.n;
		
		return $html;
	}
	
	//-----------
	
	public function edit( $row, $assocs, $option, $title ) 
	{
		$types = array('article','book','booklet','conference','inbook','incollection','inproceedings','journal','magazine','manual','mastersthesis','misc','phdthesis','proceedings','techreport','unpublished');
		
		jimport('joomla.html.editor');
		$editor =& JEditor::getInstance();
		
		?>
		<div id="content-header" class="full">
			<h2><?php echo $title; ?></h2>
		</div>
		<div class="main section">
		
			<form action="index.php" method="post" id="hubForm">
				<div class="explaination">
					<p>Please enter the information for the work that references content on this site. <strong>Not all fields may apply to the citation</strong> - fill in only those that do.</p>
				</div>
				<fieldset>
					<h3><?php echo JText::_('DETAILS'); ?></h3>

					<div class="group twoup">
					<label for="type">
						<?php echo JText::_('TYPE'); ?>:
						<?php echo CitationsHtml::select('type', $types, $row->type); ?>
					</label>

					<label for="cite">
						<?php echo JText::_('CITE_KEY'); ?>:
						<input type="text" name="cite" id="cite" size="30" maxlength="250" value="<?php echo $row->cite; ?>" />
						<span style="font-size: 90%;color:#aaa;"><?php echo JText::_('CITE_KEY_EXPLANATION'); ?></span>
					</label>
					</div>

					<label for="ref_type">
						<?php echo JText::_('REF_TYPE'); ?>:
						<input type="text" name="ref_type" id="ref_type" size="11" maxlength="50" value="<?php echo $row->ref_type; ?>" />
					</label>
					
					<div class="group threeup">
					<label for="date_submit">
						<?php echo JText::_('DATE_SUBMITTED'); ?>:
						<input type="text" name="date_submit" id="date_submit" size="30" maxlength="250" value="<?php echo $row->date_submit; ?>" />
						<span class="hint">YYYY-MM-DD HH:MM:SS</span>
					</label>

					<label for="date_accept">
						<?php echo JText::_('DATE_ACCEPTED'); ?>:
						<input type="text" name="date_accept" id="date_accept" size="30" maxlength="250" value="<?php echo $row->date_accept; ?>" />
						<span class="hint">YYYY-MM-DD HH:MM:SS</span>
					</label>

					<label for="date_publish">
						<?php echo JText::_('DATE_PUBLISHED'); ?>:
						<input type="text" name="date_publish" id="date_publish" size="30" maxlength="250" value="<?php echo $row->date_publish; ?>" />
						<span class="hint">YYYY-MM-DD HH:MM:SS</span>
					</label>
					</div>

					<!--
					<div class="group twoup">
					<label for="year">
						<?php echo JText::_('YEAR'); ?>:
						<input type="text" name="year" id="year" size="4" maxlength="4" value="<?php echo $row->year; ?>" />
					</label>

					<label for="month">
						<?php echo JText::_('MONTH'); ?>:
						<input type="text" name="month" id="month" size="11" maxlength="50" value="<?php echo $row->month; ?>" />
					</label>
					</div> -->

					<label for="author">
						<?php echo JText::_('AUTHORS'); ?>:
						<input type="text" name="author" id="author" size="30" value="<?php echo $row->author; ?>" />
						<span class="hint">Lastname, Firstname; Lastname, Firstname; Lastname ...</span>
					</label>

					<label for="editor">
						<?php echo JText::_('EDITORS'); ?>:
						<input type="text" name="editor" id="editor" size="30" maxlength="250" value="<?php echo $row->editor; ?>" />
						<span class="hint">Lastname, Firstname; Lastname, Firstname; Lastname ...</span>
					</label>

					<label for="title">
						<?php echo JText::_('TITLE_CHAPTER'); ?>:
						<input type="text" name="title" id="title" size="30" maxlength="250" value="<?php echo $row->title; ?>" />
					</label>

					<label for="booktitle">
						<?php echo JText::_('BOOK_TITLE'); ?>:
						<input type="text" name="booktitle" id="booktitle" size="30" maxlength="250" value="<?php echo $row->booktitle; ?>" />
					</label>

					<label for="journal">
						<?php echo JText::_('JOURNAL'); ?>:
						<input type="text" name="journal" id="journal" size="30" maxlength="250" value="<?php echo $row->journal; ?>" />
					</label>
					
					<div class="group threeup">
					<label for="volume">
						<?php echo JText::_('VOLUME'); ?>:
						<input type="text" name="volume" id="volume" size="11" maxlength="11" value="<?php echo $row->volume; ?>" />
					</label>

					<label for="number">
						<?php echo JText::_('ISSUE'); ?>:
						<input type="text" name="number" id="number" size="11" maxlength="50" value="<?php echo $row->number; ?>" />
					</label>

					<label for="pages">
						<?php echo JText::_('PAGES'); ?>:
						<input type="text" name="pages" id="pages" size="11" maxlength="250" value="<?php echo $row->pages; ?>" />
					</label>
					</div>
					
					<div class="group twoup">
					<label for="isbn">
						<?php echo JText::_('ISBN'); ?>:
						<input type="text" name="isbn" id="isbn" size="11" maxlength="50" value="<?php echo $row->isbn; ?>" />
					</label>

					<label for="doi">
						<abbr title="Digital Object Identifier"><?php echo JText::_('DOI'); ?></abbr>:
						<input type="text" name="doi" id="doi" size="30" maxlength="250" value="<?php echo $row->doi; ?>" />
					</label>
					</div>

					<label for="series">
						<?php echo JText::_('SERIES'); ?>:
						<input type="text" name="series" id="series" size="30" maxlength="250" value="<?php echo $row->series; ?>" />
					</label>

					<label for="edition">
						<?php echo JText::_('EDITION'); ?>:
						<input type="text" name="edition" id="edition" size="30" maxlength="250" value="<?php echo $row->edition; ?>" /> 
						<span style="font-size: 90%;color:#aaa;"><?php echo JText::_('EDITION_EXPLANATION'); ?></span>
					</label>

					<label for="school">
						<?php echo JText::_('SCHOOL'); ?>:
						<input type="text" name="school" id="school" size="30" maxlength="250" value="<?php echo $row->school; ?>" />
					</label>

					<label for="publisher">
						<?php echo JText::_('PUBLISHER'); ?>:
						<input type="text" name="publisher" id="publisher" size="30" maxlength="250" value="<?php echo $row->publisher; ?>" />
					</label>

					<label for="institution">
						<?php echo JText::_('INSTITUTION'); ?>:
						<input type="text" name="institution" id="institution" size="30" maxlength="250" value="<?php echo $row->institution; ?>" /> 
						<span style="font-size: 90%;color:#aaa;"><?php echo JText::_('INSTITUTION_EXPLANATION'); ?></span>
					</label>

					<label for="address">
						<?php echo JText::_('ADDRESS'); ?>:
						<input type="text" name="address" id="address" size="30" maxlength="250" value="<?php echo $row->address; ?>" />
					</label>

					<label for="location">
						<?php echo JText::_('LOCATION'); ?>:
						<input type="text" name="location" id="location" size="30" maxlength="250" value="<?php echo $row->location; ?>" /> 
						<span style="font-size: 90%;color:#aaa;"><?php echo JText::_('LOCATION_EXPLANATION'); ?></span>
					</label>

					<label for="howpublished">
						<?php echo JText::_('PUBLISH_METHOD'); ?>:
						<input type="text" name="howpublished" id="howpublished" size="30" maxlength="250" value="<?php echo $row->howpublished; ?>" /> 
						<span style="font-size: 90%;color:#aaa;"><?php echo JText::_('PUBLISH_METHOD_EXPLANATION'); ?></span>
					</label>

					<label for="uri">
						<?php echo JText::_('URL'); ?>:
						<input type="text" name="uri" id="uri" size="30" maxlength="250" value="<?php echo $row->url; ?>" />
					</label>

					<label for="eprint">
						<?php echo JText::_('EPRINT'); ?>:
						<input type="text" name="eprint" id="eprint" size="30" maxlength="250" value="<?php echo $row->eprint; ?>" />
						<span style="font-size: 90%;color:#aaa;"><?php echo JText::_('EPRINT_EXPLANATION'); ?></span>
					</label>

					<label>
						<?php echo JText::_('NOTES'); ?>:
						<textarea name="note" rows="15" cols="10"><?php echo stripslashes($row->note); ?></textarea>
					</label>
				</fieldset><div class="clear"></div>
				<div class="explaination">
					<p>Please enter all the resources, articles, or topic pages the work references.</p>
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
									echo t.t.t.'  <tr>'.n;
									echo t.t.t.'   <td><input type="text" name="assocs['.$i.'][oid]" value="'.$assocs[$i]->oid.'" /></td>'.n;
									echo t.t.t.'   <td><input type="text" name="assocs['.$i.'][type]" value="'.$assocs[$i]->type.'" /></td>'.n;
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
				</fieldset><div class="clear"></div>
				<fieldset>
					<h3><?php echo JText::_('AFFILIATION'); ?></h3>
					
					<label>
						<input type="checkbox" class="option" name="affiliated" id="affiliated" value="1"<?php if ($row->affiliated) { echo ' checked="checked"'; } ?> />
						<?php echo JText::_('AFFILIATED_WITH_YOUR_ORG'); ?>
					</label>

					<label>
						<input type="checkbox" class="option" name="fundedby" id="fundedby" value="1"<?php if ($row->fundedby) { echo ' checked="checked"'; } ?> />
						<?php echo JText::_('FUNDED_BY_YOUR_ORG'); ?>
					</label>
					
					<input type="hidden" name="uid" value="<?php echo $row->uid; ?>" />
					<input type="hidden" name="created" value="<?php echo $row->created; ?>" />
					<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
					<input type="hidden" name="option" value="<?php echo $option; ?>" />
					<input type="hidden" name="task" value="save" />
				</fieldset>
				<div class="clear"></div>
				<p class="submit"><input type="submit" name="create" value="Save" /></p>
			</form>
		</div>
		<?php
	}
}
?>
