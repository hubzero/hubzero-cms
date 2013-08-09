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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

if (!defined('n')) {

/**
 * Description for ''n''
 */
	define('n',"\n");

/**
 * Description for ''t''
 */
	define('t',"\t");

/**
 * Description for ''r''
 */
	define('r',"\r");

/**
 * Description for ''a''
 */
	define('a','&amp;');
}

include_once( JPATH_ROOT . DS . 'components' . DS . 'com_publications' . DS . 'helpers' . DS . 'tags.php' );

/**
 * Html helper class
 */
class PublicationsHtml 
{	
	/**
	 * Show gallery images
	 * 
	 * @param      array $shots
	 * @param      string $path
	 *
	 * @return     string
	 */	
	public function showGallery( $shots = array(), $path = '')
	{
		if (empty($shots) || !$path) 
		{
			return;
		}	
			
		$d = @dir(JPATH_ROOT.$path);
		if (!$d) 
		{
			return;
		}
		
		$html = '';
		$els = '';
		$i = 0;
		$k = 0;
		$g = 0;
		
		// Get default thumbnail
		$config =& JComponentHelper::getParams( 'com_publications' );
		$defaultThumb = $config->get('gallery_thumb', '/components/com_publications/assets/img/gallery_thumb.gif');
		
		// Go through schreenshots
		foreach ($shots as $shot) 
		{				
			if (!$shot->srcfile || !is_file(JPATH_ROOT.$path.DS.$shot->srcfile)) 
			{
				continue;
			}
			// Get extentsion
			$ext = explode('.', $shot->srcfile);
			$ext = strtolower(end($ext));
			
			// Get thumbnail
			$thumb = PublicationsHtml::createThumbName($shot->srcfile, '_tn', $extension = 'png');
			$src = $path.DS.$thumb;
			if (!is_file(JPATH_ROOT.$src)) 
			{
				$src = 	$defaultThumb;
			}

			if (is_file(JPATH_ROOT.$src)) 
			{
				$els .=  ($i==0 ) ? '<div class="showcase-pane">'."\n" : '';
				$title = $shot->title ? $shot->title : basename($shot->filename);
				if ($ext == 'swf' || $ext == 'mov') 
				{
					$g++;					
					$els .= ' <a class="video"  href="'.$path.DS.$shot->srcfile.'" title="'.$title.'">';
					$els .= '<img src="'.$src.'" alt="'.$title.'" /></a>';
				} 
				else 
				{
					$k++;
					$els .= ' <a rel="lightbox" href="'.$path.DS.$shot->srcfile.'" title="'.$title.'">';
					$els .= '<img src="'.$src.'" alt="'.$title.'" class="thumbima" /></a>';
				}
				$els .=  ($i == (count($shots) - 1)) ? '</div>'."\n" : '';
				$i++;
			}
		}
		
		if ($els && $i > 0) 
		{
			$html .= '<div id="showcase">'."\n" ;
			$html .= '<div id="showcase-prev" ></div>'."\n";
			$html .= '  <div id="showcase-window">'."\n";							
			$html .= $els;
			$html .= '  </div>'."\n";
			$html .= '  <div id="showcase-next" ></div>'."\n";	
			$html .= '</div>'."\n";	
		}
	
		return $html;
	}
	
	/**
	 * Create thumb name
	 * 
	 * @param      string $image
	 * @param      string $tn
	 * @param      string $ext
	 *
	 * @return     string
	 */
	public function createThumbName( $image=null, $tn='_thumb', $ext = '' )
	{
		if (!$image) 
		{
			$image = $this->image;
		}
		if (!$image) 
		{
			$this->setError( JText::_('No image set.') );
			return false;
		}
		
		$image = explode('.',$image);
		$n = count($image);
		if ($n > 1) 
		{
			$image[$n-2] .= $tn;
			$end = array_pop($image);
			if ($ext) 
			{
				$image[] = $ext;
			}
			else 
			{
				$image[] = $end;
			}
			
			$thumb = implode('.',$image);
		}
		else 
		{
			// No extension
			$thumb = $image[0];
			$thumb .= $tn;
			if ($ext) 
			{
				$thumb .= '.'.$ext;
			}
		}	
		return $thumb;
	}

	/**
	 * Display a list of skill levels
	 * 
	 * @param      array   $levels List of levels
	 * @param      integer $sel    Selected level
	 * @return     string HTML
	 */
	public function skillLevelCircle( $levels = array(), $sel = 0 )
	{
		$html = '';
		
		$html.= '<div class="audience_wrap">'.n;
		$html.= '<ul class="audiencelevel">'.n;
		foreach ($levels as $level)  {	
			$class = $level->label != $sel ? ' isoff' : '';
			$class = $level->label != $sel && $level->label == 'level0' ? '_isoff' : $class;
			if($level->label != $sel && $sel == 'level0') {
				$class .= " hidden";
			}
			$html .= t.t.t.' <li class="'.$level->label.$class.'"><span>&nbsp;</span></li>'.n;				
		}
		$html.= t.t.t.'</ul>'.n;
		$html.= '</div>'.n;
		return $html;	
	}
	
	/**
	 * Display skill level pop-up
	 * 
	 * @param      array  $labels       Skill levels
	 * @param      string $audiencelink Link to learn more about skill levels
	 * @return     string HTML
	 */
	public function skillLevelPopup( $labels = array(), $audiencelink)
	{
		$html  = t.t.'<div class="explainscale">'.n;
		$html .= PublicationsHtml::skillLevelTable($labels, $audiencelink);	
		$html .= t.t.'</div>'.n;
		return $html;
	}

	/**
	 * Display a table of skill levels
	 * 
	 * @param      array  $labels       Skill levels
	 * @param      string $audiencelink Link to learn more about skill levels
	 * @return     string HTML
	 */
	public function skillLevelTable( $labels = array(), $audiencelink)
	{	
		$html  = '';
		$html .= t.'<table class="skillset">'.n;
		$html .= t.t.'<thead>'.n;
		$html .= t.t.t.'<tr>'.n;
		$html .= t.t.t.'<td colspan = "2" class="combtd">'.JText::_('Difficulty Level').'</td>'.n;
		$html .= t.t.t.'<td>'.JText::_('Target Audience').'</td>'.n;
		$html .= t.t.t.'</tr>'.n;
		$html .= t.t.'</thead>'.n;
		$html .= t.t.'<tbody>'.n;

		foreach ($labels as $label) 
		{
			$ul    = PublicationsHtml::skillLevelCircle($labels, $label->label);
			$html .= PublicationsHtml::tableRow($ul, $label->title, $label->description);
		}
		$html .= t.t.'</tbody>'.n;	
		$html .= t.'</table>'.n;
		$html .= t.'<p class="learnmore"><a href="'.$audiencelink.'">'.JText::_('Learn more').' &rsaquo;</a></p>'.n;
		return $html;	
	}
	
