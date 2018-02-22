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

$this->css('compare.css');
?>
<header id="content-header">
	<h2><?php echo Lang::txt('COM_PUBLICATIONS') . ': ' . Lang::txt('COM_PUBLICATIONS_COMPARE'); ?></h2>
</header>

<section class="main section">
	<div class="diff-info">
		<div class="grid">
			<div class="col span6">
				<div class="diff-side diff-lft">
					<span class="diff-id"><?php echo '#' . $this->lft->get('publication_id') . ', v' . $this->lft->get('version_label'); ?></span>
					<span class="diff-meta">
						<span class="diff-published"><?php echo Lang::txt('COM_PUBLICATIONS_PUBLISHED'); ?>:
							<?php if ($this->lft->isPublished()) {
								$dt = $this->lft->get('publish_up');
								if (!$dt || $dt == '0000-00-00 00:00:00')
								{
									$dt = $this->lft->get('approved');
									if (!$dt || $dt == '0000-00-00 00:00:00')
									{
										$dt = $this->lft->get('submitted');
										if (!$dt || $dt == '0000-00-00 00:00:00')
										{
											$dt = $this->lft->get('created');
										}
									}
								}
								?>
								<time datetime="<?php echo Date::of($dt)->format('Y-m-d\TH:i:s\Z'); ?>"><?php echo Date::of($dt)->toLocal(); ?></time></span>
							<?php } else { ?>
								--
							<?php } ?>
						</span>
					</span>
				</div>
			</div>
			<div class="col span6 diff-rgt omega">
				<div class="diff-side diff-rgt">
					<span class="diff-id"><?php echo '#' . $this->rgt->get('publication_id') . ', v' . $this->rgt->get('version_label'); ?></span>
					<span class="diff-meta">
						<span class="diff-published"><?php echo Lang::txt('COM_PUBLICATIONS_PUBLISHED'); ?>:
							<?php if ($this->rgt->isPublished()) {
								$dt = $this->rgt->get('publish_up');
								if (!$dt || $dt == '0000-00-00 00:00:00')
								{
									$dt = $this->rgt->get('approved');
									if (!$dt || $dt == '0000-00-00 00:00:00')
									{
										$dt = $this->rgt->get('submitted');
										if (!$dt || $dt == '0000-00-00 00:00:00')
										{
											$dt = $this->rgt->get('created');
										}
									}
								}
								?>
								<time datetime="<?php echo Date::of($dt)->format('Y-m-d\TH:i:s\Z'); ?>"><?php echo Date::of($dt)->toLocal(); ?></time></span>
							<?php } else { ?>
								--
							<?php } ?>
						</span>
					</span>
				</div>
			</div>
		</div>
	</div>
	<div class="diff-results">
		<?php
		foreach ($this->diffs as $key => $result)
		{
			if (is_array($result))
			{
				foreach ($result as $k => $v)
				{
					if ($key == 'metadata')
					{
						foreach ($this->customFields['fields'] as $field)
						{
							if ($field['name'] == $k)
							{
								echo '<h3 class="diff-area" id="diffed-' . $k . '"><span>' . $this->escape($field['label']) . '</span></h3>';
							}
						}
					}
					echo ($v ? $v : '<p class="diff-unchanged">' . Lang::txt('COM_PUBLICATIONS_NO_CHANGES') . '</p>');
				}
			}
			else
			{
				echo '<h3 class="diff-area" id="diffed-' . $key . '"><span>' . Lang::txt('COM_PUBLICATIONS_' . strtoupper($key)) . '</span></h3>';
				echo ($result ? $result : '<p class="diff-unchanged">' . Lang::txt('COM_PUBLICATIONS_NO_CHANGES') . '</p>');
			}
		}

		$lattachments = $this->lft->attachments()->order('element_id', 'asc')->order('ordering', 'asc')->rows();
		$rattachments = $this->rgt->attachments()->order('element_id', 'asc')->order('ordering', 'asc')->rows();

		$attachments = array();

		if ($lattachments->count() && $rattachments->count())
		{
			$l = 0;
			foreach ($lattachments as $lattachment)
			{
				$info = new stdClass;
				$info->lft = $lattachment;
				$info->rgt = null;

				$match = false;
				$key = $l;

				/* This isn't quite working as intended. @TODO: Revisit
				$r = 0;
				foreach ($rattachments as $rattachment)
				{
					if ($lattachment->get('type') == 'file')
					{
						if ($lattachment->get('content_hash') == $rattachment->get('content_hash'))
						{
							$match = true;
						}
					}
					else if ($lattachment->get('type') == 'link')
					{
						if ($lattachment->get('path') == $rattachment->get('path'))
						{
							$match = true;
						}
					}
					else if ($lattachment->get('type') == 'publication')
					{
						if ($lattachment->get('path') == $rattachment->get('path'))
						{
							$match = true;
						}
					}

					if ($match)
					{
						$key = $r;
						$info->rgt = $rattachment;
						break;
					}

					$r++;
				}

				if (!$info->rgt)
				{*/
					$z = 0;
					foreach ($rattachments as $rattachment)
					{
						if ($z == $l)
						{
							$info->rgt = $rattachment;
							break;
						}
						$z++;
					}
				//}

				$attachments[$key] = $info;

				$l++;
			}

			$r = 0;
			foreach ($rattachments as $rattachment)
			{
				if (isset($attachments[$r]))
				{
					$r++;
					continue;
				}

				$info = new stdClass;
				$info->lft = null;
				$info->rgt = $rattachment;

				$attachments[$r] = $info;

				$r++;
			}
			?>
			<h3 class="diff-area" id="diffed-files"><span><?php echo Lang::txt('COM_PUBLICATIONS_ATTACHMENTS'); ?></span></h3>
			<table class="differences differences-sidebyside">
				<tbody>
					<?php
					foreach ($attachments as $i => $att)
					{
						$cls = 'change-equal';

						if (!$att->lft || !$att->rgt || $att->lft->get('type') != $att->rgt->get('type'))
						{
							$cls = 'change-replace';
						}
						else
						{
							if ($att->lft->get('type') == 'file')
							{
								if ($att->lft->get('content_hash') != $att->rgt->get('content_hash'))
								{
									$cls = 'change-replace';
								}
							}
							else if ($att->lft->get('type') == 'link')
							{
								if ($att->lft->get('path') != $att->rgt->get('path'))
								{
									$cls = 'change-replace';
								}
							}
							else if ($att->rgt->get('type') == 'publication')
							{
								if ($att->lft->get('path') != $att->rgt->get('path'))
								{
									$cls = 'change-replace';
								}
							}
						}
						?>
						<tr class="<?php echo $cls; ?>">
							<th><?php echo ($att->lft ? ($i + 1) : ''); ?></th>
							<td class="left">
								<?php
								if ($att->lft)
								{
									echo $att->lft->get('type') . ' &mdash; ' . $this->escape($att->lft->get('title', $att->lft->get('path')));
								}
								?>
							</td>
							<th><?php echo ($att->rgt ? ($i + 1) : ''); ?></th>
							<td class="right">
								<?php
								if ($att->rgt)
								{
									echo $att->rgt->get('type') . ' &mdash; ' . $this->escape($att->rgt->get('title', $att->rgt->get('path')));
								}
								?>
							</td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
			<?php
		}
		?>
	</div>
</section>
