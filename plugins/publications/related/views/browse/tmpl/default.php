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

$dateFormat = '%d %b, %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M, Y';
	$tz = false;
}

$juser =& JFactory::getUser();
$database =& JFactory::getDBO();

// Get publications helper & author classes
$helper = new PublicationHelper($database);
$pa = new PublicationAuthor( $database );
$authorlist = '';

?>
<h3><?php echo JText::_('PLG_PUBLICATION_RELATED_HEADER'); ?></h3>
<?php if ($this->related) { ?>
<table class="related-publications">
	<tbody>
<?php
foreach ($this->related as $line)
{
	if($line->section == 'Topic') {
		if ($line->group != '' && $line->scope != '') {
			$sef = JRoute::_('index.php?option=com_groups&scope='.$line->scope.'&pagename='.$line->alias);
		} else {
			$sef = JRoute::_('index.php?option=com_topics&scope='.$line->scope.'&pagename='.$line->alias);
		}
	}
	else {
		$class = PublicationsHtml::getRatingClass( $line->rating );
		
		// Get version authors
		$authors = $pa->getAuthors($line->version);
		$authorlist = $helper->showContributors( $authors, false, true );

		// If the user is logged in, get their rating for this publication
		if (!$juser->get('guest')) {
			$mr = new PublicationReview( $database );
			$myrating = $mr->loadUserRating( $line->id, $juser->get('id'), $line->version );
		} else {
			$myrating = 0;
		}
		$myclass = PublicationsHtml::getRatingClass( $myrating );

		// Get the SEF for the publication
		if ($line->alias) {
			$sef = JRoute::_('index.php?option='.$this->option.'&alias='. $line->alias);
		} else {
			$sef = JRoute::_('index.php?option='.$this->option.'&id='. $line->id);
		}
	}

	// Make sure we have an SEF, otherwise it's a querystring
	if (strstr($sef,'option=')) {
		$d = '&amp;';
	} else {
		$d = '?';
	}

	// Format the ranking
	$line->ranking = round($line->ranking, 1);
	$r = (10*$line->ranking);
	if (intval($r) < 10) {
		$r = '0'.$r;
	}
?>
		<tr>
			<td>
<?php  if($line->section == 'Topic') { ?>
		<a href="<?php echo $sef; ?>"><?php echo stripslashes($line->title); ?></a>
<?php }
		
else { ?>
	<?php if($line->section == 'Series') { echo JText::_('PLG_PUBLICATION_RELATED_PART_OF'); } ?>
				<a href="<?php echo $sef; ?>" class="fixedResourceTip" title="DOM:rsrce<?php echo $line->id; ?>"><?php echo stripslashes($line->title); ?></a>
				<div style="display:none;" id="rsrce<?php echo $line->id; ?>">
					<h4><?php echo stripslashes($line->title); ?></h4>
					<div>
						<table>
							<tbody>
								<tr>
									<th><?php echo JText::_('PLG_PUBLICATION_RELATED_TYPE'); ?></th>
									<td><?php echo $line->section; ?></td>
								</tr>
<?php if ($authorlist) { ?>
								<tr>
									<th><?php echo JText::_('PLG_PUBLICATION_RELATED_CONTRIBUTORS'); ?></th>
									<td><?php echo $authorlist; ?></td>
								</tr>
<?php } ?>
								<tr>
									<th><?php echo JText::_('PLG_PUBLICATION_RELATED_DATE'); ?></th>
									<td><?php echo JHTML::_('date',$line->published_up, $dateFormat, $tz); ?></td>
								</tr>
								<tr>
									<th><?php echo JText::_('PLG_PUBLICATION_RELATED_AVG_RATING'); ?></th>
									<td><span class="avgrating<?php echo $class; ?>"><span><?php echo JText::sprintf('OUT_OF_5_STARS',$line->rating); ?></span>&nbsp;</span> (<?php echo $line->times_rated; ?>)</td>
								</tr>
							</tbody>
						</table>
					</div>
					<?php echo Hubzero_View_Helper_Html::shortenText( stripslashes($line->abstract) ); ?>
				</div>
<?php } ?>
			</td>
			<td class="type"><?php echo $line->section; ?></td>
		</tr>
<?php } ?>
	</tbody>
</table>
<?php } else { ?>
<p><?php echo JText::_('PLG_PUBLICATION_RELATED_NO_RESULTS_FOUND'); ?></p>
<?php } ?>
