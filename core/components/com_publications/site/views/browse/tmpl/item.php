<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

$cls = array('publication');
switch ($this->line->get('master_access'))
{
	case 1:
		$cls[] = 'registered';
		break;
	case 2:
		$cls[] = 'protected';
		break;
	case 3:
		$cls[] = 'private';
		break;
	case 0:
	default:
		$cls[] = 'public';
		break;
}

$info = array();
if ($this->thedate)
{
	$info[] = $this->thedate;
}
if (($this->line->category && !intval($this->filters['category'])))
{
	$info[] = $this->line->cat_name;
}
if ($this->authors && $this->params->get('show_authors'))
{
	$info[] = Lang::txt('COM_PUBLICATIONS_CONTRIBUTORS') . ': ' . \Components\Publications\Helpers\Html::showContributors( $this->authors, false, true );
}
if ($this->line->doi)
{
	$info[] = 'doi:' . $this->line->doi;
}

?>
<li class="<?php echo implode(' ', $cls); ?>">
	<div class="pub-thumb"><img width="40" height="40" src="<?php echo Route::url($this->line->link('thumb')); ?>" alt="" /></div>
	<div class="pub-details">
		<p class="title"><a href="<?php echo Route::url($this->line->link()); ?>"><?php echo $this->escape($this->line->title); ?></a></p>

		<?php
		if ($this->params->get('show_ranking') && $this->config->get('show_ranking'))
		{
			$ranking = round($this->line->get('master_ranking'), 1);

			$r = (10 * $ranking);
			if (intval($r) < 10)
			{
				$r = '0' . $r;
			}
			?>
			<div class="metadata">
				<dl class="rankinfo">
					<dt class="ranking">
						<span class="rank">
							<span class="rank-<?php echo $r; ?>" style="width: <?php echo $r; ?>%;"><?php echo Lang::txt('COM_PUBLICATIONS_THIS_HAS'); ?></span>
						</span><?php echo number_format($ranking, 1) . ' ' . Lang::txt('COM_PUBLICATIONS_RANKING'); ?>
					</dt>
					<dd>
						<p><?php echo Lang::txt('COM_PUBLICATIONS_RANKING_EXPLANATION'); ?></p>
						<div></div>
					</dd>
				</dl>
			</div>
			<?php
		}
		elseif ($this->params->get('show_rating') && $this->config->get('show_rating'))
		{
			switch ($this->line->get('master_rating'))
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

			if ($this->line->get('master_rating') > 5)
			{
				$class = ' five-stars';
			}
			?>
			<div class="metadata">
				<p class="rating"><span title="<?php echo Lang::txt('COM_PUBLICATIONS_OUT_OF_5_STARS', $this->line->get('master_rating')); ?>" class="avgrating<?php echo $class; ?>"><span><?php echo Lang::txt('COM_PUBLICATIONS_OUT_OF_5_STARS', $this->line->get('master_rating')); ?></span>&nbsp;</span></p>
			</div>
			<?php
		}
		?>

		<p class="details"><?php echo implode(' <span class="separator">|</span> ', $info); ?></p>
		<?php
		$content = '';
		if ($this->line->get('abstract'))
		{
			$content = $this->line->get('abstract');
		}
		else if ($this->line->get('description'))
		{
			$content = $this->line->get('description');
		}
		echo \Hubzero\Utility\String::truncate(stripslashes($content), 300);
		?>
	</div>
</li>