	/**
	 * Show skill levels
	 * 
	 * @param      array   $audience     Audiences
	 * @param      integer $numlevels    Number of levels to dipslay
	 * @param      string  $pop 		 Pop-up content
	 * @return     string HTML
	 */	
	public function showSkillLevel( $audience, $numlevels = 4, $pop = '' )
	{
		$html 		= '';
		$levels 	= array();
		$labels 	= array();
		$selected 	= array();
		$txtlabel 	= '';
				
		if ($audience) 
		{
			$html .= t.t.'<div class="showscale">'.n;
			
			for ($i = 0, $n = $numlevels; $i <= $n; $i++) 
			{
				$lb = 'label'.$i;
				$lv = 'level'.$i;
				$ds = 'desc'.$i;
				$levels[$lv] 		  	 = $audience->$lv;
				$labels[$lv]['title']    = $audience->$lb;
				$labels[$lv]['desc']     = $audience->$ds;
				if ($audience->$lv) 
				{
					$selected[]			 = $lv;
				}
			}
			
			$html.= '<ul class="audiencelevel">'.n;
			
			// colored circles
			foreach ($levels as $key => $value)  
			{				
				$class = !$value ? ' isoff' : '';
				$class = !$value && $key == 'level0' ? '_isoff' : $class;
				$html .= ' <li class="'.$key.$class.'"><span>&nbsp;</span></li>'.n;				
			}
			
			// figure out text label
			if (count($selected) == 1) 
			{
					$txtlabel = $labels[$selected[0]]['title'];
			} 
			elseif (count($selected) > 1) 
			{
					
					$first 	    = array_shift($selected);
					$first		= $labels[$first]['title'];
					$firstbits  = explode("-", $first);
					$first 	    = array_shift($firstbits);
					
					$last 		= end($selected);
					$last		= $labels[$last]['title'];
					$lastbits  	= explode("-", $last);
					$last	   	= end($lastbits);
											
					$txtlabel  	= $first.'-'.$last;
			}
			else 
			{
				return false;
			}
			
			$html.= ' <li class="txtlabel">'.$txtlabel.'</li>'.n;
			$html.= '</ul>'.n;
			$html .= t.t.'</div>'.n;
			
			// Informational pop-up
			if ($pop) 
			{
				$html .= $pop;
			}
			
			return Hubzero_View_Helper_Html::div($html, 'usagescale');		
		}
		
		return $html;				
	}
		
	/**
	 * Write metadata information for a publication
	 * 
	 * @param      string  $option 			Component name
	 * @param      object  $params    		Publication params
	 * @param      object  $publication   	Publication object
	 * @param      string  $statshtml 		Usage data to append
	 * @param      array   $sections  		Active plugins' names
	 * @param      string  $version     	Version name
	 * @param      string  $xtra      		Extra content to append
	 * @param      object $lastPubRelease   Publication latest public version 
	 * @return     string HTML
	 */
	public function metadata($option, $params, $publication, $statshtml, $sections, $version = 'default', $xtra='', $lastPubRelease = '')
	{
		$html = '';
		$id = $publication->id;
		$ranking = $publication->master_ranking;
		if ($publication->state != 1) 
		{
			$text = $version == 'dev' ? JText::_('COM_PUBLICATIONS_METADATA_DEV') : JText::_('COM_PUBLICATIONS_METADATA_UNAVAILABLE'); 
			return '<div class="metaplaceholder"><p>' . $text . '</p></div>' . "\n";
		}
		else if($publication->main == 1) 
		{
			if ($params->get('show_ranking')) 
			{
				$rank = round($ranking, 1);

				$r = (10*$rank);
				if (intval($r) < 10) 
				{
					$r = '0'.$r;
				}

				$html .= '<dl class="rankinfo">'."\n";
				$html .= "\t".'<dt class="ranking"><span class="rank-'.$r.'">This resource has a</span> '.number_format($rank,1).' Ranking</dt>'."\n";
				$html .= "\t".'<dd>'."\n";
				$html .= "\t\t".'<p>'."\n";
				$html .= "\t\t\t".'Ranking is calculated from a formula comprised of <a href="'.JRoute::_('index.php?option=com_resources&id='.$id.'&active=reviews').'">user reviews</a> and usage statistics. <a href="about/ranking/">Learn more &rsaquo;</a>'."\n";
				$html .= "\t\t".'</p>'."\n";
				$html .= "\t\t".'<div>'."\n";
				$html .= $statshtml;
				$html .= "\t\t".'</div>'."\n";
				$html .= "\t".'</dd>'."\n";
				$html .= '</dl>'."\n";
			}
			$html .= ($xtra) ? $xtra : '';
			foreach ($sections as $section)
			{
				$html .= (isset($section['metadata'])) ? $section['metadata'] : '';
			}
			$html .= Hubzero_View_Helper_Html::div('', 'clear');
			return Hubzero_View_Helper_Html::div($html, 'metadata');
		}
		else {
			if ($lastPubRelease && $lastPubRelease->id != $publication->version_id) 
			{
				$html .= '<p>'.JText::_('COM_PUBLICATIONS_METADATA_ARCHIVE');
				$html .= ' [<a href="'. JRoute::_('index.php?option='.$option.'&id='.
						$publication->id.'&v='.$lastPubRelease->version_number).'">'.$lastPubRelease->version_label.'</a>]';
				$html .= ' ' . JText::_('COM_PUBLICATIONS_METADATA_ARCHIVE_INFO');
				$html .= '</p>';
			}
			$html .= ($xtra) ? $xtra : '';
			foreach ($sections as $section)
			{
				$html .= (isset($section['metadata'])) ? $section['metadata'] : '';
			}
			$html .= Hubzero_View_Helper_Html::div('', 'clear');
			return Hubzero_View_Helper_Html::div($html, 'metadata');	
		}
	}
			
