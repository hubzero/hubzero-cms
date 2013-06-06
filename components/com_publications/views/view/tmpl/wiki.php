<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

// Load wiki configs
$wiki_config =& JComponentHelper::getParams( 'com_wiki' ); 
			
// Change all relative links to point to correct locations
$weed = DS . 'wiki' . DS . $this->masterscope . DS . $this->page->pagename . DS;

// Transform the wikitext to HTML
ximport('Hubzero_Wiki_Parser');
$p =& Hubzero_Wiki_Parser::getInstance();

$wikiconfig = array(
	'option'   => 'com_wiki',
	'scope'    => $this->page->scope,
	'pagename' => $this->page->pagename, 
	'pageid'   => $this->page->id
);

// Parse text
$text = $p->parse( $this->page->pagetext, $wikiconfig );

$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
if (preg_match_all("/$regexp/siU", $text, $matches)) 
{    
	foreach ($matches[2] as $match)
	{
		$pagename = str_replace($weed, '', $match); 
		
		// Check if there is a note with this name within this project
		$found = $this->helper->getWikiPage(basename($pagename), $this->publication, $this->masterscope);
		if ($found)
		{
			$text = str_replace($weed. $pagename, DS . 'publications' . DS . $this->publication->id . DS . 'wiki?pageid=' . $found->id, $text );
		}
	}
}

?>
<div class="wiki-wrap">
	<p class="wiki-back"><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->publication->id); ?>"><?php echo JText::_('COM_PUBLICATIONS_BACK_TO_PUBLICATION'); ?>  &ldquo;<?php echo $this->publication->title; ?>&rdquo;</a></p>
	<div class="wiki-content">
		<?php echo $text; ?>
	</div>
</div>
