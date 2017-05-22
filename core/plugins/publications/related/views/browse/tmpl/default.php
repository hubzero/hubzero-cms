<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

// No direct access
defined('_HZEXEC_') or die();

// Add stylesheet
$this->css('assets/css/related.css');

$database = App::get('db');

// Get author class
$pa = new \Components\Publications\Tables\Author($database);
$authorlist = '';

?>
<h3><?php echo Lang::txt('PLG_PUBLICATION_RELATED_HEADER'); ?></h3>
<?php if ($this->related) { ?>
	<table class="related-publications">
		<tbody>
			<?php
			foreach ($this->related as $line)
			{
				if ($line->section == 'Topic')
				{
					if ($line->group != '' && $line->scope != '')
					{
						$sef = Route::url('index.php?option=com_groups&scope='.$line->scope.'&pagename='.$line->alias);
					}
					else
					{
						$sef = Route::url('index.php?option=com_wiki&scope='.$line->scope.'&pagename='.$line->alias);
					}
				}
				else
				{
					$class = \Components\Publications\Helpers\Html::getRatingClass($line->rating);

					// Get version authors
					$authors = $pa->getAuthors($line->version);
					$authorlist = \Components\Publications\Helpers\Html::showContributors($authors, false, true);

					// If the user is logged in, get their rating for this publication
					$myrating = 0;
					if (!User::isGuest())
					{
						$mr = new \Components\Publications\Tables\Review($database);
						$myrating = $mr->loadUserRating($line->id, User::get('id'), $line->version);
					}
					$myclass = \Components\Publications\Helpers\Html::getRatingClass($myrating);

					// Get the SEF for the publication
					$sef = Route::url('index.php?option=' . $this->option . ($line->alias ? '&alias=' . $line->alias : '&id=' . $line->id));
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
					<td>
						<?php if ($line->section == 'Topic') { ?>
							<a href="<?php echo $sef; ?>"><?php echo stripslashes($line->title); ?></a>
						<?php } else { ?>
							<?php if ($line->section == 'Series') { echo Lang::txt('PLG_PUBLICATION_RELATED_PART_OF'); } ?>
								<a href="<?php echo $sef; ?>" class="fixedResourceTip" title="DOM:rsrce<?php echo $line->id; ?>"><?php echo $this>escape(stripslashes($line->title)); ?></a>
								<div style="display:none;" id="rsrce<?php echo $line->id; ?>">
									<h4><?php echo stripslashes($line->title); ?></h4>
									<div>
										<table>
											<tbody>
												<tr>
													<th><?php echo Lang::txt('PLG_PUBLICATION_RELATED_TYPE'); ?></th>
													<td><?php echo $line->section; ?></td>
												</tr>
											<?php if ($authorlist) { ?>
												<tr>
													<th><?php echo Lang::txt('PLG_PUBLICATION_RELATED_CONTRIBUTORS'); ?></th>
													<td><?php echo $authorlist; ?></td>
												</tr>
											<?php } ?>
												<tr>
													<th><?php echo Lang::txt('PLG_PUBLICATION_RELATED_DATE'); ?></th>
													<td><?php echo Date::of($line->published_up)->toLocal('d M, Y'); ?></td>
												</tr>
												<tr>
													<th><?php echo Lang::txt('PLG_PUBLICATION_RELATED_AVG_RATING'); ?></th>
													<td><span class="avgrating<?php echo $class; ?>"><span><?php echo Lang::txt('OUT_OF_5_STARS', $line->rating); ?></span>&nbsp;</span> (<?php echo $line->times_rated; ?>)</td>
												</tr>
											</tbody>
										</table>
									</div>
									<?php echo \Hubzero\Utility\String::truncate(stripslashes($line->abstract), 300); ?>
								</div>
						<?php } ?>
					</td>
					<td class="type"><?php echo $line->section; ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
<?php } else { ?>
	<p><?php echo Lang::txt('PLG_PUBLICATION_RELATED_NO_RESULTS_FOUND'); ?></p>
<?php } ?>
