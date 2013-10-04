<?php
/**
 * @package		HUBzero CMS
 * @author		Sudheera R. Fernando <sudheera@xconsole.org>
 * @copyright	Copyright 2012-2013 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 * Copyright 2012-2013 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License,
 * version 3 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
$document =& JFactory::getDocument();

?>
<div id="prj-db-list">
	<div id="plg-header">
		<h3 class="error">CONFIGURATION ERROR</h3>
	</div>
	
	<h3>Please contact the HUB Administrator.</h3>
	<p>The <strong>projects databases plugin parameters<sup>[**]</sup></strong> need to be updated with the correct mysql server host, usernames and passwords.
	<br /><br /> 
	** Administrator Backend -> <a target="_blank" href="/administrator/index.php?option=com_plugins">Plugin Manager</a> -> search for "projects - databases"</p>
</div>