	/**
	 * Show license information for a publication
	 * 
	 * @param      object  $publication   	Publication object
	 * @param      string  $version     	Version name
	 * @param      string  $option 			Component name
	 * @param      object  $license 		Publication license object
	 * @param      string  $class  			CSS class for the license hyperlink 
	 * @return     string HTML
	 */
	public function showLicense( $publication, $version, $option, $license = '', $class = "showinbox" )
	{
		if (!$license) 
		{
			return false;
		}
		$cls = strtolower($license->name);
		$custom = $publication->license_text ? $publication->license_text : '';
		$lnk = $license->url ? $license->url : '';
		$title = strtolower($license->title) != 'custom' ? $license->title : '';
		$url = JRoute::_('index.php?option='.$option.'&id='.$publication->id.'&task=license').'?v='.$version;
		
		$html  = '<p class="'.$cls.' license">'.JText::_('COM_PUBLICATIONS_LICENSED_UNDER').' ';
		if ($title) 
		{
			if ($lnk && !$custom) 
			{
				$html .= '<a href="'.$lnk.'" rel="external">'.$title.'</a>';
			}			
			elseif ($custom) 
			{
				$html .= $title.' '.JText::_('COM_PUBLICATIONS_LICENSED_ACCORDING_TO').' ';
				$html .= $custom 
					   ? '<a href="'.$url.'" class="'.$class.'">'.JText::_('COM_PUBLICATIONS_LICENSED_THESE_TERMS').'</a>'
					   : '<a rel="external" href="'.$lnk.'">'.JText::_('COM_PUBLICATIONS_LICENSED_THIS_DEED').'</a>';
			}
		}
		else 
		{
			$html .= '<a href="'.$url.'" class="'.$class.'">'.JText::_('COM_PUBLICATIONS_LICENSED_THESE_TERMS').'</a>'; 
		}
		$html .= '</p>';
		
		return $html;
	}
	
	/**
	 * Display resource sub view content
	 * 
	 * @param      array  $sections Active plugins' content
	 * @param      array  $cats     Active plugins' names
	 * @param      string $active   Current plugin name
	 * @param      string $h        Hide class
	 * @param      string $c        Extra classes
	 * @return     string HTML
	 */	
	public function sections( $sections, $cats, $active='about', $h, $c ) 
	{
		$html = '';
		
		if (!$sections) 
		{
			return $html;
		}
		
		$k = 0;
		foreach ($sections as $section) 
		{	
			if ($section['html'] != '') 
			{				
				$cls  = ($c) ? $c.' ' : '';
				if (key($cats[$k]) != $active) 
				{
					$cls .= ($h) ? $h.' ' : '';
				}
				$html .= Hubzero_View_Helper_Html::div( $section['html'], $cls.'section', key($cats[$k]).'-section' );
			}
			$k++;
		}
		
		return $html;
	}
	
	/**
	 * Output tab controls for resource plugins (sub views)
	 * 
	 * @param      string $option Component name
	 * @param      string $id     Publication ID
	 * @param      array  $cats   Active plugins' names
	 * @param      string $active Current plugin name
	 * @param      string $alias  Publication alias
	 * @param      string $version  Publication version
	 * @return     string HTML
	 */
	public function tabs( $option, $id, $cats, $active = 'about', $alias = '', $version = '' ) 
	{
		$html  = '';
		$html .= "\t".'<ul class="sub-menu">'."\n";
		$i = 1;
		foreach ($cats as $cat)
		{
			$name = key($cat);
			if ($name != '') 
			{
				if ($alias) 
				{
					$url = JRoute::_('index.php?option='.$option.'&alias='.$alias.'&active='.$name);
				} 
				else 
				{
					$url = JRoute::_('index.php?option='.$option.'&id='.$id.'&active='.$name);
				}
				if ($version && $version != 'default') 
				{
					$url .= '?v='.$version;
				}
				if (strtolower($name) == $active) 
				{
					$app =& JFactory::getApplication();
					$pathway =& $app->getPathway();
					$pathway->addItem($cat[$name],$url);
					
					if ($active != 'about') {
						$document =& JFactory::getDocument();
						$title = $document->getTitle();
						$document->setTitle( $title.': '.$cat[$name] );
					}
				}
				$html .= "\t\t".'<li id="sm-'.$i.'"';
				$html .= (strtolower($name) == $active) ? ' class="active"' : '';
				$html .= '><a class="tab" rel="'.$name.'" href="'.$url.'"><span>'.$cat[$name].'</span></a></li>'."\n";
				$i++;	
			}
		}
		$html .= "\t".'</ul>'."\n";
		
		return $html;
	}
	
	/**
	 * Generate COins microformat
	 * 
	 * @param      object $cite     	Pub citation data
	 * @param      object $publication 	Publication
	 * @param      object $config   	Component config
	 * @param      object $helper   	Publication Helper
	 * @return     string HTML
	 */	
	public function citationCOins($cite, $publication, $config, $helper)
	{
		if (!$cite) 
		{
			return '';
		}
		
		$html  = '<span ';
		$html .= ' class="Z3988"';
		$html .= ' title="ctx_ver=Z39.88-2004&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Ajournal';
		$html .= isset($publication->doi) 
			? '&amp;rft_id=info%3Adoi%2F'.$publication->doi
			: '';

		$html .= '&amp;rft.genre=unknown';
		$html .= '&amp;rft.atitle='.urlencode($cite->title);
		$html .= '&amp;rft.date='.urlencode($cite->year);
		
		$author_array = $publication->_authors;

		if ($author_array) 
		{
			for ($i = 0; $i < count($author_array); $i++) 
			{
				if ($author_array[$i]->lastName || $author_array[$i]->firstName) 
				{
					$name = stripslashes($author_array[$i]->firstName) .' ';
					$name .= stripslashes($author_array[$i]->lastName);
				} 
				else 
				{
					$name = $author_array[$i]->name;
				}

				$html.= '&amp;rft.au='.urlencode($name);
			}
		}
		
		$html.= '"></span>'."\n";
		
		return $html;
	}
	
