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

$dateFormat = '%d %b, %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M, Y';
	$tz = false;
}

$juser =& JFactory::getUser();
$database =& JFactory::getDBO();
?>
<h3><?php echo JText::_('PLG_RESOURCES_RELATED_HEADER'); ?></h3>
<?php if ($this->related) { ?>
<table class="related-resources">
	<tbody>
<?php
foreach ($this->related as $line)
{
	if ($line->section != 'Topic') {
		$class = ResourcesHtml::getRatingClass( $line->rating );

		$resourceEx = new ResourceExtended( $line->id, $database );
		$resourceEx->getContributors();

		// If the user is logged in, get their rating for this resource
		if (!$juser->get('guest')) {
			$mr = new ResourcesReview( $database );
			$myrating = $mr->loadUserRating( $line->id, $juser->get('id') );
		} else {
			$myrating = 0;
		}
		$myclass = ResourcesHtml::getRatingClass( $myrating );

		// Get the SEF for the resource
		if ($line->alias) {
			$sef = JRoute::_('index.php?option='.$this->option.'&alias='. $line->alias);
		} else {
			$sef = JRoute::_('index.php?option='.$this->option.'&id='. $line->id);
		}
	} else {
		if ($line->group_cn != '' && $line->scope != '') {
			$sef = JRoute::_('index.php?option=com_groups&scope='.$line->scope.'&pagename='.$line->alias);
		} else {
			$sef = JRoute::_('index.php?option=com_wiki&scope='.$line->scope.'&pagename='.$line->alias);
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
			<td class="ranking"><?php echo number_format($line->ranking,1); ?> <span class="rank-<?php echo $r; ?>"><?php echo JText::_('PLG_RESOURCES_RELATED_RANKING'); ?></span></td>
			<td>
<?php if ($line->section != 'Topic') { ?>
				<?php echo JText::_('PLG_RESOURCES_RELATED_PART_OF'); ?> 
				<a href="<?php echo $sef; ?>" class="fixedResourceTip" title="DOM:rsrce<?php echo $line->id; ?>"><?php echo stripslashes($line->title); ?></a>
				<div style="display:none;" id="rsrce<?php echo $line->id; ?>">
					<h4><?php echo stripslashes($line->title); ?></h4>
					<div>
						<table>
							<tbody>
								<tr>
									<th><?php echo JText::_('PLG_RESOURCES_RELATED_TYPE'); ?></th>
									<td><?php echo $line->section; ?></td>
								</tr>
<?php if ($resourceEx->contributors) { ?>
								<tr>
									<th><?php echo JText::_('PLG_RESOURCES_RELATED_CONTRIBUTORS'); ?></th>
									<td><?php echo $resourceEx->contributors; ?></td>
								</tr>
<?php } ?>
								<tr>
									<th><?php echo JText::_('PLG_RESOURCES_RELATED_DATE'); ?></th>
									<td><?php echo JHTML::_('date',$line->publish_up, $dateFormat, $tz); ?></td>
								</tr>
								<tr>
									<th><?php echo JText::_('PLG_RESOURCES_RELATED_AVG_RATING'); ?></th>
									<td><span class="avgrating<?php echo $class; ?>"><span><?php echo JText::sprintf('OUT_OF_5_STARS',$line->rating); ?></span>&nbsp;</span> (<?php echo $line->times_rated; ?>)</td>
								</tr>
								<tr>
									<th><?php echo JText::_('PLG_RESOURCES_RELATED_RATE_THIS'); ?></th>
									<td>
										<ul class="starsz<?php echo $myclass; ?>">
											<li class="str1"><a href="<?php echo $sef; ?>/reviews<?php echo $d; ?>action=addreview&amp;myrating=1#reviewform" title="<?php echo JText::_('PLG_RESOURCES_RELATED_RATING_POOR'); ?>"><?php echo JText::_('PLG_RESOURCES_RELATED_RATING_1_STAR'); ?></a></li>
											<li class="str2"><a href="<?php echo $sef; ?>/reviews<?php echo $d; ?>action=addreview&amp;myrating=2#reviewform" title="<?php echo JText::_('PLG_RESOURCES_RELATED_RATING_FAIR'); ?>"><?php echo JText::_('PLG_RESOURCES_RELATED_RATING_2_STARS'); ?></a></li>
											<li class="str3"><a href="<?php echo $sef; ?>/reviews<?php echo $d; ?>action=addreview&amp;myrating=3#reviewform" title="<?php echo JText::_('PLG_RESOURCES_RELATED_RATING_GOOD'); ?>"><?php echo JText::_('PLG_RESOURCES_RELATED_RATING_3_STARS'); ?></a></li>
											<li class="str4"><a href="<?php echo $sef; ?>/reviews<?php echo $d; ?>action=addreview&amp;myrating=4#reviewform" title="<?php echo JText::_('PLG_RESOURCES_RELATED_RATING_VERY_GOOD'); ?>"><?php echo JText::_('PLG_RESOURCES_RELATED_RATING_4_STARS'); ?></a></li>
											<li class="str5"><a href="<?php echo $sef; ?>/reviews<?php echo $d; ?>action=addreview&amp;myrating=5#reviewform" title="<?php echo JText::_('PLG_RESOURCES_RELATED_RATING_EXCELLENT'); ?>"><?php echo JText::_('PLG_RESOURCES_RELATED_RATING_5_STARS'); ?></a></li>
										</ul>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<?php echo Hubzero_View_Helper_Html::shortenText( stripslashes($line->introtext) ); ?>
				</div>
<?php } else { ?>
				<a href="<?php echo $sef; ?>"><?php echo stripslashes($line->title); ?></a>
<?php } ?>
			</td>
			<td class="type"><?php echo $line->section; ?></td>
		</tr>
<?php } ?>
	</tbody>
</table>
<?php } else { ?>
<p><?php echo JText::_('PLG_RESOURCES_RELATED_NO_RESULTS_FOUND'); ?></p>
<?php } ?>
