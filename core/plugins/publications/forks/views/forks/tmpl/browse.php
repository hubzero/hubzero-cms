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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();
?>
	<?php /*if ($v = $this->publication->version->get('forked_from')) { ?>
			<?php
			$db = App::get('db');
			$db->setQuery("SELECT publication_id FROM `#__publication_versions` WHERE `id`=" . $db->quote($v));

			$p = $db->loadResult();

			$publication = new Components\Publications\Models\Publication($p, 'default', $v);
			?>
			<h3 class="section-header">
				<?php echo Lang::txt('PLG_PUBLICATIONS_FORKS_FROM'); ?>
			</h3>
			<div class="publication-fork icon-fork fork-source">
				<div class="publication-datetime">
					<span class="publication-date"><time datetime="<?php echo $publication->version->get('created'); ?>"><?php echo Date::of($publication->version->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time></span>
					<span class="publication-time"><time datetime="<?php echo $publication->version->get('created'); ?>"><?php echo Date::of($publication->version->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1')); ?></time></span>
				</div>
				<div class="publication-details">
					<div class="publication-title">
						<?php if ($publication->version->get('state') == 1 &&
							($publication->version->get('published_up') == '0000-00-00 00:00:00' || ($publication->version->get('published_up') != '0000-00-00 00:00:00' && $publication->version->get('published_up') <= Date::toSql())) &&
							($publication->version->get('published_down') == '0000-00-00 00:00:00' || ($publication->version->get('published_down') != '0000-00-00 00:00:00' && $publication->version->get('published_down') > Date::toSql()))) { ?>
							<a href="<?php echo Route::url('index.php?option=com_publications&id=' . $publication->get('id') . '&v=' . $publication->version->get('version_number')); ?>">
								<?php echo $this->escape($publication->version->get('title')); ?>
							</a>
						<?php } else { ?>
							<?php echo $this->escape($publication->version->get('title')); ?>
							<span class="publication-status"><?php echo Lang::txt('(unpublished)'); ?></span>
						<?php } ?>
						<span class="publication-version"><abbr title="<?php echo Lang::txt('Version'); ?>">v</abbr> <?php echo $this->escape($publication->version->get('version_label')); ?></span>
					</div>
					<div class="publication-meta">
						<?php
						$creator = User::getInstance($publication->version->get('created_by'));
						$name = $this->escape($creator->get('name', Lang::txt('unknown')));
						if (in_array($creator->get('access'), User::getAuthorisedViewLevels()))
						{
							$name = '<a href="' . Route::url('index.php?option=com_members&id=' . $creator->get('id')) . '">' . $name . '</a>';
						}
						?>
						<?php echo Lang::txt('PLG_PUBLICATIONS_FORKS_FORKED_BY', $name); ?>
					</div>
				</div>
			</div>
	<?php }*/ ?>

	<h3 class="section-header">
		<?php echo Lang::txt('PLG_PUBLICATIONS_FORKS'); ?>
	</h3>
	<div class="publication-forks">
		<?php if (count($this->forks)) { ?>
			<?php foreach ($this->forks as $publication) { ?>
				<?php
				$isPublished = $publication->version->get('state') == 1
						&& ($publication->version->get('published_up') == '0000-00-00 00:00:00' || ($publication->version->get('published_up') != '0000-00-00 00:00:00' && $publication->version->get('published_up') <= Date::toSql()))
						&& ($publication->version->get('published_down') == '0000-00-00 00:00:00' || ($publication->version->get('published_down') != '0000-00-00 00:00:00' && $publication->version->get('published_down') > Date::toSql()));
				?>
				<div class="publication-fork icon-fork <?php echo ($isPublished ? 'published' : 'unpublished'); ?>">
					<div class="publication-datetime">
						<span class="publication-date"><time datetime="<?php echo $publication->version->get('created'); ?>"><?php echo Date::of($publication->version->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time></span>
						<span class="publication-time"><time datetime="<?php echo $publication->version->get('created'); ?>"><?php echo Date::of($publication->version->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1')); ?></time></span>
					</div>
					<div class="publication-details">
						<div class="publication-title">
							<?php if ($isPublished) { ?>
								<a href="<?php echo Route::url('index.php?option=com_publications&id=' . $publication->get('id') . '&v=' . $publication->version->get('version_number')); ?>">
									<?php echo $this->escape($publication->version->get('title')); ?>
								</a>
							<?php } else { ?>
								<?php echo $this->escape($publication->version->get('title')); ?>
								<span class="publication-status"><?php echo Lang::txt('COM_PUBLICATIONS_STATUS_UNPUBLISHED'); ?></span>
							<?php } ?>
							<span class="publication-version"><abbr title="<?php echo Lang::txt('COM_PUBLICATIONS_VERSION'); ?>">v</abbr> <?php echo $this->escape($publication->version->get('version_label')); ?></span>
						</div>
						<div class="publication-meta">
							<?php
							$creator = User::getInstance($publication->version->get('created_by'));
							$name = $this->escape($creator->get('name', Lang::txt('unknown')));
							if (in_array($creator->get('access'), User::getAuthorisedViewLevels()))
							{
								$name = '<a href="' . Route::url('index.php?option=com_members&id=' . $creator->get('id')) . '">' . $name . '</a>';
							}
							?>
							<?php echo Lang::txt('PLG_PUBLICATIONS_FORKS_FORKED_BY', $name); ?>
						</div>
					</div>
					<?php if ($isPublished) { ?>
						<a class="btn" href="<?php echo Route::url('index.php?option=com_publications&task=compare&left=' . $this->publication->version->get('id') . '&right=' . $publication->version->get('id')); ?>"><?php echo Lang::txt('PLG_PUBLICATIONS_FORKS_DIFF'); ?></a>
					<?php } ?>
				</div>
			<?php } ?>
		<?php } else { ?>
			<div class="results-none">
				<p><?php echo Lang::txt('PLG_PUBLICATIONS_FORKS_NONE'); ?></p>
				<p><?php echo Lang::txt('PLG_PUBLICATIONS_FORKS_EXPLANATION'); ?></p>
			</div>
		<?php } ?>
	</div>
