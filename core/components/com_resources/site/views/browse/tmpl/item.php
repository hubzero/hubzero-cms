<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$params = $this->line->params;

switch ($this->line->access)
{
	case 1:
		$cls = 'registered';
		break;
	case 2:
		$cls = 'special';
		break;
	case 3:
		$cls = 'protected';
		break;
	case 4:
		$cls = 'private';
		break;
	case 0:
	default:
		$cls = 'public';
		break;
}

if ($params->get('supportedtag') && isset($this->supported))
{
	if (in_array($this->line->id, $this->supported))
	{
		$cls .= ' supported';
	}
}
?>

<li class="<?php echo $cls; ?>">
	<p class="title">
		<a href="<?php echo Route::url($this->line->link()); ?>">
			<?php echo $this->escape(stripslashes($this->line->title)); ?>
		</a>
	</p>

	<?php if ($params->get('show_ranking')) { ?>
		<div class="metadata">
			<dl class="rankinfo">
				<dt class="ranking">
					<?php
					$this->line->set('ranking', round($this->line->get('ranking'), 1));

					$r = (10 * $this->line->get('ranking'));
					?>
					<span class="rank">
						<span class="rank-<?php echo $r; ?>" style="width: <?php echo $r; ?>%;"><?php echo Lang::txt('COM_RESOURCES_THIS_HAS'); ?></span>
					</span>
					<?php echo number_format($this->line->get('ranking'), 1) . ' ' . Lang::txt('COM_RESOURCES_RANKING'); ?>
				</dt>
				<dd>
					<p><?php echo Lang::txt('COM_RESOURCES_RANKING_EXPLANATION'); ?></p>
					<div>
						<?php
						$database = App::get('db');

						// Get statistics info
						if ($this->line->isTool())
						{
							$stats = new \Components\Resources\Helpers\Usage\Tools($database, $this->line->id, $this->line->get('type'), $this->line->rating);
						}
						else
						{
							$stats = new \Components\Resources\Helpers\Usage\Andmore($database, $this->line->id, $this->line->get('type'), $this->line->rating);
						}
						echo $stats->display();
						?>
					</div>
				</dd>
			</dl>
		</div>
	<?php } elseif ($params->get('show_rating')) { ?>
		<div class="metadata">
			<p class="rating">
				<span title="<?php echo Lang::txt('COM_RESOURCES_OUT_OF_5_STARS', $this->line->get('rating')); ?>" class="avgrating<?php echo $this->line->rating; ?>">
					<span><?php echo Lang::txt('COM_RESOURCES_OUT_OF_5_STARS', $this->line->get('rating')); ?></span>&nbsp;
				</span>
			</p>
		</div>
	<?php } ?>

	<p class="details">
		<?php
		$info = array();
		if ($thedate = $this->line->date)
		{
			$info[] = $thedate;
		}

		if ($params->get('show_type'))
		{
			$info[] = stripslashes($this->line->type->get('type'));
		}

		if ($this->line->authors->count() && $params->get('show_authors'))
		{
			$authors = $this->line->authorsList();

			if (trim($authors))
			{
				$info[] = Lang::txt('COM_RESOURCES_CONTRIBUTORS') . ': ' . $authors;
			}
		}

		echo implode(' <span>|</span> ', $info);
		?>
	</p>

	<p class="result-description">
		<?php
		$content = '';
		if ($this->line->get('introtext'))
		{
			$content = $this->line->get('introtext');
		}
		else if ($this->line->get('fulltxt'))
		{
			$content = $this->line->get('fulltxt');
			$content = preg_replace("#<nb:(.*?)>(.*?)</nb:(.*?)>#s", '', $content);
			$content = trim($content);
		}

		echo \Hubzero\Utility\Str::truncate(strip_tags(\Hubzero\Utility\Sanitize::stripAll(stripslashes($content))), 300);
		?>
	</p>
</li>