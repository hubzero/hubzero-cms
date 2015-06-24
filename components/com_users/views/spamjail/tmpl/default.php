<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$plugin = \JPluginHelper::getPlugin('system', 'spamjail');
$params = new \JRegistry($plugin->params);
?>

<header id="content-header">
	<h2><?php echo JText::_('COM_USERS_SPAM_DETECTED'); ?></h2>
</header>

<section class="section">
	<p><?php echo JText::_('COM_USERS_SPAM_MESSAGE'); ?></p>

	<?php if ($video = $params->get('spam_video', false)) : ?>
		<p><?php echo JText::_('COM_USERS_SPAM_VIDEO'); ?></p>
		<div class="video" style="text-align:center;">
			<iframe width="420" height="315" src="https://www.youtube.com/embed/<?php echo $video; ?>" frameborder="0" allowfullscreen></iframe>
		</div>
	<?php endif; ?>
</section>