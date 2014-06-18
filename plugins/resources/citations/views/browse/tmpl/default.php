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
defined('_JEXEC') or die( 'Restricted access' );

$numaff = 0;
$numnon = 0;

// Did we get any results back?
if ($this->citations)
{
	// Get a needed library
	include_once(JPATH_ROOT . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'format.php');

	// Set some vars
	$affiliated = '';
	$nonaffiliated = '';

	$formatter = new CitationFormat;
	//$formatter->setFormat($this->format);

	// Loop through the citations and build the HTML
	foreach ($this->citations as $cite)
	{
		$item  = "\t" . '<li>' . "\n";
		$item .= CitationFormat::formatReference($cite, '');
		$item .= "\t\t" . '<p class="details">' . "\n";
		$item .= "\t\t\t" . '<a href="' . JRoute::_('index.php?option=com_citations&task=download&id=' . $cite->id . '&format=bibtex&no_html=1') . '" title="' . JText::_('PLG_RESOURCES_CITATIONS_DOWNLOAD_BIBTEX') . '">BibTex</a> <span>|</span> ' . "\n";
		$item .= "\t\t\t" . '<a href="' . JRoute::_('index.php?option=com_citations&task=download&id=' . $cite->id . '&format=endnote&no_html=1') . '" title="' . JText::_('PLG_RESOURCES_CITATIONS_DOWNLOAD_ENDNOTE') . '">EndNote</a>' . "\n";
		if ($cite->eprint)
		{
			if ($cite->eprint)
			{
				$item .= "\t\t\t" . ' <span>|</span> <a href="' . stripslashes($cite->eprint) . '">' . JText::_('PLG_RESOURCES_CITATIONS_ELECTRONIC_PAPER') . '</a>' . "\n";
			}
		}
		$item .= "\t\t" . '</p>' . "\n";
		$item .= "\t" . '</li>' . "\n";

		// Decide which group to add it to
		if ($cite->affiliated)
		{
			$affiliated .= $item;
			$numaff++;
		}
		else
		{
			$nonaffiliated .= $item;
			$numnon++;
		}
	}
}
?>
<h3>
	<?php echo JText::_('PLG_RESOURCES_CITATIONS'); ?>
	<span>
		<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->resource->id . '&active=citations#nonaffiliated'); ?>"><?php echo JText::_('PLG_RESOURCES_CITATIONS_NONAFF'); ?> (<?php echo $numnon; ?>)</a> |
		<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->resource->id . '&active=citations#affiliated'); ?>"><?php echo JText::_('PLG_RESOURCES_CITATIONS_AFF'); ?> (<?php echo $numaff; ?>)</a>
	</span>
</h3>
<?php if ($this->citations) { ?>
	<?php if ($nonaffiliated) { ?>
		<h4><?php echo JText::_('PLG_RESOURCES_CITATIONS_NOT_AFFILIATED'); ?></h4>
		<ul class="citations results">
			<?php echo $nonaffiliated; ?>
		</ul>
	<?php } ?>
	<?php if ($affiliated) { ?>
		<h4><?php echo JText::_('PLG_RESOURCES_CITATIONS_AFFILIATED'); ?></h4>
		<ul class="citations results">
			<?php echo $affiliated; ?>
		</ul>
	<?php } ?>
<?php } else { ?>
	<p><?php echo JText::_('PLG_RESOURCES_CITATIONS_NO_CITATIONS_FOUND'); ?></p>
<?php } ?>