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

$sparams = new \Hubzero\Config\Registry($this->course->offering()->section()->get('params'));

$units = $this->course->offering()->units();
$unit  = $this->course->offering()->unit($this->unit);

if (!$unit)
{
	App::abort(404, Lang::txt('uh-oh'));
}

$aggroups = $unit->assetgroups();

$lecture = $unit->assetgroup($this->group);
if (!$lecture)
{
	App::abort(404, Lang::txt('uh-oh'));
}

$base    = $this->course->offering()->link() . '&active=outline';
$altBase = $this->course->offering()->link();
$current = $unit->assetgroups()->key();

if (!$this->course->offering()->access('view') && (!$sparams->get('preview', 0) || ($sparams->get('preview', 0) == 2 && $unit->get('ordering') > 1))) { ?>
	<p class="info"><?php echo Lang::txt('Access to this content is restricted to students only. You must be enrolled to view the content.'); ?></p>
<?php } else { ?>

	<?php if ($this->course->offering()->access('manage')) { ?>
		<?php if (!$lecture->isPublished()) { ?>
		<div class="asset-status unpublished">
			<span><?php echo Lang::txt('This lecture is <strong>unpublished</strong>.'); ?></span>
		</div>
		<?php } ?>
		<?php if ($lecture->isDraft()) { ?>
		<div class="asset-status draft">
			<span><?php echo Lang::txt('This lecture is in <strong>draft</strong> mode.'); ?></span>
		</div>
		<?php } ?>
		<?php if ($lecture->isPublished() && !$lecture->isAvailable()) { ?>
		<div class="asset-status pending">
			<span><?php echo Lang::txt('This lecture is <strong>scheduled</strong> to be available at %s.', $lecture->get('publish_up')); ?></span>
		</div>
		<?php } ?>
	<?php } ?>

	<div class="video container">
		<div class="video-wrap">
			<div class="video-player-wrap">
			<?php
			$used = 0;
			if ($lecture->assets()->total())
			{
				// Render video
				foreach ($lecture->assets() as $a)
				{
					if ($a->get('type') == 'video' && ($a->isPublished() || (!$a->isPublished() && $this->course->offering()->access('manage'))))
					{
						// Prefer published assets
						if ($used)
						{
							if (!$used_asset->isPublished() && $a->isPublished())
							{
								$used       = $a->get('id');
								$used_title = $a->get('title');
								$used_asset = $a;
							}
						}
						else
						{
							$used       = $a->get('id');
							$used_title = $a->get('title');
							$used_asset = $a;
						}
					}
				}

				if ($used)
				{
					// Check prerequisites
					$member = $this->course->offering()->section()->member(User::get('id'));
					if (is_null($member->get('section_id')))
					{
						$member->set('section_id', $this->course->offering()->section()->get('id'));
					}
					$prerequisites = $member->prerequisites($this->course->offering()->gradebook());

					if (!$this->course->offering()->access('manage') && !$prerequisites->hasMet('asset', $used_asset->get('id')))
					{
						$prereqs      = $prerequisites->get('asset', $used_asset->get('id'));
						$requirements = array();
						foreach ($prereqs as $pre)
						{
							$reqAsset = new \Components\Courses\Models\Asset($pre['scope_id']);
							$requirements[] = $reqAsset->get('title');
						}

						$requirements = implode(', ', $requirements);

						// Redirect back to the course outline
						App::redirect(
							Route::url($base),
							Lang::txt('COM_COURSES_ERROR_ASSET_HAS_PREREQ', $requirements),
							'warning'
						);
						return;
					}

					echo $used_asset->render($this->course);

					if ($this->course->offering()->access('manage'))
					{
						?>
						<?php if (!$used_asset->isPublished()) { ?>
						<div class="asset-status unpublished">
							<span><?php echo Lang::txt('This asset is <strong>unpublished</strong>.'); ?></span>
						</div>
						<?php } ?>
						<?php if ($used_asset->isDraft()) { ?>
						<div class="asset-status draft">
							<span><?php echo Lang::txt('This asset is in <strong>draft</strong> mode.'); ?></span>
						</div>
						<?php } ?>
						<?php if ($used_asset->isPublished() && !$used_asset->isAvailable()) { ?>
						<div class="asset-status pending">
							<span><?php echo Lang::txt('This asset is <strong>scheduled</strong> to be available at %s.', $used_asset->get('publish_up')); ?></span>
						</div>
						<?php } ?>
						<?php
					}
				}
			}
			?>
			</div><!-- / .video-player-wrap -->
			<div class="video-meta">
				<h3>
					<?php if (trim($lecture->get('title')) !== '--') : ?>
						<?php echo $lecture->get('title'); ?>
					<?php else : ?>
						<?php echo $used_title; ?>
					<?php endif; ?>
				</h3>

				<ul class="lecture-assets">
					<?php
					$exams = array();
					// Are there any assets?
					if ($lecture->assets()->total())
					{
						// Loop through the assets
						foreach ($lecture->assets() as $a)
						{
							// Was this asset already used elsewhere on the page?
							// This should generally only happen with the video asset
							if ($a->get('id') == $used || $a->isDeleted())
							{
								continue;
							}
							if (!$this->course->offering()->access('manage'))
							{
								if (!$a->isPublished())
								{
									continue;
								}
							}
							$href = Route::url($altBase . '&asset=' . $a->get('id'));
							/*if ($a->get('type') == 'video')
							{
								$href = Route::url($base . '&unit=' . $unit->get('alias') . '&b=' . $lecture->get('alias'));
							}*/
							$cls = 'download';
							if ($a->get('type') == 'exam')
							{
								$cls = 'edit';
								$exams[] = '<a class="' . $cls . ' btn" href="' . $href . '" target="_blank">' . $this->escape(stripslashes($a->get('title'))) . '</a>';
							}
							else
							{
								if ($a->get('type') == 'link')
								{
									$cls = 'link';
								}
								echo '<li><a class="' . $cls . '" href="' . $href . '" target="_blank">' . $this->escape(stripslashes($a->get('title'))) . '</a></li>';
							}
						}
					}
					else
					{
						echo '<li><small>' . Lang::txt('COURSES_NO_ASSETS_FOR_GROUPING') . '</small></li>';
					}
					?>
				</ul>
			</div>

			<p class="lecture-nav">
			<?php
				$lecture->key($current);

				$found = false;

				if (!$lecture->isFirst())
				{
					$found = false;
					// Find the previous lecture
					$ky = $lecture->key();
					for ($ky; $ky >= 0; $ky--)
					{
						$lecture->key($ky);
						$prev = $lecture->sibling('prev');
						if ($prev && $prev->isPublished() && $prev->assets()->total() > 0)
						{
							$found = true;
							?>
							<a class="icon-prev prev btn" href="<?php echo Route::url($base . '&unit=' . $unit->get('alias') . '&b=' . $lecture->sibling('prev')->get('alias')); ?>">
								<?php echo Lang::txt('Prev'); ?>
							</a>
							<?php
							break;
						}
					}
				}

				if (!$found) { ?>
				<span class="icon-prev disabled prev btn">
					<?php echo Lang::txt('Prev'); ?>
				</span>
				<?php }

				$gAlias = '';
				$key = $aggroups->key();
				// If NOT the last assetgroup
				if (!$unit->assetgroups()->isLast())
				{
					foreach ($unit->assetgroups() as $k => $assetgroup)
					{
						if ($k <= $current)
						{
							continue;
						}

						if ($assetgroup->isPublished())
						{
							$gAlias = $assetgroup->get('alias');
							break;
						}
					}
				}

				if (!$gAlias) { ?>
				<span class="icon-next disabled next opposite btn">
					<?php echo Lang::txt('Next'); ?>
				</span>
				<?php } else { ?>
				<a class="icon-next next opposite btn" href="<?php echo Route::url($base . '&unit=' . $unit->get('alias') . '&b=' . $gAlias); ?>">
					<?php echo Lang::txt('Next'); ?>
				</a>
				<?php }

				if (count($exams) > 0)
				{
					echo implode("\n", $exams);
				}
			?>
			</p>

		<?php if ($lecture->get('description')) { ?>
			<p class="lecture-description">
				<?php echo $this->escape(stripslashes($lecture->get('description'))); ?>
			</p>
		<?php } ?>
		</div><!-- / .video-wrap -->
	</div><!-- / .video container -->

	<?php
		// Trigger event
		$results = Event::trigger('courses.onCourseAfterLecture', array(
			$this->course,
			$unit,
			$lecture
		));
		// Output results
		echo implode("\n", $results);
	?>
<?php }