	/**
	 * Generate a citation for a publication
	 * 
	 * @param      string  $option    Component name
	 * @param      object  $cite      Citation data
	 * @param      object  $pub       Publication
	 * @param      string  $citations Citations to prepend
	 * @param      string  $version   Version name
	 * @return     string HTML
	 */	
	public function citation( $option, $cite, $pub, $citations, $version = 'default') 
	{
		include_once( JPATH_ROOT . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'format.php' );
		include_once( JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'type.php' );
		
		$cconfig  =& JComponentHelper::getParams( 'com_citations' );
		
		$formatter = new CitationFormat();
		$formatter->setTemplate('ieee');

		$html  = '<p>'.JText::_('COM_PUBLICATIONS_CITATION_INSTRUCTIONS').'</p>'."\n";
		$html .= $citations;
		if ($cite) 
		{
			$html .= '<ul class="citations results">'."\n";
			$html .= "\t".'<li>'."\n";
			$html .= $formatter->formatCitation($cite, false, true, $cconfig);
			if ($version != 'dev') 
			{
				$html .= "\t\t".'<p class="details">'."\n";
				$html .= "\t\t\t".'<a href="index.php?option='.$option.'&task=citation&id='
					.$pub->id.'&format=bibtex&no_html=1&v='.$version.'" title="'
					.JText::_('COM_PUBLICATIONS_DOWNLOAD_BIBTEX_FORMAT').'">BibTex</a> <span>|</span> '."\n";
				$html .= "\t\t\t".'<a href="index.php?option='.$option.'&task=citation&id='
					.$pub->id.'&format=endnote&no_html=1&v='.$version.'" title="'
					.JText::_('COM_PUBLICATIONS_DOWNLOAD_ENDNOTE_FORMAT').'">EndNote</a>'."\n";
				$html .= "\t\t".'</p>'."\n";
			}
			$html .= "\t".'</li>'."\n";
			$html .= '</ul>'."\n";
		}
		
		return $html;
	}
	
	/**
	 * Process metadata for a publication
	 * 
	 * @param      string  $metadata  	Pub metadata
	 * @param      object  $category  	Pub category
	 * @param      boolean $table     	Put data in html table?
	 * @param      integer $id 		  	Pub id
	 * @param      string  $option    	Component name
	 * @param      object  $parser    	Wiki parser
	 * @param      array   $wikicinfig  Wiki config
	 * @return     array
	 */	
	public function processMetadata( $metadata = '', $category = '', $table = 1, $id, $option, $parser, $wikiconfig ) 
	{	
		$html = '';
		
		if (!$category) 
		{
			$fields = array('credits', 'sponsoredby', 'references');
		}
		else
		{
			// Get publication type fields
			$fields = array();
			if (trim($category->customFields) != '') 
			{
				$fs = explode("\n", trim($category->customFields));
				foreach ($fs as $f) 
				{
					$fields[] = explode('=', $f);
				}
			}
		}
		
		// Filter meta data
		if (!empty($fields)) {
			for ($i=0, $n=count( $fields ); $i < $n; $i++) 
			{
				preg_match("#<nb:".$fields[$i][0].">(.*?)</nb:".$fields[$i][0].">#s", $metadata, $matches);
				if (count($matches) > 0) 
				{
					$match = $matches[0];
					$match = str_replace('<nb:'.$fields[$i][0].'>','',$match);
					$match = str_replace('</nb:'.$fields[$i][0].'>','',$match);
				} 
				else 
				{
					$match = '';
				}

				// Explore the text and pull out all matches
				array_push($fields[$i], $match);
			}
		}

		if (!empty($fields)) 
		{
			for ($i=0, $n=count( $fields ); $i < $n; $i++) 
			{
				// Explore the text and pull out all matches
				array_push($fields[$i], PublicationsHtml::parseTag($metadata, $fields[$i][0]));
			}
		}
		
		// Display table rows
		$citations = '';
		foreach ($fields as $field) 
		{
			if (end($field) != NULL) 
			{	
				// Parse wiki if not HTML already
				$wiki = end($field) == strip_tags(end($field)) ? $parser->parse( end($field), $wikiconfig ) : end($field);
				
				if ($field[0] == 'citations') 
				{
					$citations = $wiki;
				} 
				else 
				{
					if ($table) 
					{
						$html .= PublicationsHtml::tableRow( $field[1], $wiki );	
					}
					else 
					{
						$html .= '<p class="pub-review-label">'.$field[1].'</p>';
						$html .= $wiki ;
					}
				}
			}
		}
				
		return array('html' => $html, 'citations' => $citations);
	}

	/**
	 * Display certain supporting docs and/or link to more
	 * 
	 * @param      object  $publication   	Publication object
	 * @param      string  $version     	Version name
	 * @param      string  $option 			Component name
	 * @param      object  $children 		Publication attachments
	 * @param      boolean $restricted 		
	 * @return     string HTML
	 */
	public function sortSupportingDocs( $publication, $version, $option, $children, $restricted, $archive = '' ) 
	{
		if ($restricted) 
		{
			return false;
		}
		
		// Set counts		
		$docs = 0;
		
		$html = '';
		$supln  = '<ul class="supdocln">'."\n";
		$supli  = array();
		
		// Archival package?
		//$publication->base == 'databases' && 
		if (file_exists($archive) && $publication->base == 'databases')
		{
			$url = JRoute::_('index.php?option=com_publications&id='.$this->publication->id.'&task=serve').'?v=' . $version . '&amp;render=archive';
			$supli[] = ' <li class="archival-package"><a href="'.$url.'" title="'. JText::_('COM_PUBLICATIONS_DOWNLOAD_ARCHIVE_PACKAGE') .'">' . JText::_('COM_PUBLICATIONS_ARCHIVE_PACKAGE') . '</a></li>'."\n";
			$docs++;
		}
		
		if ($children)
		{
			foreach ($children as $child) 
			{			
				$docs++;									
				$child->title = $child->title ? stripslashes($child->title) : '';				
				$child->title = str_replace( '"', '&quot;', $child->title );
				$child->title = str_replace( '&amp;', '&', $child->title );
				$child->title = str_replace( '&', '&amp;', $child->title );
				$child->title = str_replace( '&amp;quot;', '&quot;', $child->title );

				$params = new JParameter( $child->params );
				switch ( $child->type ) 
				{
					case 'file': 
					default:				
						$default_type = 'download'; 		
						break;
					case 'link': 				
						$default_type = 'external'; 		
						break;
				}
				$serveas = $params->get('serveas', $default_type);
				$ftype = PublicationsHtml::getFileExtension($child->path);
				$class = $params->get('class', $ftype);
				$doctitle = $params->get('title', $child->title);

				// Things we want to highlight
				$toShow = array('iTunes', 'iTunes U', 'Syllabus', 'Audio', 'Video', 'Slides');

				$url   = JRoute::_('index.php?option=com_publications&id='.$this->publication->id.'&task=serve').'?a='.$child->id;
				$extra = '';
				
				switch ( $serveas ) 
				{
					case 'download': 
					default:				
						break;
					case 'external':
						$extra = ' rel="external"'; 						
						break;
					case 'inlineview': 				
						$class = 'play';
						$url  .= a . 'render=inline';		
						break;
				}	
				
				if (in_array($doctitle, $toShow)) 
				{
					$supli[] = ' <li><a class="'.$class.'" href="'.$url.'" title="'.$child->title.'"' . $extra . '>'.$doctitle.'</a></li>'."\n";				
				}
			}
		}	
		
		$sdocs = count( $supli ) > 2 ? 2 : count( $supli );
		$otherdocs = $docs - $sdocs;
		$otherdocs = ($sdocs + $otherdocs) == 3  ? 0 : $otherdocs;

		for ($i=0; $i < count( $supli ); $i++) 
		{
			$supln .=  $i < 2 ? $supli[$i] : '';
			$supln .=  $i == 2 && !$otherdocs ? $supli[$i] : '';
		}	
		
		// View more link?			
		if ($docs > 0 && $otherdocs > 0) 
		{
			$supln .= ' <li class="otherdocs"><a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$publication->id.a.'active=supportingdocs').'" title="'.JText::_('View All').' '.$docs.' '.JText::_('Supporting Documents').' ">'.$otherdocs.' '.JText::_('more').' &rsaquo;</a></li>'."\n";
		}
		 
		if (!$sdocs && $docs > 0) 
		{
			$html .= "\t\t".'<p class="viewalldocs"><a href="'.JRoute::_('index.php?option='.$this->option.'&id='.$publication->id.a.'active=supportingdocs').'">'.JText::_('COM_PUBLICATIONS_IN_DEVELOPMENT_DOCS_AVAIL').' ('.$docs.')</a></p>'."\n";
		}
		
		$supln .= '</ul>'."\n";
		$html .= $sdocs ? $supln : '';
		return $html;			
	}
	
