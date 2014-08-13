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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$config = array(
	'option'   => 'com_courses',
	'scope'    => $this->course->get('alias') . DS . $this->course->offering()->alias() . DS . 'asset',
	'pagename' => $this->asset->id,
	'pageid'   => '',
	'filepath' => DS . 'site' . DS . 'courses' . DS . $this->course->get('id') . DS . $this->asset->id,
	'domain'   => $this->course->get('alias')
);

$this->model->set('content', stripslashes($this->model->get('content')));
\JPluginHelper::importPlugin('content');
\JDispatcher::getInstance()->trigger('onContentPrepare', array(
	'com_courses.asset.content',
	&$this->model,
	&$config
));
?>

<header id="content-header">
	<h2><?php echo $this->asset->title ?></h2>

	<div id="content-header-extra">
		<p>
			<a class="icon-prev back btn" href="<?php echo JRoute::_($this->course->offering()->link() . '&active=outline'); ?>">
				<?php echo JText::_('Back to course'); ?>
			</a>
		</p>
	</div>
</header>

<div class="wiki-page-body">
	<p>
		<?php echo $this->model->get('content'); ?>
	</p>
</div>
