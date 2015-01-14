<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2015 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2015 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$reveal = strtolower(JRequest::getWord('reveal', ''));

$base = rtrim(JURI::getInstance()->base(true), '/');
?>
<div id="hubzilla"<?php if ($reveal == 'eastereggs') { echo ' class="revealed"'; } ?> style="top: <?php echo $this->params->get('posTop', 'auto'); ?>; right: <?php echo $this->params->get('posRight', '5px'); ?>; bottom: <?php echo $this->params->get('posBottom', '5px'); ?>; left: <?php echo $this->params->get('posLeft', 'auto'); ?>;">
	<audio preload="auto" id="hubzilla-roar">
		<source src="<?php echo $base; ?>/modules/mod_hubzilla/assets/sounds/roar.ogg" type="audio/ogg" />
		<source src="<?php echo $base; ?>/modules/mod_hubzilla/assets/sounds/roar.mp3" type="audio/mp3" />
	</audio>
</div>