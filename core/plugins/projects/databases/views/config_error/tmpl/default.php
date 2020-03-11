<?php
/**
 * @package		HUBzero CMS
 * @author		Sudheera R. Fernando <sudheera@xconsole.org>
 * @copyright	Copyright 2012-2015 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2012-2015 HUBzero Foundation, LLC.
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
defined('_HZEXEC_') or die();
?>

<div id="system-message-container">
<dl id="system-message">
	<dt class="error">Error</dt>
	<dd class="error message">
		<ul>
			<li>CONFIGURATION ERROR</li>
		</ul>
	</dd>
</dl>
<h3>Please contact the HUB Administrator.</h3>
	<p>The <strong>projects databases plugin parameters<sup>[**]</sup></strong> need to be updated with the correct mysql server host, usernames and passwords.
	<br /><br />
	** Administrator Backend -> <a rel="noopener noreferrer" target="_blank" href="/administrator/index.php?option=com_plugins">Plugin Manager</a> -> search for "projects - databases"</p>
</div>
