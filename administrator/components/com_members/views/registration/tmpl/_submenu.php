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
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$controller = JRequest::getCmd('controller', 'registration');
?>
<nav role="navigation" class="sub sub-navigation">
	<ul>
		<li>
			<a<?php if ($controller == 'registration') { echo ' class="active"'; } ?> href="index.php?option=com_members&amp;controller=registration">Config</a>
		</li>
		<li>
			<a<?php if ($controller == 'organizations') { echo ' class="active"'; } ?> href="index.php?option=com_members&amp;controller=organizations">Organizations</a>
		</li>
		<li>
			<a<?php if ($controller == 'employers') { echo ' class="active"'; } ?> href="index.php?option=com_members&amp;controller=employers">Employer Types</a>
		</li>
		<li>
			<a<?php if ($controller == 'incremental') { echo ' class="active"'; } ?> href="index.php?option=com_members&amp;controller=incremental">Incremental Registration</a>
		</li>
		<li>
			<a<?php if ($controller == 'premis') { echo ' class="active"'; } ?> href="index.php?option=com_members&amp;controller=premis">PREMIS Data Import</a>
		</li>
	</ul>
</nav><!-- / .sub-navigation -->