	//-------------
	
	public function showVersionInfo ( $publication, $version, $option, $config, $lastPubRelease ) 
	{
		$dateFormat = '%b %d, %Y';
		$tz = null;

		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$dateFormat = 'm d, Y';
			$tz = false;
		}

		$text = '';
		if($version == 'dev') 
		{
			// Dev version
			$class = 'devversion';
			$text .= JText::_('COM_PUBLICATIONS_VERSION').' <strong>'
			      .$publication->version_label.'</strong> ('.JText::_('COM_PUBLICATIONS_IN_DEVELOPMENT').')';
			$text .= '<span class="block">'.JText::_('COM_PUBLICATIONS_CREATED').' ';
			$text .= JText::_('COM_PUBLICATIONS_ON').' '.JHTML::_('date', $publication->created, $dateFormat, $tz).'</span>';
		}
		else
		{
			$class = 'curversion';
			$text .= ($publication->main == 1 || $publication->state == 1) ? '' : '<strong>'.JText::_('COM_PUBLICATIONS_ARCHIVE').'</strong> ';
			$text .= JText::_('COM_PUBLICATIONS_VERSION').' <strong>'.$publication->version_label.'</strong>';
			$now = date( 'Y-m-d H:i:s', time() );
			
			switch ($publication->state) 
			{
				case 1:  
					$text .= ' - ';
					$text .= ($publication->published_up > $now) 
						? JText::_('COM_PUBLICATIONS_TO_BE_RELEASED') 
						: strtolower(JText::_('COM_PUBLICATIONS_PUBLISHED'));
					$text .= ' ' . JText::_('COM_PUBLICATIONS_ON').' '.JHTML::_('date', $publication->published_up, $dateFormat, $tz).' ';          
					break;
				case 4:  
					$text .= ' ('.strtolower(JText::_('COM_PUBLICATIONS_READY')).')';  
					$text .= '<span class="block">'.JText::_('COM_PUBLICATIONS_FINALIZED').' ';
					$text .= JText::_('COM_PUBLICATIONS_ON') . ' ' 
					. JHTML::_('date', $publication->published_up, $dateFormat, $tz).'</span>';  
					$class = 'ready';        
					break;
				case 5:
					$text .= ' ('.strtolower(JText::_('COM_PUBLICATIONS_PENDING_APPROVAL')).')';  
					$text .= '<span class="block">'.JText::_('COM_PUBLICATIONS_SUBMITTED').' ';
					$text .= JText::_('COM_PUBLICATIONS_ON') . ' '
					.JHTML::_('date', $publication->submitted, $dateFormat, $tz).'</span>';
					if ($publication->published_up > $now) 
					{
						$text .= '<span class="block">';
						$text .= JText::_('COM_PUBLICATIONS_TO_BE_RELEASED') . ' ' . JText::_('COM_PUBLICATIONS_ON') . ' ' 
							. JHTML::_('date', $publication->published_up, $dateFormat, $tz);
						$text .= '</span>';
					}
					$class = 'pending';       
					break;	
				case 6:
					$text .= ' ('.strtolower(JText::_('COM_PUBLICATIONS_DARK_ARCHIVE')).')'; 
					$text .= '<span class="block">';
					$text .= ($publication->published_up > $now) 
						? JText::_('COM_PUBLICATIONS_TO_BE_RELEASED') 
						: JText::_('COM_PUBLICATIONS_RELEASED');

					$text .= ' ' . JText::_('COM_PUBLICATIONS_ON') . ' ' 
						. JHTML::_('date', $publication->published_up, $dateFormat, $tz);
					$text .= '</span>';
					$text .= $publication->ark ? '<span class="archid">'.JText::_('ark').':'.$publication->ark.'</span>' : '';  
					$class = 'archived';   
					break;
				case 0:
					$text .= ' ('.strtolower(JText::_('COM_PUBLICATIONS_UNPUBLISHED')).')';  
					$text .= '<span class="block">'.JText::_('COM_PUBLICATIONS_RELEASED').' ';
					$text .= JText::_('COM_PUBLICATIONS_ON') . ' '
					. JHTML::_('date', $publication->published_up, $dateFormat, $tz).'</span>';
					$class = $publication->main == 1 ? 'unpublished' : 'archive';        
					break;		
			}
		}

		// Show DOI if available
		if ($version != 'dev' && $publication->doi) {
			$text .= "\t\t".'<span class="doi">'.'doi:'.$publication->doi;
			$text .= ' - <span><a href="'. JRoute::_('index.php?option='.$option.'&id='.
			$publication->id.'&active=about'). '#citethis">'.JText::_('cite this').'</a></span></span>'."\n";
		}
		
		// Show current release information
		if($lastPubRelease && $lastPubRelease->id != $publication->version_id) {
			$text .= "\t\t" . '<span class="block">' . JText::_('COM_PUBLICATIONS_LAST_PUB_RELEASE') 
			. ' <a href="'. JRoute::_('index.php?option='.$option.'&id='.
			$publication->id.'&v='.$lastPubRelease->version_number).'">'.$lastPubRelease->version_label.'</a></span>';
		}
		
