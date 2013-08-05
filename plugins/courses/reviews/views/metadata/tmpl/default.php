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
defined('_JEXEC') or die('Restricted access');

$filters = array(
	'state' => 1,
	'item_id' => $this->course->get('id'),
	'item_type' => 'courses',
	'parent' => 0
);

$total = 0;
$avg   = 0;
$distribution = array(
	0 => 0,
	1 => 0,
	2 => 0,
	3 => 0,
	4 => 0,
	5 => 0
);

$ratings = $this->tbl->ratings($filters);
if ($ratings)
{
	$sum = 0;
	$total = count($ratings);
	foreach ($ratings as $item)
	{
		$distribution[$item->rating]++;
		$sum += $item->rating;
	}

	// Find the average of all ratings
	$avg = ($total > 0) ? $sum / $total : 0;

	// Round to the nearest half
	$avg = ($avg > 0) ? round($avg*2)/2 : 0;
}

switch ($avg)
{
	case 0.5: $cls = ' half-stars';      break;
	case 1:   $cls = ' one-stars';       break;
	case 1.5: $cls = ' onehalf-stars';   break;
	case 2:   $cls = ' two-stars';       break;
	case 2.5: $cls = ' twohalf-stars';   break;
	case 3:   $cls = ' three-stars';     break;
	case 3.5: $cls = ' threehalf-stars'; break;
	case 4:   $cls = ' four-stars';      break;
	case 4.5: $cls = ' fourhalf-stars';  break;
	case 5:   $cls = ' five-stars';      break;
	case 0:
	default:  $cls = ' no-stars';      break;
}
?>
<div class="reviews-summary">
	<p class="avgrating <?php echo $cls; ?>">
		<strong><span><?php echo $avg; ?> out of 5 stars</span> (<?php echo $total; ?>)</strong>
	</p>
	<table class="reviews-distribution">
		<caption>Rating distribution</caption>
		<tbody>
			<tr>
				<th>5 star</th>
				<td>
					<span class="review-base">
						<strong class="review-bar" style="width: <?php echo $total ? round($distribution[5] / $total, 2)*100 : 0; ?>%;">
							<span><?php echo $this->escape($distribution[5]); ?></span>
						</strong>
					</span>
				</td>
			</tr>
			<tr>
				<th>4 star</th>
				<td>
					<span class="review-base">
						<strong class="review-bar" style="width: <?php echo $total ? round($distribution[4] / $total, 2)*100 : 0; ?>%;">
							<span><?php echo $this->escape($distribution[4]); ?></span>
						</strong>
					</span>
				</td>
			</tr>
			<tr>
				<th>3 star</th>
				<td>
					<span class="review-base">
						<strong class="review-bar" style="width: <?php echo $total ? round($distribution[3] / $total, 2)*100 : 0; ?>%;">
							<span><?php echo $this->escape($distribution[3]); ?></span>
						</strong>
					</span>
				</td>
			</tr>
			<tr>
				<th>2 star</th>
				<td>
					<span class="review-base">
						<strong class="review-bar" style="width: <?php echo $total ? round($distribution[2] / $total, 2)*100 : 0; ?>%;">
							<span><?php echo $this->escape($distribution[2]); ?></span>
						</strong>
					</span>
				</td>
			</tr>
			<tr>
				<th>1 star</th>
				<td>
					<span class="review-base">
						<strong class="review-bar" style="width: <?php echo $total ? round($distribution[1] / $total, 2)*100 : 0; ?>%;">
							<span><?php echo $this->escape($distribution[1]); ?></span>
						</strong>
					</span>
				</td>
			</tr>
		</tbody>
	</table>
</div>