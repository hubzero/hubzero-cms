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

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_resources_citations' );
	
//-----------

class plgResourcesCitations extends JPlugin
{
	function plgResourcesCitations(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'resources', 'citations' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	function &onResourcesAreas( $resource )
	{
		$areas = array(
			'citations' => JText::_('CITATIONS')
		);
		return $areas;
	}

	//-----------

	function onResources( $resource, $option, $areas, $rtrn='all' )
	{
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (!array_intersect( $areas, $this->onResourcesAreas( $resource ) ) 
			&& !array_intersect( $areas, array_keys( $this->onResourcesAreas( $resource ) ) )) {
				$rtrn = 'metadata';
			}
		}
		
		$database =& JFactory::getDBO();

		$xuser =& XFactory::getUser();

		// Get a needed library
		include_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_citations'.DS.'citations.class.php');

		// Get reviews for this resource
		$c = new CitationsCitation( $database );
		$citations = $c->getCitations( 'resource', $resource->id );
		
		$html = '';
		if ($rtrn == 'all' || $rtrn == 'html') {
			$numaff = 0;
			$numnon = 0;
			
			// Did we get any results back?
			if ($citations) {
				// Get a needed library
				include_once(JPATH_ROOT.DS.'components'.DS.'com_citations'.DS.'citations.formatter.php');

				// Set some vars
				$affiliated = '';
				$nonaffiliated = '';

				// Loop through the citations and build the HTML
				foreach ($citations as $cite) 
				{
					$item  = t.'<li>'.n;
					$item .= t.t.CitationsFormatter::formatReference($cite,$cite->url).n;
					$item .= t.t.'<p class="details">'.n;
					$item .= t.t.t.'<a href="index2.php?option=com_citations'.a.'task=download'.a.'id='.$cite->id.a.'format=bibtex'.a.'no_html=1" title="'.JText::_('DOWNLOAD_BIBTEX').'">BibTex</a> <span>|</span> '.n;
					$item .= t.t.t.'<a href="index2.php?option=com_citations'.a.'task=download'.a.'id='.$cite->id.a.'format=endnote'.a.'no_html=1" title="'.JText::_('DOWNLOAD_ENDNOTE').'">EndNote</a>'.n;
					if ($cite->eprint) {
						if ($cite->eprint) {
							$item .= t.t.t.' <span>|</span> <a href="'.stripslashes($cite->eprint).'">'.JText::_('ELECTRONIC_PAPER').'</a>'.n;
						}
					}
					//if ($xuser->get('uid') == $cite->uid) {
					//	$html .= t.t.t.' <span>|</span> <a class="edit button" href="'.sefRelToAbs('index.php?option='.$option.a.'id='.$resource->id.a.'task=editcitation#citationform').'" title="Edit your citation">edit</a>'.n;
					//}
					$item .= t.t.'</p>'.n;
					$item .= t.'</li>'.n;

					// Decide which group to add it to
					if ($cite->affiliated) {
						$affiliated .= $item;
						$numaff++;
					} else {
						$nonaffiliated .= $item;
						$numnon++;
					}
				}
				$sbjt = '';
				if ($nonaffiliated) {
					$sbjt .= ResourcesHtml::hed(4,JText::_('CITATION_NOT_AFFILIATED')).n;
					$sbjt .= '<ul class="citations results">'.n;
					$sbjt .= $nonaffiliated;
					$sbjt .= '</ul>'.n;
				}
				if ($affiliated) {
					$sbjt .= ResourcesHtml::hed(4,JText::_('CITATION_AFFILIATED')).n;
					$sbjt .= '<ul class="citations results">'.n;
					$sbjt .= $affiliated;
					$sbjt .= '</ul>'.n;
				}
			} else {
				$sbjt = '<p>'.JText::_('NO_CITATIONS_FOUND').'</p>'.n;
			}

			$html  = ResourcesHtml::hed(3,'<a name="citations"></a>'.JText::_('CITATIONS').'<span><a href="'.JRoute::_('index.php?option=com_resources'.a.'id='.$resource->id.a.'active=citations#nonaffiliated').'">Non-affiliated ('.$numnon.')</a>  |  <a href="'.JRoute::_('index.php?option=com_resources'.a.'id='.$resource->id.a.'active=citations#affiliated').'">Affiliated ('.$numaff.')</a></span>').n;
			/*
			$html .= ResourcesHtml::aside(
						'<p>The following are publications that have cited this resource, separated by their affiliation to the site or its parent organization.</p>'
						.'<ul><li><a href="'.JRoute::_('index.php?option=com_resources'.a.'id='.$resource->id.a.'active=citations#nonaffiliated').'">Non-affiliated ('.$numnon.')</a></li>'
						.'<li><a href="'.JRoute::_('index.php?option=com_resources'.a.'id='.$resource->id.a.'active=citations#affiliated').'">Affiliated ('.$numaff.')</a></li></ul>'
						//'<p><a href="'.sefRelToAbs('index.php?option='.$option.a.'task=addcitation'.a.'id='.$id.'#citationform').'" class="add">Add a citation</a></p>'
					);
					*/
			//$html .= ResourcesHtml::subject($sbjt);
			$html .= $sbjt;
		}

		$metadata = '';
		if ($rtrn == 'all' || $rtrn == 'metadata') {
			if ($resource->alias) {
				$url = JRoute::_('index.php?option=com_resources'.a.'alias='.$resource->alias.a.'active=citations');
			} else {
				$url = JRoute::_('index.php?option=com_resources'.a.'id='.$resource->id.a.'active=citations');
			}
			
			$metadata  = '<p class="citation"><a href="'.$url.'">'.count($citations).' citation';
			$metadata .= (count($citations) == 1) ? '' : 's';
			$metadata .= '</a></p>'.n;
		}

		$arr = array(
				'html'=>$html,
				'metadata'=>$metadata
			);

		return $arr;
	}
}


