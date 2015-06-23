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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();


$reveal = strtolower(Request::getWord('reveal', ''));

$base = rtrim(Request::base(true), '/');
?>
<div id="hubzilla"<?php if ($reveal == 'eastereggs') { echo ' class="revealed"'; } ?> style="top: <?php echo $this->params->get('posTop', 'auto'); ?>; right: <?php echo $this->params->get('posRight', '5px'); ?>; bottom: <?php echo $this->params->get('posBottom', '5px'); ?>; left: <?php echo $this->params->get('posLeft', 'auto'); ?>;">
	<audio preload="auto" id="hubzilla-roar">
		<source src="<?php echo $base; ?>/modules/mod_hubzilla/assets/sounds/roar.ogg" type="audio/ogg" />
		<source src="<?php echo $base; ?>/modules/mod_hubzilla/assets/sounds/roar.mp3" type="audio/mp3" />
	</audio>
</div>