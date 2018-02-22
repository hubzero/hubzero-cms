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

// Launcher layout metadata (big icons)
if (!empty($this->launcherLayout) && $this->publication->main == 1 && $this->publication->state == 1)
{
	echo '<ul class="metaitems">';
	foreach ($this->sections as $section)
	{
		if (isset($section['name']) && isset($section['count']))
		{
			echo '<li class="meta-' . $section['name'] . '">';

			if ($section['name'] != 'usage')
			{
				echo '<a href="' . Route::url($this->publication->link() . '&active=' . $section['name'] ) . '" title="' . Lang::txt('COM_PUBLICATIONS_META_TITLE_' . strtoupper($section['name'])) . '">';
			}

			echo '<span class="icon"></span><span class="label">' . $section['count'] . '</span>';
			if ($section['name'] != 'usage')
			{
				echo '</a>';
			}
			echo '</li>';
		}
	}
	echo '</ul>';
	return;
}

// Non-published version
if (!$this->publication->isPublished())
{
	$text = $this->publication->isDev()
		? Lang::txt('COM_PUBLICATIONS_METADATA_DEV')
		: Lang::txt('COM_PUBLICATIONS_METADATA_UNAVAILABLE');
	echo '<div class="metaplaceholder"><p>' . $text . '</p></div>' . "\n";
	return;
}

$database = \App::get('db');

$data = '';
foreach ($this->sections as $section)
{
	$data .= (!empty($section['metadata'])) ? $section['metadata'] : '';
}

if ($this->params->get('show_ranking') || $this->params->get('show_audience') || $this->params->get('supportedtag') || $data)
{
?>
<div class="metadata">
	<?php

	if ($this->params->get('show_ranking', 0))
	{
		$rank = round($this->publication->ranking, 1);
		$r = (10*$rank);
		?>
		<dl class="rankinfo">
			<dt class="ranking">
				<span class="rank"><span class="rank-<?php echo $r; ?>" style="width: <?php echo $r; ?>%;">This publication has a</span></span> <?php echo number_format($rank, 1); ?> Ranking
			</dt>
			<dd>
				<p>
					Ranking is calculated from a formula comprised of <a href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->publication->id . '&active=reviews'); ?>">user reviews</a> and usage statistics. <a href="about/ranking/">Learn more &rsaquo;</a>
				</p>
				<div></div>
			</dd>
		</dl>
		<?php
	}

	// Supported publication?
	$rt = new \Components\Publications\Helpers\Tags( $database );
	$supported = $rt->checkTagUsage( $this->config->get('supportedtag'), $this->publication->id );

	if ($supported)
	{
		$tag = \Components\Tags\Models\Tag::oneByTag($this->config->get('supportedtag'));

		$sl = $this->config->get('supportedlink');
		if ($sl)
		{
			$link = $sl;
		}
		else
		{
			$link = Route::url('index.php?option=com_tags&tag=' . $tag->get('tag'));
		}

		echo  '<p class="supported"><a href="' . $link . '">' . $tag->get('raw_tag') . '</a></p>';
	}

	// Show audience
	if ($this->params->get('show_audience'))
	{
		$ra 		= new \Components\Publications\Tables\Audience( $database );
		$audience 	= $ra->getAudience(
			$this->publication->id,
			$this->publication->version_id,
			$getlabels = 1,
			$numlevels = 4
		);

		$this->view('_audience', 'view')
					->set('audience', $audience)
					->set('showtips', true)
					->set('numlevels', 4)
					->set('audiencelink', $this->params->get('audiencelink'))
					->display();
	}

	// Archive version?
	if ($this->lastPubRelease && $this->lastPubRelease->id != $this->publication->version_id)
	{ ?>
		<p>
			<?php echo Lang::txt('COM_PUBLICATIONS_METADATA_ARCHIVE'); ?>
			[<a href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' .
					$this->publication->id . '&v=' . $this->lastPubRelease->version_number); ?>"><?php echo $this->lastPubRelease->version_label; ?></a>]
			<?php echo Lang::txt('COM_PUBLICATIONS_METADATA_ARCHIVE_INFO'); ?>
		</p>
	<?php }

	// Show section data
	echo $data; ?>
	<div class="clear"></div>
</div><!-- / .metadata -->
<?php } ?>