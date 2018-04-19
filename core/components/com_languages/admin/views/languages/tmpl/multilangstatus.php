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

// No direct access.
defined('_HZEXEC_') or die();

$notice_homes     = $this->homes == 2 || $this->homes == 1 && ($this->language_filter || $this->switchers != 0);
$notice_disabled  = !$this->language_filter	&& ($this->homes > 1 || $this->switchers != 0);
$notice_switchers = !$this->switchers && ($this->homes > 1 || $this->language_filter);
?>
<div class="mod-multilangstatus">
	<?php if (!$this->language_filter && $this->switchers == 0) : ?>
		<?php if ($this->homes == 1) : ?>
			<p><?php echo Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_NONE'); ?></p>
		<?php else: ?>
			<p><?php echo Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_USELESS_HOMES'); ?></p>
		<?php endif; ?>
	<?php else: ?>
	<table class="adminlist">
		<tbody>
		<?php if ($notice_homes) : ?>
			<tr>
				<td>
					<?php echo Html::asset('image', 'menu/icon-16-alert.png', Lang::txt('WARNING'), null, true); ?>
				</td>
				<td>
					<?php echo Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_HOMES_MISSING'); ?>
				</td>
			</tr>
		<?php endif; ?>
		<?php if ($notice_disabled) : ?>
			<tr>
				<td>
					<?php echo Html::asset('image', 'menu/icon-16-alert.png', Lang::txt('WARNING'), null, true); ?>
				</td>
				<td>
					<?php echo Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_LANGUAGEFILTER_DISABLED'); ?>
				</td>
			</tr>
		<?php endif; ?>
		<?php if ($notice_switchers) : ?>
			<tr>
				<td>
					<?php echo Html::asset('image', 'menu/icon-16-alert.png', Lang::txt('WARNING'), null, true); ?>
				</td>
				<td>
					<?php echo Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_LANGSWITCHER_UNPUBLISHED'); ?>
				</td>
			</tr>
		<?php endif; ?>
		<?php foreach ($this->contentlangs as $contentlang) : ?>
			<?php if (array_key_exists($contentlang->lang_code, $this->homepages) && (!array_key_exists($contentlang->lang_code, $this->site_langs) || !$contentlang->published)) : ?>
				<tr>
					<td>
						<?php echo Html::asset('image', 'menu/icon-16-alert.png', Lang::txt('WARNING'), null, true); ?>
					</td>
					<td>
						<?php echo Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_ERROR_CONTENT_LANGUAGE', $contentlang->lang_code); ?>
					</td>
				</tr>
			<?php endif; ?>
		<?php endforeach; ?>
		</tbody>
	</table>
	<table class="adminlist" style="border-top: 1px solid #CCCCCC;">
		<thead>
			<tr>
				<th>
					<?php echo Lang::txt('JDETAILS'); ?>
				</th>
				<th>
					<?php echo Lang::txt('JSTATUS'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_LANGUAGEFILTER'); ?>
				</th>
				<td class="center">
					<?php if ($this->language_filter) : ?>
						<?php echo Lang::txt('JENABLED'); ?>
					<?php else : ?>
						<?php echo Lang::txt('JDISABLED'); ?>
					<?php endif; ?>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_LANGSWITCHER_PUBLISHED'); ?>
				</th>
				<td class="center">
					<?php if ($this->switchers != 0) : ?>
						<?php echo $this->switchers; ?>
					<?php else : ?>
						<?php echo Lang::txt('JNONE'); ?>
					<?php endif; ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php if ($this->homes > 1) : ?>
						<?php echo Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_HOMES_PUBLISHED_INCLUDING_ALL'); ?>
					<?php else : ?>
						<?php echo Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_HOMES_PUBLISHED'); ?>
					<?php endif; ?>
				</th>
				<td class="center">
					<?php if ($this->homes > 1) : ?>
						<?php echo $this->homes; ?>
					<?php else : ?>
						<?php echo Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_HOMES_PUBLISHED_ALL'); ?>
					<?php endif; ?>
				</td>
			</tr>
		</tbody>
	</table>
	<table class="adminlist" style="border-top: 1px solid #CCCCCC;">
		<thead>
			<tr>
				<th>
					<?php echo Lang::txt('JGRID_HEADING_LANGUAGE'); ?>
				</th>
				<th>
					<?php echo Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_SITE_LANG_PUBLISHED'); ?>
				</th>
				<th>
					<?php echo Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_CONTENT_LANGUAGE_PUBLISHED'); ?>
				</th>
				<th>
					<?php echo Lang::txt('COM_LANGUAGES_MULTILANGSTATUS_HOMES_PUBLISHED'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->statuses as $status) : ?>
				<?php if ($status->element) : ?>
					<tr>
						<td>
							<?php echo $status->element; ?>
						</td>
				<?php endif; ?>
				<?php if ($status->element) : // Published Site languages ?>
						<td class="center">
							<?php echo Html::asset('image', 'admin/tick.png', Lang::txt('JON'), null, true); ?>
						</td>
				<?php else : ?>
						<td class="center">
							<?php echo Lang::txt('JNO'); ?>
						</td>
				<?php endif; ?>
				<?php if ($status->lang_code && $status->published) : // Published Content languages ?>
						<td class="center">
							<?php echo Html::asset('image', 'admin/tick.png', Lang::txt('JON'), null, true); ?>
						</td>
				<?php else : ?>
						<td class="center">
							<?php echo Html::asset('image', 'menu/icon-16-notice.png', Lang::txt('JON'), null, true); ?>
						</td>
				<?php endif; ?>
				<?php if ($status->home_language) : // Published Home pages ?>
						<td class="center">
							<?php echo Html::asset('image', 'admin/tick.png', Lang::txt('JON'), null, true); ?>
						</td>
				<?php else : ?>
						<td class="center">
							<?php echo Html::asset('image', 'menu/icon-16-deny.png', Lang::txt('WARNING'), null, true); ?>
						</td>
				<?php endif; ?>
				</tr>
			<?php endforeach; ?>
			<?php foreach ($this->contentlangs as $contentlang) : ?>
				<?php if (!array_key_exists($contentlang->lang_code, $this->site_langs)) : ?>
					<tr>
						<td>
							<?php echo $contentlang->lang_code; ?>
						</td>
						<td class="center">
							<?php echo Html::asset('image', 'menu/icon-16-notice.png', Lang::txt('NOTICE'), null, true); ?>
						</td>
						<td class="center">
							<?php if ($contentlang->published) : ?>
								<?php echo Html::asset('image', 'admin/tick.png', Lang::txt('JON'), null, true); ?>
							<?php elseif (!$contentlang->published && array_key_exists($contentlang->lang_code, $this->homepages)) : ?>
								<?php echo Html::asset('image', 'menu/icon-16-deny.png', Lang::txt('WARNING'), null, true); ?>
							<?php elseif (!$contentlang->published) : ?>
								<?php echo Html::asset('image', 'menu/icon-16-notice.png', Lang::txt('NOTICE'), null, true); ?>
							<?php endif; ?>
						</td>
						<td class="center">
							<?php if (!array_key_exists($contentlang->lang_code, $this->homepages)) : ?>
								<?php echo Html::asset('image', 'menu/icon-16-notice.png', Lang::txt('NOTICE'), null, true); ?>
							<?php else : ?>
								<?php echo Html::asset('image', 'admin/tick.png', Lang::txt('JON'), null, true); ?>
							<?php endif; ?>
						</td>
				<?php endif; ?>
			<?php endforeach; ?>
			</tr>
		</tbody>
	</table>
	<?php endif; ?>
</div>
