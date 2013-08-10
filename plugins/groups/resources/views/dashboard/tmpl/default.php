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

if ($this->results) {
	ximport('Hubzero_View_Helper_Html');

	include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'tables'.DS.'review.php' );

	plgGroupsResources::documents();

	$database =& JFactory::getDBO();
	$juser =& JFactory::getUser();
?>
	<table class="related-resources" summary="<?php echo JText::_('PLG_GROUPS_RESOURCES_DASHBOARD_SUMMARY'); ?>">
		<tbody>
<?php
	foreach ($this->results as $line)
	{
		switch ($line->rating)
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

		$helper = new ResourcesHelper( $line->id, $database );
		$helper->getContributors();

		// If the user is logged in, get their rating for this resource
		if (!$juser->get('guest')) {
			$mr = new ResourcesReview( $database );
			$myrating = $mr->loadUserRating( $line->id, $juser->get('id') );
		} else {
			$myrating = 0;
		}
		switch ($myrating)
		{
			case 0.5: $myclass = ' half-stars';      break;
			case 1:   $myclass = ' one-stars';       break;
			case 1.5: $myclass = ' onehalf-stars';   break;
			case 2:   $myclass = ' two-stars';       break;
			case 2.5: $myclass = ' twohalf-stars';   break;
			case 3:   $myclass = ' three-stars';     break;
			case 3.5: $myclass = ' threehalf-stars'; break;
			case 4:   $myclass = ' four-stars';      break;
			case 4.5: $myclass = ' fourhalf-stars';  break;
			case 5:   $myclass = ' five-stars';      break;
			case 0:
			default:  $myclass = ' no-stars';      break;
		}

		// Encode some potentially troublesome characters
		$line->title = Hubzero_View_Helper_Html::xhtml( $line->title );

		// Make sure we have an SEF, otherwise it's a querystring
		if (strstr($line->href,'option=')) {
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
<?php if ($this->config->get('show_ranking')) { ?>
				<td class="ranking"><?php echo number_format($line->ranking,1); ?> <span class="rank-<?php echo $r; ?>"><?php echo JText::_('PLG_GROUPS_RESOURCES_RANKING'); ?></span></td>
<?php } elseif ($this->config->get('show_rating')) { ?>
				<td class="rating"><span class="avgrating<?php echo $class; ?>"><span><?php echo JText::sprintf('PLG_GROUPS_RESOURCES_OUT_OF_5_STARS',$line->rating); ?></span>&nbsp;</span></td>
<?php } ?>
				<td>
					<a href="<?php echo $line->href; ?>" class="fixedResourceTip" title="DOM:rsrce<?php echo $line->id; ?>"><?php echo $line->title ; ?></a>
					<div style="display:none;" id="rsrce<?php echo $line->id; ?>">
						<h4><?php echo $line->title; ?></h4>
						<div>
							<table summary="<?php echo $line->title; ?>">
								<tbody>
									<tr>
										<th><?php echo JText::_('PLG_GROUPS_RESOURCES_TYPE'); ?></th>
										<td><?php echo $line->section; ?></td>
									</tr>
<?php if ($helper->contributors) { ?>
									<tr>
										<th><?php echo JText::_('PLG_GROUPS_RESOURCES_CONTRIBUTORS'); ?></th>
										<td><?php echo $helper->contributors; ?></td>
									</tr>
<?php } ?>
									<tr>
										<th><?php echo JText::_('PLG_GROUPS_RESOURCES_DATE'); ?></th>
										<td><?php echo JHTML::_('date',$line->publish_up, $dateFormat, $tz); ?></td>
									</tr>
									<tr>
										<th><?php echo JText::_('PLG_GROUPS_RESOURCES_AVG_RATING'); ?></th>
										<td><span class="avgrating<?php echo $class; ?>"><span><?php echo JText::sprintf('PLG_GROUPS_RESOURCES_OUT_OF_5_STARS',$line->rating); ?></span>&nbsp;</span> (<?php echo $line->times_rated; ?>)</td>
									</tr>
									<tr>
										<th><?php echo JText::_('PLG_GROUPS_RESOURCES_RATE_THIS'); ?></th>
										<td>
											<ul class="starsz<?php echo $myclass; ?>">
												<li class="str1"><a href="<?php echo $line->href.$d; ?>task=addreview&amp;myrating=1#reviewform" title="<?php echo JText::_('PLG_GROUPS_RESOURCES_RATING_POOR'); ?>"><?php echo JText::_('PLG_GROUPS_RESOURCES_RATING_1_STAR'); ?></a></li>
												<li class="str2"><a href="<?php echo $line->href.$d; ?>task=addreview&amp;myrating=2#reviewform" title="<?php echo JText::_('PLG_GROUPS_RESOURCES_RATING_FAIR'); ?>"><?php echo JText::_('PLG_GROUPS_RESOURCES_RATING_2_STARS'); ?></a></li>
												<li class="str3"><a href="<?php echo $line->href.$d; ?>task=addreview&amp;myrating=3#reviewform" title="<?php echo JText::_('PLG_GROUPS_RESOURCES_RATING_GOOD'); ?>"><?php echo JText::_('PLG_GROUPS_RESOURCES_RATING_3_STARS'); ?></a></li>
												<li class="str4"><a href="<?php echo $line->href.$d; ?>task=addreview&amp;myrating=4#reviewform" title="<?php echo JText::_('PLG_GROUPS_RESOURCES_RATING_VERY_GOOD'); ?>"><?php echo JText::_('PLG_GROUPS_RESOURCES_RATING_4_STARS'); ?></a></li>
												<li class="str5"><a href="<?php echo $line->href.$d; ?>task=addreview&amp;myrating=5#reviewform" title="<?php echo JText::_('PLG_GROUPS_RESOURCES_RATING_EXCELLENT'); ?>"><?php echo JText::_('PLG_GROUPS_RESOURCES_RATING_5_STARS'); ?></a></li>
											</ul>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<?php echo Hubzero_View_Helper_Html::shortenText( $line->itext ); ?>
					</div>
				</td>
				<td class="type"><?php echo $line->area; ?></td>
			</tr>
<?php
	}
?>
		</tbody>
	</table>
<?php } else { ?>
	<p><?php echo JText::_('PLG_GROUPS_RESOURCES_NONE'); ?></p>
<?php } ?>
