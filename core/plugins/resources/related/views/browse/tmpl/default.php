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

$database = App::get('db');
?>
<h3><?php echo Lang::txt('PLG_RESOURCES_RELATED_HEADER'); ?></h3>

<?php if ($this->related) { ?>
	<table class="related-resources">
		<tbody>
		<?php
		foreach ($this->related as $line)
		{
			if ($line->section != 'Topic')
			{
				$class = \Components\Resources\Helpers\Html::getRatingClass($line->rating);

				$resourceEx = new \Components\Resources\Helpers\Helper($line->id, $database);
				$resourceEx->getContributors();

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
				$myclass = \Components\Resources\Helpers\Html::getRatingClass($myrating);

				// Get the SEF for the resource
				if ($line->alias)
				{
					$sef = Route::url('index.php?option=' . $this->option . '&alias='. $line->alias);
				}
				else
				{
					$sef = Route::url('index.php?option=' . $this->option . '&id='. $line->id);
				}
			}
			else
			{
				if ($line->group_cn != '' && $line->scope != '')
				{
					$sef = Route::url('index.php?option=com_groups&scope=' . $line->scope . '&pagename=' . $line->alias);
				}
				else
				{
					$sef = Route::url('index.php?option=com_wiki&scope=' . $line->scope . '&pagename=' . $line->alias);
				}
			}

			// Make sure we have an SEF, otherwise it's a querystring
			$d = (strstr($sef,'option=')) ? '&' : '?';

			// Format the ranking
			$line->ranking = round($line->ranking, 1);
			$r = (10*$line->ranking);
			if (intval($r) < 10)
			{
				$r = '0' . $r;
			}
		?>
			<tr>
				<td class="ranking"><?php echo number_format($line->ranking, 1); ?> <span class="rank-<?php echo $r; ?>"><?php echo Lang::txt('PLG_RESOURCES_RELATED_RANKING'); ?></span></td>
				<td>
				<?php if ($line->section != 'Topic') { ?>
					<?php echo Lang::txt('PLG_RESOURCES_RELATED_PART_OF'); ?>
					<a href="<?php echo $sef; ?>" class="fixedResourceTip" title="DOM:rsrce<?php echo $line->id; ?>"><?php echo stripslashes($line->title); ?></a>
					<div style="display:none;" id="rsrce<?php echo $line->id; ?>">
						<h4><?php echo stripslashes($line->title); ?></h4>
						<div>
							<table>
								<tbody>
									<tr>
										<th><?php echo Lang::txt('PLG_RESOURCES_RELATED_TYPE'); ?></th>
										<td><?php echo $line->section; ?></td>
									</tr>
								<?php if ($resourceEx->contributors) { ?>
									<tr>
										<th><?php echo Lang::txt('PLG_RESOURCES_RELATED_CONTRIBUTORS'); ?></th>
										<td><?php echo $resourceEx->contributors; ?></td>
									</tr>
								<?php } ?>
									<tr>
										<th><?php echo Lang::txt('PLG_RESOURCES_RELATED_DATE'); ?></th>
										<td><?php echo Date::of($line->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></td>
									</tr>
									<tr>
										<th><?php echo Lang::txt('PLG_RESOURCES_RELATED_AVG_RATING'); ?></th>
										<td><span class="avgrating<?php echo $class; ?>"><span><?php echo Lang::txt('OUT_OF_5_STARS',$line->rating); ?></span>&nbsp;</span> (<?php echo $line->times_rated; ?>)</td>
									</tr>
									<tr>
										<th><?php echo Lang::txt('PLG_RESOURCES_RELATED_RATE_THIS'); ?></th>
										<td>
											<ul class="starsz<?php echo $myclass; ?>">
												<li class="str1"><a href="<?php echo $sef; ?>/reviews<?php echo $d; ?>action=addreview&amp;myrating=1#reviewform" title="<?php echo Lang::txt('PLG_RESOURCES_RELATED_RATING_POOR'); ?>"><?php echo Lang::txt('PLG_RESOURCES_RELATED_RATING_1_STAR'); ?></a></li>
												<li class="str2"><a href="<?php echo $sef; ?>/reviews<?php echo $d; ?>action=addreview&amp;myrating=2#reviewform" title="<?php echo Lang::txt('PLG_RESOURCES_RELATED_RATING_FAIR'); ?>"><?php echo Lang::txt('PLG_RESOURCES_RELATED_RATING_2_STARS'); ?></a></li>
												<li class="str3"><a href="<?php echo $sef; ?>/reviews<?php echo $d; ?>action=addreview&amp;myrating=3#reviewform" title="<?php echo Lang::txt('PLG_RESOURCES_RELATED_RATING_GOOD'); ?>"><?php echo Lang::txt('PLG_RESOURCES_RELATED_RATING_3_STARS'); ?></a></li>
												<li class="str4"><a href="<?php echo $sef; ?>/reviews<?php echo $d; ?>action=addreview&amp;myrating=4#reviewform" title="<?php echo Lang::txt('PLG_RESOURCES_RELATED_RATING_VERY_GOOD'); ?>"><?php echo Lang::txt('PLG_RESOURCES_RELATED_RATING_4_STARS'); ?></a></li>
												<li class="str5"><a href="<?php echo $sef; ?>/reviews<?php echo $d; ?>action=addreview&amp;myrating=5#reviewform" title="<?php echo Lang::txt('PLG_RESOURCES_RELATED_RATING_EXCELLENT'); ?>"><?php echo Lang::txt('PLG_RESOURCES_RELATED_RATING_5_STARS'); ?></a></li>
											</ul>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<?php echo \Hubzero\Utility\String::truncate(stripslashes($line->introtext), 300); ?>
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
	<p><?php echo Lang::txt('PLG_RESOURCES_RELATED_NO_RESULTS_FOUND'); ?></p>
<?php } ?>
