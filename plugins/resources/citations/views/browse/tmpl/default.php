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

$numaff = 0;
$numnon = 0;

// Did we get any results back?
if ($this->citations) {
	// Get a needed library
	include_once(JPATH_ROOT.DS.'components'.DS.'com_citations'.DS.'citations.format.php');

	// Set some vars
	$affiliated = '';
	$nonaffiliated = '';
	
	$formatter = new CitationsFormat;
	$formatter->setFormat($this->format);
	
	// Loop through the citations and build the HTML
	foreach ($this->citations as $cite) 
	{
		$item  = "\t".'<li>'."\n";
		$item .= $formatter->formatReference($cite, '');
		$item .= "\t\t".'<p class="details">'."\n";
		$item .= "\t\t\t".'<a href="'.JRoute::_('index.php?option=com_citations&task=download&id='.$cite->id.'&format=bibtex&no_html=1').'" title="'.JText::_('PLG_RESOURCES_CITATIONS_DOWNLOAD_BIBTEX').'">BibTex</a> <span>|</span> '."\n";
		$item .= "\t\t\t".'<a href="'.JRoute::_('index.php?option=com_citations&task=download&id='.$cite->id.'&format=endnote&no_html=1').'" title="'.JText::_('PLG_RESOURCES_CITATIONS_DOWNLOAD_ENDNOTE').'">EndNote</a>'."\n";
		if ($cite->eprint) {
			if ($cite->eprint) {
				$item .= "\t\t\t".' <span>|</span> <a href="'.stripslashes($cite->eprint).'">'.JText::_('PLG_RESOURCES_CITATIONS_ELECTRONIC_PAPER').'</a>'."\n";
			}
		}
		$item .= "\t\t".'</p>'."\n";
		$item .= "\t".'</li>'."\n";

		// Decide which group to add it to
		if ($cite->affiliated) {
			$affiliated .= $item;
			$numaff++;
		} else {
			$nonaffiliated .= $item;
			$numnon++;
		}
	}
}
?>
<h3>
	<a name="citations"></a>
	<?php echo JText::_('PLG_RESOURCES_CITATIONS'); ?> 
	<span>
		<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->resource->id.'&active=citations#nonaffiliated'); ?>"><?php echo JText::_('PLG_RESOURCES_CITATIONS_NONAFF'); ?> (<?php echo $numnon; ?>)</a> | 
		<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->resource->id.'&active=citations#affiliated'); ?>"><?php echo JText::_('PLG_RESOURCES_CITATIONS_AFF'); ?> (<?php echo $numaff; ?>)</a>
	</span>
</h3>

		
		
<?php 
	//
	// Cite this work add-in
	//
	$citations = '';
	$config = JFactory::getConfig();
	$sef = JRoute::_('index.php?option='.$this->option.a.'id='.$this->resource->id);
	$thedate = ($this->resource->publish_up != '0000-00-00 00:00:00') 
				 ? $this->resource->publish_up 
				 : $this->resource->created;

	$database =& JFactory::getDBO();
	// Initiate a resource helper class
	$helper = new ResourcesHelper( $this->resource->id, $database );

	// Citation instructions
	$helper->getUnlinkedContributors();
	// Build our citation object
	$cite = new stdClass();
	$cite->title = $this->resource->title;
	$cite->year = JHTML::_('date', $thedate, '%Y');
	$juri =& JURI::getInstance();
	if (substr($sef,0,1) == '/') {
		$sef = substr($sef,1,strlen($sef));
	}
	$cite->location = $juri->base().$sef;
	$cite->date = date( "Y-m-d H:i:s" );			
	$cite->url = '';
	$cite->type = '';
	$cite->author = $helper->ul_contributors;
	if (isset($this->resource->doi)) {
		$cite->doi = $config->get('doi').'r'.$this->resource->id.'.'.$this->resource->doi;
	}
	
	$citeinstruct  = ResourcesHtml::citation( $option, $cite, $this->resource->id, $citations, $this->resource->type );
	$citeinstruct .= ResourcesHtml::citationCOins($cite, $this->resource, $config, $helper);
	echo ResourcesHtml::tableRow( '<a name="citethis"></a>'.JText::_('COM_RESOURCES_CITE_THIS'), $citeinstruct );

?>
<?php
if ($this->citations) { 
	if ($nonaffiliated) { 
?>
	<h4><?php echo JText::_('PLG_RESOURCES_CITATIONS_NOT_AFFILIATED'); ?></h4>
	<ul class="citations results">
		<?php echo $nonaffiliated; ?>
	</ul>
<?php
	}
	if ($affiliated) {
?>
	<h4><?php echo JText::_('PLG_RESOURCES_CITATIONS_AFFILIATED'); ?></h4>
	<ul class="citations results">
		<?php echo $affiliated; ?>
	</ul>
<?php
	}
} else {
?>
	<p><?php echo JText::_('PLG_RESOURCES_CITATIONS_NO_CITATIONS_FOUND'); ?></p>
<?php
}
?>