		// Output
		if ($text) 
		{
			return '<p class="'.$class.'">'.$text.'</p>';
		}
		return false;
	}
	
	//-----------
	
	public function showAccessMessage( $publication, $option, $authorized, $restricted, $editlink = '' ) 
	{
		$dateFormat = '%b %d, %Y';
		$tz = null;

		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$dateFormat = 'm d, Y';
			$tz = false;
		}

		$msg = '';
		$project = '';
		$now = date( 'Y-m-d H:i:s', time() );
		
		if ($publication->project_provisioned == 0)
		{
			$project.= JText::_('COM_PUBLICATIONS_FROM_PROJECT');
			$project.= $authorized == 1 ?
			' <a href="' . JRoute::_('index.php?option=com_projects&alias=' . $publication->project_alias) .'">' : ' <strong>';
			$project.= Hubzero_View_Helper_Html::shortenText($publication->project_title, 50, 0);
			$project.= $authorized == 1 ? '</a>' : '</strong>';			
		}
			
		// Show message to restricted users
		if ($restricted && !$authorized) 
		{
			$class = 'warning';
			switch ($publication->access) 
			{
				case 1:  
					$msg = JText::_('COM_PUBLICATIONS_STATUS_MSG_REGISTERED');         
					break;
				case 2:  
					$msg = JText::_('COM_PUBLICATIONS_STATUS_MSG_RESTRICTED');          
					break;
				case 3: 
					$msg = JText::_('COM_PUBLICATIONS_STATUS_MSG_PRIVATE');          
					break;	
			}
		}
		// Show message to publication owners		
		elseif ($authorized) 
		{
			$class= 'info';
			switch ($publication->state) 
			{
				case 1:  
					$msg = JText::_('COM_PUBLICATIONS_STATUS_MSG_PUBLISHED').' '; 
					switch ($publication->access) 
					{
						case 0:  
							$msg .= JText::_('COM_PUBLICATIONS_STATUS_MSG_WITH_PUBLIC');         
							break;
						case 1:  
							$msg .= JText::_('COM_PUBLICATIONS_STATUS_MSG_WITH_REGISTERED');         
							break;
						case 2:  
							$msg .= JText::_('COM_PUBLICATIONS_STATUS_MSG_WITH_RESTRICTED');          
							break;
						case 3: 
							$msg .= JText::_('COM_PUBLICATIONS_STATUS_MSG_WITH_PRIVATE');          
							break;	
					}
					
					if ($publication->published_up > $now) 
					{
						$msg = JText::_('COM_PUBLICATIONS_STATUS_MSG_PUBLISHED_EMBARGO') 
							. ' ' . JHTML::_('date', $publication->published_up, $dateFormat, $tz) ; 
					}
					
					break;
				case 4:  
					$msg = JText::_('COM_PUBLICATIONS_STATUS_MSG_POSTED');          
					break;
				case 3: 
					$msg = $publication->versions 
					     ? JText::_('COM_PUBLICATIONS_STATUS_MSG_DRAFT_VERSION') 
					     : JText::_('COM_PUBLICATIONS_STATUS_MSG_DRAFT');          
					break;	
				case 0: 
					$msg = $publication->default_version_status == 0 
						 ? JText::_('COM_PUBLICATIONS_STATUS_MSG_UNPUBLISHED')
						 : JText::_('COM_PUBLICATIONS_STATUS_MSG_UNPUBLISHED_VERSION');         
					break;	
				case 5: 
					$msg = $publication->versions 
						 ? JText::_('COM_PUBLICATIONS_STATUS_MSG_PENDING')
						 : JText::_('COM_PUBLICATIONS_STATUS_MSG_PENDING_VERSION');          
					break;	
				case 6: 
					$msg = JText::_('COM_PUBLICATIONS_STATUS_MSG_DARK_ARCHIVE');          
					break;
			}
			if($authorized == 3)
			{
				$msg .= ' '.JText::_('COM_PUBLICATIONS_PREVIEW_ACCESS'); 
			}
			if($editlink && ($authorized == 1 || $authorized == 2 || $authorized == 4)) {
				$msg .= ' <a href="'.$editlink.'">'.JText::_('COM_PUBLICATIONS_STATUS_MSG_MANAGE_PUBLICATION').'</a>.';
			}
			if($project && $publication->project_status != 3) {
				$msg .= ' <span class="fromproject">'.$project.'</span>';
			}
		}
		if($msg) {
			return '<p class="'.$class.' statusmsg">'.$msg.'</p>';
		}
		return false;
	}
	
	//-----------
	
	public function getState( $state ) 
	{	
		switch ($state) 
		{
			case 0: 
				return strtolower(JText::_('COM_PUBLICATIONS_UNPUBLISHED'));  
				break;
			case 4:  
				return strtolower(JText::_('COM_PUBLICATIONS_POSTED'));       
				break;
			case 5:  
				return strtolower(JText::_('COM_PUBLICATIONS_PENDING'));       
				break;
			case 6:  
				return strtolower(JText::_('COM_PUBLICATIONS_ARCHIVE'));       
				break;
			case 1: 
			default: 
				return strtolower(JText::_('COM_PUBLICATIONS_PUBLISHED'));         
				break; 
		}           
	}
	
	//-----------
	
	public function showSubInfo( $publication, $option ) 
	{	
		$action = $publication->state == 1 ? JText::_('COM_PUBLICATIONS_LISTED_IN') : JText::_('COM_PUBLICATIONS_IN');
		$html = '<p class="pubinfo">'.$action.' '.' <a href="'.JRoute::_('index.php?option='.$option.'&category='.$publication->cat_url).'">'.$publication->cat_name.'</a>';
		
		// Show group if group project
		if($publication->project_group) 
		{
			ximport('Hubzero_Group');
			$group = new Hubzero_Group();
			if (Hubzero_Group::exists($publication->project_group)) 
			{
				$group = Hubzero_Group::getInstance( $publication->project_group );
				$html .= ' | '.JText::_('COM_PUBLICATIONS_PUBLICATION_BY_GROUP').' <a href="/groups/'.$group->get('cn').'">'.$group->get('description').'</a>';
			}
		}
		$html .= '</p>'."\n";
			
		return $html;
	}
	
	//-----------
	
	public function title( $option, $publication, $params, $show_edit, $config=null, $show_posted=1 ) 
	{
		$txt = '';
		$txt .= stripslashes($publication->title);	
		$html  = Hubzero_View_Helper_Html::hed(2,$txt)."\n";
		$html  = Hubzero_View_Helper_Html::div($html,'full','content-header');
		
		return $html;
	}
	
	//-----------

	public function drawPrimaryButton( $option, $publication, $version, 
	$content, $path, $serveas = 'download', $restricted = 0, $authorized = 0 )
	{
		
		$task 		= 'serve';		
		$url  		= JRoute::_('index.php?option=com_publications&id=' 
					. $publication->id . '&v=' . $publication->version_number . '&task=' . $task);
		$action 	= '';
		$xtra 		= '';
		$title  	= 'Access publication';
		$pop    	= '';
		$class  	= '';
		$disabled 	= 0;
		$msg		= 'Access Publication';
		
		// Is content available?
		if ($publication->state == 0) 
		{
			$class = 'link_disabled';
			$pop = JText::_('COM_PUBLICATIONS_STATE_UNPUBLISHED_POP');
			$disabled = 1;
		}
		elseif ($restricted && !$authorized) 
		{
			$class = 'link_disabled';
			$pop = $publication->access == 1 
			     ? JText::_('COM_PUBLICATIONS_STATE_REGISTERED_POP') 
			     : JText::_('COM_PUBLICATIONS_STATE_RESTRICTED_POP');
			$disabled = 1;
		}
		elseif ($content['primary'][0]->type == 'file' ) 
		{
			$fpath = $content['primary'][0]->path;
			if (!$fpath || !file_exists(JPATH_ROOT . $path . DS . $fpath)) 
			{
				return '<p class="error statusmsg">'.JText::_('COM_PUBLICATIONS_ERROR_CONTENT_UNAVAILABLE').'</p>';
			}
		}
		
		$primary = $content['primary'][0];
		switch ($serveas)
		{
			case 'download':
			case 'tardownload':
			default:
				$msg   = JText::_('COM_PUBLICATIONS_DOWNLOAD_PUBLICATION');
				$xtra  = count($content['primary']) == 1 ? strtoupper(PublicationsHtml::getFileExtension($content['primary'][0]->path)) : NULL;
				$extra = (count($content['primary']) > 1 || $serveas == 'tardownload') ? 'ZIP' : NULL;				
				break;

			case 'video':
			case 'inlineview':				
				$msg   = JText::_('COM_PUBLICATIONS_VIEW_PUBLICATION');
				$url .= $serveas == 'video' ? a . 'render=video' : '';
				
				if (!$disabled) 
				{
					$class = 'play';
				}
				break;
				
			case 'invoke':
				$msg   = JText::_('Launch tool');
				$class = 'launchtool';
				break;
				
			case 'external':
				
				if ($content['primary'][0]->type == 'note')
				{
				//	$class = 'play'; // lightboxed
				}
				else
				{
					$action = 'rel="external"';
				}
				
				break;
		}
		
		
		$title = $title ? $title : $msg;
		$pop   = $pop ? Hubzero_View_Helper_Html::warning($pop) : '';
		
		return PublicationsHtml::primaryButton($class, $url, $msg, $xtra, $title, $action, $disabled, $pop);		
	}
	
	//-----------

	public function primaryButton($class, $href, $msg, $xtra='', $title='', $action='', $disabled=false, $pop = '')
	{
		$title = htmlentities($title, ENT_QUOTES);
		$out = '';
		
		if ($disabled) {
			$out .= "\t".'<p id="primary-document"><span ';
			$out .= $class ? 'class="'.$class.'"' : '';
			$out .= ' >'.$msg.'</span></p>'."\n";
		} else {
			$out .= "\t".'<p id="primary-document"><a ';
			$out .= $class ? 'class="'.$class.'"' : '';
			$out .= ' href="'.$href.'" title="'.$title.'" '.$action.'>'.$msg;
			$out .= $xtra ? ' <span class="caption">('.$xtra.')</span>' : '';
			$out .= '</a></p>'."\n";
		}
		
		if ($pop) {
			$out .= "\t".'<div id="primary-document_pop"><div>'.$pop.'</div></div>'."\n";
		}
		
		return $out;
	}

	//-------------------------------------------------------------
	// Browse
	//-------------------------------------------------------------

	public function writeResults( &$database, &$lines, $filters = array(), $show_date = 3 ) 
	{
		$dateFormat = '%d %b %Y';
		$tz = null;

		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$dateFormat = 'd m Y';
			$tz = false;
		}

		$juser =& JFactory::getUser();

		$config =& JComponentHelper::getParams( 'com_publications' );
		
		$html  = '<ol class="resources results">'."\n";
		foreach ($lines as $line)
		{			
			// Get version authors
			$pa = new PublicationAuthor( $database );
			$authors = $pa->getAuthors($line->version_id);
			
			// Check if project owner
			$objO  = new ProjectOwner( $database );
				
			// Determine if they have access to edit
			if (!$juser->get('guest')) {
				if ($line->created_by == $juser->get('id') || $objO->isOwner($juser->get('id'), $line->project_id)) {
					$show_edit = 2;
				}
			}
				
			// Get parameters
			$params = clone($config);
			$rparams = new JParameter( $line->params );
			$params->merge( $rparams );
			
			// Instantiate a new view
			$view = new JView( array('name'=>'browse','layout'=>'item') );
			$view->option = 'com_publications';
			$view->config = $config;
			$view->params = $params;
			$view->juser = $juser;
			$view->authors = $authors;
			$view->line = $line;
			$view->filters = $filters;
			
			// Get publications helper
			$helper = new PublicationHelper($database);
			$view->helper = $helper;

			// Set the display date
			switch ($show_date) 
			{
				case 0: $view->thedate = ''; break;
				case 1: $view->thedate = JHTML::_('date', $line->created, $dateFormat, $tz);    	break;
				case 2: $view->thedate = JHTML::_('date', $line->modified, $dateFormat, $tz);   	break;
				case 3: $view->thedate = JHTML::_('date', $line->published_up, $dateFormat, $tz); break;
			}
			
			$html .= $view->loadTemplate();
		}
		$html .= '</ol>'."\n";
		
		return $html;
	}
	
	//-------------------------------------------------------------
	// Misc
	//-------------------------------------------------------------

	public function getRatingClass($rating=0)
	{
		switch ($rating) 
		{
			case 0.5: $class = ' half-stars';      break;
			case 1:   $class = ' one-stars';       break;
			case 1.5: $class = ' onehalf-stars';   break;
			case 2:   $class = ' two-stars';       break;
			case 2.5: $class = ' twohalf-stars';   break;
			case 3:   $class = ' three-stars';     break;
			case 3.5: $class = ' threehalf-stars'; break;
			case 4:   $class = ' four-stars';      break;
			case 4.5: $class = ' fourhalf-stars';  break;
			case 5:   $class = ' five-stars';      break;
			case 0:
			default:  $class = ' no-stars';      break;
		}
		return $class;
	}

	//-----------
	
	public function encode_html($str, $quotes=1)
	{
		$str = stripslashes($str);
		$a = array(
			'&' => '&#38;',
			'<' => '&#60;',
			'>' => '&#62;',
		);
		if ($quotes) $a = $a + array(
			"'" => '&#39;',
			'"' => '&#34;',
		);

		return strtr($str, $a);
	}
	
	//-----------

	public function getFileAttribs( $path, $base_path='', $fsize=0 )
	{
		// Return nothing if no path provided
		if (!$path) {
			return '';
		}
		
		if ($base_path) {
			// Strip any trailing slash
			if (substr($base_path, -1) == DS) { 
				$base_path = substr($base_path, 0, strlen($base_path) - 1);
			}
			// Ensure a starting slash
			if (substr($base_path, 0, 1) != DS) { 
				$base_path = DS.$base_path;
			}
		}
		
		// Ensure a starting slash
		if (substr($path, 0, 1) != DS) { 
			$path = DS.$path;
			if (substr($path, 0, strlen($base_path)) == $base_path) {
				// Do nothing
			} else {
				$path = $base_path.$path;
			}
		}
		
		$path = JPATH_ROOT.$path;

		$file_name_arr = explode('.',$path);
	    $type = end($file_name_arr);
		if (strlen($type) > 4) {
			$type = 'HTML';
		}
		$type = strtoupper($type);
		
		$fs = '';
		
		// Get the file size if the file exist
		if (file_exists( $path )) {
			$fs = filesize( $path );
		}
		
		$html  = '<span class="caption">('.$type;
		if ($fs) {
			switch ($type)
			{
				case 'HTM':
				case 'HTML':
				case 'PHP':
				case 'ASF':
				case 'SWF': $fs = ''; break;
				default: 
					$fs = ($fsize) ? $fs : PublicationsHtml::formatsize($fs); 
					break;
			}
		
			$html .= ($fs) ? ', '.$fs : '';
		}
		$html .= ')</span>';
		
		return $html;
	}
	
	//-----------

	public function formatsize($file_size) 
	{
		if ($file_size >= 1073741824) {
			$file_size = round($file_size / 1073741824 * 100) / 100 . ' <abbr title="gigabytes">Gb</abbr>';
		} elseif ($file_size >= 1048576) {
			$file_size = round($file_size / 1048576 * 100) / 100 . ' <abbr title="megabytes">Mb</abbr>';
		} elseif ($file_size >= 1024) {
			$file_size = round($file_size / 1024 * 100) / 100 . ' <abbr title="kilobytes">Kb</abbr>';
		} else {
			$file_size = $file_size . ' <abbr title="bytes">b</abbr>';
		}
		return $file_size;
	}
	
	//-----------
	
	public function cleanText($text, $desclen=300)
	{
		$elipse = false;

		$text = preg_replace( "'<script[^>]*>.*?</script>'si", "", $text );
		$text = str_replace( '{mosimage}', '', $text );
		$text = str_replace( "\n", ' ', $text );
		$text = str_replace( "\r", ' ', $text );
		$text = preg_replace( '/<a\s+.*href=["\']([^"\']+)["\'][^>]*>([^<]*)<\/a>/i','\\2', $text );
		$text = preg_replace( '/<!--.+?-->/', '', $text);
		$text = preg_replace( '/{.+?}/', '', $text);
		$text = strip_tags( $text );
		if (strlen($text) > $desclen) $elipse = true;
		$text = substr( $text, 0, $desclen );
		if ($elipse) $text .= '&#8230;';
		$text = trim($text);
		
		return $text;
	}

	//-----------

	public function passed( $msg, $tag='p' )
	{
		return '<'.$tag.' class="passed">'.$msg.'</'.$tag.'>'."\n";
	}

	//-----------

	public function archive( $msg, $tag='p' )
	{
		return '<'.$tag.' class="archive">'.$msg.'</'.$tag.'>'."\n";
	}
	
	//-----------
	
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}

	//-----------
	
	public function aside($txt, $id='')
	{
		return Hubzero_View_Helper_Html::div($txt, 'aside', $id);
	}
	
	//-----------
	
	public function subject($txt, $id='')
	{
		return Hubzero_View_Helper_Html::div($txt, 'subject', $id);
	}

	//-----------

	public function formSelect($name, $array, $value, $class='')
	{
		$out  = '<select name="'.$name.'" id="'.$name.'"';
		$out .= ($class) ? ' class="'.$class.'">'."\n" : '>'."\n";
		foreach ($array as $avalue => $alabel) 
		{
			$selected = ($avalue == $value || $alabel == $value)
					  ? ' selected="selected"'
					  : '';
			$out .= ' <option value="'.$avalue.'"'.$selected.'>'.$alabel.'</option>'."\n";
		}
		$out .= '</select>'."\n";
		return $out;
	}

	//-----------

	public function tableRow($h, $c='', $s='')
	{
		$html  = t.'  <tr>'.n;
		$html .= t.'   <th>'.$h.'</th>'.n;
		$html .= t.'   <td>';
		$html .= ($c) ? $c : '&nbsp;';
		$html .= '</td>'.n;
		if($s) {
			$html .= t.'   <td class="secondcol">';
			$html .= $s;
			$html .= '</td>'.n;
		}
		$html .= t.'  </tr>'.n;
		
		return $html;
	}
	
	//-----------
	
	public function getFileExtension($url)
	{
		$type = '';
		$arr  = explode('.',$url);
		$type = count($arr) > 1 ? end($arr) : '';
		$type = (strlen($type) > 4) ? 'html' : $type;
		$type = (strlen($type) > 3) 
			  ? substr($type, 0, 3)
			  : $type;
		return $type;
	}
	
	//-----------
	
	public function parseTag($text, $tag)
	{
		preg_match("#<nb:".$tag.">(.*?)</nb:".$tag.">#s", $text, $matches);
		if (count($matches) > 0) 
		{
			$match = $matches[0];
			$match = str_replace('<nb:'.$tag.'>','',$match);
			$match = str_replace('</nb:'.$tag.'>','',$match);
		} else 
		{
			$match = '';
		}
		return $match;
	}
	
}
