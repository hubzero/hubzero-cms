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
	include_once( JPATH_ROOT . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'format.php' );

	// Set some vars
	$affiliated = '';
	$nonaffiliated = '';

	$formatter = new CitationFormat;
	$formatter->setTemplate($this->format);

	// Loop through the citations and build the HTML
	foreach ($this->citations as $cite)
	{

		$showLinks = ($cite->title && $cite->author && $cite->publisher) ? true : false;

		$item  = "\t".'<li>'."\n";
		$formatted = $cite->formatted ? $cite->formatted : CitationFormat::formatReference($cite, '');

		if ($cite->doi && $cite->url)
		{
			$formatted = str_replace('doi:' . $cite->doi, '<a href="' . $cite->url . '" rel="external">'
				. 'doi:' . $cite->doi . '</a>', $formatted);
		}

		$item .= $formatted;
		$item .= "\t\t".'<p class="details">'."\n";
		if ($showLinks)
		{
			$item .= "\t\t\t".'<a href="'.JRoute::_('index.php?option=com_citations&task=download&id='.$cite->id.'&format=bibtex&no_html=1').'" title="'.JText::_('PLG_PUBLICATION_CITATIONS_DOWNLOAD_BIBTEX').'">BibTex</a> <span>|</span> '."\n";
			$item .= "\t\t\t".'<a href="'.JRoute::_('index.php?option=com_citations&task=download&id='.$cite->id.'&format=endnote&no_html=1').'" title="'.JText::_('PLG_PUBLICATION_CITATIONS_DOWNLOAD_ENDNOTE').'">EndNote</a>'."\n";
		}
		if ($cite->eprint) {
			if ($cite->eprint) {
				$item .= "\t\t\t".' <span>|</span> <a href="'.stripslashes($cite->eprint).'">'.JText::_('PLG_PUBLICATION_CITATIONS_ELECTRONIC_PAPER').'</a>'."\n";
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
	<?php echo JText::_('PLG_PUBLICATION_CITATIONS'); ?>
	<span>
		<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->publication->id.'&active=citations&v=' . $this->publication->version_number . '#nonaffiliated'); ?>"><?php echo JText::_('PLG_PUBLICATION_CITATIONS_NONAFF'); ?> (<?php echo $numnon; ?>)</a> |
		<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->publication->id.'&active=citations&v=' . $this->publication->version_number . '#affiliated'); ?>"><?php echo JText::_('PLG_PUBLICATION_CITATIONS_AFF'); ?> (<?php echo $numaff; ?>)</a>
	</span>
</h3>
<?php
if ($this->citations) {
	if ($nonaffiliated) {
?>
	<a name="nonaffiliated"></a>
	<h4><?php echo JText::_('PLG_PUBLICATION_CITATIONS_NOT_AFFILIATED'); ?></h4>
	<ul class="citations results">
		<?php echo $nonaffiliated; ?>
	</ul>
<?php
	}
	if ($affiliated) {
?>
	<a name="affiliated"></a>
	<h4><?php echo JText::_('PLG_PUBLICATION_CITATIONS_AFFILIATED'); ?></h4>
	<ul class="citations results">
		<?php echo $affiliated; ?>
	</ul>
<?php
	}
} else {
?>
	<p><?php echo JText::_('PLG_PUBLICATION_CITATIONS_NO_CITATIONS_FOUND'); ?></p>
<?php
}
?>
