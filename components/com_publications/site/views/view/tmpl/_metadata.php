<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   GNU General Public License, version 2 (GPLv2)
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

// Launcher layout metadata (big icons)
if (!empty($this->launcherLayout) && $this->publication->main == 1 && $this->publication->state == 1)
{
	echo '<ul class="metaitems">';
	foreach ($this->sections as $section)
	{
		if (isset($section['name']) && isset($section['count']))
		{
			echo '<li class="meta-' . $section['name']
			. '">';

			if ($section['name'] != 'usage')
			{
				echo '<a href="' . Route::url('index.php?option=' . $this->option . '&id=' . $this->publication->id . '&active=' . $section['name'] ) . '" title="' . Lang::txt('COM_PUBLICATIONS_META_TITLE_' . strtoupper($section['name'])) . '">';
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
if ($this->publication->state != 1)
{
	$text = $this->version == 'dev'
		? Lang::txt('COM_PUBLICATIONS_METADATA_DEV')
		: Lang::txt('COM_PUBLICATIONS_METADATA_UNAVAILABLE');
	echo '<div class="metaplaceholder"><p>' . $text . '</p></div>' . "\n";
	return;
}

$database = \JFactory::getDbo();

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
		$tag = new \Components\Tags\Tables\Tag( $database );
		$tag->loadTag($this->config->get('supportedtag'));

		$sl = $this->config->get('supportedlink');
		if ($sl)
		{
			$link = $sl;
		}
		else
		{
			$link = Route::url('index.php?option=com_tags&tag=' . $tag->tag);
		}

		echo  '<p class="supported"><a href="' . $link . '">' . $tag->raw_tag . '</a></p>';
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