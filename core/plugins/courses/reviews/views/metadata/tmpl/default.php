<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

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

$ratings = \Components\Courses\Models\Comment::all()
	->whereEquals('item_id', $this->course->get('id'))
	->whereEquals('item_type', 'courses')
	->whereEquals('parent', 0)
	->whereEquals('state', array(
		Components\Courses\Models\Comment::STATE_PUBLISHED
	))
	->rows();

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
	case 0.5:
		$cls = ' half-stars';
		break;
	case 1:
		$cls = ' one-stars';
		break;
	case 1.5:
		$cls = ' onehalf-stars';
		break;
	case 2:
		$cls = ' two-stars';
		break;
	case 2.5:
		$cls = ' twohalf-stars';
		break;
	case 3:
		$cls = ' three-stars';
		break;
	case 3.5:
		$cls = ' threehalf-stars';
		break;
	case 4:
		$cls = ' four-stars';
		break;
	case 4.5:
		$cls = ' fourhalf-stars';
		break;
	case 5:
		$cls = ' five-stars';
		break;
	case 0:
	default:
		$cls = ' no-stars';
		break;
}

$this->css('
	.review-bar-5star {
		"width: ' . ($total ? round($distribution[5] / $total, 2)*100 : 0) . '%;
	}
	.review-bar-4star {
		"width: ' . ($total ? round($distribution[4] / $total, 2)*100 : 0) . '%;
	}
	.review-bar-3star {
		"width: ' . ($total ? round($distribution[3] / $total, 2)*100 : 0) . '%;
	}
	.review-bar-2star {
		"width: ' . ($total ? round($distribution[2] / $total, 2)*100 : 0) . '%;
	}
	.review-bar-1star {
		"width: ' . ($total ? round($distribution[1] / $total, 2)*100 : 0) . '%;
	}
');
?>
<div class="reviews-summary">
	<p class="avgrating <?php echo $cls; ?>">
		<strong><span><?php echo Lang::txt('PLG_COURSES_REVIEWS_RATING_OUT_OF_5_STARS', $avg); ?></span> (<?php echo $total; ?>)</strong>
	</p>
	<table class="reviews-distribution">
		<caption><?php echo Lang::txt('PLG_COURSES_REVIEWS_RATING_DISTRIBUTION'); ?></caption>
		<tbody>
			<tr>
				<th><?php echo Lang::txt('PLG_COURSES_REVIEWS_RATING_5_STAR'); ?></th>
				<td>
					<span class="review-base">
						<strong class="review-bar review-bar-5star">
							<span><?php echo $this->escape($distribution[5]); ?></span>
						</strong>
					</span>
				</td>
			</tr>
			<tr>
				<th><?php echo Lang::txt('PLG_COURSES_REVIEWS_RATING_4_STAR'); ?></th>
				<td>
					<span class="review-base">
						<strong class="review-bar review-bar-4star">
							<span><?php echo $this->escape($distribution[4]); ?></span>
						</strong>
					</span>
				</td>
			</tr>
			<tr>
				<th><?php echo Lang::txt('PLG_COURSES_REVIEWS_RATING_3_STAR'); ?></th>
				<td>
					<span class="review-base">
						<strong class="review-bar review-bar-3star">
							<span><?php echo $this->escape($distribution[3]); ?></span>
						</strong>
					</span>
				</td>
			</tr>
			<tr>
				<th><?php echo Lang::txt('PLG_COURSES_REVIEWS_RATING_2_STAR'); ?></th>
				<td>
					<span class="review-base">
						<strong class="review-bar review-bar-2star">
							<span><?php echo $this->escape($distribution[2]); ?></span>
						</strong>
					</span>
				</td>
			</tr>
			<tr>
				<th><?php echo Lang::txt('PLG_COURSES_REVIEWS_RATING_1_STAR'); ?></th>
				<td>
					<span class="review-base">
						<strong class="review-bar review-bar-1star">
							<span><?php echo $this->escape($distribution[1]); ?></span>
						</strong>
					</span>
				</td>
			</tr>
		</tbody>
	</table>
</div>