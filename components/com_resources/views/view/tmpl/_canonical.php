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

if ($canonical = $this->model->attribs->get('canonical', ''))
{
	$title = $canonical;
	$url   = $canonical;

	if (preg_match('/^(\/?resources\/(.+))/i', $canonical, $matches))
	{
		$model = ResourcesModelResource::getInstance($matches[2]);
		$title = $model->resource->title;
		$url   = JRoute::_('index.php?option=' . $this->option . ($model->resource->alias ? '&alias=' . $model->resource->alias : '&id=' . $model->resource->id));
	}
	else if (is_numeric($canonical))
	{
		$model = ResourcesModelResource::getInstance(intval($canonical));
		$title = $model->resource->title;
		$url   = JRoute::_('index.php?option=' . $this->option . ($model->resource->alias ? '&alias=' . $model->resource->alias : '&id=' . $model->resource->id));
	}

	if (!preg_match('/^(https?:|mailto:|ftp:|gopher:|news:|file:|rss:)/i', $url))
	{
		$url = rtrim(JURI::base(), DS) . DS . ltrim($url, DS);
	}
	?>
	<div class="new-version grid">
		<div class="col span8">
			&nbsp;
		</div>
		<div class="col span4 omega">
			<p><?php echo JText::_('COM_RESOURCES_NEWER_VER_AVAIL'); ?></p>
		</div>
	</div>
	<div class="new-version-message">
		<div class="inner">
			<h3><?php echo JText::_('COM_RESOURCES_NEWER_VER_AVAIL'); ?></h3>
			<p><?php echo JText::_('COM_RESOURCES_NEWER_VER_AVAIL_EXTENDED'); ?> <a href="<?php echo $url; ?>"><?php echo $title; ?></a></p>
		</div>
	</div>

	<?php
}