class PlgResourcesCitationsHelper
{
	private $_data  = array();
	public $error = NULL;
	
	//-----------

	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}
	
	//-----------
	
	public function execute()
	{
		// Incoming action
		$action = JRequest::getVar( 'action', '' );
		
		$this->loggedin = true;
		
		if ($action) {
			// Check the user's logged-in status
			$juser =& JFactory::getUser();
			if ($juser->get('guest')) {
				$this->loggedin = false;
				return;
			}
		}
		
		// Perform an action
		switch ( $action ) 
		{
			case 'editcitation':   $this->editcitation();   break;
			case 'addcitation':    $this->editcitation();   break;
			case 'deletecitation': $this->deletecitation(); break;
			case 'savecitation':   $this->savecitation();   break;
			default: $this->editcitation(); break;
		}
	}
	
	//-----------
	
	public function editcitation()
	{
		$database =& JFactory::getDBO();
		$resource =& $this->resource;
		$juser =& JFactory::getUser();
		
		include_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_citations'.DS.'citations.class.php');
				
		// Retrieve a citation ID if we're editing
		$cid = JRequest::getInt( 'cid', 0 );
		
		// Do we have an ID?
		if (!$resource->id) {
			// No - fail! Can't do anything else without an ID
			echo ResourcesHtml::error( JText::_('NO_RESOURCE_ID') );
			return;
		}
		
		$citation = new CitationsCitation( $database );
		$citation->load( $cid );
		if (!$cid) {
			// New citation - get the user's ID
			$citation->uid = $juser->get('username');
			$citation->rid = $id;
		}
		
		// Store the citation object in our registry
		$this->citation = $citation;
	}
	
	//-----------
	
	public function savecitation()
	{
		include_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_citations'.DS.'citations.class.php');
		
		$database =& JFactory::getDBO();
		
		// Incoming
		$pid = JRequest::getInt( 'rid', 0, 'post' );
		
		// Bind the form data to our object
		$row = new CitationsCitation( $database );
		if (!$row->bind( $_POST )) {
			echo ResourcesHtml::alert( $row->getError() );
			exit();
		}

		// If new, set the creation datetime
		if (!$row->id) {
			$row->created = date( 'Y-m-d H:i:s', time() );
			
			$auth = substr($row->author,0,5);
			$auth = strtolower(trim($auth));
			$row->cite = $row->uid.$auth.$row->year;
		}
		
		$row->url = trim(JRequest::getVar( 'uri', '', 'post' ));
		
		// Check for missing (required) fields
		if (!$row->check()) {
			echo ResourcesHtml::alert( $row->getError() );
			exit();
		}
		// Save the data
		if (!$row->store()) {
			echo ResourcesHtml::alert( $row->getError() );
			exit();
		}
	}
	
	//-----------
	
	public function deletecitation()
	{
		include_once(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_citations'.DS.'citations.class.php');
		
		$database =& JFactory::getDBO();
		
		// Incoming
		$pid = JRequest::getInt( 'pid', 0 );
		$cid = JRequest::getInt( 'id', 0 );
		
		if ($cid) {
			// Delete the citation
			$citation = new CitationsCitation( $database );
			$citation->delete( $cid );
		}
	}
	
	//-----------
	
	public function citationForm( $citation, $option ) 
	{
		$html  = '<form action="index.php" method="post" id="hubForm">'.n;
		$html .= t.'<fieldset>'.n;
		$html .= ResourcesHtml::hed(3,'<a name="citationform"></a>Add a citation').n;
		
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.'Type:'.n;
		$html .= t.t.t.'<select name="type">'.n;
		$html .= t.t.t.t.'<option value="article" selected="selected">Article</option>'.n;
		$html .= t.t.t.t.'<option value="book">Book</option>'.n;
		$html .= t.t.t.t.'<option value="booklet">Booklet</option>'.n;
		$html .= t.t.t.t.'<option value="conference">Conference</option>'.n;
		$html .= t.t.t.t.'<option value="inbook">Inbook</option>'.n;
		$html .= t.t.t.t.'<option value="incollection">Incollection</option>'.n;
		$html .= t.t.t.t.'<option value="inproceedings">Inproceedings</option>'.n;
		$html .= t.t.t.t.'<option value="manual">Manual</option>'.n;
		$html .= t.t.t.t.'<option value="mastersthesis">Masters thesis</option>'.n;
		$html .= t.t.t.t.'<option value="misc">Miscellaneous</option>'.n;
		$html .= t.t.t.t.'<option value="phdthesis">PhD thesis</option>'.n;
		$html .= t.t.t.t.'<option value="proceedings">Proceedings</option>'.n;
		$html .= t.t.t.t.'<option value="techreport">Tech report</option>'.n;
		$html .= t.t.t.t.'<option value="unpublished">Unpublished</option>'.n;
		$html .= t.t.t.'</select>'.n;
		$html .= t.t.'</label>'.n;
		
		$html .= t.t.'<div class="group">'.n;
		$html .= t.t.t.'<label>'.n;
		$html .= t.t.t.t.'Year:'.n;
		$html .= t.t.t.t.'<input type="text" name="year" size="4" maxlength="4" value="'.$citation->year.'" />'.n;
		$html .= t.t.t.'</label>'.n;
		$html .= t.t.t.'<label>'.n;
		$html .= t.t.t.t.'Month:'.n;
		$html .= t.t.t.t.'<input type="text" name="month" size="11" maxlength="50" value="'.$citation->month.'" />'.n;
		$html .= t.t.t.'</label>'.n;
		$html .= t.t.'</div><!-- / .group -->'.n;
		
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.'Author(s) separated by semicolons (;):'.n;
		$html .= t.t.t.'<input type="text" name="author" value="'. $citation->author .'" size="38" />'.n;
		$html .= t.t.'</label>'.n;
		
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.'Editor(s):'.n;
		$html .= t.t.t.'<input type="text" name="editor" value="'. $citation->editor .'" size="38" />'.n;
		$html .= t.t.'</label>'.n;
		
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.'Chapter/Article Title:'.n;
		$html .= t.t.t.'<input type="text" name="title" value="'. $citation->title .'" size="38" />'.n;
		$html .= t.t.'</label>'.n;
		
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.'Book title (if from a book):'.n;
		$html .= t.t.t.'<input type="text" name="booktitle" value="'. $citation->booktitle .'" size="38" />'.n;
		$html .= t.t.'</label>'.n;

		$html .= t.t.'<label>'.n;
		$html .= t.t.t.'Journal title (if from a journal):'.n;
		$html .= t.t.t.'<input type="text" name="journal" value="'. $citation->journal .'" size="38" />'.n;
		$html .= t.t.'</label>'.n;
		
		$html .= t.t.'<div class="group">'.n;
		$html .= t.t.t.'<label>'.n;
		$html .= t.t.t.t.'Volume:'.n;
		$html .= t.t.t.t.'<input type="text" name="volume" size="11" maxlength="11" value="'.$citation->volume.'" />'.n;
		$html .= t.t.t.'</label>'.n;
		$html .= t.t.t.'<label>'.n;
		$html .= t.t.t.t.'Issue/Number:'.n;
		$html .= t.t.t.t.'<input type="text" name="number" size="11" maxlength="50" value="'.$citation->number.'" />'.n;
		$html .= t.t.t.'</label>'.n;
		$html .= t.t.'</div><!-- / .group -->'.n;
		
		$html .= t.t.'<div class="group">'.n;
		$html .= t.t.t.'<label>'.n;
		$html .= t.t.t.t.'Pages:'.n;
		$html .= t.t.t.t.'<input type="text" name="pages" size="11" maxlength="250" value="'.$citation->pages.'" />'.n;
		$html .= t.t.t.'</label>'.n;
		$html .= t.t.t.'<label>'.n;
		$html .= t.t.t.t.'ISBN/ISSN:'.n;
		$html .= t.t.t.t.'<input type="text" name="isbn" size="11" maxlength="250" value="'.$citation->isbn.'" />'.n;
		$html .= t.t.t.'</label>'.n;
		$html .= t.t.'</div><!-- / .group -->'.n;

		$html .= t.t.'<div class="group">'.n;
		$html .= t.t.t.'<label>'.n;
		$html .= t.t.t.t.'Series:'.n;
		$html .= t.t.t.t.'<input type="text" name="series" size="11" maxlength="250" value="'.$citation->series.'" />'.n;
		$html .= t.t.t.'</label>'.n;
		$html .= t.t.t.'<label>'.n;
		$html .= t.t.t.t.'Edition:'.n;
		$html .= t.t.t.t.'<input type="text" name="edition" size="11" maxlength="250" value="'.$citation->edition.'" />'.n;
		$html .= t.t.t.t.'<span class="hint">The edition of a book, long form (such as "first" or "second")</span>'.n;
		$html .= t.t.t.'</label>'.n;
		$html .= t.t.'</div><!-- / .group -->'.n;
		
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.'School:'.n;
		$html .= t.t.t.'<input type="text" name="school" value="'. $citation->school .'" size="38" />'.n;
		$html .= t.t.'</label>'.n;
		
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.'Publisher:'.n;
		$html .= t.t.t.'<input type="text" name="publisher" value="'. $citation->publisher .'" size="38" />'.n;
		$html .= t.t.'</label>'.n;
		
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.'Institution:'.n;
		$html .= t.t.t.'<input type="text" name="institution" value="'. $citation->institution .'" size="38" />'.n;
		$html .= t.t.t.'<span class="hint">The institution involved in publishing, but not necessarily the publisher</span>'.n;
		$html .= t.t.'</label>'.n;
		
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.'Address:'.n;
		$html .= t.t.t.'<input type="text" name="address" value="'. $citation->address .'" size="38" />'.n;
		$html .= t.t.'</label>'.n;
		
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.'Location:'.n;
		$html .= t.t.t.'<input type="text" name="location" value="'. $citation->location .'" size="38" />'.n;
		$html .= t.t.t.'<span class="hint">A location such as the city the conference took place</span>'.n;
		$html .= t.t.'</label>'.n;
		
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.'How published:'.n;
		$html .= t.t.t.'<input type="text" name="howpublished" value="'. $citation->howpublished .'" size="38" />'.n;
		$html .= t.t.t.'<span class="hint">How it was published, if the publishing method is nonstandard</span>'.n;
		$html .= t.t.'</label>'.n;
		
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.'URL:'.n;
		$html .= t.t.t.'<input type="text" name="uri" value="'. $citation->url .'" size="38" />'.n;
		$html .= t.t.t.'<span class="hint">A link to this document\'s online reference</span>'.n;
		$html .= t.t.'</label>'.n;
		
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.'E-print:'.n;
		$html .= t.t.t.'<input type="text" name="eprint" value="'. $citation->eprint .'" size="38" />'.n;
		$html .= t.t.t.'<span class="hint">A link to an electronic version of the document such as a PDF</span>'.n;
		$html .= t.t.'</label>'.n;
		
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.'Text Snippet/Notes:'.n;
		$html .= t.t.t.'<textarea name="note" rows="7" cols="35">'. $citation->note .'</textarea>'.n;
		$html .= t.t.t.'<span class="hint">The portion of text that references this resource.</span>'.n;
		$html .= t.t.'</label>'.n;
		
		$html .= t.t.'<input type="hidden" name="cite" value="'. $citation->cite .'" />'.n;
		$html .= t.t.'<input type="hidden" name="created" value="'. $citation->created .'" />'.n;
		$html .= t.t.'<input type="hidden" name="id" value="'. $citation->id .'" />'.n;
		$html .= t.t.'<input type="hidden" name="uid" value="'. $citation->uid .'" />'.n;
		$html .= t.t.'<input type="hidden" name="rid" value="'. $citation->rid .'" />'.n;
		$html .= t.t.'<input type="hidden" name="option" value="'. $option .'" />'.n;
		$html .= t.t.'<input type="hidden" name="task" value="view" />'.n;
		$html .= t.t.'<input type="hidden" name="action" value="savecitation" />'.n;
		$html .= t.t.'<input type="hidden" name="active" value="citations" />'.n;
		$html .= t.t.'<p class="submit"><input type="submit" value="Submit" /></p>'.n;
		$html .= t.'</fieldset>'.n;
		$html .= '</form>'.n;
		
		return $html;
	}
}