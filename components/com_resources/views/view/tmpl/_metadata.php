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

$database = JFactory::getDBO();
?>
<div class="metadata">
<?php
if ($this->model->params->get('show_ranking', 0)) 
{
	$lastCitation = end($this->model->citations());
	if ($this->model->isTool())
	{
		$stats = new ToolStats($database, $this->model->resource->id, $this->model->resource->type, $this->model->resource->rating, count($this->model->citations()), $lastCitation->created);
	}
	else
	{
		$stats = new AndmoreStats($database, $this->model->resource->id, $this->model->resource->type, $this->model->resource->rating, count($this->model->citations()), $lastCitation->created);
	}

	$rank = round($this->model->resource->ranking, 1);

	$r = (10*$rank);
	if (intval($r) < 10) 
	{
		$r = '0' . $r;
	}
?>
	<dl class="rankinfo">
		<dt class="ranking"><span class="rank-<?php echo $r; ?>">This resource has a</span> <?php echo number_format($rank, 1); ?> Ranking</dt>
		<dd>
			<p>
				Ranking is calculated from a formula comprised of <a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->model->resource->id . '&active=reviews'); ?>">user reviews</a> and usage statistics. <a href="about/ranking/">Learn more &rsaquo;</a>
			</p>
			<div>
				<?php echo $stats->display(); ?>
			</div>
		</dd>
	</dl>
<?php
}

if ($this->model->params->get('show_audience')) 
{
	include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . $this->option . DS . 'tables' . DS . 'audience.php');
	include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . $this->option . DS . 'tables' . DS . 'audience.level.php');
	$ra = new ResourceAudience($database);
	$audience = $ra->getAudience($this->model->resource->id, $versionid = 0 , $getlabels = 1, $numlevels = 4);
	echo ResourcesHtml:: showSkillLevel($audience, $showtips = 1, $numlevels = 4, $this->model->params->get('audiencelink'));
}

if ($this->model->params->get('supportedtag'))
{
	$rt = new ResourcesTags($database);
	if ($rt->checkTagUsage($this->model->params->get('supportedtag'), $this->model->resource->id)) 
	{
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'helpers' . DS . 'handler.php');

		$tag = new TagsTableTag($database);
		$tag->loadTag($this->model->params->get('supportedtag'));
?>
	<p class="supported">
		<a href="<?php echo $this->model->params->get('supportedlink', JRoute::_('index.php?option=com_tags&tag=' . $tag->tag)); ?>"><?php echo $this->escape(stripslashes($tag->raw_tag)); ?></a>
	</p>
<?php
	}
}

foreach ($this->sections as $section)
{
	echo (isset($section['metadata'])) ? $section['metadata'] : '';
}
?>
	<div class="clear"></div>
</div><!-- / .metadata -->