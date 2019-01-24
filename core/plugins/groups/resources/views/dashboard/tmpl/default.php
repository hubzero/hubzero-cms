<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if ($this->results) {
	include_once \Component::path('com_resources') . DS . 'tables' . DS . 'review.php';

	plgGroupsResources::documents();

	$database = App::get('db');
?>
	<table class="related-resources">
		<tbody>
		<?php
			foreach ($this->results as $line)
			{
				switch ($line->rating)
				{
					case 0.5:
						$class = ' half-stars';
						break;
					case 1:
						$class = ' one-stars';
						break;
					case 1.5:
						$class = ' onehalf-stars';
						break;
					case 2:
						$class = ' two-stars';
						break;
					case 2.5:
						$class = ' twohalf-stars';
						break;
					case 3:
						$class = ' three-stars';
						break;
					case 3.5:
						$class = ' threehalf-stars';
						break;
					case 4:
						$class = ' four-stars';
						break;
					case 4.5:
						$class = ' fourhalf-stars';
						break;
					case 5:
						$class = ' five-stars';
						break;
					case 0:
					default:
						$class = ' no-stars';
						break;
				}

				$helper = new \Components\Resources\Helpers\Helper($line->id, $database);
				$helper->getContributors();

				// If the user is logged in, get their rating for this resource
				if (!User::isGuest())
				{
					$mr = new \Components\Resources\Tables\Review($database);
					$myrating = $mr->loadUserRating($line->id, User::get('id'));
				}
				else
				{
					$myrating = 0;
				}
				switch ($myrating)
				{
					case 0.5:
						$class = ' half-stars';
						break;
					case 1:
						$class = ' one-stars';
						break;
					case 1.5:
						$class = ' onehalf-stars';
						break;
					case 2:
						$class = ' two-stars';
						break;
					case 2.5:
						$class = ' twohalf-stars';
						break;
					case 3:
						$class = ' three-stars';
						break;
					case 3.5:
						$class = ' threehalf-stars';
						break;
					case 4:
						$class = ' four-stars';
						break;
					case 4.5:
						$class = ' fourhalf-stars';
						break;
					case 5:
						$class = ' five-stars';
						break;
					case 0:
					default:
						$class = ' no-stars';
						break;
				}

				// Encode some potentially troublesome characters
				$line->title = $this->escape($line->title);

				// Make sure we have an SEF, otherwise it's a querystring
				if (strstr($line->href, 'option='))
				{
					$d = '&amp;';
				}
				else
				{
					$d = '?';
				}

				// Format the ranking
				$line->ranking = round($line->ranking, 1);
				$r = (10*$line->ranking);
				if (intval($r) < 10)
				{
					$r = '0' . $r;
				}
		?>
			<tr>
			<?php if ($this->config->get('show_ranking')) { ?>
				<td class="ranking"><?php echo number_format($line->ranking, 1); ?> <span class="rank-<?php echo $r; ?>"><?php echo Lang::txt('PLG_GROUPS_RESOURCES_RANKING'); ?></span></td>
			<?php } elseif ($this->config->get('show_rating')) { ?>
				<td class="rating"><span class="avgrating<?php echo $class; ?>"><span><?php echo Lang::txt('PLG_GROUPS_RESOURCES_OUT_OF_5_STARS', $line->rating); ?></span>&nbsp;</span></td>
			<?php } ?>
				<td>
					<a href="<?php echo $line->href; ?>" class="fixedResourceTip" title="DOM:rsrce<?php echo $line->id; ?>"><?php echo $line->title; ?></a>
					<div class="hide" id="rsrce<?php echo $line->id; ?>">
						<h4><?php echo $line->title; ?></h4>
						<div>
							<table summary="<?php echo $line->title; ?>">
								<tbody>
									<tr>
										<th><?php echo Lang::txt('PLG_GROUPS_RESOURCES_TYPE'); ?></th>
										<td><?php echo $line->section; ?></td>
									</tr>
								<?php if ($helper->contributors) { ?>
									<tr>
										<th><?php echo Lang::txt('PLG_GROUPS_RESOURCES_CONTRIBUTORS'); ?></th>
										<td><?php echo $helper->contributors; ?></td>
									</tr>
								<?php } ?>
									<tr>
										<th><?php echo Lang::txt('PLG_GROUPS_RESOURCES_DATE'); ?></th>
										<td><?php echo Date::of($line->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></td>
									</tr>
									<tr>
										<th><?php echo Lang::txt('PLG_GROUPS_RESOURCES_AVG_RATING'); ?></th>
										<td><span class="avgrating<?php echo $class; ?>"><span><?php echo Lang::txt('PLG_GROUPS_RESOURCES_OUT_OF_5_STARS', $line->rating); ?></span>&nbsp;</span> (<?php echo $line->times_rated; ?>)</td>
									</tr>
									<tr>
										<th><?php echo Lang::txt('PLG_GROUPS_RESOURCES_RATE_THIS'); ?></th>
										<td>
											<ul class="starsz<?php echo $myclass; ?>">
												<li class="str1"><a href="<?php echo $line->href.$d; ?>task=addreview&amp;myrating=1#reviewform" title="<?php echo Lang::txt('PLG_GROUPS_RESOURCES_RATING_POOR'); ?>"><?php echo Lang::txt('PLG_GROUPS_RESOURCES_RATING_1_STAR'); ?></a></li>
												<li class="str2"><a href="<?php echo $line->href.$d; ?>task=addreview&amp;myrating=2#reviewform" title="<?php echo Lang::txt('PLG_GROUPS_RESOURCES_RATING_FAIR'); ?>"><?php echo Lang::txt('PLG_GROUPS_RESOURCES_RATING_2_STARS'); ?></a></li>
												<li class="str3"><a href="<?php echo $line->href.$d; ?>task=addreview&amp;myrating=3#reviewform" title="<?php echo Lang::txt('PLG_GROUPS_RESOURCES_RATING_GOOD'); ?>"><?php echo Lang::txt('PLG_GROUPS_RESOURCES_RATING_3_STARS'); ?></a></li>
												<li class="str4"><a href="<?php echo $line->href.$d; ?>task=addreview&amp;myrating=4#reviewform" title="<?php echo Lang::txt('PLG_GROUPS_RESOURCES_RATING_VERY_GOOD'); ?>"><?php echo Lang::txt('PLG_GROUPS_RESOURCES_RATING_4_STARS'); ?></a></li>
												<li class="str5"><a href="<?php echo $line->href.$d; ?>task=addreview&amp;myrating=5#reviewform" title="<?php echo Lang::txt('PLG_GROUPS_RESOURCES_RATING_EXCELLENT'); ?>"><?php echo Lang::txt('PLG_GROUPS_RESOURCES_RATING_5_STARS'); ?></a></li>
											</ul>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<?php echo \Hubzero\Utility\Str::truncate($line->itext, 300); ?>
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
	<p><?php echo Lang::txt('PLG_GROUPS_RESOURCES_NONE'); ?></p>
<?php } 